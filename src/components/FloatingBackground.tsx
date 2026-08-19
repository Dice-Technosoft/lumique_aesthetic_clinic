import { useMemo } from 'react';
import {
  Sparkles,
  HeartHandshake,
  Microscope,
  ShieldCheck,
  Stethoscope,
  Flower2,
  Star,
  Zap,
  Scissors,
  Droplet,
  Sun,
  Leaf,
} from 'lucide-react';

const ICONS = [Sparkles, HeartHandshake, Microscope, ShieldCheck, Stethoscope, Flower2, Star, Zap, Scissors, Droplet, Sun, Leaf];

interface FloatingIcon {
  id: number;
  iconIndex: number;
  x: number;
  y: number;
  size: number;
  duration: number;
  delay: number;
  opacity: number;
}

export default function FloatingBackground({ count = 14 }: { count?: number }) {
  const icons = useMemo<FloatingIcon[]>(() => {
    return Array.from({ length: count }, (_, i) => ({
      id: i,
      iconIndex: i % ICONS.length,
      x: Math.random() * 100,
      y: Math.random() * 100,
      size: 18 + Math.random() * 28,
      duration: 12 + Math.random() * 16,
      delay: Math.random() * 10,
      opacity: 0.04 + Math.random() * 0.08,
    }));
  }, [count]);

  return (
    <div className="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
      {icons.map((item) => {
        const Icon = ICONS[item.iconIndex];
        return (
          <div
            key={item.id}
            className="absolute animate-float-icon"
            style={{
              left: `${item.x}%`,
              top: `${item.y}%`,
              animationDuration: `${item.duration}s`,
              animationDelay: `${item.delay}s`,
              opacity: item.opacity,
            }}
          >
            <Icon size={item.size} className="text-crimson" strokeWidth={1} />
          </div>
        );
      })}
    </div>
  );
}
