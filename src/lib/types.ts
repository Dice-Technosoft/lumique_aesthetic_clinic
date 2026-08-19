export interface Category {
  id: string;
  name: string;
  slug: string;
  description: string | null;
  icon: string | null;
  display_order: number;
}

export interface Treatment {
  id: string;
  category_id: string;
  title: string;
  slug: string;
  short_intro: string | null;
  hero_image: string | null;
  description: string | null;
  who_is_it_for: string | null;
  benefits: string | null;
  procedure_overview: string | null;
  treatment_process: string | null;
  expected_results: string | null;
  recovery_info: string | null;
  num_sessions: string | null;
  doctor_recommendation: string | null;
  is_featured: boolean;
  display_order: number;
  category?: Category;
}

export interface TreatmentSection {
  id: string;
  treatment_id: string;
  title: string;
  body: string;
  display_order: number;
}

export interface TreatmentFAQ {
  id: string;
  treatment_id: string;
  question: string;
  answer: string;
  display_order: number;
}

export interface BeforeAfterPhoto {
  id: string;
  treatment_id: string;
  before_image: string | null;
  after_image: string | null;
  caption: string | null;
  display_order: number;
}

export interface TreatmentVideo {
  id: string;
  treatment_id: string;
  title: string;
  video_url: string;
  thumbnail: string | null;
  display_order: number;
}

export interface DoctorProfile {
  id: string;
  name: string | null;
  title: string | null;
  photo: string | null;
  introduction: string | null;
  professional_profile: string | null;
  qualifications: string | null;
  experience: string | null;
  specializations: string | null;
  areas_of_expertise: string | null;
  treatment_philosophy: string | null;
  clinic_approach: string | null;
  certifications: string | null;
  achievements: string | null;
}

export interface BlogCategory {
  id: string;
  name: string;
  slug: string;
  display_order: number;
}

export interface BlogPost {
  id: string;
  title: string;
  slug: string;
  featured_image: string | null;
  excerpt: string | null;
  content: string | null;
  author: string | null;
  blog_category_id: string | null;
  tags: string | null;
  seo_title: string | null;
  seo_description: string | null;
  meta_keywords: string | null;
  published_at: string | null;
  status: string;
  created_at: string;
  blog_category?: BlogCategory;
}

export interface Appointment {
  id: string;
  name: string;
  phone: string;
  email: string | null;
  treatment_id: string | null;
  treatment_category: string | null;
  preferred_date: string | null;
  preferred_time: string | null;
  message: string | null;
  status: string;
  admin_notes: string | null;
  created_at: string;
  treatment?: Treatment;
}

export interface ClinicSettings {
  id: string;
  clinic_name: string;
  address: string | null;
  phone: string | null;
  whatsapp: string | null;
  email: string | null;
  working_hours: string | null;
  map_embed: string | null;
  logo_url: string | null;
}

export const APPOINTMENT_STATUSES = [
  'new',
  'contacted',
  'confirmed',
  'completed',
  'cancelled',
  'no-show',
] as const;
