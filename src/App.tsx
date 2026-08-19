import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { Suspense, lazy } from 'react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import ScrollToTop from '@/components/ScrollToTop';
import LoadingScreen from '@/components/LoadingScreen';

const Home = lazy(() => import('@/pages/Home'));
const About = lazy(() => import('@/pages/About'));
const Treatments = lazy(() => import('@/pages/Treatments'));
const TreatmentDetail = lazy(() => import('@/pages/TreatmentDetail'));
const Blog = lazy(() => import('@/pages/Blog'));
const BlogPost = lazy(() => import('@/pages/BlogPost'));
const Contact = lazy(() => import('@/pages/Contact'));
const Admin = lazy(() => import('@/pages/Admin'));

function App() {
  return (
    <BrowserRouter>
      <ScrollToTop />
      <div className="flex min-h-screen flex-col bg-ivory">
        <Routes>
          <Route
            path="/admin/*"
            element={
              <Suspense fallback={<LoadingScreen />}>
                <Admin />
              </Suspense>
            }
          />
          <Route
            path="/*"
            element={
              <>
                <Header />
                <main className="flex-1">
                  <Suspense fallback={<LoadingScreen />}>
                    <Routes>
                      <Route path="/" element={<Home />} />
                      <Route path="/about" element={<About />} />
                      <Route path="/treatments" element={<Treatments />} />
                      <Route path="/treatments/:slug" element={<TreatmentDetail />} />
                      <Route path="/blog" element={<Blog />} />
                      <Route path="/blog/:slug" element={<BlogPost />} />
                      <Route path="/contact" element={<Contact />} />
                      <Route path="*" element={<NotFound />} />
                    </Routes>
                  </Suspense>
                </main>
                <Footer />
              </>
            }
          />
        </Routes>
      </div>
    </BrowserRouter>
  );
}

function NotFound() {
  return (
    <div className="flex min-h-[60vh] flex-col items-center justify-center gap-6 px-5 text-center">
      <p className="text-6xl font-bold text-crimson">404</p>
      <h1 className="heading-3">Page Not Found</h1>
      <p className="body-text max-w-md">
        The page you are looking for does not exist or has been moved.
      </p>
      <a href="/" className="btn-primary">Return Home</a>
    </div>
  );
}

export default App;
