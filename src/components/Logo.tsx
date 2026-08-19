import { Link } from 'react-router-dom';

const logoSrc = '/images/WhatsApp_Image_2026-08-17_at_15.25.33 copy.jpeg';

export default function Logo({ variant = 'dark' }: { variant?: 'dark' | 'light' }) {
  const textColor = variant === 'light' ? 'text-white' : 'text-charcoal';
  const subColor = variant === 'light' ? 'text-white/60' : 'text-charcoal/50';

  return (
    <Link to="/" className="flex items-center gap-3 group" aria-label="Lumique Aesthetic Clinic Home">
      <span className="relative h-12 w-12 shrink-0 overflow-hidden bg-crimson">
        <img
          src={logoSrc}
          alt="Lumique Aesthetic Clinic logo"
          className="absolute left-0 top-[-64.5%] h-[233%] w-full object-fill"
        />
      </span>
      <span className="flex flex-col leading-none">
        <span className={`font-serif text-lg font-bold tracking-tight ${textColor}`}>LUMIQUE</span>
        <span className={`text-[10px] font-medium tracking-[0.2em] uppercase ${subColor}`}>
          Aesthetic Clinic
        </span>
      </span>
    </Link>
  );
}
