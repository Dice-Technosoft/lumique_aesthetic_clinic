import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { supabase } from '@/lib/supabase';
import type { BlogPost } from '@/lib/types';
import { Calendar, ArrowLeft, ArrowRight, Phone } from 'lucide-react';
import Reveal from '@/components/Reveal';
import FloatingBackground from '@/components/FloatingBackground';

export default function BlogPost() {
  const { slug } = useParams<{ slug: string }>();
  const [post, setPost] = useState<BlogPost | null>(null);
  const [related, setRelated] = useState<BlogPost[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!slug) return;
    setLoading(true);
    (async () => {
      const { data } = await supabase
        .from('blog_posts')
        .select('*, blog_category:blog_categories(*)')
        .eq('slug', slug)
        .eq('status', 'published')
        .maybeSingle();

      setPost(data);

      if (data?.blog_category_id) {
        const { data: relatedData } = await supabase
          .from('blog_posts')
          .select('*, blog_category:blog_categories(*)')
          .eq('blog_category_id', data.blog_category_id)
          .eq('status', 'published')
          .neq('id', data.id)
          .order('published_at', { ascending: false })
          .limit(3);
        setRelated(relatedData || []);
      }

      setLoading(false);

      if (data?.seo_title) {
        document.title = data.seo_title;
      }
      if (data?.seo_description) {
        const metaDesc = document.querySelector('meta[name="description"]');
        if (metaDesc) metaDesc.setAttribute('content', data.seo_description);
      }
    })();

    return () => {
      document.title = 'Lumique Aesthetic Clinic | Advanced Dermatology & Aesthetic Care';
    };
  }, [slug]);

  if (loading) {
    return (
      <div className="pt-32 pb-20">
        <div className="container-luxury">
          <p className="text-charcoal/40">Loading article...</p>
        </div>
      </div>
    );
  }

  if (!post) {
    return (
      <div className="pt-40 pb-20 text-center">
        <div className="container-luxury">
          <h1 className="heading-3 mb-4">Article Not Found</h1>
          <Link to="/blog" className="btn-primary">Back to Blog</Link>
        </div>
      </div>
    );
  }

  const renderContent = (content: string) => {
    const lines = content.split('\n');
    return lines.map((line, i) => {
      const trimmed = line.trim();
      if (trimmed.startsWith('### ')) {
        return (
          <h3 key={i} className="font-serif text-xl font-semibold text-charcoal mt-8 mb-4">
            {trimmed.slice(4)}
          </h3>
        );
      }
      if (trimmed.startsWith('## ')) {
        return (
          <h2 key={i} className="font-serif text-2xl font-bold text-charcoal mt-10 mb-4">
            {trimmed.slice(3)}
          </h2>
        );
      }
      if (trimmed.startsWith('# ')) {
        return (
          <h1 key={i} className="font-serif text-3xl font-bold text-charcoal mt-10 mb-4">
            {trimmed.slice(2)}
          </h1>
        );
      }
      if (trimmed.startsWith('- ')) {
        return (
          <li key={i} className="ml-6 text-charcoal/70 leading-relaxed list-disc mb-2">
            {trimmed.slice(2)}
          </li>
        );
      }
      if (/^\d+\.\s/.test(trimmed)) {
        return (
          <li key={i} className="ml-6 text-charcoal/70 leading-relaxed list-decimal mb-2">
            {trimmed.replace(/^\d+\.\s/, '')}
          </li>
        );
      }
      if (trimmed === '') {
        return <div key={i} className="h-4" />;
      }
      return (
        <p key={i} className="text-charcoal/70 leading-relaxed mb-4">
          {trimmed}
        </p>
      );
    });
  };

  return (
    <>
      {/* Hero */}
      <section className="pt-32 pb-16 lg:pt-40 lg:pb-20 bg-gradient-to-b from-soft-red/40 to-ivory relative overflow-hidden">
        <FloatingBackground count={8} />
        <div className="container-luxury max-w-3xl relative z-10">
          <Link
            to="/blog"
            className="inline-flex items-center gap-2 text-sm text-charcoal/60 hover:text-crimson transition-colors mb-6"
          >
            <ArrowLeft size={16} />
            All Articles
          </Link>
          <p className="text-xs font-semibold tracking-wider uppercase text-crimson mb-4">
            {post.blog_category?.name || 'Article'}
          </p>
          <h1 className="font-serif text-3xl lg:text-4xl font-bold text-charcoal mb-6 text-balance leading-tight">
            {post.title}
          </h1>
          <div className="flex items-center gap-4 text-sm text-charcoal/40">
            {post.author && <span>By {post.author}</span>}
            {post.author && post.published_at && <span>·</span>}
            <span className="flex items-center gap-1.5">
              <Calendar size={14} />
              {post.published_at
                ? new Date(post.published_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                  })
                : ''}
            </span>
          </div>
        </div>
      </section>

      {/* Featured Image */}
      {post.featured_image && (
        <div className="container-luxury max-w-4xl">
          <div className="aspect-[16/9] overflow-hidden bg-soft-red -mt-8 lg:-mt-12">
            <img
              src={post.featured_image}
              alt={post.title}
              className="h-full w-full object-cover"
            />
          </div>
        </div>
      )}

      {/* Content */}
      <section className="py-16 lg:py-24">
        <div className="container-luxury max-w-3xl">
          {post.excerpt && (
            <p className="font-serif text-xl text-charcoal/80 leading-relaxed mb-10 italic border-l-4 border-crimson pl-6">
              {post.excerpt}
            </p>
          )}
          <div className="blog-content">{renderContent(post.content || '')}</div>

          {/* Tags */}
          {post.tags && (
            <div className="mt-12 pt-8 border-t border-charcoal/10">
              <div className="flex flex-wrap gap-2">
                {post.tags.split(',').map((tag, i) => (
                  <span
                    key={i}
                    className="px-3 py-1.5 bg-soft-red text-xs font-medium text-crimson"
                  >
                    {tag.trim()}
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 lg:py-20 bg-gradient-to-br from-burgundy via-crimson to-crimson-dark relative overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-10 left-10 w-72 h-72 rounded-full bg-white blur-3xl animate-pulse-slow" />
          <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-gold blur-3xl animate-pulse-slow" />
        </div>
        <div className="container-luxury relative z-10 text-center">
          <h2 className="heading-3 mb-6 text-balance max-w-2xl mx-auto text-white">
            Have Questions About This Topic?
          </h2>
          <div className="flex flex-wrap items-center justify-center gap-4">
            <Link to="/contact" className="group inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-crimson font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-gold hover:text-white hover:shadow-xl">
              <Calendar size={18} className="transition-transform group-hover:scale-110" />
              Book a Consultation
            </Link>
            <a href="tel:+918879550581" className="inline-flex items-center justify-center gap-2 px-8 py-4 border border-white/30 text-white font-medium text-sm tracking-wide uppercase transition-all duration-300 hover:bg-white/10">
              <Phone size={18} />
              Contact Us
            </a>
          </div>
        </div>
      </section>

      {/* Related */}
      {related.length > 0 && (
        <section className="py-20 lg:py-28 relative overflow-hidden">
          <FloatingBackground count={6} />
          <div className="container-luxury relative z-10">
            <Reveal>
              <div className="mb-12">
                <p className="section-label">Keep Reading</p>
                <h2 className="heading-2 text-balance">Related Articles</h2>
              </div>
            </Reveal>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {related.map((rel, i) => (
                <Reveal key={rel.id} delay={i * 100}>
                  <Link
                    to={`/blog/${rel.slug}`}
                    className="group bg-white border border-charcoal/5 overflow-hidden transition-all duration-500 hover:shadow-xl hover:shadow-crimson/5 hover:-translate-y-1"
                  >
                    <div className="relative aspect-[16/10] overflow-hidden bg-soft-red">
                      <img
                        src={rel.featured_image || 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=600'}
                        alt={rel.title}
                        className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                    </div>
                    <div className="p-6">
                      <h3 className="font-serif text-lg font-semibold text-charcoal mb-2 group-hover:text-crimson transition-colors line-clamp-2">
                        {rel.title}
                      </h3>
                      <span className="inline-flex items-center gap-1.5 text-xs font-medium text-crimson tracking-wider uppercase">
                        Read More
                        <ArrowRight size={14} className="transition-transform group-hover:translate-x-1" />
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
