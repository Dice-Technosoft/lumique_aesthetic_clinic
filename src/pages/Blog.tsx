import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { supabase } from '@/lib/supabase';
import type { BlogPost, BlogCategory } from '@/lib/types';
import { Search } from 'lucide-react';
import Reveal from '@/components/Reveal';
import FloatingBackground from '@/components/FloatingBackground';

export default function Blog() {
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [categories, setCategories] = useState<BlogCategory[]>([]);
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      const [{ data: cats }, { data: postData }] = await Promise.all([
        supabase.from('blog_categories').select('*').order('display_order'),
        supabase
          .from('blog_posts')
          .select('*, blog_category:blog_categories(*)')
          .eq('status', 'published')
          .order('published_at', { ascending: false }),
      ]);
      setCategories(cats || []);
      setPosts(postData || []);
      setLoading(false);
    })();
  }, []);

  const filtered = posts.filter((post) => {
    const matchesCategory =
      activeCategory === 'all' || post.blog_category?.slug === activeCategory;
    const matchesSearch =
      !search ||
      post.title.toLowerCase().includes(search.toLowerCase()) ||
      (post.excerpt || '').toLowerCase().includes(search.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  const featured = filtered[0];
  const rest = filtered.slice(1);

  return (
    <>
      {/* Hero */}
      <section className="pt-32 pb-16 lg:pt-40 lg:pb-24 bg-gradient-to-b from-soft-red/40 to-ivory relative overflow-hidden">
        <FloatingBackground count={10} />
        <div className="container-luxury relative z-10">
          <Reveal>
            <div className="max-w-3xl">
              <p className="section-label">Patient Education</p>
              <h1 className="heading-1 text-balance mb-6">Expert Insights & Skincare Knowledge</h1>
              <p className="body-text max-w-2xl">
                Explore our blog for dermatology insights, treatment guides, and expert advice on
                skin, hair, laser, and aesthetic care.
              </p>
            </div>
          </Reveal>
        </div>
      </section>

      {/* Search & Filter */}
      <section className="border-y border-charcoal/5 bg-white sticky top-16 z-30">
        <div className="container-luxury">
          <div className="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 py-4">
            <div className="flex items-center gap-1 overflow-x-auto scrollbar-hide">
              <button
                onClick={() => setActiveCategory('all')}
                className={`px-4 py-2 text-sm font-medium whitespace-nowrap transition-colors ${
                  activeCategory === 'all' ? 'text-crimson' : 'text-charcoal/60 hover:text-crimson'
                }`}
              >
                All
              </button>
              {categories.map((cat) => (
                <button
                  key={cat.id}
                  onClick={() => setActiveCategory(cat.slug)}
                  className={`px-4 py-2 text-sm font-medium whitespace-nowrap transition-colors ${
                    activeCategory === cat.slug ? 'text-crimson' : 'text-charcoal/60 hover:text-crimson'
                  }`}
                >
                  {cat.name}
                </button>
              ))}
            </div>
            <div className="relative lg:w-64">
              <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-charcoal/30" />
              <input
                type="text"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="Search articles..."
                className="w-full pl-10 pr-4 py-2.5 bg-ivory border border-charcoal/10 text-sm focus:outline-none focus:border-crimson transition-colors"
              />
            </div>
          </div>
        </div>
      </section>

      {/* Articles */}
      <section className="py-20 lg:py-28 relative overflow-hidden">
        <FloatingBackground count={6} />
        <div className="container-luxury relative z-10">
          {loading ? (
            <p className="text-charcoal/40">Loading articles...</p>
          ) : filtered.length === 0 ? (
            <div className="text-center py-20">
              <p className="text-charcoal/50 mb-4">No articles found.</p>
            </div>
          ) : (
            <>
              {/* Featured */}
              {featured && activeCategory === 'all' && !search && (
                <Link
                  to={`/blog/${featured.slug}`}
                  className="group grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-16 lg:mb-20 items-center"
                >
                  <div className="relative aspect-[16/10] overflow-hidden bg-soft-red">
                    <img
                      src={featured.featured_image || 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800'}
                      alt={featured.title}
                      className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                  </div>
                  <div>
                    <p className="text-xs font-semibold tracking-wider uppercase text-crimson mb-4">
                      {featured.blog_category?.name || 'Article'} · Featured
                    </p>
                    <h2 className="font-serif text-2xl lg:text-3xl font-bold text-charcoal mb-4 group-hover:text-crimson transition-colors text-balance">
                      {featured.title}
                    </h2>
                    <p className="text-charcoal/60 leading-relaxed mb-6">{featured.excerpt}</p>
                    <div className="flex items-center gap-4 text-sm text-charcoal/40">
                      <span>{featured.author}</span>
                      <span>·</span>
                      <span>
                        {featured.published_at
                          ? new Date(featured.published_at).toLocaleDateString('en-US', {
                              year: 'numeric',
                              month: 'long',
                              day: 'numeric',
                            })
                          : ''}
                      </span>
                    </div>
                  </div>
                </Link>
              )}

              {/* Grid */}
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                {(featured && activeCategory === 'all' && !search ? rest : filtered).map((post) => (
                  <Link
                    key={post.id}
                    to={`/blog/${post.slug}`}
                    className="group bg-white border border-charcoal/5 overflow-hidden transition-all duration-500 hover:shadow-xl hover:shadow-crimson/5 hover:-translate-y-1"
                  >
                    <div className="relative aspect-[16/10] overflow-hidden bg-soft-red">
                      <img
                        src={post.featured_image || 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=600'}
                        alt={post.title}
                        className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-charcoal/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
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
                      <div className="flex items-center justify-between text-xs text-charcoal/40">
                        <span>{post.author}</span>
                        <span>
                          {post.published_at
                            ? new Date(post.published_at).toLocaleDateString('en-US', {
                                month: 'short',
                                day: 'numeric',
                                year: 'numeric',
                              })
                            : ''}
                        </span>
                      </div>
                    </div>
                  </Link>
                ))}
              </div>
            </>
          )}
        </div>
      </section>
    </>
  );
}
