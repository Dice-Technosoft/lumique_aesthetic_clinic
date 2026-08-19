import { useEffect, useState, useCallback } from 'react';
import { supabase } from '@/lib/supabase';
import type { Treatment, Category } from '@/lib/types';
import { Plus, Pencil, Trash2, X, Star, Search } from 'lucide-react';

const emptyForm: Partial<Treatment> = {
  title: '',
  slug: '',
  short_intro: '',
  hero_image: '',
  description: '',
  who_is_it_for: '',
  benefits: '',
  procedure_overview: '',
  treatment_process: '',
  expected_results: '',
  recovery_info: '',
  num_sessions: '',
  doctor_recommendation: '',
  is_featured: false,
  display_order: 0,
  category_id: '',
};

export default function AdminTreatments() {
  const [treatments, setTreatments] = useState<Treatment[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [editing, setEditing] = useState<Partial<Treatment> | null>(null);
  const [saving, setSaving] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    const [{ data: treats }, { data: cats }] = await Promise.all([
      supabase.from('treatments').select('*, category:categories(*)').order('display_order'),
      supabase.from('categories').select('*').order('display_order'),
    ]);
    setTreatments(treats || []);
    setCategories(cats || []);
    setLoading(false);
  }, []);

  useEffect(() => { load(); }, [load]);

  const filtered = treatments.filter((t) =>
    !search || t.title.toLowerCase().includes(search.toLowerCase()) || t.slug.includes(search.toLowerCase())
  );

  const startEdit = (t: Treatment | null) => {
    if (t) {
      setEditing({ ...t });
    } else {
      setEditing({ ...emptyForm, category_id: categories[0]?.id || '' });
    }
  };

  const save = async () => {
    if (!editing || !editing.title || !editing.slug || !editing.category_id) return;
    setSaving(true);
    const slug = editing.slug.trim().toLowerCase().replace(/\s+/g, '-');

    if (editing.id) {
      await supabase.from('treatments').update({
        ...editing,
        slug,
        updated_at: new Date().toISOString(),
      }).eq('id', editing.id);
    } else {
      await supabase.from('treatments').insert({ ...editing, slug });
    }
    setSaving(false);
    setEditing(null);
    load();
  };

  const remove = async (id: string) => {
    if (!confirm('Delete this treatment? All its content will be removed.')) return;
    await supabase.from('treatments').delete().eq('id', id);
    load();
  };

  const inputClass = 'w-full px-3 py-2 bg-white border border-charcoal/10 text-sm text-charcoal focus:outline-none focus:border-crimson transition-colors';
  const labelClass = 'block text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-1.5';

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="font-serif text-3xl font-bold text-charcoal mb-1">Treatments</h1>
          <p className="text-sm text-charcoal/50">Manage all treatment services.</p>
        </div>
        <button onClick={() => startEdit(null)} className="btn-primary !px-4 !py-2.5 !text-xs">
          <Plus size={16} />
          Add Treatment
        </button>
      </div>

      <div className="relative mb-4 max-w-sm">
        <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-charcoal/30" />
        <input
          type="text"
          placeholder="Search treatments..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className={`${inputClass} pl-9`}
        />
      </div>

      <div className="bg-white border border-charcoal/5 overflow-x-auto">
        {loading ? (
          <p className="p-8 text-center text-sm text-charcoal/40">Loading...</p>
        ) : filtered.length === 0 ? (
          <p className="p-8 text-center text-sm text-charcoal/40">No treatments found.</p>
        ) : (
          <table className="w-full">
            <thead>
              <tr className="border-b border-charcoal/5">
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Title</th>
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3 hidden sm:table-cell">Category</th>
                <th className="text-center text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Featured</th>
                <th className="text-right text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-charcoal/5">
              {filtered.map((t) => (
                <tr key={t.id} className="hover:bg-ivory/50 transition-colors">
                  <td className="px-5 py-4">
                    <p className="font-medium text-sm text-charcoal">{t.title}</p>
                    <p className="text-xs text-charcoal/40">/treatments/{t.slug}</p>
                  </td>
                  <td className="px-5 py-4 hidden sm:table-cell">
                    <span className="text-xs px-2.5 py-1 bg-soft-red text-crimson">{t.category?.name || '—'}</span>
                  </td>
                  <td className="px-5 py-4 text-center">
                    {t.is_featured && <Star size={16} className="text-gold inline" fill="currentColor" />}
                  </td>
                  <td className="px-5 py-4 text-right">
                    <div className="flex items-center justify-end gap-2">
                      <button onClick={() => startEdit(t)} className="p-1.5 text-charcoal/40 hover:text-crimson transition-colors">
                        <Pencil size={15} />
                      </button>
                      <button onClick={() => remove(t.id)} className="p-1.5 text-charcoal/40 hover:text-red-600 transition-colors">
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
                {editing.id ? 'Edit Treatment' : 'Add Treatment'}
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
                  <input type="text" value={editing.slug || ''} onChange={(e) => setEditing({ ...editing, slug: e.target.value })} className={inputClass} placeholder="e.g. acne-treatment" />
                </div>
              </div>
              <div>
                <label className={labelClass}>Category *</label>
                <select value={editing.category_id || ''} onChange={(e) => setEditing({ ...editing, category_id: e.target.value })} className={inputClass}>
                  <option value="">Select category</option>
                  {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                </select>
              </div>
              <div>
                <label className={labelClass}>Hero Image URL</label>
                <input type="text" value={editing.hero_image || ''} onChange={(e) => setEditing({ ...editing, hero_image: e.target.value })} className={inputClass} placeholder="https://..." />
              </div>
              <div>
                <label className={labelClass}>Short Intro</label>
                <textarea value={editing.short_intro || ''} onChange={(e) => setEditing({ ...editing, short_intro: e.target.value })} rows={2} className={inputClass} />
              </div>
              <div>
                <label className={labelClass}>Description</label>
                <textarea value={editing.description || ''} onChange={(e) => setEditing({ ...editing, description: e.target.value })} rows={3} className={inputClass} />
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Who Is It For</label>
                  <textarea value={editing.who_is_it_for || ''} onChange={(e) => setEditing({ ...editing, who_is_it_for: e.target.value })} rows={2} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>Benefits</label>
                  <textarea value={editing.benefits || ''} onChange={(e) => setEditing({ ...editing, benefits: e.target.value })} rows={2} className={inputClass} />
                </div>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Procedure Overview</label>
                  <textarea value={editing.procedure_overview || ''} onChange={(e) => setEditing({ ...editing, procedure_overview: e.target.value })} rows={2} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>Treatment Process</label>
                  <textarea value={editing.treatment_process || ''} onChange={(e) => setEditing({ ...editing, treatment_process: e.target.value })} rows={2} className={inputClass} />
                </div>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Expected Results</label>
                  <textarea value={editing.expected_results || ''} onChange={(e) => setEditing({ ...editing, expected_results: e.target.value })} rows={2} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>Recovery Info</label>
                  <textarea value={editing.recovery_info || ''} onChange={(e) => setEditing({ ...editing, recovery_info: e.target.value })} rows={2} className={inputClass} />
                </div>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Number of Sessions</label>
                  <input type="text" value={editing.num_sessions || ''} onChange={(e) => setEditing({ ...editing, num_sessions: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>Display Order</label>
                  <input type="number" value={editing.display_order || 0} onChange={(e) => setEditing({ ...editing, display_order: parseInt(e.target.value) || 0 })} className={inputClass} />
                </div>
              </div>
              <div>
                <label className={labelClass}>Doctor Recommendation</label>
                <textarea value={editing.doctor_recommendation || ''} onChange={(e) => setEditing({ ...editing, doctor_recommendation: e.target.value })} rows={2} className={inputClass} />
              </div>
              <label className="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" checked={editing.is_featured || false} onChange={(e) => setEditing({ ...editing, is_featured: e.target.checked })} className="accent-crimson" />
                <span className="text-sm text-charcoal">Featured on homepage</span>
              </label>
            </div>
            <div className="flex items-center justify-end gap-3 p-5 border-t border-charcoal/5 sticky bottom-0 bg-white">
              <button onClick={() => setEditing(null)} className="btn-secondary !px-5 !py-2.5 !text-xs">Cancel</button>
              <button onClick={save} disabled={saving} className="btn-primary !px-5 !py-2.5 !text-xs disabled:opacity-50">
                {saving ? 'Saving...' : 'Save Treatment'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
