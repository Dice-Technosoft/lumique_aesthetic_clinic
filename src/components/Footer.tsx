import { Link } from 'react-router-dom';
import { Phone, Mail, MapPin, Clock, Instagram, Facebook, Youtube } from 'lucide-react';
import WhatsAppIcon from './WhatsAppIcon';

const logoSrc = '/images/WhatsApp_Image_2026-08-17_at_15.25.33 copy.jpeg';

export default function Footer() {
  return (
    <footer className="bg-charcoal text-white">
      <div className="container-luxury py-16 lg:py-20">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
          {/* Brand */}
          <div className="lg:col-span-1">
            <div className="mb-6">
              <div className="flex items-center gap-3">
                <span className="relative h-12 w-12 shrink-0 overflow-hidden bg-crimson">
                  <img
                    src={logoSrc}
                    alt="Lumique Aesthetic Clinic logo"
                    className="absolute left-0 top-[-64.5%] h-[233%] w-full object-fill"
                  />
                </span>
                <div className="flex flex-col leading-none">
                  <span className="font-serif text-lg font-bold tracking-tight text-white">
                    LUMIQUE
                  </span>
                  <span className="text-[10px] font-medium tracking-[0.2em] uppercase text-white/50">
                    Aesthetic Clinic
                  </span>
                </div>
              </div>
            </div>
            <p className="text-sm leading-relaxed text-white/60 mb-6">
              Advanced dermatology and aesthetic care designed around you. Personalized treatments
              for skin, hair, laser, and aesthetic enhancement.
            </p>
            <div className="flex items-center gap-3">
              <a href="#" aria-label="Lumique on Facebook" className="flex h-9 w-9 items-center justify-center border border-white/10 transition-colors hover:bg-crimson hover:border-crimson">
                <Facebook size={16} className="text-white/70" />
              </a>
              <a href="#" aria-label="Lumique on Instagram" className="flex h-9 w-9 items-center justify-center border border-white/10 transition-colors hover:bg-crimson hover:border-crimson">
                <Instagram size={16} className="text-white/70" />
              </a>
              <a href="#" aria-label="Lumique on YouTube" className="flex h-9 w-9 items-center justify-center border border-white/10 transition-colors hover:bg-crimson hover:border-crimson">
                <Youtube size={16} className="text-white/70" />
              </a>
              <a href="https://wa.me/918879550581" target="_blank" rel="noopener noreferrer" aria-label="Lumique on WhatsApp" className="flex h-9 w-9 items-center justify-center border border-white/10 transition-colors hover:bg-crimson hover:border-crimson">
                <WhatsAppIcon size={16} className="text-white/70" />
              </a>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-xs font-semibold tracking-[0.2em] uppercase text-white/40 mb-6">
              Quick Links
            </h4>
            <ul className="space-y-3">
              {[
                { label: 'Home', path: '/' },
                { label: 'About Doctor', path: '/about' },
                { label: 'Treatments', path: '/treatments' },
                { label: 'Blog', path: '/blog' },
                { label: 'Contact', path: '/contact' },
                { label: 'Book Appointment', path: '/contact' },
              ].map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.path}
                    className="text-sm text-white/60 hover:text-crimson transition-colors duration-300"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Treatments */}
          <div>
            <h4 className="text-xs font-semibold tracking-[0.2em] uppercase text-white/40 mb-6">
              Treatments
            </h4>
            <ul className="space-y-3">
              {[
                'Skin Treatments',
                'Hair Transplantation',
                'Laser Treatments',
                'Tattoo Removal',
                'Aesthetic Treatments',
              ].map((label) => (
                <li key={label}>
                  <Link
                    to="/treatments"
                    className="text-sm text-white/60 hover:text-crimson transition-colors duration-300"
                  >
                    {label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="text-xs font-semibold tracking-[0.2em] uppercase text-white/40 mb-6">
              Get in Touch
            </h4>
            <ul className="space-y-4">
              <li className="flex items-start gap-3">
                <MapPin size={16} className="text-crimson mt-0.5 shrink-0" />
                <span className="text-sm text-white/60 leading-relaxed">
                  123 Medical Center Drive, Suite 200, Beverly Hills, CA 90210
                </span>
              </li>
              <li className="flex items-center gap-3">
                <Phone size={16} className="text-crimson shrink-0" />
                <a href="tel:+918879550581" className="text-sm text-white/60 hover:text-crimson transition-colors">
                  +91 88795 50581
                </a>
              </li>
              <li className="flex items-center gap-3">
                <Mail size={16} className="text-crimson shrink-0" />
                <a href="mailto:info@lumiqueclinic.com" className="text-sm text-white/60 hover:text-crimson transition-colors">
                  info@lumiqueclinic.com
                </a>
              </li>
              <li className="flex items-start gap-3">
                <Clock size={16} className="text-crimson mt-0.5 shrink-0" />
                <span className="text-sm text-white/60 leading-relaxed">
                  Mon - Fri: 9AM - 7PM<br />
                  Sat: 10AM - 5PM<br />
                  Sun: Closed
                </span>
              </li>
            </ul>
          </div>
        </div>

        <div className="mt-16 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
          <p className="text-xs text-white/40">
            © {new Date().getFullYear()} Lumique Aesthetic Clinic. All rights reserved.
          </p>
          <div className="flex items-center gap-6">
            <a href="#" className="text-xs text-white/40 hover:text-crimson transition-colors">Privacy Policy</a>
            <a href="#" className="text-xs text-white/40 hover:text-crimson transition-colors">Terms of Service</a>
            <Link to="/admin" className="text-xs text-white/40 hover:text-crimson transition-colors">Admin</Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
