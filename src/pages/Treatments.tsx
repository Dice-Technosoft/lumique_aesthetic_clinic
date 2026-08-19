import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { supabase } from '@/lib/supabase';
import type { Category, Treatment } from '@/lib/types';
import Reveal from '@/components/Reveal';
import FloatingBackground from '@/components/FloatingBackground';
import { ChevronRight, Calendar, Sparkles, Scissors, Zap, Eraser, Flower2 } from 'lucide-react';

const categoryIcons: Record<string, typeof Sparkles> = {
  skin: Sparkles,
  hair: Scissors,
  laser: Zap,
  'tattoo-removal': Eraser,
  'aesthetic-treatments': Flower2,
};

export default function Treatments() {
  const [categories, setCategories] = useState<Category[]>([]);
  const [treatments, setTreatments] = useState<Treatment[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      const [{ data: cats }, { data: treats }] = await Promise.all([
        supabase.from('categories').select('*').order('display_order'),
        supabase
          .from('treatments')
          .select('*, category:categories(*)')
          .order('display_order'),
      ]);
      setCategories(cats || []);
      setTreatments(treats || []);
      setLoading(false);
    })();
  }, []);

  if (loading) {
    return (
      <div className="pt-32 pb-20">
        <div className="container-luxury">
          <p className="text-charcoal/40">Loading treatments...</p>
        </div>
      </div>
    );
  }

  return (
    <>
      {/* Hero */}
      <section className="relative pt-32 pb-16 lg:pt-40 lg:pb-24 bg-gradient-to-b from-soft-red/40 to-ivory overflow-hidden">
        <FloatingBackground count={10} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="max-w-3xl">
              <p className="section-label">Our Treatments</p>
              <h1 className="heading-1 text-balance mb-6">
                Comprehensive Care for Skin, Hair, Laser & Aesthetics
              </h1>
              <p className="body-text max-w-2xl">
                Explore our full range of advanced dermatology and aesthetic treatments, each
                personalized to deliver safe, effective, and natural-looking results.
              </p>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Category Navigation */}
      <section className="border-y border-charcoal/5 bg-white sticky top-16 z-30 backdrop-blur-md bg-white/90">
        <div className="container-luxury">
          <div className="flex items-center gap-1 overflow-x-auto py-4 scrollbar-hide">
            {categories.map((cat) => {
              const Icon = categoryIcons[cat.slug] || Sparkles;
              return (
                <a
                  key={cat.id}
                  href={`#${cat.slug}`}
                  className="flex items-center gap-2 px-4 py-2 text-sm font-medium text-charcoal/60 hover:text-crimson whitespace-nowrap transition-colors group"
                >
                  <Icon size={16} className="text-crimson/60 group-hover:text-crimson transition-colors" />
                  {cat.name}
                </a>
              );
            })}
          </div>
        </div>
      </section>

      {/* Treatment Categories */}
      <section className="py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={8} />
        <div className="container-luxury relative z-10 space-y-20 lg:space-y-28">
          {categories.map((category) => {
            const categoryTreatments = treatments.filter(
              (t) => t.category_id === category.id
            );
            if (categoryTreatments.length === 0) return null;
            const Icon = categoryIcons[category.slug] || Sparkles;

            return (
              <div key={category.id} id={category.slug} className="scroll-mt-32">
                <Reveal>
                  <div className="flex items-end justify-between mb-10 lg:mb-12 border-b border-charcoal/10 pb-6">
                    <div className="max-w-2xl">
                      <div className="flex items-center gap-3 mb-3">
                        <div className="flex h-10 w-10 items-center justify-center bg-soft-red">
                          <Icon size={20} className="text-crimson" />
                        </div>
                        <h2 className="font-serif text-3xl lg:text-4xl font-bold text-charcoal">
                          {category.name}
                        </h2>
                      </div>
                      <p className="text-charcoal/60 leading-relaxed">{category.description}</p>
                    </div>
                    <span className="font-serif text-5xl font-bold text-soft-red hidden lg:block">
                      {String(categoryTreatments.length).padStart(2, '0')}
                    </span>
                  </div>
                </Reveal>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                  {categoryTreatments.map((treatment, i) => (
                    <Reveal key={treatment.id} delay={i * 80}>
                      <Link
                        to={`/treatments/${treatment.slug}`}
                        className="group bg-white border border-charcoal/5 overflow-hidden transition-all duration-500 hover:shadow-xl hover:shadow-crimson/5 hover:-translate-y-1"
                      >
                        <div className="relative aspect-[4/3] overflow-hidden bg-soft-red">
                          <img
                            src={treatment.hero_image || `https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=600`}
                            alt={treatment.title}
                            className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                          />
                          <div className="absolute inset-0 bg-gradient-to-t from-charcoal/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                        </div>
                        <div className="p-6">
                          <h3 className="font-serif text-lg font-semibold text-charcoal mb-2 group-hover:text-crimson transition-colors">
                            {treatment.title}
                          </h3>
                          <p className="text-sm text-charcoal/60 leading-relaxed line-clamp-2 mb-4">
                            {treatment.short_intro}
                          </p>
                          <span className="inline-flex items-center gap-1.5 text-xs font-medium text-crimson tracking-wider uppercase">
                            Learn More
                            <ChevronRight size={14} className="transition-transform group-hover:translate-x-1" />
                          </span>
                        </div>
                      </Link>
                    </Reveal>
                  ))}
                </div>
              </div>
            );
          })}
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 lg:py-28 bg-gradient-to-br from-burgundy via-crimson to-crimson-dark relative overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-10 left-10 w-72 h-72 rounded-full bg-white blur-3xl animate-pulse-slow" />
          <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-gold blur-3xl animate-pulse-slow" />
        </div>
        <div className="container-luxury relative z-10 text-center">
          <Reveal>
            <h2 className="heading-2 mb-6 text-balance max-w-2xl mx-auto text-white">
              Not Sure Which Treatment Is Right for You?
            </h2>
            <p className="text-lg text-white/70 mb-10 max-w-xl mx-auto">
              Book a consultation and our expert will recommend the best treatment plan for your
              unique concerns.
            </p>
            <Link to="/contact" className="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-crimson font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-gold hover:text-white hover:shadow-xl">
              <Calendar size={18} className="transition-transform group-hover:scale-110" />
              Book a Consultation
            </Link>
          </Reveal>
        </div>
      </section>
    </>
  );
}
