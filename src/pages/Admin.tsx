import { Routes, Route, NavLink, Link } from 'react-router-dom';
import { useState } from 'react';
import {
  LayoutDashboard,
  Calendar,
  Stethoscope,
  FileText,
  User,
  Settings,
  Menu,
  X,
  Tag,
} from 'lucide-react';
import AdminDashboard from '@/admin/Dashboard';
import AdminAppointments from '@/admin/Appointments';
import AdminTreatments from '@/admin/Treatments';
import AdminCategories from '@/admin/Categories';
import AdminBlog from '@/admin/Blog';
import AdminDoctor from '@/admin/Doctor';
import AdminSettings from '@/admin/Settings';

const navItems = [
  { label: 'Dashboard', path: '/admin', icon: LayoutDashboard, end: true },
  { label: 'Appointments', path: '/admin/appointments', icon: Calendar },
  { label: 'Treatments', path: '/admin/treatments', icon: Stethoscope },
  { label: 'Categories', path: '/admin/categories', icon: Tag },
  { label: 'Blog Posts', path: '/admin/blog', icon: FileText },
  { label: 'Doctor Profile', path: '/admin/doctor', icon: User },
  { label: 'Settings', path: '/admin/settings', icon: Settings },
];

export default function Admin() {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="min-h-screen bg-ivory">
      {/* Top Bar */}
      <div className="fixed top-0 left-0 right-0 z-40 bg-charcoal text-white h-14 flex items-center px-4 lg:px-6">
        <button
          className="lg:hidden mr-3"
          onClick={() => setSidebarOpen(!sidebarOpen)}
        >
          {sidebarOpen ? <X size={22} /> : <Menu size={22} />}
        </button>
        <div className="flex items-center gap-3">
          <span className="relative h-8 w-8 shrink-0 overflow-hidden bg-crimson">
            <img
              src="/images/WhatsApp_Image_2026-08-17_at_15.25.33 copy.jpeg"
              alt="Unique Aesthetic Clinic logo"
              className="absolute left-0 top-[-64.5%] h-[233%] w-full object-fill"
            />
          </span>
          <span className="font-serif text-base font-semibold">Unique Aesthetic — Admin</span>
        </div>
        <div className="ml-auto">
          <Link to="/" className="text-xs text-white/60 hover:text-white transition-colors">
            View Site →
          </Link>
        </div>
      </div>

      {/* Sidebar */}
      <aside
        className={`fixed top-14 left-0 bottom-0 w-60 bg-white border-r border-charcoal/10 z-30 transition-transform duration-300 ${
          sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        }`}
      >
        <nav className="p-4 space-y-1">
          {navItems.map((item) => (
            <NavLink
              key={item.path}
              to={item.path}
              end={item.end}
              onClick={() => setSidebarOpen(false)}
              className={({ isActive }) =>
                `flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors ${
                  isActive
                    ? 'bg-soft-red text-crimson'
                    : 'text-charcoal/60 hover:bg-ivory hover:text-charcoal'
                }`
              }
            >
              <item.icon size={18} />
              {item.label}
            </NavLink>
          ))}
        </nav>
      </aside>

      {/* Overlay */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 top-14 z-20 bg-charcoal/30 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* Content */}
      <div className="lg:ml-60 pt-14">
        <div className="p-5 lg:p-8">
          <Routes>
            <Route index element={<AdminDashboard />} />
            <Route path="appointments" element={<AdminAppointments />} />
            <Route path="treatments" element={<AdminTreatments />} />
            <Route path="categories" element={<AdminCategories />} />
            <Route path="blog" element={<AdminBlog />} />
            <Route path="doctor" element={<AdminDoctor />} />
            <Route path="settings" element={<AdminSettings />} />
          </Routes>
        </div>
      </div>
    </div>
  );
}
