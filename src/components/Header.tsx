import { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Menu, X, Phone, Instagram, Facebook, Youtube } from 'lucide-react';
import Logo from './Logo';
import WhatsAppIcon from './WhatsAppIcon';

const navLinks = [
  { label: 'Home', path: '/' },
  { label: 'About Us', path: '/about' },
  { label: 'Treatments', path: '/treatments' },
  { label: 'Articles', path: '/blog' },
  { label: 'Contact', path: '/contact' },
];

export default function Header() {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const location = useLocation();

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 20);
    onScroll();
    window.addEventListener('scroll', onScroll);
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  useEffect(() => {
    setMobileOpen(false);
  }, [location.pathname]);

  const isActive = (path: string) =>
    path === '/' ? location.pathname === '/' : location.pathname.startsWith(path);

  return (
    <>
      <header
        className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${
          scrolled
            ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-charcoal/5'
            : 'bg-transparent'
        }`}
      >
        <div className="container-luxury">
          <div className="flex items-center justify-between py-4 lg:py-5">
            <Logo />

            <nav className="hidden lg:flex items-center gap-8">
              {navLinks.map((link) => (
                <Link
                  key={link.path}
                  to={link.path}
                  className={`text-sm font-medium tracking-wide transition-colors duration-300 relative group ${
                    isActive(link.path) ? 'text-crimson' : 'text-charcoal hover:text-crimson'
                  }`}
                >
                  {link.label}
                  <span
                    className={`absolute -bottom-1.5 left-0 h-px bg-crimson transition-all duration-300 ${
                      isActive(link.path) ? 'w-full' : 'w-0 group-hover:w-full'
                    }`}
                  />
                </Link>
              ))}
            </nav>

            <div className="hidden lg:flex items-center gap-3">
              <div className="flex items-center gap-1.5 mr-2">
                <a href="#" aria-label="Lumique on Facebook" className="flex h-8 w-8 items-center justify-center rounded-full bg-charcoal text-white transition-all hover:bg-crimson hover:-translate-y-0.5">
                  <Facebook size={14} />
                </a>
                <a href="#" aria-label="Lumique on Instagram" className="flex h-8 w-8 items-center justify-center rounded-full bg-charcoal text-white transition-all hover:bg-crimson hover:-translate-y-0.5">
                  <Instagram size={14} />
                </a>
                <a href="#" aria-label="Lumique on YouTube" className="flex h-8 w-8 items-center justify-center rounded-full bg-charcoal text-white transition-all hover:bg-crimson hover:-translate-y-0.5">
                  <Youtube size={14} />
                </a>
                <a href="https://wa.me/918879550581" target="_blank" rel="noopener noreferrer" aria-label="Lumique on WhatsApp" className="flex h-8 w-8 items-center justify-center rounded-full bg-charcoal text-white transition-all hover:bg-crimson hover:-translate-y-0.5">
                  <WhatsAppIcon size={14} />
                </a>
              </div>
              <a
                href="tel:+918879550581"
                className="flex items-center gap-2 text-sm font-medium text-charcoal hover:text-crimson transition-colors"
              >
                <Phone size={16} className="text-crimson" />
                <span>+91 88795 50581</span>
              </a>
              <Link to="/contact" className="btn-primary !px-5 !py-2.5 !text-xs">
                Enquire Now
              </Link>
            </div>

            <button
              className="lg:hidden p-2 -mr-2"
              onClick={() => setMobileOpen(!mobileOpen)}
              aria-label="Toggle menu"
            >
              {mobileOpen ? (
                <X size={24} className={scrolled ? 'text-charcoal' : 'text-charcoal'} />
              ) : (
                <Menu size={24} className={scrolled ? 'text-charcoal' : 'text-charcoal'} />
              )}
            </button>
          </div>
        </div>
      </header>

      {/* Mobile menu */}
      <div
        className={`fixed inset-0 z-40 lg:hidden transition-all duration-400 ${
          mobileOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'
        }`}
      >
        <div className="absolute inset-0 bg-charcoal/40 backdrop-blur-sm" onClick={() => setMobileOpen(false)} />
        <div
          className={`absolute top-0 right-0 bottom-0 w-[80%] max-w-sm bg-white shadow-2xl transition-transform duration-400 ${
            mobileOpen ? 'translate-x-0' : 'translate-x-full'
          }`}
        >
          <div className="flex flex-col h-full pt-24 px-8 pb-8">
            <nav className="flex flex-col gap-1">
              {navLinks.map((link) => (
                <Link
                  key={link.path}
                  to={link.path}
                  className={`py-4 text-lg font-medium border-b border-charcoal/5 transition-colors ${
                    isActive(link.path) ? 'text-crimson' : 'text-charcoal hover:text-crimson'
                  }`}
                >
                  {link.label}
                </Link>
              ))}
            </nav>
            <div className="mt-auto flex flex-col gap-3">
              <a href="tel:+918879550581" className="flex items-center gap-2 text-charcoal">
                <Phone size={18} className="text-crimson" />
                <span className="font-medium">+91 88795 50581</span>
              </a>
              <Link to="/contact" className="btn-primary w-full">
                Book Appointment
              </Link>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
