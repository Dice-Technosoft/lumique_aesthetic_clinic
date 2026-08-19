import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import type { ClinicSettings } from '@/lib/types';
import { Save, Settings as SettingsIcon } from 'lucide-react';

export default function AdminSettings() {
  const [settings, setSettings] = useState<Partial<ClinicSettings>>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);

  useEffect(() => {
    (async () => {
      const { data } = await supabase.from('clinic_settings').select('*').maybeSingle();
      setSettings(data || {});
      setLoading(false);
    })();
  }, []);

  const save = async () => {
    setSaving(true);
    setSaved(false);
    if (settings.id) {
      await supabase.from('clinic_settings').update({ ...settings, updated_at: new Date().toISOString() }).eq('id', settings.id);
    } else {
      const { data } = await supabase.from('clinic_settings').insert(settings).select().maybeSingle();
      if (data) setSettings(data);
    }
    setSaving(false);
    setSaved(true);
    window.setTimeout(() => setSaved(false), 2500);
  };

  const inputClass = 'w-full px-3 py-2.5 bg-white border border-charcoal/10 text-sm text-charcoal focus:outline-none focus:border-crimson transition-colors';
  const labelClass = 'block text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-1.5';
  const update = (key: keyof ClinicSettings, value: string) => setSettings({ ...settings, [key]: value });

  if (loading) return <p className="text-sm text-charcoal/40">Loading settings...</p>;

  return (
    <div className="max-w-4xl">
      <div className="flex items-start justify-between gap-4 mb-6">
        <div>
          <h1 className="font-serif text-3xl font-bold text-charcoal mb-1">Clinic Settings</h1>
          <p className="text-sm text-charcoal/50">Update the contact details shown across the public site.</p>
        </div>
        <button onClick={save} disabled={saving} className="btn-primary !px-4 !py-2.5 !text-xs disabled:opacity-50">
          <Save size={15} /> {saving ? 'Saving...' : saved ? 'Saved' : 'Save Changes'}
        </button>
      </div>

      <div className="bg-white border border-charcoal/5 p-5 sm:p-8">
        <div className="flex items-center gap-3 pb-6 mb-6 border-b border-charcoal/5">
          <div className="flex h-11 w-11 items-center justify-center bg-soft-red">
            <SettingsIcon size={21} className="text-crimson" />
          </div>
          <div>
            <h2 className="font-serif text-lg font-semibold text-charcoal">Public clinic information</h2>
            <p className="text-xs text-charcoal/40">These details appear on the contact page and footer.</p>
          </div>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label className={labelClass}>Clinic name</label>
            <input value={settings.clinic_name || ''} onChange={(e) => update('clinic_name', e.target.value)} className={inputClass} />
          </div>
          <div>
            <label className={labelClass}>Email</label>
            <input type="email" value={settings.email || ''} onChange={(e) => update('email', e.target.value)} className={inputClass} />
          </div>
          <div>
            <label className={labelClass}>Phone</label>
            <input value={settings.phone || ''} onChange={(e) => update('phone', e.target.value)} className={inputClass} />
          </div>
          <div>
            <label className={labelClass}>WhatsApp</label>
            <input value={settings.whatsapp || ''} onChange={(e) => update('whatsapp', e.target.value)} className={inputClass} />
          </div>
          <div className="sm:col-span-2">
            <label className={labelClass}>Address</label>
            <textarea rows={2} value={settings.address || ''} onChange={(e) => update('address', e.target.value)} className={inputClass} />
          </div>
          <div className="sm:col-span-2">
            <label className={labelClass}>Working hours</label>
            <textarea rows={3} value={settings.working_hours || ''} onChange={(e) => update('working_hours', e.target.value)} className={inputClass} placeholder="Monday - Friday: 9:00 AM - 7:00 PM" />
          </div>
          <div className="sm:col-span-2">
            <label className={labelClass}>Google Maps embed HTML</label>
            <textarea rows={4} value={settings.map_embed || ''} onChange={(e) => update('map_embed', e.target.value)} className={`${inputClass} font-mono text-xs`} placeholder="Paste the Google Maps iframe embed code" />
          </div>
        </div>
      </div>
    </div>
  );
}
