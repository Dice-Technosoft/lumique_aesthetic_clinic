import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { supabase } from '@/lib/supabase';
import type { DoctorProfile } from '@/lib/types';
import Reveal from '@/components/Reveal';
import FloatingBackground from '@/components/FloatingBackground';
import {
  ShieldCheck,
  Microscope,
  HeartHandshake,
  Sparkles,
  Award,
  Stethoscope,
  Calendar,
  ArrowRight,
  Target,
  Eye,
  Gem,
  Users,
} from 'lucide-react';

export default function About() {
  const [doctor, setDoctor] = useState<DoctorProfile | null>(null);

  useEffect(() => {
    (async () => {
      const { data } = await supabase
        .from('doctor_profile')
        .select('*')
        .maybeSingle();
      setDoctor(data);
    })();
  }, []);

  const renderField = (label: string, value: string | null | undefined, Icon: typeof Award) => {
    if (!value) return null;
    return (
      <Reveal>
        <div className="border-l-2 border-soft-red pl-6 py-2 transition-colors hover:border-crimson">
          <div className="flex items-center gap-2 mb-3">
            <Icon size={18} className="text-crimson" />
            <h3 className="font-serif text-xl font-semibold text-charcoal">{label}</h3>
          </div>
          <p className="text-charcoal/70 leading-relaxed whitespace-pre-line">{value}</p>
        </div>
      </Reveal>
    );
  };

  return (
    <>
      {/* Hero with floating background */}
      <section className="relative pt-32 pb-16 lg:pt-40 lg:pb-24 bg-gradient-to-b from-soft-red/40 to-ivory overflow-hidden">
        <FloatingBackground count={10} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="max-w-3xl">
              <p className="section-label">About the Doctor</p>
              <h1 className="heading-1 text-balance mb-6">
                {doctor?.title || 'Lead Dermatologist & Aesthetic Specialist'}
              </h1>
              <p className="body-text max-w-2xl">
                {doctor?.introduction ||
                  'Our lead dermatologist brings years of specialized experience in dermatology and aesthetic medicine, dedicated to delivering natural-looking, transformative results.'}
              </p>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Clinic Story / About Us */}
      <section className="py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={8} />
        <div className="container-luxury relative z-10">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <Reveal>
              <div className="relative">
                <div className="relative overflow-hidden">
                  <img
                    src="https://images.pexels.com/photos/11024139/pexels-photo-11024139.jpeg?auto=compress&cs=tinysrgb&w=800"
                    alt="Lumique Aesthetic Clinic interior"
                    className="w-full aspect-[4/5] object-cover"
                  />
                </div>
                <div className="absolute -bottom-8 -right-6 w-52 h-52 overflow-hidden border-8 border-white shadow-xl hidden lg:block animate-float">
                  <img
                    src="https://images.pexels.com/photos/7108264/pexels-photo-7108264.jpeg?auto=compress&cs=tinysrgb&w=400"
                    alt="Doctor consulting patient"
                    className="w-full h-full object-cover"
                  />
                </div>
                <div className="absolute top-8 -left-6 bg-gold w-20 h-20 hidden lg:block animate-float-slow" />
              </div>
            </Reveal>

            <Reveal delay={150}>
              <div>
                <p className="section-label">Our Story</p>
                <h2 className="heading-2 mb-6 text-balance">
                  A Clinic Built on Trust, Expertise & Care
                </h2>
                <p className="body-text mb-6">
                  Lumique Aesthetic Clinic was founded with a singular vision: to provide world-class
                  dermatology and aesthetic treatments in a warm, welcoming environment. We believe
                  that everyone deserves to feel confident in their own skin.
                </p>
                <p className="body-text mb-8">
                  From advanced laser therapies to hair restoration and aesthetic enhancements, every
                  treatment at our clinic is backed by years of medical expertise, cutting-edge
                  technology, and a deeply personal approach to patient care.
                </p>
                <div className="grid grid-cols-2 gap-4">
                  {[
                    { icon: Target, label: 'Our Mission', desc: 'Deliver safe, effective, personalized care' },
                    { icon: Eye, label: 'Our Vision', desc: 'Be the most trusted aesthetic clinic' },
                    { icon: Gem, label: 'Our Values', desc: 'Integrity, excellence, compassion' },
                    { icon: Users, label: 'Our Promise', desc: 'Natural results, every time' },
                  ].map((item, i) => (
                    <div key={i} className="p-5 bg-ivory border border-charcoal/5 transition-all hover:border-crimson/20 hover:shadow-lg hover:shadow-crimson/5">
                      <div className="flex h-10 w-10 items-center justify-center bg-soft-red mb-3">
                        <item.icon size={18} className="text-crimson" />
                      </div>
                      <h3 className="font-serif text-sm font-semibold text-charcoal mb-1">{item.label}</h3>
                      <p className="text-xs text-charcoal/50 leading-relaxed">{item.desc}</p>
                    </div>
                  ))}
                </div>
              </div>
            </Reveal>
          </div>
        </div>
      </section>

      {/* Profile Section */}
      <section className="py-20 lg:py-28 bg-white relative overflow-hidden">
        <FloatingBackground count={8} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="text-center max-w-2xl mx-auto mb-16">
              <p className="section-label">Doctor's Profile</p>
              <h2 className="heading-2 text-balance">Qualifications & Expertise</h2>
            </div>
          </Reveal>
          <div className="grid grid-cols-1 lg:grid-cols-5 gap-12 lg:gap-16">
            {/* Photo */}
            <div className="lg:col-span-2">
              <Reveal>
                <div className="relative">
                  <div className="overflow-hidden bg-soft-red">
                    <img
                      src={doctor?.photo || 'https://images.pexels.com/photos/32160039/pexels-photo-32160039.jpeg?auto=compress&cs=tinysrgb&w=800'}
                      alt={doctor?.name || 'Doctor'}
                      className="w-full aspect-[3/4] object-cover"
                    />
                  </div>
                  <div className="absolute -top-4 -right-4 bg-crimson px-6 py-4 hidden lg:block animate-float">
                    <p className="font-serif text-lg font-semibold text-white">{doctor?.name || 'Our Doctor'}</p>
                  </div>
                  <div className="absolute -bottom-4 -left-4 bg-gold w-24 h-24 hidden lg:block animate-float-slow" />
                </div>
              </Reveal>
            </div>

            {/* Details */}
            <div className="lg:col-span-3 space-y-8">
              {renderField('Professional Profile', doctor?.professional_profile, Stethoscope)}
              {renderField('Qualifications', doctor?.qualifications, Award)}
              {renderField('Experience', doctor?.experience, Stethoscope)}
              {renderField('Specializations', doctor?.specializations, Microscope)}
              {renderField('Areas of Expertise', doctor?.areas_of_expertise, Sparkles)}
              {renderField('Treatment Philosophy', doctor?.treatment_philosophy, HeartHandshake)}
              {renderField('Clinic Approach', doctor?.clinic_approach, ShieldCheck)}
              {renderField('Certifications', doctor?.certifications, Award)}
              {renderField('Professional Achievements', doctor?.achievements, Award)}
            </div>
          </div>
        </div>
      </section>

      {/* Why Patients Choose Us */}
      <section className="bg-ivory py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={10} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="text-center max-w-2xl mx-auto mb-16">
              <p className="section-label">Our Promise</p>
              <h2 className="heading-2 text-balance">Why Patients Choose Us</h2>
            </div>
          </Reveal>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[
              {
                icon: HeartHandshake,
                title: 'Personalized Treatment Plans',
                desc: 'Every patient receives a customized treatment plan designed for their unique skin type, concerns, and goals.',
              },
              {
                icon: Microscope,
                title: 'Advanced Technology',
                desc: 'We invest in the latest dermatological technology and evidence-based protocols for optimal results.',
              },
              {
                icon: HeartHandshake,
                title: 'Patient-Focused Approach',
                desc: 'Your comfort, concerns, and questions are always prioritized throughout your treatment journey.',
              },
              {
                icon: ShieldCheck,
                title: 'Safety-First Treatment',
                desc: 'Every procedure follows strict safety protocols with medical-grade standards and professional oversight.',
              },
              {
                icon: Sparkles,
                title: 'Natural-Looking Results',
                desc: 'We enhance your natural beauty, never transforming. Subtle, elegant, and authentic results.',
              },
              {
                icon: Stethoscope,
                title: 'Professional Clinical Environment',
                desc: 'A luxurious, hygienic, and welcoming clinical space designed for your comfort and confidence.',
              },
            ].map((item, i) => (
              <Reveal key={i} delay={i * 80}>
                <div className="group p-8 bg-white border border-charcoal/5 transition-all duration-500 hover:border-crimson/20 hover:shadow-xl hover:shadow-crimson/5 hover:-translate-y-1 h-full">
                  <div className="flex h-14 w-14 items-center justify-center bg-soft-red mb-6 transition-all duration-300 group-hover:bg-crimson group-hover:scale-110">
                    <item.icon size={24} className="text-crimson transition-colors duration-300 group-hover:text-white" />
                  </div>
                  <h3 className="font-serif text-xl font-semibold text-charcoal mb-3 group-hover:text-crimson transition-colors">{item.title}</h3>
                  <p className="text-sm text-charcoal/60 leading-relaxed">{item.desc}</p>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>

      {/* Stats strip */}
      <section className="bg-charcoal py-16 relative overflow-hidden">
        <FloatingBackground count={8} />
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

      {/* CTA */}
      <section className="py-20 lg:py-28 bg-gradient-to-br from-burgundy via-crimson to-crimson-dark relative overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-10 left-10 w-72 h-72 rounded-full bg-white blur-3xl animate-pulse-slow" />
          <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-gold blur-3xl animate-pulse-slow" />
        </div>
        <div className="container-luxury relative z-10 text-center">
          <Reveal>
            <h2 className="heading-2 mb-6 text-balance max-w-2xl mx-auto text-white">
              Schedule Your Consultation Today
            </h2>
            <p className="text-lg text-white/70 mb-10 max-w-xl mx-auto">
              Take the first step toward healthier skin, restored hair, and renewed confidence.
            </p>
            <Link to="/contact" className="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-crimson font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-gold hover:text-white hover:shadow-xl">
              <Calendar size={18} className="transition-transform group-hover:scale-110" />
              Book an Appointment
              <ArrowRight size={16} className="transition-transform group-hover:translate-x-1" />
            </Link>
          </Reveal>
        </div>
      </section>
    </>
  );
}
