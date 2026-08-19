import { useState } from 'react';
import { supabase } from '@/lib/supabase';
import type { Treatment } from '@/lib/types';
import { Send, CheckCircle, AlertCircle, Loader2 } from 'lucide-react';

interface AppointmentFormProps {
  treatments: Treatment[];
  compact?: boolean;
}

export default function AppointmentForm({ treatments, compact = false }: AppointmentFormProps) {
  const [form, setForm] = useState({
    name: '',
    phone: '',
    email: '',
    treatment_id: '',
    preferred_date: '',
    preferred_time: '',
    message: '',
  });
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');
  const [errorMsg, setErrorMsg] = useState('');

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
  ) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.name.trim() || !form.phone.trim()) {
      setStatus('error');
      setErrorMsg('Please fill in your name and phone number.');
      return;
    }

    const honeypot = (e.target as HTMLFormElement).querySelector('input[name="website"]') as HTMLInputElement;
    if (honeypot && honeypot.value) {
      return;
    }

    setStatus('loading');
    setErrorMsg('');

    const selectedTreatment = treatments.find((t) => t.id === form.treatment_id);
    const treatmentCategory = selectedTreatment?.category?.name || selectedTreatment?.title || '';

    const { error } = await supabase.from('appointments').insert({
      name: form.name.trim(),
      phone: form.phone.trim(),
      email: form.email.trim() || null,
      treatment_id: form.treatment_id || null,
      treatment_category: treatmentCategory || null,
      preferred_date: form.preferred_date || null,
      preferred_time: form.preferred_time || null,
      message: form.message.trim() || null,
    });

    if (error) {
      setStatus('error');
      setErrorMsg('Something went wrong. Please try again or call us directly.');
      return;
    }

    setStatus('success');
    setForm({
      name: '',
      phone: '',
      email: '',
      treatment_id: '',
      preferred_date: '',
      preferred_time: '',
      message: '',
    });
  };

  if (status === 'success') {
    return (
      <div className="flex flex-col items-center justify-center py-12 text-center">
        <div className="flex h-16 w-16 items-center justify-center bg-soft-red rounded-full mb-6">
          <CheckCircle size={32} className="text-crimson" />
        </div>
        <h3 className="heading-3 mb-3">Request Received</h3>
        <p className="body-text max-w-md mb-6">
          Thank you for reaching out. Our team will contact you shortly to confirm your appointment.
        </p>
        <button
          onClick={() => setStatus('idle')}
          className="btn-secondary"
        >
          Book Another Appointment
        </button>
      </div>
    );
  }

  const inputClass = 'w-full px-4 py-3 bg-white border border-charcoal/10 text-sm text-charcoal placeholder:text-charcoal/30 focus:outline-none focus:border-crimson transition-colors';

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      {/* Honeypot field */}
      <input type="text" name="website" value="" onChange={() => {}} className="hidden" tabIndex={-1} autoComplete="off" />

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/60 mb-2">
            Name *
          </label>
          <input
            type="text"
            name="name"
            value={form.name}
            onChange={handleChange}
            required
            className={inputClass}
            placeholder="Your full name"
          />
        </div>
        <div>
          <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/60 mb-2">
            Phone *
          </label>
          <input
            type="tel"
            name="phone"
            value={form.phone}
            onChange={handleChange}
            required
            className={inputClass}
            placeholder="Your phone number"
          />
        </div>
      </div>

      <div>
        <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/60 mb-2">
          Email
        </label>
        <input
          type="email"
          name="email"
          value={form.email}
          onChange={handleChange}
          className={inputClass}
          placeholder="Your email address"
        />
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/60 mb-2">
            Treatment
          </label>
          <select name="treatment_id" value={form.treatment_id} onChange={handleChange} className={inputClass}>
            <option value="">Select a treatment</option>
            {treatments.map((t) => (
              <option key={t.id} value={t.id}>
                {t.title}
              </option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/60 mb-2">
            Preferred Date
          </label>
          <input
            type="date"
            name="preferred_date"
            value={form.preferred_date}
            onChange={handleChange}
            min={new Date().toISOString().split('T')[0]}
            className={inputClass}
          />
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/60 mb-2">
            Preferred Time
          </label>
          <select name="preferred_time" value={form.preferred_time} onChange={handleChange} className={inputClass}>
            <option value="">Select time</option>
            <option value="morning">Morning (9AM - 12PM)</option>
            <option value="afternoon">Afternoon (12PM - 4PM)</option>
            <option value="evening">Evening (4PM - 7PM)</option>
          </select>
        </div>
        <div className={`${compact ? 'hidden' : ''}`}>
          <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/60 mb-2">
            &nbsp;
          </label>
          <p className="text-xs text-charcoal/40 py-3">
            We'll confirm availability within 24 hours.
          </p>
        </div>
      </div>

      <div>
        <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/60 mb-2">
          Message
        </label>
        <textarea
          name="message"
          value={form.message}
          onChange={handleChange}
          rows={compact ? 3 : 4}
          className={inputClass}
          placeholder="Tell us about your concerns or any questions you have"
        />
      </div>

      {status === 'error' && (
        <div className="flex items-center gap-2 p-3 bg-soft-red text-crimson text-sm">
          <AlertCircle size={16} />
          <span>{errorMsg}</span>
        </div>
      )}

      <button type="submit" disabled={status === 'loading'} className="btn-primary w-full disabled:opacity-50">
        {status === 'loading' ? (
          <>
            <Loader2 size={16} className="animate-spin" />
            Sending Request...
          </>
        ) : (
          <>
            <Send size={16} />
            Book Appointment
          </>
        )}
      </button>
    </form>
  );
}
