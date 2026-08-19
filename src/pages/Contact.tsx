import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import type { Treatment, ClinicSettings } from '@/lib/types';
import AppointmentForm from '@/components/AppointmentForm';
import Reveal from '@/components/Reveal';
import FloatingBackground from '@/components/FloatingBackground';
import { Phone, Mail, MapPin, Clock, MessageCircle } from 'lucide-react';

export default function Contact() {
  const [treatments, setTreatments] = useState<Treatment[]>([]);
  const [settings, setSettings] = useState<ClinicSettings | null>(null);

  useEffect(() => {
    (async () => {
      const [{ data: treats }, { data: settingsData }] = await Promise.all([
        supabase
          .from('treatments')
          .select('*, category:categories(*)')
          .order('display_order'),
        supabase
          .from('clinic_settings')
          .select('*')
          .maybeSingle(),
      ]);
      setTreatments(treats || []);
      setSettings(settingsData);
    })();
  }, []);

  const phone = settings?.phone || '+91 88795 50581';
  const whatsapp = settings?.whatsapp || phone;
  const email = settings?.email || 'hello@lumiqueclinic.com';
  const address = settings?.address || '123 Medical Center Drive, Suite 200, Beverly Hills, CA 90210';

  return (
    <>
      {/* Hero */}
      <section className="pt-32 pb-16 lg:pt-40 lg:pb-24 bg-gradient-to-b from-soft-red/40 to-ivory relative overflow-hidden">
        <FloatingBackground count={10} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="max-w-3xl">
              <p className="section-label">Get in Touch</p>
              <h1 className="heading-1 text-balance mb-6">Contact & Appointments</h1>
              <p className="body-text max-w-2xl">
                Book your appointment or reach out with any questions. Our team is ready to help
                you begin your journey to healthier skin, hair, and renewed confidence.
              </p>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Contact Info + Form */}
      <section className="py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={8} />
        <div className="container-luxury relative z-10">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
            {/* Info */}
            <div>
              <p className="section-label">Clinic Information</p>
              <h2 className="heading-2 mb-10">Visit Our Clinic</h2>

              <div className="space-y-8">
                <div className="flex items-start gap-4">
                  <div className="flex h-12 w-12 items-center justify-center bg-soft-red shrink-0">
                    <MapPin size={20} className="text-crimson" />
                  </div>
                  <div>
                    <h3 className="font-serif text-lg font-semibold text-charcoal mb-1">Address</h3>
                    <p className="text-sm text-charcoal/60 leading-relaxed">{address}</p>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="flex h-12 w-12 items-center justify-center bg-soft-red shrink-0">
                    <Phone size={20} className="text-crimson" />
                  </div>
                  <div>
                    <h3 className="font-serif text-lg font-semibold text-charcoal mb-1">Phone</h3>
                    <a href={`tel:${phone.replace(/\s/g, '')}`} className="text-sm text-charcoal/60 hover:text-crimson transition-colors">
                      {phone}
                    </a>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="flex h-12 w-12 items-center justify-center bg-soft-red shrink-0">
                    <MessageCircle size={20} className="text-crimson" />
                  </div>
                  <div>
                    <h3 className="font-serif text-lg font-semibold text-charcoal mb-1">WhatsApp</h3>
                    <a
                      href={`https://wa.me/${whatsapp.replace(/[^0-9]/g, '')}`}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-sm text-charcoal/60 hover:text-crimson transition-colors"
                    >
                      {whatsapp}
                    </a>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="flex h-12 w-12 items-center justify-center bg-soft-red shrink-0">
                    <Mail size={20} className="text-crimson" />
                  </div>
                  <div>
                    <h3 className="font-serif text-lg font-semibold text-charcoal mb-1">Email</h3>
                    <a href={`mailto:${email}`} className="text-sm text-charcoal/60 hover:text-crimson transition-colors">
                      {email}
                    </a>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="flex h-12 w-12 items-center justify-center bg-soft-red shrink-0">
                    <Clock size={20} className="text-crimson" />
                  </div>
                  <div>
                    <h3 className="font-serif text-lg font-semibold text-charcoal mb-1">Working Hours</h3>
                    <p className="text-sm text-charcoal/60 leading-relaxed whitespace-pre-line">
                      {settings?.working_hours || 'Monday - Friday: 9:00 AM - 7:00 PM\nSaturday: 10:00 AM - 5:00 PM\nSunday: Closed'}
                    </p>
                  </div>
                </div>
              </div>

              {/* Map */}
              <div className="mt-10">
                <div className="aspect-[4/3] overflow-hidden border border-charcoal/10">
                  {settings?.map_embed ? (
                    <div dangerouslySetInnerHTML={{ __html: settings.map_embed }} className="w-full h-full" />
                  ) : (
                    <iframe
                      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3305.7332480437136!2d-118.40373292377223!3d34.07355921447478!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80c2bc04d6d147ab%3A0x1f5e8267a0c3f336!2sBeverly%20Hills%2C%20CA!5e0!3m2!1sen!2sus!4v1700000000000"
                      width="100%"
                      height="100%"
                      style={{ border: 0 }}
                      allowFullScreen
                      loading="lazy"
                      referrerPolicy="no-referrer-when-downgrade"
                      title="Clinic Location"
                    />
                  )}
                </div>
              </div>
            </div>

            {/* Form */}
            <div>
              <div className="bg-white p-8 lg:p-10 border border-charcoal/5">
                <p className="section-label">Book an Appointment</p>
                <h2 className="heading-3 mb-2">Schedule Your Visit</h2>
                <p className="text-sm text-charcoal/50 mb-8">
                  Fill out the form below and we'll contact you to confirm your appointment.
                </p>
                <AppointmentForm treatments={treatments} />
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
