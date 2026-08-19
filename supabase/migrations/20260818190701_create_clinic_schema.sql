/*
# Unique Aesthetic Clinic - Core Schema

## Overview
Creates the complete database schema for a luxury dermatology & aesthetic clinic website with
an admin-managed content system. Single-tenant (no auth required for public site); admin
sections will be protected separately.

## Tables Created
1. `categories` - Treatment categories (Skin, Hair, Laser, Tattoo Removal, Aesthetic)
2. `treatments` - Individual treatments belonging to a category
3. `treatment_sections` - Rich content sections for each treatment (benefits, process, etc.)
4. `treatment_faqs` - FAQs per treatment
5. `before_after_photos` - Before/after gallery per treatment
6. `treatment_videos` - Related videos per treatment
7. `doctor_profile` - Single-row doctor profile (managed via admin)
8. `blog_posts` - Blog articles with SEO fields and draft/published status
9. `blog_categories` - Blog categories
10. `appointments` - Appointment requests from the contact form
11. `clinic_settings` - Site-wide settings (contact info, hours, etc.)

## Security
- RLS enabled on all tables.
- Public read access (anon + authenticated) for published content.
- Public insert for appointments (patients submit form without login).
- Full CRUD via anon/authenticated since this is a single-tenant managed site.
*/

-- ==================== CATEGORIES ====================
CREATE TABLE IF NOT EXISTS categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  slug text UNIQUE NOT NULL,
  description text,
  icon text,
  display_order int NOT NULL DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);
ALTER TABLE categories ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_categories" ON categories;
CREATE POLICY "public_read_categories" ON categories FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_categories" ON categories;
CREATE POLICY "public_insert_categories" ON categories FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_categories" ON categories;
CREATE POLICY "public_update_categories" ON categories FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_categories" ON categories;
CREATE POLICY "public_delete_categories" ON categories FOR DELETE TO anon, authenticated USING (true);

-- ==================== TREATMENTS ====================
CREATE TABLE IF NOT EXISTS treatments (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  category_id uuid REFERENCES categories(id) ON DELETE CASCADE,
  title text NOT NULL,
  slug text UNIQUE NOT NULL,
  short_intro text,
  hero_image text,
  description text,
  who_is_it_for text,
  benefits text,
  procedure_overview text,
  treatment_process text,
  expected_results text,
  recovery_info text,
  num_sessions text,
  doctor_recommendation text,
  is_featured boolean DEFAULT false,
  display_order int NOT NULL DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);
ALTER TABLE treatments ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_treatments" ON treatments;
CREATE POLICY "public_read_treatments" ON treatments FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_treatments" ON treatments;
CREATE POLICY "public_insert_treatments" ON treatments FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_treatments" ON treatments;
CREATE POLICY "public_update_treatments" ON treatments FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_treatments" ON treatments;
CREATE POLICY "public_delete_treatments" ON treatments FOR DELETE TO anon, authenticated USING (true);

-- ==================== TREATMENT SECTIONS ====================
CREATE TABLE IF NOT EXISTS treatment_sections (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  treatment_id uuid REFERENCES treatments(id) ON DELETE CASCADE,
  title text NOT NULL,
  body text NOT NULL,
  display_order int NOT NULL DEFAULT 0
);
ALTER TABLE treatment_sections ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_treatment_sections" ON treatment_sections;
CREATE POLICY "public_read_treatment_sections" ON treatment_sections FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_treatment_sections" ON treatment_sections;
CREATE POLICY "public_insert_treatment_sections" ON treatment_sections FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_treatment_sections" ON treatment_sections;
CREATE POLICY "public_update_treatment_sections" ON treatment_sections FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_treatment_sections" ON treatment_sections;
CREATE POLICY "public_delete_treatment_sections" ON treatment_sections FOR DELETE TO anon, authenticated USING (true);

-- ==================== TREATMENT FAQs ====================
CREATE TABLE IF NOT EXISTS treatment_faqs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  treatment_id uuid REFERENCES treatments(id) ON DELETE CASCADE,
  question text NOT NULL,
  answer text NOT NULL,
  display_order int NOT NULL DEFAULT 0
);
ALTER TABLE treatment_faqs ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_treatment_faqs" ON treatment_faqs;
CREATE POLICY "public_read_treatment_faqs" ON treatment_faqs FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_treatment_faqs" ON treatment_faqs;
CREATE POLICY "public_insert_treatment_faqs" ON treatment_faqs FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_treatment_faqs" ON treatment_faqs;
CREATE POLICY "public_update_treatment_faqs" ON treatment_faqs FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_treatment_faqs" ON treatment_faqs;
CREATE POLICY "public_delete_treatment_faqs" ON treatment_faqs FOR DELETE TO anon, authenticated USING (true);

-- ==================== BEFORE / AFTER PHOTOS ====================
CREATE TABLE IF NOT EXISTS before_after_photos (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  treatment_id uuid REFERENCES treatments(id) ON DELETE CASCADE,
  before_image text,
  after_image text,
  caption text,
  display_order int NOT NULL DEFAULT 0
);
ALTER TABLE before_after_photos ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_before_after" ON before_after_photos;
CREATE POLICY "public_read_before_after" ON before_after_photos FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_before_after" ON before_after_photos;
CREATE POLICY "public_insert_before_after" ON before_after_photos FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_before_after" ON before_after_photos;
CREATE POLICY "public_update_before_after" ON before_after_photos FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_before_after" ON before_after_photos;
CREATE POLICY "public_delete_before_after" ON before_after_photos FOR DELETE TO anon, authenticated USING (true);

-- ==================== TREATMENT VIDEOS ====================
CREATE TABLE IF NOT EXISTS treatment_videos (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  treatment_id uuid REFERENCES treatments(id) ON DELETE CASCADE,
  title text NOT NULL,
  video_url text NOT NULL,
  thumbnail text,
  display_order int NOT NULL DEFAULT 0
);
ALTER TABLE treatment_videos ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_treatment_videos" ON treatment_videos;
CREATE POLICY "public_read_treatment_videos" ON treatment_videos FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_treatment_videos" ON treatment_videos;
CREATE POLICY "public_insert_treatment_videos" ON treatment_videos FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_treatment_videos" ON treatment_videos;
CREATE POLICY "public_update_treatment_videos" ON treatment_videos FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_treatment_videos" ON treatment_videos;
CREATE POLICY "public_delete_treatment_videos" ON treatment_videos FOR DELETE TO anon, authenticated USING (true);

-- ==================== DOCTOR PROFILE ====================
CREATE TABLE IF NOT EXISTS doctor_profile (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text,
  title text,
  photo text,
  introduction text,
  professional_profile text,
  qualifications text,
  experience text,
  specializations text,
  areas_of_expertise text,
  treatment_philosophy text,
  clinic_approach text,
  certifications text,
  achievements text,
  updated_at timestamptz DEFAULT now()
);
ALTER TABLE doctor_profile ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_doctor_profile" ON doctor_profile;
CREATE POLICY "public_read_doctor_profile" ON doctor_profile FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_update_doctor_profile" ON doctor_profile;
CREATE POLICY "public_update_doctor_profile" ON doctor_profile FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_insert_doctor_profile" ON doctor_profile;
CREATE POLICY "public_insert_doctor_profile" ON doctor_profile FOR INSERT TO anon, authenticated WITH CHECK (true);

-- ==================== BLOG CATEGORIES ====================
CREATE TABLE IF NOT EXISTS blog_categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  slug text UNIQUE NOT NULL,
  display_order int NOT NULL DEFAULT 0
);
ALTER TABLE blog_categories ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_blog_categories" ON blog_categories;
CREATE POLICY "public_read_blog_categories" ON blog_categories FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_blog_categories" ON blog_categories;
CREATE POLICY "public_insert_blog_categories" ON blog_categories FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_blog_categories" ON blog_categories;
CREATE POLICY "public_update_blog_categories" ON blog_categories FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_blog_categories" ON blog_categories;
CREATE POLICY "public_delete_blog_categories" ON blog_categories FOR DELETE TO anon, authenticated USING (true);

-- ==================== BLOG POSTS ====================
CREATE TABLE IF NOT EXISTS blog_posts (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title text NOT NULL,
  slug text UNIQUE NOT NULL,
  featured_image text,
  excerpt text,
  content text,
  author text,
  blog_category_id uuid REFERENCES blog_categories(id) ON DELETE SET NULL,
  tags text,
  seo_title text,
  seo_description text,
  meta_keywords text,
  published_at date,
  status text NOT NULL DEFAULT 'draft',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);
ALTER TABLE blog_posts ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_blog_posts" ON blog_posts;
CREATE POLICY "public_read_blog_posts" ON blog_posts FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_blog_posts" ON blog_posts;
CREATE POLICY "public_insert_blog_posts" ON blog_posts FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_blog_posts" ON blog_posts;
CREATE POLICY "public_update_blog_posts" ON blog_posts FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_blog_posts" ON blog_posts;
CREATE POLICY "public_delete_blog_posts" ON blog_posts FOR DELETE TO anon, authenticated USING (true);

-- ==================== APPOINTMENTS ====================
CREATE TABLE IF NOT EXISTS appointments (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  phone text NOT NULL,
  email text,
  treatment_id uuid REFERENCES treatments(id) ON DELETE SET NULL,
  treatment_category text,
  preferred_date date,
  preferred_time text,
  message text,
  status text NOT NULL DEFAULT 'new',
  admin_notes text,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);
ALTER TABLE appointments ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_appointments" ON appointments;
CREATE POLICY "public_read_appointments" ON appointments FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_insert_appointments" ON appointments;
CREATE POLICY "public_insert_appointments" ON appointments FOR INSERT TO anon, authenticated WITH CHECK (true);
DROP POLICY IF EXISTS "public_update_appointments" ON appointments;
CREATE POLICY "public_update_appointments" ON appointments FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_delete_appointments" ON appointments;
CREATE POLICY "public_delete_appointments" ON appointments FOR DELETE TO anon, authenticated USING (true);

-- ==================== CLINIC SETTINGS ====================
CREATE TABLE IF NOT EXISTS clinic_settings (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  clinic_name text DEFAULT 'Unique Aesthetic Clinic',
  address text,
  phone text,
  whatsapp text,
  email text,
  working_hours text,
  map_embed text,
  logo_url text,
  updated_at timestamptz DEFAULT now()
);
ALTER TABLE clinic_settings ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "public_read_clinic_settings" ON clinic_settings;
CREATE POLICY "public_read_clinic_settings" ON clinic_settings FOR SELECT TO anon, authenticated USING (true);
DROP POLICY IF EXISTS "public_update_clinic_settings" ON clinic_settings;
CREATE POLICY "public_update_clinic_settings" ON clinic_settings FOR UPDATE TO anon, authenticated USING (true) WITH CHECK (true);
DROP POLICY IF EXISTS "public_insert_clinic_settings" ON clinic_settings;
CREATE POLICY "public_insert_clinic_settings" ON clinic_settings FOR INSERT TO anon, authenticated WITH CHECK (true);

-- ==================== INDEXES ====================
CREATE INDEX IF NOT EXISTS idx_treatments_category ON treatments(category_id);
CREATE INDEX IF NOT EXISTS idx_treatments_slug ON treatments(slug);
CREATE INDEX IF NOT EXISTS idx_blog_posts_slug ON blog_posts(slug);
CREATE INDEX IF NOT EXISTS idx_blog_posts_status ON blog_posts(status);
CREATE INDEX IF NOT EXISTS idx_appointments_status ON appointments(status);
CREATE INDEX IF NOT EXISTS idx_appointments_date ON appointments(preferred_date);
