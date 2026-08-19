import { useEffect, useState, useCallback } from 'react';
import { supabase } from '@/lib/supabase';
import type { Category } from '@/lib/types';
import { Plus, Pencil, Trash2, X } from 'lucide-react';

export default function AdminCategories() {
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);
  const [editing, setEditing] = useState<Partial<Category> | null>(null);
  const [saving, setSaving] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    const { data } = await supabase.from('categories').select('*').order('display_order');
    setCategories(data || []);
    setLoading(false);
  }, []);

  useEffect(() => { load(); }, [load]);

  const save = async () => {
    if (!editing || !editing.name || !editing.slug) return;
    setSaving(true);
    const slug = editing.slug.trim().toLowerCase().replace(/\s+/g, '-');
    if (editing.id) {
      await supabase.from('categories').update({ ...editing, slug }).eq('id', editing.id);
    } else {
      await supabase.from('categories').insert({ ...editing, slug });
    }
    setSaving(false);
    setEditing(null);
    load();
  };

  const remove = async (id: string) => {
    if (!confirm('Delete this category? All treatments in this category will also be deleted.')) return;
    await supabase.from('categories').delete().eq('id', id);
    load();
  };

  const inputClass = 'w-full px-3 py-2 bg-white border border-charcoal/10 text-sm text-charcoal focus:outline-none focus:border-crimson transition-colors';
  const labelClass = 'block text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-1.5';

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="font-serif text-3xl font-bold text-charcoal mb-1">Categories</h1>
          <p className="text-sm text-charcoal/50">Organize treatments into categories.</p>
        </div>
        <button onClick={() => setEditing({ name: '', slug: '', description: '', icon: '', display_order: 0 })} className="btn-primary !px-4 !py-2.5 !text-xs">
          <Plus size={16} />
          Add Category
        </button>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {loading ? (
          <p className="text-sm text-charcoal/40">Loading...</p>
        ) : categories.length === 0 ? (
          <p className="text-sm text-charcoal/40">No categories yet.</p>
        ) : (
          categories.map((cat) => (
            <div key={cat.id} className="bg-white border border-charcoal/5 p-5">
              <div className="flex items-start justify-between mb-3">
                <div>
                  <h3 className="font-serif text-lg font-semibold text-charcoal">{cat.name}</h3>
                  <p className="text-xs text-charcoal/40">/{cat.slug}</p>
                </div>
                <div className="flex items-center gap-2">
                  <button onClick={() => setEditing({ ...cat })} className="p-1.5 text-charcoal/40 hover:text-crimson transition-colors">
                    <Pencil size={15} />
                  </button>
                  <button onClick={() => remove(cat.id)} className="p-1.5 text-charcoal/40 hover:text-red-600 transition-colors">
                    <Trash2 size={15} />
                  </button>
                </div>
              </div>
              <p className="text-sm text-charcoal/60 leading-relaxed">{cat.description || 'No description'}</p>
              <p className="text-xs text-charcoal/40 mt-3">Order: {cat.display_order}</p>
            </div>
          ))
        )}
      </div>

      {editing && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-charcoal/40 backdrop-blur-sm" onClick={() => setEditing(null)}>
          <div className="bg-white max-w-md w-full" onClick={(e) => e.stopPropagation()}>
            <div className="flex items-center justify-between p-5 border-b border-charcoal/5">
              <h2 className="font-serif text-xl font-semibold text-charcoal">
                {editing.id ? 'Edit Category' : 'Add Category'}
              </h2>
              <button onClick={() => setEditing(null)}><X size={20} className="text-charcoal/40 hover:text-charcoal" /></button>
            </div>
            <div className="p-5 space-y-4">
              <div>
                <label className={labelClass}>Name *</label>
                <input type="text" value={editing.name || ''} onChange={(e) => setEditing({ ...editing, name: e.target.value })} className={inputClass} />
              </div>
              <div>
                <label className={labelClass}>Slug *</label>
                <input type="text" value={editing.slug || ''} onChange={(e) => setEditing({ ...editing, slug: e.target.value })} className={inputClass} placeholder="e.g. skin-treatments" />
              </div>
              <div>
                <label className={labelClass}>Description</label>
                <textarea value={editing.description || ''} onChange={(e) => setEditing({ ...editing, description: e.target.value })} rows={3} className={inputClass} />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Display Order</label>
                  <input type="number" value={editing.display_order || 0} onChange={(e) => setEditing({ ...editing, display_order: parseInt(e.target.value) || 0 })} className={inputClass} />
                </div>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 p-5 border-t border-charcoal/5">
              <button onClick={() => setEditing(null)} className="btn-secondary !px-5 !py-2.5 !text-xs">Cancel</button>
              <button onClick={save} disabled={saving} className="btn-primary !px-5 !py-2.5 !text-xs disabled:opacity-50">
                {saving ? 'Saving...' : 'Save'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
