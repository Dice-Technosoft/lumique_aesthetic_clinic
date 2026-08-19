import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import type { DoctorProfile } from '@/lib/types';
import { Save, UserRound } from 'lucide-react';

const fields: Array<{ key: keyof DoctorProfile; label: string; rows: number }> = [
  { key: 'name', label: 'Doctor name', rows: 1 },
  { key: 'title', label: 'Professional title', rows: 1 },
  { key: 'photo', label: 'Photo URL', rows: 1 },
  { key: 'introduction', label: 'Introduction', rows: 4 },
  { key: 'professional_profile', label: 'Professional profile', rows: 5 },
  { key: 'qualifications', label: 'Qualifications', rows: 4 },
  { key: 'experience', label: 'Experience', rows: 4 },
  { key: 'specializations', label: 'Specializations', rows: 4 },
  { key: 'areas_of_expertise', label: 'Areas of expertise', rows: 4 },
  { key: 'treatment_philosophy', label: 'Treatment philosophy', rows: 4 },
  { key: 'clinic_approach', label: 'Clinic approach', rows: 4 },
  { key: 'certifications', label: 'Certifications', rows: 4 },
  { key: 'achievements', label: 'Professional achievements', rows: 4 },
];

export default function AdminDoctor() {
  const [profile, setProfile] = useState<Partial<DoctorProfile>>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);

  useEffect(() => {
    (async () => {
      const { data } = await supabase.from('doctor_profile').select('*').maybeSingle();
      setProfile(data || {});
      setLoading(false);
    })();
  }, []);

  const save = async () => {
    setSaving(true);
    setSaved(false);
    if (profile.id) {
      await supabase.from('doctor_profile').update({ ...profile, updated_at: new Date().toISOString() }).eq('id', profile.id);
    } else {
      const { data } = await supabase.from('doctor_profile').insert(profile).select().maybeSingle();
      if (data) setProfile(data);
    }
    setSaving(false);
    setSaved(true);
    window.setTimeout(() => setSaved(false), 2500);
  };

  const inputClass = 'w-full px-3 py-2.5 bg-white border border-charcoal/10 text-sm text-charcoal focus:outline-none focus:border-crimson transition-colors';
  const labelClass = 'block text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-1.5';

  if (loading) return <p className="text-sm text-charcoal/40">Loading doctor profile...</p>;

  return (
    <div className="max-w-4xl">
      <div className="flex items-start justify-between gap-4 mb-6">
        <div>
          <h1 className="font-serif text-3xl font-bold text-charcoal mb-1">Doctor Profile</h1>
          <p className="text-sm text-charcoal/50">Keep public professional information accurate and current.</p>
        </div>
        <button onClick={save} disabled={saving} className="btn-primary !px-4 !py-2.5 !text-xs disabled:opacity-50">
          <Save size={15} /> {saving ? 'Saving...' : saved ? 'Saved' : 'Save Changes'}
        </button>
      </div>

      <div className="bg-white border border-charcoal/5 p-5 sm:p-8">
        <div className="flex items-center gap-3 pb-6 mb-6 border-b border-charcoal/5">
          <div className="flex h-11 w-11 items-center justify-center bg-soft-red">
            <UserRound size={21} className="text-crimson" />
          </div>
          <div>
            <h2 className="font-serif text-lg font-semibold text-charcoal">Public profile content</h2>
            <p className="text-xs text-charcoal/40">Only enter verified credentials and achievements.</p>
          </div>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
          {fields.map((field) => (
            <div key={field.key} className={field.rows > 1 ? 'sm:col-span-2' : ''}>
              <label className={labelClass}>{field.label}</label>
              {field.rows > 1 ? (
                <textarea
                  rows={field.rows}
                  value={(profile[field.key] as string) || ''}
                  onChange={(e) => setProfile({ ...profile, [field.key]: e.target.value })}
                  className={inputClass}
                />
              ) : (
                <input
                  type="text"
                  value={(profile[field.key] as string) || ''}
                  onChange={(e) => setProfile({ ...profile, [field.key]: e.target.value })}
                  className={inputClass}
                />
              )}
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
