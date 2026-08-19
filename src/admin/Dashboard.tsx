import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { supabase } from '@/lib/supabase';
import type { Appointment } from '@/lib/types';
import { Calendar, Stethoscope, FileText, Users, ArrowRight, Clock } from 'lucide-react';

export default function AdminDashboard() {
  const [stats, setStats] = useState({
    appointments: 0,
    newAppointments: 0,
    treatments: 0,
    blogPosts: 0,
    publishedPosts: 0,
  });
  const [recent, setRecent] = useState<Appointment[]>([]);

  useEffect(() => {
    (async () => {
      const [appts, treats, posts, recentAppts] = await Promise.all([
        supabase.from('appointments').select('*'),
        supabase.from('treatments').select('*', { count: 'exact', head: true }),
        supabase.from('blog_posts').select('*'),
        supabase
          .from('appointments')
          .select('*, treatment:treatments(*)')
          .order('created_at', { ascending: false })
          .limit(5),
      ]);

      setStats({
        appointments: appts.data?.length || 0,
        newAppointments: appts.data?.filter((a) => a.status === 'new').length || 0,
        treatments: treats.count || 0,
        blogPosts: posts.data?.length || 0,
        publishedPosts: posts.data?.filter((p) => p.status === 'published').length || 0,
      });
      setRecent(recentAppts.data || []);
    })();
  }, []);

  const cards = [
    {
      label: 'Total Appointments',
      value: stats.appointments,
      sub: `${stats.newAppointments} new`,
      icon: Calendar,
      link: '/admin/appointments',
      color: 'bg-crimson',
    },
    {
      label: 'Treatments',
      value: stats.treatments,
      sub: 'Active services',
      icon: Stethoscope,
      link: '/admin/treatments',
      color: 'bg-gold',
    },
    {
      label: 'Blog Posts',
      value: stats.blogPosts,
      sub: `${stats.publishedPosts} published`,
      icon: FileText,
      link: '/admin/blog',
      color: 'bg-burgundy',
    },
  ];

  const statusColors: Record<string, string> = {
    new: 'bg-soft-red text-crimson',
    contacted: 'bg-blue-100 text-blue-700',
    confirmed: 'bg-green-100 text-green-700',
    completed: 'bg-charcoal text-white',
    cancelled: 'bg-red-100 text-red-700',
    'no-show': 'bg-orange-100 text-orange-700',
  };

  return (
    <div>
      <h1 className="font-serif text-3xl font-bold text-charcoal mb-2">Dashboard</h1>
      <p className="text-sm text-charcoal/50 mb-8">Overview of your clinic's activity.</p>

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-10">
        {cards.map((card) => (
          <Link
            key={card.label}
            to={card.link}
            className="group bg-white p-6 border border-charcoal/5 transition-all hover:shadow-lg"
          >
            <div className="flex items-center justify-between mb-4">
              <div className={`flex h-10 w-10 items-center justify-center ${card.color}`}>
                <card.icon size={20} className="text-white" />
              </div>
              <ArrowRight size={16} className="text-charcoal/20 group-hover:text-crimson transition-colors" />
            </div>
            <p className="font-serif text-3xl font-bold text-charcoal">{card.value}</p>
            <p className="text-sm text-charcoal/60 mt-1">{card.label}</p>
            <p className="text-xs text-charcoal/40 mt-1">{card.sub}</p>
          </Link>
        ))}
      </div>

      <div className="bg-white border border-charcoal/5">
        <div className="flex items-center justify-between p-5 border-b border-charcoal/5">
          <h2 className="font-serif text-lg font-semibold text-charcoal">Recent Appointments</h2>
          <Link to="/admin/appointments" className="text-xs font-medium text-crimson hover:underline">
            View All
          </Link>
        </div>
        {recent.length === 0 ? (
          <p className="p-8 text-center text-sm text-charcoal/40">No appointments yet.</p>
        ) : (
          <div className="divide-y divide-charcoal/5">
            {recent.map((appt) => (
              <div key={appt.id} className="flex items-center justify-between p-5">
                <div className="min-w-0">
                  <p className="font-medium text-charcoal text-sm">{appt.name}</p>
                  <p className="text-xs text-charcoal/50 mt-0.5">
                    {appt.treatment_category || appt.treatment?.title || 'General'} · {appt.phone}
                  </p>
                </div>
                <div className="flex items-center gap-3 shrink-0">
                  <span className="text-xs text-charcoal/40 hidden sm:block">
                    {new Date(appt.created_at).toLocaleDateString()}
                  </span>
                  <span className={`px-2.5 py-1 text-xs font-medium capitalize ${statusColors[appt.status] || 'bg-gray-100 text-gray-700'}`}>
                    {appt.status.replace('-', ' ')}
                  </span>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
