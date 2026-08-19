import { useEffect, useState, useCallback } from 'react';
import { supabase } from '@/lib/supabase';
import type { BlogPost, BlogCategory } from '@/lib/types';
import { Plus, Pencil, Trash2, X, Search, Eye, EyeOff } from 'lucide-react';

export default function AdminBlog() {
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [categories, setCategories] = useState<BlogCategory[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [editing, setEditing] = useState<Partial<BlogPost> | null>(null);
  const [saving, setSaving] = useState(false);
  const [showCatModal, setShowCatModal] = useState(false);
  const [newCat, setNewCat] = useState({ name: '', slug: '' });

  const load = useCallback(async () => {
    setLoading(true);
    const [{ data: postData }, { data: catData }] = await Promise.all([
      supabase.from('blog_posts').select('*, blog_category:blog_categories(*)').order('created_at', { ascending: false }),
      supabase.from('blog_categories').select('*').order('display_order'),
    ]);
    setPosts(postData || []);
    setCategories(catData || []);
    setLoading(false);
  }, []);

  useEffect(() => { load(); }, [load]);

  const filtered = posts.filter((p) =>
    (!search || p.title.toLowerCase().includes(search.toLowerCase())) &&
    (statusFilter === 'all' || p.status === statusFilter)
  );

  const startEdit = (post: BlogPost | null) => {
    if (post) {
      setEditing({ ...post });
    } else {
      setEditing({
        title: '',
        slug: '',
        featured_image: '',
        excerpt: '',
        content: '',
        author: '',
        blog_category_id: categories[0]?.id || '',
        tags: '',
        seo_title: '',
        seo_description: '',
        meta_keywords: '',
        published_at: new Date().toISOString().split('T')[0],
        status: 'draft',
      });
    }
  };

  const save = async () => {
    if (!editing || !editing.title || !editing.slug) return;
    setSaving(true);
    const slug = editing.slug.trim().toLowerCase().replace(/\s+/g, '-');
    if (editing.id) {
      await supabase.from('blog_posts').update({ ...editing, slug, updated_at: new Date().toISOString() }).eq('id', editing.id);
    } else {
      await supabase.from('blog_posts').insert({ ...editing, slug });
    }
    setSaving(false);
    setEditing(null);
    load();
  };

  const remove = async (id: string) => {
    if (!confirm('Delete this blog post?')) return;
    await supabase.from('blog_posts').delete().eq('id', id);
    load();
  };

  const addCategory = async () => {
    if (!newCat.name || !newCat.slug) return;
    const slug = newCat.slug.trim().toLowerCase().replace(/\s+/g, '-');
    await supabase.from('blog_categories').insert({ name: newCat.name, slug, display_order: categories.length });
    setNewCat({ name: '', slug: '' });
    setShowCatModal(false);
    load();
  };

  const inputClass = 'w-full px-3 py-2 bg-white border border-charcoal/10 text-sm text-charcoal focus:outline-none focus:border-crimson transition-colors';
  const labelClass = 'block text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-1.5';

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="font-serif text-3xl font-bold text-charcoal mb-1">Blog Posts</h1>
          <p className="text-sm text-charcoal/50">Manage articles and blog categories.</p>
        </div>
        <div className="flex items-center gap-2">
          <button onClick={() => setShowCatModal(true)} className="btn-secondary !px-4 !py-2.5 !text-xs">
            <Plus size={16} />
            Category
          </button>
          <button onClick={() => startEdit(null)} className="btn-primary !px-4 !py-2.5 !text-xs">
            <Plus size={16} />
            Add Post
          </button>
        </div>
      </div>

      <div className="flex flex-col sm:flex-row gap-3 mb-4">
        <div className="relative flex-1 max-w-sm">
          <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-charcoal/30" />
          <input type="text" placeholder="Search posts..." value={search} onChange={(e) => setSearch(e.target.value)} className={`${inputClass} pl-9`} />
        </div>
        <select value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)} className={`${inputClass} max-w-[180px]`}>
          <option value="all">All Statuses</option>
          <option value="published">Published</option>
          <option value="draft">Draft</option>
        </select>
      </div>

      <div className="bg-white border border-charcoal/5 overflow-x-auto">
        {loading ? (
          <p className="p-8 text-center text-sm text-charcoal/40">Loading...</p>
        ) : filtered.length === 0 ? (
          <p className="p-8 text-center text-sm text-charcoal/40">No posts found.</p>
        ) : (
          <table className="w-full">
            <thead>
              <tr className="border-b border-charcoal/5">
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Title</th>
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3 hidden sm:table-cell">Category</th>
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3 hidden md:table-cell">Date</th>
                <th className="text-center text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Status</th>
                <th className="text-right text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-charcoal/5">
              {filtered.map((post) => (
                <tr key={post.id} className="hover:bg-ivory/50 transition-colors">
                  <td className="px-5 py-4">
                    <p className="font-medium text-sm text-charcoal">{post.title}</p>
                    <p className="text-xs text-charcoal/40">/blog/{post.slug}</p>
                  </td>
                  <td className="px-5 py-4 hidden sm:table-cell">
                    <span className="text-xs px-2.5 py-1 bg-soft-red text-crimson">{post.blog_category?.name || '—'}</span>
                  </td>
                  <td className="px-5 py-4 hidden md:table-cell">
                    <p className="text-sm text-charcoal/70">{post.published_at || '—'}</p>
                  </td>
                  <td className="px-5 py-4 text-center">
                    <span className={`inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 ${post.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}`}>
                      {post.status === 'published' ? <Eye size={12} /> : <EyeOff size={12} />}
                      {post.status}
                    </span>
                  </td>
                  <td className="px-5 py-4 text-right">
                    <div className="flex items-center justify-end gap-2">
                      <button onClick={() => startEdit(post)} className="p-1.5 text-charcoal/40 hover:text-crimson transition-colors">
                        <Pencil size={15} />
                      </button>
                      <button onClick={() => remove(post.id)} className="p-1.5 text-charcoal/40 hover:text-red-600 transition-colors">
                        <Trash2 size={15} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>

      {/* Edit Modal */}
      {editing && (
        <div className="fixed inset-0 z-50 flex items-start justify-center p-4 bg-charcoal/40 backdrop-blur-sm overflow-y-auto" onClick={() => setEditing(null)}>
          <div className="bg-white max-w-2xl w-full my-8" onClick={(e) => e.stopPropagation()}>
            <div className="flex items-center justify-between p-5 border-b border-charcoal/5 sticky top-0 bg-white z-10">
              <h2 className="font-serif text-xl font-semibold text-charcoal">
                {editing.id ? 'Edit Post' : 'Add Post'}
              </h2>
              <button onClick={() => setEditing(null)}><X size={20} className="text-charcoal/40 hover:text-charcoal" /></button>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Title *</label>
                  <input type="text" value={editing.title || ''} onChange={(e) => setEditing({ ...editing, title: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>Slug *</label>
                  <input type="text" value={editing.slug || ''} onChange={(e) => setEditing({ ...editing, slug: e.target.value })} className={inputClass} placeholder="e.g. acne-treatment-guide" />
                </div>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Category</label>
                  <select value={editing.blog_category_id || ''} onChange={(e) => setEditing({ ...editing, blog_category_id: e.target.value })} className={inputClass}>
                    <option value="">Select category</option>
                    {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                  </select>
                </div>
                <div>
                  <label className={labelClass}>Author</label>
                  <input type="text" value={editing.author || ''} onChange={(e) => setEditing({ ...editing, author: e.target.value })} className={inputClass} />
                </div>
              </div>
              <div>
                <label className={labelClass}>Featured Image URL</label>
                <input type="text" value={editing.featured_image || ''} onChange={(e) => setEditing({ ...editing, featured_image: e.target.value })} className={inputClass} placeholder="https://..." />
              </div>
              <div>
                <label className={labelClass}>Excerpt</label>
                <textarea value={editing.excerpt || ''} onChange={(e) => setEditing({ ...editing, excerpt: e.target.value })} rows={2} className={inputClass} />
              </div>
              <div>
                <label className={labelClass}>Content (Markdown-style)</label>
                <textarea value={editing.content || ''} onChange={(e) => setEditing({ ...editing, content: e.target.value })} rows={8} className={`${inputClass} font-mono text-xs`} placeholder="# Heading&#10;Paragraph text...&#10;&#10;## Subheading&#10;- List item" />
              </div>
              <div>
                <label className={labelClass}>Tags (comma-separated)</label>
                <input type="text" value={editing.tags || ''} onChange={(e) => setEditing({ ...editing, tags: e.target.value })} className={inputClass} placeholder="acne, skincare, treatment" />
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>SEO Title</label>
                  <input type="text" value={editing.seo_title || ''} onChange={(e) => setEditing({ ...editing, seo_title: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>SEO Description</label>
                  <input type="text" value={editing.seo_description || ''} onChange={(e) => setEditing({ ...editing, seo_description: e.target.value })} className={inputClass} />
                </div>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                  <label className={labelClass}>Meta Keywords</label>
                  <input type="text" value={editing.meta_keywords || ''} onChange={(e) => setEditing({ ...editing, meta_keywords: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>Publish Date</label>
                  <input type="date" value={editing.published_at || ''} onChange={(e) => setEditing({ ...editing, published_at: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>Status</label>
                  <select value={editing.status || 'draft'} onChange={(e) => setEditing({ ...editing, status: e.target.value })} className={inputClass}>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                  </select>
                </div>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 p-5 border-t border-charcoal/5 sticky bottom-0 bg-white">
              <button onClick={() => setEditing(null)} className="btn-secondary !px-5 !py-2.5 !text-xs">Cancel</button>
              <button onClick={save} disabled={saving} className="btn-primary !px-5 !py-2.5 !text-xs disabled:opacity-50">
                {saving ? 'Saving...' : 'Save Post'}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Category Modal */}
      {showCatModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-charcoal/40 backdrop-blur-sm" onClick={() => setShowCatModal(false)}>
          <div className="bg-white max-w-md w-full" onClick={(e) => e.stopPropagation()}>
            <div className="flex items-center justify-between p-5 border-b border-charcoal/5">
              <h2 className="font-serif text-xl font-semibold text-charcoal">Add Blog Category</h2>
              <button onClick={() => setShowCatModal(false)}><X size={20} className="text-charcoal/40 hover:text-charcoal" /></button>
            </div>
            <div className="p-5 space-y-4">
              <div>
                <label className={labelClass}>Name *</label>
                <input type="text" value={newCat.name} onChange={(e) => setNewCat({ ...newCat, name: e.target.value })} className={inputClass} />
              </div>
              <div>
                <label className={labelClass}>Slug *</label>
                <input type="text" value={newCat.slug} onChange={(e) => setNewCat({ ...newCat, slug: e.target.value })} className={inputClass} placeholder="e.g. skin-care" />
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 p-5 border-t border-charcoal/5">
              <button onClick={() => setShowCatModal(false)} className="btn-secondary !px-5 !py-2.5 !text-xs">Cancel</button>
              <button onClick={addCategory} className="btn-primary !px-5 !py-2.5 !text-xs">Add</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
