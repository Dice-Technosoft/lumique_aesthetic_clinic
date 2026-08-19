import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { supabase } from '@/lib/supabase';
import type { Category, Treatment, BlogPost } from '@/lib/types';
import Reveal from '@/components/Reveal';
import FloatingBackground from '@/components/FloatingBackground';
import {
  ArrowRight,
  ShieldCheck,
  Microscope,
  HeartHandshake,
  Sparkles,
  Calendar,
  Phone,
  ChevronRight,
  Star,
  Award,
  Stethoscope,
  Scissors,
  Zap,
  Eraser,
  Flower2,
} from 'lucide-react';

const categoryIcons: Record<string, typeof Sparkles> = {
  skin: Sparkles,
  hair: Scissors,
  laser: Zap,
  'tattoo-removal': Eraser,
  'aesthetic-treatments': Flower2,
};

const categoryImages: Record<string, string> = {
  skin: 'https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=900',
  hair: 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=900',
  laser: 'https://images.pexels.com/photos/4586726/pexels-photo-4586726.jpeg?auto=compress&cs=tinysrgb&w=900',
  'tattoo-removal': 'https://images.pexels.com/photos/7446683/pexels-photo-7446683.jpeg?auto=compress&cs=tinysrgb&w=900',
  'aesthetic-treatments': 'https://images.pexels.com/photos/14438367/pexels-photo-14438367.jpeg?auto=compress&cs=tinysrgb&w=900',
};

export default function Home() {
  const [categories, setCategories] = useState<Category[]>([]);
  const [featuredTreatments, setFeaturedTreatments] = useState<Treatment[]>([]);
  const [blogPosts, setBlogPosts] = useState<BlogPost[]>([]);

  useEffect(() => {
    (async () => {
      const [{ data: cats }, { data: treatments }, { data: posts }] = await Promise.all([
        supabase.from('categories').select('*').order('display_order'),
        supabase
          .from('treatments')
          .select('*, category:categories(*)')
          .eq('is_featured', true)
          .order('display_order')
          .limit(6),
        supabase
          .from('blog_posts')
          .select('*, blog_category:blog_categories(*)')
          .eq('status', 'published')
          .order('published_at', { ascending: false })
          .limit(3),
      ]);
      setCategories(cats || []);
      setFeaturedTreatments(treatments || []);
      setBlogPosts(posts || []);
    })();
  }, []);

  return (
    <>
      {/* Hero */}
      <section className="relative min-h-screen flex items-center pt-24 overflow-hidden">
        <div className="absolute inset-0">
          <img
            src="https://images.pexels.com/photos/7446659/pexels-photo-7446659.jpeg?auto=compress&cs=tinysrgb&w=1920"
            alt="Premium dermatology treatment"
            className="h-full w-full object-cover scale-105 animate-ken-burns"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-charcoal/85 via-charcoal/55 to-charcoal/20" />
          <div className="absolute inset-0 bg-gradient-to-t from-ivory via-transparent to-transparent" />
        </div>

        <div className="container-luxury relative z-10">
          <div className="max-w-2xl text-white">
            <div className="flex items-center gap-3 mb-6 fade-in">
              <span className="h-px w-10 bg-gold" />
              <p className="text-xs font-semibold tracking-[0.3em] uppercase text-gold">
                Lumique Aesthetic Clinic
              </p>
            </div>
            <p className="fade-in-delay-1 text-sm tracking-[0.15em] uppercase text-white/60 mb-4">
              Skin · Hair · Laser · Aesthetic
            </p>
            <h1 className="fade-in-delay-1 font-serif text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.1] text-balance mb-6">
              Advanced Dermatology & Aesthetic Care Designed Around You
            </h1>
            <p className="fade-in-delay-2 text-lg text-white/70 leading-relaxed mb-10 max-w-xl">
              Personalized skin, hair, laser, and aesthetic treatments delivered by expert
              professionals in a luxurious clinical environment.
            </p>
            <div className="fade-in-delay-3 flex flex-wrap items-center gap-4">
              <Link to="/contact" className="btn-primary group">
                <Calendar size={18} className="transition-transform group-hover:scale-110" />
                Book an Appointment
              </Link>
              <Link
                to="/treatments"
                className="group inline-flex items-center justify-center gap-2 px-7 py-3.5 border border-white/30 text-white font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:border-white hover:bg-white/10"
              >
                Explore Treatments
                <ArrowRight size={18} className="transition-transform group-hover:translate-x-1" />
              </Link>
            </div>
          </div>
        </div>

        {/* Floating stat cards */}
        <div className="absolute bottom-32 right-12 hidden xl:flex flex-col gap-4 z-10">
          {[
            { value: '500+', label: 'Happy Patients' },
            { value: '50+', label: 'Treatments' },
            { value: '10+', label: 'Years Experience' },
          ].map((stat, i) => (
            <div
              key={i}
              className="bg-white/10 backdrop-blur-md border border-white/20 px-6 py-4 text-white fade-in-delay-3"
              style={{ animationDelay: `${0.6 + i * 0.15}s` }}
            >
              <p className="font-serif text-3xl font-bold text-gold">{stat.value}</p>
              <p className="text-xs tracking-wider uppercase text-white/60">{stat.label}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Clinic snapshot */}
      <section className="relative z-20 -mt-10 px-5 sm:px-8 lg:px-12">
        <div className="mx-auto grid max-w-7xl grid-cols-1 overflow-hidden rounded-2xl bg-white shadow-2xl shadow-charcoal/10 md:grid-cols-2 lg:grid-cols-4">
          <div className="p-6 lg:p-7">
            <p className="text-[10px] font-semibold uppercase tracking-[0.22em] text-crimson mb-2">Visit Lumique</p>
            <p className="font-serif text-lg font-semibold text-charcoal">Your confidence, cared for.</p>
            <p className="mt-2 text-sm leading-relaxed text-charcoal/55">A calm, elevated clinic experience built around your goals.</p>
          </div>
          <div className="border-t border-charcoal/10 p-6 lg:border-l lg:border-t-0 lg:p-7">
            <p className="text-xs font-semibold uppercase tracking-wider text-charcoal/40 mb-2">Call us</p>
            <a href="tel:+918879550581" className="font-serif text-lg font-semibold text-charcoal hover:text-crimson transition-colors">+91 88795 50581</a>
            <p className="mt-2 text-sm text-charcoal/55">Personal guidance from our clinic team</p>
          </div>
          <div className="border-t border-charcoal/10 p-6 lg:border-l lg:border-t-0 lg:p-7">
            <p className="text-xs font-semibold uppercase tracking-wider text-charcoal/40 mb-2">Opening hours</p>
            <p className="font-serif text-lg font-semibold text-charcoal">Mon – Sat</p>
            <p className="mt-2 text-sm text-charcoal/55">9:00 AM – 7:00 PM</p>
          </div>
          <div className="border-t border-charcoal/10 bg-charcoal p-6 lg:border-l lg:border-t-0 lg:p-7">
            <p className="text-xs font-semibold uppercase tracking-wider text-white/50 mb-2">Start your journey</p>
            <p className="font-serif text-lg font-semibold text-white">Ready when you are.</p>
            <Link to="/contact" className="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-gold hover:text-white transition-colors">Book a consultation <ArrowRight size={15} /></Link>
          </div>
        </div>
      </section>

      {/* Trust Bar */}
      <section className="bg-ivory relative z-10">
        <div className="container-luxury pt-20 pb-10">
          <Reveal>
            <div className="mx-auto max-w-2xl text-center">
              <p className="section-label">Why Lumique</p>
              <h2 className="heading-2 text-balance">Care that feels personal, results that feel natural</h2>
              <p className="body-text mt-4">Every treatment at Lumique is designed around your skin, your comfort and your goals — backed by medical-grade technology and a team that genuinely listens.</p>
            </div>
          </Reveal>
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8 mt-12">
            {[
              { icon: ShieldCheck, label: 'Safety-First', sub: 'Treatment Approach' },
              { icon: Microscope, label: 'Advanced', sub: 'Technology & Equipment' },
              { icon: HeartHandshake, label: 'Personalized', sub: 'Treatment Plans' },
              { icon: Sparkles, label: 'Natural-Looking', sub: 'Aesthetic Results' },
            ].map((item, i) => (
              <Reveal key={i} delay={i * 100}>
                <div className="group flex flex-col items-center text-center lg:flex-row lg:items-center gap-3 lg:gap-4">
                  <div className="flex h-12 w-12 items-center justify-center bg-soft-red shrink-0 transition-colors duration-300 group-hover:bg-crimson">
                    <item.icon size={22} className="text-crimson transition-colors duration-300 group-hover:text-white" />
                  </div>
                  <div>
                    <p className="font-serif text-base font-semibold text-charcoal">{item.label}</p>
                    <p className="text-xs text-charcoal/50">{item.sub}</p>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Treatments Preview */}
      <section className="py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={8} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="flex flex-col lg:flex-row lg:items-end justify-between mb-12 lg:mb-16 gap-6">
              <div className="max-w-2xl">
                <p className="section-label">Our Specialties</p>
                <h2 className="heading-2 text-balance">
                  Comprehensive Treatments for Every Concern
                </h2>
              </div>
              <Link
                to="/treatments"
                className="group inline-flex items-center gap-2 text-sm font-medium text-crimson"
              >
                View All Treatments
                <ArrowRight size={16} className="transition-transform group-hover:translate-x-1" />
              </Link>
            </div>
          </Reveal>

          {/* Category cards */}
          <div className="grid grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-6 mb-16">
            {categories.map((cat, i) => {
              const Icon = categoryIcons[cat.slug] || Sparkles;
              return (
                <Reveal key={cat.id} delay={i * 80}>
                  <Link
                    to={`/treatments#${cat.slug}`}
                    className="group relative overflow-hidden bg-white border border-charcoal/5 transition-all duration-500 hover:border-crimson/20 hover:shadow-xl hover:shadow-crimson/5 hover:-translate-y-1"
                  >
                    <div className="relative aspect-[3/4] overflow-hidden bg-soft-red">
                      <img
                        src={categoryImages[cat.slug] || categoryImages.skin}
                        alt={cat.name}
                        className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-charcoal/85 via-charcoal/10 to-transparent" />
                      <div className="absolute inset-x-0 bottom-0 p-5 text-left">
                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 backdrop-blur-sm mb-3 transition-all duration-300 group-hover:bg-crimson group-hover:scale-110">
                          <Icon size={18} className="text-crimson transition-colors duration-300 group-hover:text-white" />
                        </div>
                        <h3 className="font-serif text-lg font-semibold text-white mb-1">{cat.name}</h3>
                        <p className="text-xs text-white/70 leading-relaxed line-clamp-2">
                          {cat.description}
                        </p>
                      </div>
                    </div>
                    <span className="absolute bottom-0 left-0 h-0.5 w-0 bg-crimson transition-all duration-500 group-hover:w-full" />
                  </Link>
                </Reveal>
              );
            })}
          </div>

          {/* Featured treatments */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            {featuredTreatments.map((treatment, i) => (
              <Reveal key={treatment.id} delay={i * 100}>
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
                    {treatment.category && (
                      <span className="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 text-[10px] font-semibold tracking-wider uppercase text-crimson">
                        {treatment.category.name}
                      </span>
                    )}
                  </div>
                  <div className="p-6">
                    <h3 className="font-serif text-xl font-semibold text-charcoal mb-3 group-hover:text-crimson transition-colors">
                      {treatment.title}
                    </h3>
                    <p className="text-sm text-charcoal/60 leading-relaxed line-clamp-2 mb-4">
                      {treatment.short_intro}
                    </p>
                    <span className="inline-flex items-center gap-2 text-xs font-medium text-crimson tracking-wider uppercase">
                      Learn More
                      <ChevronRight size={14} className="transition-transform group-hover:translate-x-1" />
                    </span>
                  </div>
                </Link>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* About Doctor Preview - enhanced with image collage */}
      <section className="bg-white py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={8} />
        <div className="container-luxury relative z-10">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <Reveal>
              <div className="relative">
                {/* Main image */}
                <div className="relative overflow-hidden">
                  <img
                    src="https://images.pexels.com/photos/32160039/pexels-photo-32160039.jpeg?auto=compress&cs=tinysrgb&w=800"
                    alt="Lead Dermatologist"
                    className="w-full aspect-[3/4] object-cover"
                  />
                </div>
                {/* Secondary image */}
                <div className="absolute -bottom-10 -right-6 w-48 h-48 overflow-hidden border-8 border-white shadow-xl hidden lg:block">
                  <img
                    src="https://images.pexels.com/photos/3985332/pexels-photo-3985332.jpeg?auto=compress&cs=tinysrgb&w=400"
                    alt="Clinical treatment"
                    className="w-full h-full object-cover"
                  />
                </div>
                {/* Stat badge */}
                <div className="absolute -bottom-6 -left-6 bg-crimson p-6 max-w-[200px] hidden lg:block animate-float">
                  <p className="font-serif text-4xl font-bold text-white leading-none mb-2">100%</p>
                  <p className="text-sm text-white/80 leading-snug">
                    Patient-focused approach to every treatment
                  </p>
                </div>
                <div className="absolute top-6 -left-6 bg-gold w-20 h-20 hidden lg:block animate-float-slow" />
              </div>
            </Reveal>

            <Reveal delay={150}>
              <div>
                <p className="section-label">Meet Your Doctor</p>
                <h2 className="heading-2 mb-6 text-balance">
                  Expert Care You Can Trust
                </h2>
                <p className="body-text mb-8">
                  At Lumique Aesthetic Clinic, our lead dermatologist brings years of specialized
                  experience in dermatology and aesthetic medicine. Every treatment plan is designed
                  with your unique needs, safety, and natural-looking results in mind.
                </p>
                <div className="grid grid-cols-2 gap-4 mb-8">
                  {[
                    { icon: HeartHandshake, label: 'Personalized Plans' },
                    { icon: Microscope, label: 'Advanced Technology' },
                    { icon: ShieldCheck, label: 'Safety-First Care' },
                    { icon: Sparkles, label: 'Natural Results' },
                  ].map((item, i) => (
                    <div key={i} className="flex items-center gap-3 p-4 bg-ivory border border-charcoal/5 transition-colors hover:border-crimson/20">
                      <div className="flex h-10 w-10 items-center justify-center bg-soft-red shrink-0">
                        <item.icon size={18} className="text-crimson" />
                      </div>
                      <span className="text-sm font-medium text-charcoal">{item.label}</span>
                    </div>
                  ))}
                </div>
                <Link to="/about" className="btn-secondary group">
                  Learn More About the Doctor
                  <ArrowRight size={16} className="transition-transform group-hover:translate-x-1" />
                </Link>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Experience strip */}
      <section className="bg-charcoal py-16 relative overflow-hidden">
        <FloatingBackground count={10} />
        <div className="absolute inset-0 opacity-5">
          <div className="absolute top-0 right-0 w-96 h-96 rounded-full bg-crimson blur-3xl" />
        </div>
        <div className="container-luxury relative z-10">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            {[
              { icon: HeartHandshake, value: '500+', label: 'Happy Patients' },
              { icon: Stethoscope, value: '50+', label: 'Treatments Offered' },
              { icon: Award, value: '10+', label: 'Years of Experience' },
              { icon: ShieldCheck, value: '100%', label: 'Safety Standards' },
            ].map((stat, i) => (
              <Reveal key={i} delay={i * 100}>
                <div className="flex flex-col items-center">
                  <div className="flex h-12 w-12 items-center justify-center bg-crimson/20 mb-4">
                    <stat.icon size={22} className="text-crimson" />
                  </div>
                  <p className="font-serif text-3xl lg:text-4xl font-bold text-white mb-1">{stat.value}</p>
                  <p className="text-xs tracking-wider uppercase text-white/50">{stat.label}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* How It Works */}
      <section className="bg-white py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={6} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="mx-auto max-w-2xl text-center mb-16">
              <p className="section-label">Your Journey</p>
              <h2 className="heading-2 text-balance">How Lumique Works</h2>
              <p className="body-text mt-4">A simple, guided path from your first consultation to confident, lasting results.</p>
            </div>
          </Reveal>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {[
              { icon: Calendar, step: '01', title: 'Book a Consultation', desc: 'Reserve your appointment online or call us directly — we will find a time that suits you.' },
              { icon: Stethoscope, step: '02', title: 'Personal Assessment', desc: 'Your dermatologist reviews your concerns, skin type and goals to craft a tailored plan.' },
              { icon: Microscope, step: '03', title: 'Expert Treatment', desc: 'Receive your treatment in a calm, medical-grade environment using advanced technology.' },
              { icon: Sparkles, step: '04', title: 'Lasting Results', desc: 'We guide your aftercare and follow-up so results stay natural, healthy and confident.' },
            ].map((item, i) => (
              <Reveal key={i} delay={i * 100}>
                <div className="group relative h-full rounded-2xl border border-charcoal/5 bg-ivory p-7 transition-all duration-500 hover:border-crimson/20 hover:shadow-xl hover:shadow-crimson/5 hover:-translate-y-1">
                  <span className="absolute right-6 top-5 font-serif text-4xl font-bold text-soft-red transition-colors group-hover:text-crimson/20">{item.step}</span>
                  <div className="relative mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-crimson text-white shadow-lg shadow-crimson/20 transition-all duration-300 group-hover:scale-110">
                    <item.icon size={22} />
                  </div>
                  <h3 className="font-serif text-lg font-semibold text-charcoal mb-2">{item.title}</h3>
                  <p className="text-sm text-charcoal/60 leading-relaxed">{item.desc}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Testimonials */}
      <section className="bg-ivory py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={6} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="mx-auto max-w-2xl text-center mb-16">
              <p className="section-label">Patient Stories</p>
              <h2 className="heading-2 text-balance">Trusted by People Like You</h2>
              <p className="body-text mt-4">Real experiences from patients who trusted Lumique with their skin, hair and confidence.</p>
            </div>
          </Reveal>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            {[
              { name: 'Priya S.', treatment: 'Skin Rejuvenation', text: 'From the first consultation I felt heard. The results are subtle and natural — exactly what I wanted. Lumique truly cares.' },
              { name: 'Arjun M.', treatment: 'Hair Restoration', text: 'The team explained every step clearly. After my sessions I can see real growth and my confidence is back. Highly recommend.' },
              { name: 'Neha R.', treatment: 'Laser Treatment', text: 'A premium experience from start to finish. The clinic is beautiful and the results exceeded my expectations.' },
            ].map((t, i) => (
              <Reveal key={i} delay={i * 100}>
                <div className="group h-full rounded-2xl bg-white p-7 border border-charcoal/5 transition-all duration-500 hover:shadow-xl hover:shadow-crimson/5 hover:-translate-y-1">
                  <div className="flex gap-1 mb-4 text-gold">
                    {Array.from({ length: 5 }).map((_, s) => <Star key={s} size={16} fill="currentColor" />)}
                  </div>
                  <p className="text-charcoal/70 leading-relaxed mb-6 italic">“{t.text}”</p>
                  <div className="flex items-center gap-3 pt-5 border-t border-charcoal/5">
                    <div className="flex h-11 w-11 items-center justify-center rounded-full bg-crimson text-white font-serif text-base font-bold">
                      {t.name.charAt(0)}
                    </div>
                    <div>
                      <p className="font-serif text-sm font-semibold text-charcoal">{t.name}</p>
                      <p className="text-xs text-crimson tracking-wide uppercase">{t.treatment}</p>
                    </div>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 lg:py-28 bg-gradient-to-br from-burgundy via-crimson to-crimson-dark relative overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-10 left-10 w-72 h-72 rounded-full bg-white blur-3xl animate-pulse-slow" />
          <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-gold blur-3xl animate-pulse-slow" />
        </div>
        <div className="container-luxury relative z-10 text-center">
          <Reveal>
            <p className="text-xs font-semibold tracking-[0.3em] uppercase text-white/60 mb-6">
              Start Your Journey
            </p>
            <h2 className="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6 text-balance max-w-3xl mx-auto leading-tight">
              Ready to Transform Your Skin, Hair, and Confidence?
            </h2>
            <p className="text-lg text-white/70 mb-10 max-w-xl mx-auto">
              Book your consultation today and discover personalized treatments designed around you.
            </p>
            <div className="flex flex-wrap items-center justify-center gap-4">
              <Link to="/contact" className="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-crimson font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-gold hover:text-white hover:shadow-xl">
                <Calendar size={18} className="transition-transform group-hover:scale-110" />
                Book an Appointment
              </Link>
              <a
                href="tel:+918879550581"
                className="group inline-flex items-center justify-center gap-2 px-8 py-4 border border-white/30 text-white font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-white/10"
              >
                <Phone size={18} className="transition-transform group-hover:rotate-12" />
                Call Us Directly
              </a>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Blog Preview */}
      {blogPosts.length > 0 && (
        <section className="py-20 lg:py-28">
          <div className="container-luxury">
            <Reveal>
              <div className="flex flex-col lg:flex-row lg:items-end justify-between mb-12 lg:mb-16 gap-6">
                <div className="max-w-2xl">
                  <p className="section-label">Patient Education</p>
                  <h2 className="heading-2 text-balance">Insights & Expert Advice</h2>
                </div>
                <Link
                  to="/blog"
                  className="group inline-flex items-center gap-2 text-sm font-medium text-crimson"
                >
                  View All Articles
                  <ArrowRight size={16} className="transition-transform group-hover:translate-x-1" />
                </Link>
              </div>
            </Reveal>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
              {blogPosts.slice(0, 3).map((post, i) => (
                <Reveal key={post.id} delay={i * 100}>
                  <Link
                    to={`/blog/${post.slug}`}
                    className="group bg-white border border-charcoal/5 overflow-hidden transition-all duration-500 hover:shadow-xl hover:shadow-crimson/5 hover:-translate-y-1"
                  >
                    <div className="relative aspect-[16/10] overflow-hidden bg-soft-red">
                      <img
                        src={post.featured_image || 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=600'}
                        alt={post.title}
                        className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                    </div>
                    <div className="p-6">
                      <p className="text-xs font-semibold tracking-wider uppercase text-crimson mb-3">
                        {post.blog_category?.name || 'Article'}
                      </p>
                      <h3 className="font-serif text-lg font-semibold text-charcoal mb-3 group-hover:text-crimson transition-colors line-clamp-2">
                        {post.title}
                      </h3>
                      <p className="text-sm text-charcoal/60 leading-relaxed line-clamp-2 mb-4">
                        {post.excerpt}
                      </p>
                      <span className="text-xs text-charcoal/40">
                        {post.published_at
                          ? new Date(post.published_at).toLocaleDateString('en-US', {
                              year: 'numeric',
                              month: 'long',
                              day: 'numeric',
                            })
                          : ''}
                      </span>
                    </div>
                  </Link>
                </Reveal>
              ))}
            </div>
          </div>
        </section>
      )}
    </>
  );
}
