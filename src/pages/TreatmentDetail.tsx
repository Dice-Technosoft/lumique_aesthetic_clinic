import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { supabase } from '@/lib/supabase';
import type {
  Treatment,
  TreatmentFAQ,
  BeforeAfterPhoto,
  TreatmentVideo,
  Treatment as RelatedTreatment,
} from '@/lib/types';
import FloatingBackground from '@/components/FloatingBackground';
import {
  Calendar,
  Phone,
  ChevronDown,
  ArrowRight,
  ArrowLeft,
  Play,
  ImageIcon,
} from 'lucide-react';

export default function TreatmentDetail() {
  const { slug } = useParams<{ slug: string }>();
  const [treatment, setTreatment] = useState<Treatment | null>(null);
  const [faqs, setFaqs] = useState<TreatmentFAQ[]>([]);
  const [photos, setPhotos] = useState<BeforeAfterPhoto[]>([]);
  const [videos, setVideos] = useState<TreatmentVideo[]>([]);
  const [related, setRelated] = useState<RelatedTreatment[]>([]);
  const [loading, setLoading] = useState(true);
  const [openFaq, setOpenFaq] = useState<number | null>(0);

  useEffect(() => {
    if (!slug) return;
    setLoading(true);
    (async () => {
      const { data: treatmentData } = await supabase
        .from('treatments')
        .select('*, category:categories(*)')
        .eq('slug', slug)
        .maybeSingle();

      if (!treatmentData) {
        setLoading(false);
        return;
      }

      setTreatment(treatmentData);

      const [faqRes, photoRes, videoRes, relatedRes] = await Promise.all([
        supabase
          .from('treatment_faqs')
          .select('*')
          .eq('treatment_id', treatmentData.id)
          .order('display_order'),
        supabase
          .from('before_after_photos')
          .select('*')
          .eq('treatment_id', treatmentData.id)
          .order('display_order'),
        supabase
          .from('treatment_videos')
          .select('*')
          .eq('treatment_id', treatmentData.id)
          .order('display_order'),
        supabase
          .from('treatments')
          .select('*, category:categories(*)')
          .eq('category_id', treatmentData.category_id)
          .neq('id', treatmentData.id)
          .order('display_order')
          .limit(3),
      ]);

      setFaqs(faqRes.data || []);
      setPhotos(photoRes.data || []);
      setVideos(videoRes.data || []);
      setRelated(relatedRes.data || []);
      setLoading(false);
    })();
  }, [slug]);

  if (loading) {
    return (
      <div className="pt-32 pb-20">
        <div className="container-luxury">
          <p className="text-charcoal/40">Loading treatment...</p>
        </div>
      </div>
    );
  }

  if (!treatment) {
    return (
      <div className="pt-40 pb-20 text-center">
        <div className="container-luxury">
          <h1 className="heading-3 mb-4">Treatment Not Found</h1>
          <p className="body-text mb-8">The treatment you are looking for does not exist.</p>
          <Link to="/treatments" className="btn-primary">Back to Treatments</Link>
        </div>
      </div>
    );
  }

  const infoFields = [
    { label: 'Who Is It For?', value: treatment.who_is_it_for },
    { label: 'Benefits', value: treatment.benefits },
    { label: 'Procedure Overview', value: treatment.procedure_overview },
    { label: 'Treatment Process', value: treatment.treatment_process },
    { label: 'Expected Results', value: treatment.expected_results },
    { label: 'Recovery Information', value: treatment.recovery_info },
    { label: 'Number of Sessions', value: treatment.num_sessions },
  ].filter((f) => f.value);

  return (
    <>
      {/* Hero */}
      <section className="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
        <div className="absolute inset-0">
          <img
            src={treatment.hero_image || 'https://images.pexels.com/photos/7581075/pexels-photo-7581075.jpeg?auto=compress&cs=tinysrgb&w=1920'}
            alt={treatment.title}
            className="h-full w-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-charcoal/85 via-charcoal/60 to-charcoal/30" />
        </div>
        <div className="container-luxury relative z-10">
          <div className="max-w-3xl text-white">
            <Link
              to="/treatments"
              className="inline-flex items-center gap-2 text-sm text-white/60 hover:text-white transition-colors mb-6"
            >
              <ArrowLeft size={16} />
              All Treatments
            </Link>
            {treatment.category && (
              <span className="inline-block bg-crimson px-4 py-1.5 text-xs font-semibold tracking-wider uppercase text-white mb-6">
                {treatment.category.name}
              </span>
            )}
            <h1 className="font-serif text-4xl lg:text-5xl font-bold mb-6 text-balance leading-tight">
              {treatment.title}
            </h1>
            <p className="text-lg text-white/80 leading-relaxed mb-8 max-w-2xl">
              {treatment.short_intro}
            </p>
            <div className="flex flex-wrap gap-4">
              <Link to="/contact" className="btn-primary">
                <Calendar size={18} />
                Book Your Consultation
              </Link>
              <a
                href="tel:+918879550581"
                className="inline-flex items-center justify-center gap-2 px-7 py-3.5 border border-white/30 text-white font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-white/10"
              >
                <Phone size={18} />
                Contact Clinic
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Description & Info */}
      <section className="py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={8} />
        <div className="container-luxury">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-16">
            <div className="lg:col-span-2 space-y-12">
              {treatment.description && (
                <div>
                  <p className="section-label">Overview</p>
                  <h2 className="heading-3 mb-6">Treatment Description</h2>
                  <p className="body-text whitespace-pre-line">{treatment.description}</p>
                </div>
              )}

              {infoFields.map((field, i) => (
                <div key={i}>
                  <p className="section-label">Section {i + 1}</p>
                  <h2 className="heading-3 mb-6">{field.label}</h2>
                  <p className="body-text whitespace-pre-line">{field.value}</p>
                </div>
              ))}

              {/* Doctor Recommendation */}
              {treatment.doctor_recommendation && (
                <div className="bg-soft-red/50 p-8 lg:p-10 border-l-4 border-crimson">
                  <p className="section-label">Doctor's Recommendation</p>
                  <p className="font-serif text-xl text-charcoal leading-relaxed italic">
                    "{treatment.doctor_recommendation}"
                  </p>
                </div>
              )}
            </div>

            {/* Sidebar */}
            <div className="lg:col-span-1">
              <div className="sticky top-24 space-y-6">
                {/* Quick CTA */}
                <div className="bg-charcoal p-8 text-white">
                  <h3 className="font-serif text-xl font-semibold mb-4">Book This Treatment</h3>
                  <p className="text-sm text-white/60 mb-6">
                    Schedule your consultation for {treatment.title}.
                  </p>
                  <Link to="/contact" className="btn-primary w-full mb-3">
                    <Calendar size={16} />
                    Book Consultation
                  </Link>
                  <a
                    href="tel:+918879550581"
                    className="inline-flex items-center justify-center gap-2 w-full px-7 py-3.5 border border-white/20 text-white font-medium text-sm tracking-wide uppercase transition-all hover:bg-white/10"
                  >
                    <Phone size={16} />
                    Call Us
                  </a>
                </div>

                {/* Quick Facts */}
                {treatment.num_sessions && (
                  <div className="bg-white p-6 border border-charcoal/5">
                    <h4 className="text-xs font-semibold tracking-wider uppercase text-charcoal/40 mb-4">
                      Quick Facts
                    </h4>
                    <div className="space-y-3">
                      <div className="flex justify-between text-sm">
                        <span className="text-charcoal/60">Sessions</span>
                        <span className="font-medium text-charcoal">{treatment.num_sessions}</span>
                      </div>
                      {treatment.recovery_info && (
                        <div className="flex justify-between text-sm">
                          <span className="text-charcoal/60">Recovery</span>
                          <span className="font-medium text-charcoal text-right max-w-[60%]">
                            {treatment.recovery_info.split('.')[0]}.
                          </span>
                        </div>
                      )}
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Before / After Gallery */}
      {photos.length > 0 && (
        <section className="py-20 lg:py-28 bg-white relative overflow-hidden">
          <FloatingBackground count={6} />
          <div className="container-luxury">
            <div className="text-center mb-12">
              <p className="section-label">Gallery</p>
              <h2 className="heading-2 text-balance">Before & After Results</h2>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {photos.map((photo) => (
                <div key={photo.id} className="group">
                  <div className="grid grid-cols-2 gap-1">
                    <div className="relative aspect-square overflow-hidden bg-soft-red">
                      {photo.before_image ? (
                        <img src={photo.before_image} alt="Before" className="h-full w-full object-cover" />
                      ) : (
                        <div className="flex h-full items-center justify-center">
                          <ImageIcon size={24} className="text-crimson/30" />
                        </div>
                      )}
                      <span className="absolute bottom-2 left-2 bg-charcoal/70 px-2 py-1 text-[10px] uppercase tracking-wider text-white">
                        Before
                      </span>
                    </div>
                    <div className="relative aspect-square overflow-hidden bg-soft-red">
                      {photo.after_image ? (
                        <img src={photo.after_image} alt="After" className="h-full w-full object-cover" />
                      ) : (
                        <div className="flex h-full items-center justify-center">
                          <ImageIcon size={24} className="text-crimson/30" />
                        </div>
                      )}
                      <span className="absolute bottom-2 left-2 bg-crimson px-2 py-1 text-[10px] uppercase tracking-wider text-white">
                        After
                      </span>
                    </div>
                  </div>
                  {photo.caption && (
                    <p className="mt-3 text-sm text-charcoal/60 text-center">{photo.caption}</p>
                  )}
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Related Videos */}
      {videos.length > 0 && (
        <section className="py-20 lg:py-28">
          <div className="container-luxury">
            <div className="mb-12">
              <p className="section-label">Media</p>
              <h2 className="heading-2 text-balance">Related Videos</h2>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {videos.map((video) => (
                <a
                  key={video.id}
                  href={video.video_url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="group relative aspect-video overflow-hidden bg-charcoal"
                >
                  {video.thumbnail && (
                    <img
                      src={video.thumbnail}
                      alt={video.title}
                      className="h-full w-full object-cover opacity-70 transition-opacity group-hover:opacity-50"
                    />
                  )}
                  <div className="absolute inset-0 flex items-center justify-center">
                    <div className="flex h-14 w-14 items-center justify-center bg-crimson rounded-full transition-transform group-hover:scale-110">
                      <Play size={22} className="text-white ml-1" fill="white" />
                    </div>
                  </div>
                  <div className="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-charcoal/80 to-transparent">
                    <p className="text-sm font-medium text-white">{video.title}</p>
                  </div>
                </a>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* FAQs */}
      {faqs.length > 0 && (
        <section className="py-20 lg:py-28 bg-white relative overflow-hidden">
          <FloatingBackground count={6} />
          <div className="container-luxury max-w-3xl">
            <div className="text-center mb-12">
              <p className="section-label">Questions</p>
              <h2 className="heading-2 text-balance">Frequently Asked Questions</h2>
            </div>
            <div className="space-y-3">
              {faqs.map((faq, i) => (
                <div key={faq.id} className="border border-charcoal/10">
                  <button
                    onClick={() => setOpenFaq(openFaq === i ? null : i)}
                    className="flex w-full items-center justify-between p-6 text-left"
                  >
                    <span className="font-serif text-lg font-semibold text-charcoal pr-4">
                      {faq.question}
                    </span>
                    <ChevronDown
                      size={20}
                      className={`text-crimson shrink-0 transition-transform duration-300 ${
                        openFaq === i ? 'rotate-180' : ''
                      }`}
                    />
                  </button>
                  <div
                    className={`overflow-hidden transition-all duration-300 ${
                      openFaq === i ? 'max-h-96' : 'max-h-0'
                    }`}
                  >
                    <p className="px-6 pb-6 text-charcoal/70 leading-relaxed">{faq.answer}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Related Treatments */}
      {related.length > 0 && (
        <section className="py-20 lg:py-28">
          <div className="container-luxury">
            <div className="mb-12">
              <p className="section-label">Explore More</p>
              <h2 className="heading-2 text-balance">Related Treatments</h2>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {related.map((rel) => (
                <Link
                  key={rel.id}
                  to={`/treatments/${rel.slug}`}
                  className="group bg-white border border-charcoal/5 p-6 transition-all duration-500 hover:shadow-xl hover:shadow-crimson/5"
                >
                  <h3 className="font-serif text-lg font-semibold text-charcoal mb-2 group-hover:text-crimson transition-colors">
                    {rel.title}
                  </h3>
                  <p className="text-sm text-charcoal/60 leading-relaxed line-clamp-2 mb-4">
                    {rel.short_intro}
                  </p>
                  <span className="inline-flex items-center gap-1.5 text-xs font-medium text-crimson tracking-wider uppercase">
                    View Treatment
                    <ArrowRight size={14} className="transition-transform group-hover:translate-x-1" />
                  </span>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Bottom CTA */}
      <section className="py-20 lg:py-28 bg-gradient-to-br from-burgundy via-crimson to-crimson-dark relative overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-10 left-10 w-72 h-72 rounded-full bg-white blur-3xl animate-pulse-slow" />
          <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-gold blur-3xl animate-pulse-slow" />
        </div>
        <div className="container-luxury text-center">
          <h2 className="heading-2 mb-6 text-balance max-w-2xl mx-auto text-white">
            Ready to Begin Your {treatment.title} Journey?
          </h2>
          <div className="flex flex-wrap items-center justify-center gap-4">
            <Link to="/contact" className="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-crimson font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-gold hover:text-white hover:shadow-xl">
              <Calendar size={18} className="transition-transform group-hover:scale-110" />
              Book Your Consultation
            </Link>
            <a
              href="tel:+918879550581"
              className="inline-flex items-center justify-center gap-2 px-8 py-4 border border-white/30 text-white font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-white/10"
            >
              <Phone size={18} />
              Contact Clinic
            </a>
          </div>
        </div>
      </section>
    </>
  );
}
