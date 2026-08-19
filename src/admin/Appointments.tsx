import { useEffect, useState, useCallback } from 'react';
import { supabase } from '@/lib/supabase';
import type { Appointment, Treatment } from '@/lib/types';
import { APPOINTMENT_STATUSES } from '@/lib/types';
import { Search, Filter, X, Phone, Mail, Calendar, Clock, MessageSquare, Trash2 } from 'lucide-react';

export default function AdminAppointments() {
  const [appointments, setAppointments] = useState<Appointment[]>([]);
  const [treatments, setTreatments] = useState<Treatment[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [dateFilter, setDateFilter] = useState('');
  const [treatmentFilter, setTreatmentFilter] = useState('all');
  const [selected, setSelected] = useState<Appointment | null>(null);

  const loadAppointments = useCallback(async () => {
    setLoading(true);
    let query = supabase
      .from('appointments')
      .select('*, treatment:treatments(*)')
      .order('created_at', { ascending: false });

    if (statusFilter !== 'all') {
      query = query.eq('status', statusFilter);
    }
    if (dateFilter) {
      query = query.eq('preferred_date', dateFilter);
    }
    if (treatmentFilter !== 'all') {
      query = query.eq('treatment_id', treatmentFilter);
    }
    if (search) {
      query = query.or(`name.ilike.%${search}%,phone.ilike.%${search}%,email.ilike.%${search}%`);
    }

    const { data } = await query;
    setAppointments(data || []);
    setLoading(false);
  }, [statusFilter, dateFilter, treatmentFilter, search]);

  useEffect(() => {
    supabase.from('treatments').select('*').order('title').then(({ data }) => setTreatments(data || []));
  }, []);

  useEffect(() => {
    loadAppointments();
  }, [loadAppointments]);

  const updateStatus = async (id: string, status: string) => {
    await supabase.from('appointments').update({ status, updated_at: new Date().toISOString() }).eq('id', id);
    setAppointments(appointments.map((a) => (a.id === id ? { ...a, status } : a)));
    if (selected?.id === id) setSelected({ ...selected, status });
  };

  const updateNotes = async (id: string, admin_notes: string) => {
    await supabase.from('appointments').update({ admin_notes, updated_at: new Date().toISOString() }).eq('id', id);
    setAppointments(appointments.map((a) => (a.id === id ? { ...a, admin_notes } : a)));
  };

  const deleteAppointment = async (id: string) => {
    if (!confirm('Delete this appointment? This cannot be undone.')) return;
    await supabase.from('appointments').delete().eq('id', id);
    setAppointments(appointments.filter((a) => a.id !== id));
    setSelected(null);
  };

  const statusColors: Record<string, string> = {
    new: 'bg-soft-red text-crimson',
    contacted: 'bg-blue-100 text-blue-700',
    confirmed: 'bg-green-100 text-green-700',
    completed: 'bg-charcoal text-white',
    cancelled: 'bg-red-100 text-red-700',
    'no-show': 'bg-orange-100 text-orange-700',
  };

  const inputClass = 'w-full px-3 py-2 bg-white border border-charcoal/10 text-sm text-charcoal focus:outline-none focus:border-crimson transition-colors';

  return (
    <div>
      <h1 className="font-serif text-3xl font-bold text-charcoal mb-2">Appointments</h1>
      <p className="text-sm text-charcoal/50 mb-6">Manage and track all appointment requests.</p>

      {/* Filters */}
      <div className="bg-white border border-charcoal/5 p-4 mb-6">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <div className="relative">
            <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-charcoal/30" />
            <input
              type="text"
              placeholder="Search name, phone, email..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className={`${inputClass} pl-9`}
            />
          </div>
          <select value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)} className={inputClass}>
            <option value="all">All Statuses</option>
            {APPOINTMENT_STATUSES.map((s) => (
              <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1).replace('-', ' ')}</option>
            ))}
          </select>
          <select value={treatmentFilter} onChange={(e) => setTreatmentFilter(e.target.value)} className={inputClass}>
            <option value="all">All Treatments</option>
            {treatments.map((t) => (
              <option key={t.id} value={t.id}>{t.title}</option>
            ))}
          </select>
          <input type="date" value={dateFilter} onChange={(e) => setDateFilter(e.target.value)} className={inputClass} />
        </div>
        {(search || statusFilter !== 'all' || dateFilter || treatmentFilter !== 'all') && (
          <button
            onClick={() => { setSearch(''); setStatusFilter('all'); setDateFilter(''); setTreatmentFilter('all'); }}
            className="mt-3 text-xs text-crimson hover:underline"
          >
            Clear filters
          </button>
        )}
      </div>

      {/* Table */}
      <div className="bg-white border border-charcoal/5 overflow-x-auto">
        {loading ? (
          <p className="p-8 text-center text-sm text-charcoal/40">Loading...</p>
        ) : appointments.length === 0 ? (
          <p className="p-8 text-center text-sm text-charcoal/40">No appointments found.</p>
        ) : (
          <table className="w-full">
            <thead>
              <tr className="border-b border-charcoal/5">
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Patient</th>
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3 hidden sm:table-cell">Treatment</th>
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3 hidden md:table-cell">Date</th>
                <th className="text-left text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Status</th>
                <th className="text-right text-xs font-semibold tracking-wider uppercase text-charcoal/40 px-5 py-3">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-charcoal/5">
              {appointments.map((appt) => (
                <tr key={appt.id} className="hover:bg-ivory/50 transition-colors cursor-pointer" onClick={() => setSelected(appt)}>
                  <td className="px-5 py-4">
                    <p className="font-medium text-sm text-charcoal">{appt.name}</p>
                    <p className="text-xs text-charcoal/50">{appt.phone}</p>
                  </td>
                  <td className="px-5 py-4 hidden sm:table-cell">
                    <p className="text-sm text-charcoal/70">{appt.treatment_category || appt.treatment?.title || '—'}</p>
                  </td>
                  <td className="px-5 py-4 hidden md:table-cell">
                    <p className="text-sm text-charcoal/70">{appt.preferred_date || '—'}</p>
                    <p className="text-xs text-charcoal/40 capitalize">{appt.preferred_time || ''}</p>
                  </td>
                  <td className="px-5 py-4">
                    <select
                      value={appt.status}
                      onChange={(e) => { e.stopPropagation(); updateStatus(appt.id, e.target.value); }}
                      className={`text-xs font-medium px-2.5 py-1 border-0 cursor-pointer capitalize ${statusColors[appt.status] || 'bg-gray-100 text-gray-700'}`}
                    >
                      {APPOINTMENT_STATUSES.map((s) => (
                        <option key={s} value={s} className="bg-white text-charcoal">{s.replace('-', ' ')}</option>
                      ))}
                    </select>
                  </td>
                  <td className="px-5 py-4 text-right">
                    <button
                      onClick={(e) => { e.stopPropagation(); setSelected(appt); }}
                      className="text-xs font-medium text-crimson hover:underline"
                    >
                      View
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>

      {/* Detail Modal */}
      {selected && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-charcoal/40 backdrop-blur-sm" onClick={() => setSelected(null)}>
          <div className="bg-white max-w-lg w-full max-h-[90vh] overflow-y-auto" onClick={(e) => e.stopPropagation()}>
            <div className="flex items-center justify-between p-5 border-b border-charcoal/5">
              <h2 className="font-serif text-xl font-semibold text-charcoal">Appointment Details</h2>
              <button onClick={() => setSelected(null)}><X size={20} className="text-charcoal/40 hover:text-charcoal" /></button>
            </div>
            <div className="p-5 space-y-4">
              <div>
                <p className="text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-1">Patient</p>
                <p className="font-serif text-lg font-semibold text-charcoal">{selected.name}</p>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="flex items-center gap-2">
                  <Phone size={15} className="text-crimson" />
                  <a href={`tel:${selected.phone}`} className="text-sm text-charcoal/70 hover:text-crimson">{selected.phone}</a>
                </div>
                {selected.email && (
                  <div className="flex items-center gap-2">
                    <Mail size={15} className="text-crimson" />
                    <a href={`mailto:${selected.email}`} className="text-sm text-charcoal/70 hover:text-crimson truncate">{selected.email}</a>
                  </div>
                )}
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="flex items-center gap-2">
                  <Calendar size={15} className="text-crimson" />
                  <span className="text-sm text-charcoal/70">{selected.preferred_date || 'Not specified'}</span>
                </div>
                {selected.preferred_time && (
                  <div className="flex items-center gap-2">
                    <Clock size={15} className="text-crimson" />
                    <span className="text-sm text-charcoal/70 capitalize">{selected.preferred_time}</span>
                  </div>
                )}
              </div>
              <div>
                <p className="text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-1">Treatment</p>
                <p className="text-sm text-charcoal/70">{selected.treatment_category || selected.treatment?.title || 'General consultation'}</p>
              </div>
              {selected.message && (
                <div>
                  <p className="text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-1 flex items-center gap-1.5">
                    <MessageSquare size={12} /> Message
                  </p>
                  <p className="text-sm text-charcoal/70 bg-ivory p-3 leading-relaxed">{selected.message}</p>
                </div>
              )}
              <div>
                <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-2">Status</label>
                <select
                  value={selected.status}
                  onChange={(e) => updateStatus(selected.id, e.target.value)}
                  className={inputClass}
                >
                  {APPOINTMENT_STATUSES.map((s) => (
                    <option key={s} value={s}>{s.replace('-', ' ')}</option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-2">Admin Notes</label>
                <textarea
                  defaultValue={selected.admin_notes || ''}
                  onBlur={(e) => updateNotes(selected.id, e.target.value)}
                  rows={3}
                  className={inputClass}
                  placeholder="Add internal notes..."
                />
              </div>
              <div className="flex items-center justify-between pt-4 border-t border-charcoal/5">
                <p className="text-xs text-charcoal/40">
                  Submitted {new Date(selected.created_at).toLocaleString()}
                </p>
                <button
                  onClick={() => deleteAppointment(selected.id)}
                  className="flex items-center gap-1.5 text-xs text-red-600 hover:text-red-800"
                >
                  <Trash2 size={14} />
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
