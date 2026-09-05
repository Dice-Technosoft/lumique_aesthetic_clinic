<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadFollowUp;
use App\Models\LeadNote;
use App\Models\LeadSource;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SectionType;
use App\Models\SeoMeta;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClinicDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full administrative access to all modules and configurations',
        ]);

        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrative access to content, CRM, and media management',
        ]);

        $crmManagerRole = Role::create([
            'name' => 'CRM Manager',
            'slug' => 'crm-manager',
            'description' => 'Access to manage inquiries, leads, follow-ups, and customer communications',
        ]);

        $permissions = [
            'pages.view', 'pages.create', 'pages.edit', 'pages.delete',
            'services.view', 'services.create', 'services.edit', 'services.delete',
            'videos.view', 'videos.create', 'videos.edit', 'videos.delete',
            'gallery.view', 'gallery.create', 'gallery.edit', 'gallery.delete',
            'testimonials.view', 'testimonials.create', 'testimonials.edit', 'testimonials.delete',
            'faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete',
            'blog.view', 'blog.create', 'blog.edit', 'blog.delete',
            'inquiries.view', 'inquiries.edit', 'inquiries.delete',
            'leads.view', 'leads.create', 'leads.edit', 'leads.delete',
            'media.view', 'media.upload', 'media.delete',
            'settings.view', 'settings.edit',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'reports.view',
        ];

        foreach ($permissions as $perm) {
            $createdPerm = Permission::create([
                'name' => ucwords(str_replace('.', ' ', $perm)),
                'slug' => $perm,
                'group' => explode('.', $perm)[0],
            ]);
            $superAdminRole->permissions()->attach($createdPerm->id);
            $adminRole->permissions()->attach($createdPerm->id);
            if (in_array($perm, ['inquiries.view', 'inquiries.edit', 'leads.view', 'leads.create', 'leads.edit', 'reports.view'])) {
                $crmManagerRole->permissions()->attach($createdPerm->id);
            }
        }

        // 2. Admin User
        $adminUser = User::create([
            'name' => 'Dr. Alisha Vance',
            'email' => 'admin@lumiqueclinic.com',
            'phone' => '+91 88795 50581',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $adminUser->roles()->attach($superAdminRole->id);

        // 3. Site Settings
        $settings = [
            // General & Branding
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Lumique Aesthetic Clinic', 'type' => 'string'],
            ['group' => 'general', 'key' => 'tagline', 'value' => 'Advanced Dermatology & Aesthetic Care Designed Around You', 'type' => 'string'],
            ['group' => 'branding', 'key' => 'logo_url', 'value' => '/images/logo.jpeg', 'type' => 'image'],
            ['group' => 'branding', 'key' => 'favicon_url', 'value' => '/images/favicon.png', 'type' => 'image'],
            ['group' => 'theme', 'key' => 'primary_color', 'value' => '#7A1C2E', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'secondary_color', 'value' => '#D4AF37', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'accent_color', 'value' => '#B8324F', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'bg_dark', 'value' => '#14080B', 'type' => 'string'],
            
            // Contact Details
            ['group' => 'contact', 'key' => 'phone', 'value' => '+91 88795 50581', 'type' => 'string'],
            ['group' => 'contact', 'key' => 'phone_display', 'value' => '+91 88795 50581', 'type' => 'string'],
            ['group' => 'contact', 'key' => 'whatsapp', 'value' => '+918879550581', 'type' => 'string'],
            ['group' => 'contact', 'key' => 'email', 'value' => 'info@lumiqueclinic.com', 'type' => 'string'],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Ground Floor, Kenilworth Mall, Linking Road, Bandra West, Mumbai, Maharashtra 400050, India', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'working_hours', 'value' => "Monday – Saturday: 9:00 AM – 7:00 PM\nSunday: Closed", 'type' => 'text'],
            ['group' => 'contact', 'key' => 'map_embed', 'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3771.4411132644265!2d72.83354927596535!3d19.04432175296839!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c9197779a513%3A0x6b1070e28f3cb295!2sLinking%20Rd%2C%20Bandra%20West%2C%20Mumbai%2C%20Maharashtra%20400050!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin', 'type' => 'text'],
            
            // Social Media
            ['group' => 'social', 'key' => 'instagram_url', 'value' => 'https://instagram.com/lumiqueclinic', 'type' => 'string'],
            ['group' => 'social', 'key' => 'facebook_url', 'value' => 'https://facebook.com/lumiqueclinic', 'type' => 'string'],
            ['group' => 'social', 'key' => 'youtube_url', 'value' => 'https://youtube.com/@lumiqueclinic', 'type' => 'string'],
            ['group' => 'social', 'key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/lumiqueclinic', 'type' => 'string'],

            // Email Configuration
            ['group' => 'email', 'key' => 'admin_notification_email', 'value' => 'info@lumiqueclinic.com', 'type' => 'string'],
            ['group' => 'email', 'key' => 'from_name', 'value' => 'Lumique Aesthetic Clinic', 'type' => 'string'],
            ['group' => 'email', 'key' => 'from_email', 'value' => 'notifications@lumiqueclinic.com', 'type' => 'string'],

            // SEO Defaults
            ['group' => 'seo', 'key' => 'default_meta_title', 'value' => 'Lumique Aesthetic Clinic | Premier Dermatology & Aesthetic Center Mumbai', 'type' => 'string'],
            ['group' => 'seo', 'key' => 'default_meta_description', 'value' => 'Experience bespoke luxury dermatology, laser hair reduction, skin rejuvenation, and hair restoration at Lumique Aesthetic Clinic, Bandra West, Mumbai.', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'default_meta_keywords', 'value' => 'dermatologist mumbai, aesthetic clinic bandra, hydrafacial mumbai, laser hair removal bandra, prp hair treatment mumbai, dermal fillers', 'type' => 'string'],

            // About Page Customizations
            ['group' => 'about_page', 'key' => 'about_hero_title', 'value' => 'A Clinic Built on Trust, Medical Science & Compassionate Artistry', 'type' => 'string'],
            ['group' => 'about_page', 'key' => 'about_hero_description', 'value' => 'Lumique Aesthetic Clinic was founded with a singular vision: to bring together board-certified dermatological science with refined aesthetic artistry in an uplifting, patient-first sanctuary in Bandra West, Mumbai.', 'type' => 'text'],
            ['group' => 'about_page', 'key' => 'about_story_title', 'value' => 'Aesthetic Medicine Refined', 'type' => 'string'],
            ['group' => 'about_page', 'key' => 'about_story_subtitle', 'value' => 'Our Story', 'type' => 'string'],
            ['group' => 'about_page', 'key' => 'about_story_p1', 'value' => 'We believe that true aesthetic confidence does not stem from dramatic alterations, but rather from celebrating and rejuvenating your authentic self.', 'type' => 'text'],
            ['group' => 'about_page', 'key' => 'about_story_p2', 'value' => 'From multi-wavelength laser technologies to autologous hair therapy and gentle facial contouring, every protocol is backed by strict medical guidelines, cutting-edge equipment, and continuous patient dialogue.', 'type' => 'text'],
            ['group' => 'about_page', 'key' => 'about_image_1', 'value' => 'https://images.pexels.com/photos/11024139/pexels-photo-11024139.jpeg?auto=compress&cs=tinysrgb&w=800', 'type' => 'image'],
            ['group' => 'about_page', 'key' => 'about_image_2', 'value' => 'https://images.pexels.com/photos/7108264/pexels-photo-7108264.jpeg?auto=compress&cs=tinysrgb&w=400', 'type' => 'image'],
            ['group' => 'about_page', 'key' => 'about_mission_title', 'value' => 'Our Mission', 'type' => 'string'],
            ['group' => 'about_page', 'key' => 'about_mission_desc', 'value' => 'Deliver safe, natural, and personalized dermatological outcomes.', 'type' => 'text'],
            ['group' => 'about_page', 'key' => 'about_vision_title', 'value' => 'Our Vision', 'type' => 'string'],
            ['group' => 'about_page', 'key' => 'about_vision_desc', 'value' => 'Be the most trusted, evidence-based aesthetic clinic in Mumbai.', 'type' => 'text'],
        ];

        foreach ($settings as $s) {
            SiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 4. Lead Sources
        $leadSources = [
            ['name' => 'Website Contact Form', 'slug' => 'website-contact-form', 'description' => 'Inquiry submitted from the Contact page form'],
            ['name' => 'Appointment Booking Modal', 'slug' => 'appointment-booking-modal', 'description' => 'Direct consultation request from website booking modal'],
            ['name' => 'WhatsApp Concierge', 'slug' => 'whatsapp-concierge', 'description' => 'Direct incoming inquiry via clinic WhatsApp concierge link'],
            ['name' => 'Google Search / Organic', 'slug' => 'google-search', 'description' => 'Patient discovered clinic via organic search'],
            ['name' => 'Instagram & Social Media', 'slug' => 'instagram-social', 'description' => 'Inquiry from social media campaign or profile link'],
            ['name' => 'Patient Referral', 'slug' => 'patient-referral', 'description' => 'Referred by existing clinic patient'],
        ];

        foreach ($leadSources as $src) {
            LeadSource::create($src);
        }

        // 5. Lead Doctor & Team Members
        $doctor = TeamMember::create([
            'name' => 'Dr. Alisha Vance, MD, DVD',
            'slug' => 'dr-alisha-vance',
            'designation' => 'Lead Dermatologist & Medical Director',
            'qualification' => 'MBBS, MD (Dermatology, Venereology & Leprosy), Fellowship in Aesthetic Medicine (FACD)',
            'department' => 'Clinical Dermatology & Laser Aesthetics',
            'short_bio' => 'Dr. Vance brings over a decade of specialized experience in clinical dermatology, laser aesthetics, and non-surgical facial rejuvenation in Mumbai.',
            'full_bio' => 'Board-certified Dermatologist with fellowship training in Advanced Aesthetic Medicine and Cutaneous Laser Surgery from premier international medical institutions. Over 12+ years of clinical excellence and 15,000+ successful aesthetic transformations in Bandra West, Mumbai.',
            'photo' => 'https://images.pexels.com/photos/32160039/pexels-photo-32160039.jpeg?auto=compress&cs=tinysrgb&w=800',
            'experience_years' => 12,
            'social_links' => [
                'instagram' => 'https://instagram.com/dr.alishavance',
                'linkedin' => 'https://linkedin.com/in/dralishavance',
            ],
            'status' => true,
            'is_lead' => true,
            'sort_order' => 1,
        ]);

        TeamMember::create([
            'name' => 'Dr. Siddharth Kapoor, MD',
            'slug' => 'dr-siddharth-kapoor',
            'designation' => 'Senior Hair Restoration & Scalp Specialist',
            'qualification' => 'MBBS, MD (Dermatology), Trichology Fellow',
            'department' => 'Trichology & Hair Restoration',
            'short_bio' => 'Pioneer in advanced GFC, autologous PRP therapies, and precision follicular restoration with over 9 years of dedicated experience.',
            'full_bio' => 'Dr. Kapoor specializes in non-surgical and minimally invasive scalp treatments, having performed over 4,000 platelet-rich plasma and growth factor protocols.',
            'photo' => 'https://images.pexels.com/photos/5327585/pexels-photo-5327585.jpeg?auto=compress&cs=tinysrgb&w=800',
            'experience_years' => 9,
            'social_links' => ['instagram' => 'https://instagram.com/drsiddharthkapoor'],
            'status' => true,
            'is_lead' => false,
            'sort_order' => 2,
        ]);

        // 6. Clinical Service Categories
        $categoriesData = [
            [
                'name' => 'Skin Rejuvenation',
                'slug' => 'skin',
                'icon' => 'sparkles',
                'description' => 'Medical facials, chemical peels, and advanced skin barrier restoration treatments.',
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Laser Treatments',
                'slug' => 'laser',
                'icon' => 'zap',
                'description' => 'US-FDA approved laser skin resurfacing, carbon peels, and painless hair reduction.',
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Hair Restoration',
                'slug' => 'hair',
                'icon' => 'scissors',
                'description' => 'Autologous PRP, GFC, and advanced follicular growth factor therapies.',
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Tattoo Removal',
                'slug' => 'tattoo-removal',
                'icon' => 'shield',
                'description' => 'Ultra-short picosecond laser technology for safe, multi-color ink clearance.',
                'status' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Aesthetic Enhancements',
                'slug' => 'aesthetic-treatments',
                'icon' => 'heart',
                'description' => 'Subtle facial contouring, premium dermal fillers, and anti-wrinkle therapies.',
                'status' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Body Contouring',
                'slug' => 'body',
                'icon' => 'activity',
                'description' => 'Non-invasive body shaping, skin tightening, and localized cellulite reduction.',
                'status' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categoriesData as $cat) {
            ServiceCategory::create($cat);
        }

        // 7. Services & Clinical Treatments Catalog
        $servicesData = [
            [
                'title' => 'Medical HydraFacial MD®',
                'slug' => 'hydrafacial-md',
                'category' => 'skin',
                'icon' => 'sparkles',
                'featured_image' => 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'short_description' => 'Deep vortex cleansing, painless extractions, and intense antioxidant & peptide infusion for instant radiant skin.',
                'description' => 'The patented HydraFacial MD® protocol combines medical-grade exfoliation with automated vacuum extraction and deep dermal hydration. Suitable for all skin types with zero downtime.',
                'duration' => '45 Minutes',
                'price_starting_at' => '₹4,999',
                'benefits' => [
                    'Instant luminous red-carpet radiance',
                    'Painless extraction of blackheads and sebum plugs',
                    'Zero downtime or post-procedure redness',
                    'Deeply plumps and hydrates the dermal matrix'
                ],
                'procedure_steps' => [
                    'Vortex Cleansing & Exfoliation',
                    'Gentle Glycolic Acid Peel',
                    'Automated Vacuum Extraction',
                    'Antioxidant & Hyaluronic Infusion',
                    'Collagen Red-Light LED Phototherapy'
                ],
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Hollywood Carbon Laser Peel',
                'slug' => 'carbon-laser-peel',
                'category' => 'laser',
                'icon' => 'zap',
                'featured_image' => 'https://images.pexels.com/photos/4586726/pexels-photo-4586726.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'short_description' => 'Q-Switched Nd:YAG laser treatment that vaporizes liquid carbon to deeply purify pores, diminish acne, and boost collagen.',
                'description' => 'Often referred to as the China Doll or Porcelain Laser Facial, this treatment exfoliates the skin four times deeper than chemical scrubs while stimulating long-term dermal collagen fibers.',
                'duration' => '50 Minutes',
                'price_starting_at' => '₹6,499',
                'benefits' => [
                    'Significantly reduces active acne and oily sheen',
                    'Visibly tightens enlarged open pores',
                    'Evens out blotchy pigmentation and acne marks',
                    'Leaves skin silky smooth with a porcelain glow'
                ],
                'procedure_steps' => [
                    'Deep antiseptic sanitization',
                    'Liquid carbon lotion application',
                    'Photoacoustic Q-Switched laser pass',
                    'Cooling peptide sheet mask',
                    'Broad-spectrum barrier sunscreen'
                ],
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Advanced PRP / GFC Hair Restoration',
                'slug' => 'prp-gfc-hair-restoration',
                'category' => 'hair',
                'icon' => 'scissors',
                'featured_image' => 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'short_description' => 'Autologous concentrated growth factor therapy that reactivates dormant follicles and reverses thinning hair.',
                'description' => 'Using proprietary centrifugation protocols, we isolate highly concentrated autologous platelets and active growth factors to strengthen follicle roots, arrest shedding, and induce thick, natural hair density.',
                'duration' => '60 Minutes',
                'price_starting_at' => '₹7,999',
                'benefits' => [
                    'Stops excessive hair shedding within 3 to 4 weeks',
                    'Stimulates new sprout density and follicle thickness',
                    '100% natural, bio-compatible autologous procedure',
                    'Virtually painless with topical numbing'
                ],
                'procedure_steps' => [
                    'Blood draw and specialized centrifugation',
                    'High-strength topical scalp anesthetic',
                    'Micro-injection into thinning zones',
                    'LLLT Photobiomodulation laser helmet'
                ],
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Picosecond Laser Tattoo Removal',
                'slug' => 'picosecond-tattoo-removal',
                'category' => 'tattoo-removal',
                'icon' => 'eraser',
                'featured_image' => 'https://images.pexels.com/photos/7446683/pexels-photo-7446683.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'short_description' => 'Ultra-short picosecond pulses shatter ink into micro-particles for fast clearance with zero scarring.',
                'description' => 'Our US-FDA approved Picosecond laser utilizes photoacoustic shockwaves to disintegrate stubborn multi-colored tattoo ink into microscopic dust that the lymphatic system clears safely.',
                'duration' => '30–45 Minutes',
                'price_starting_at' => '₹3,499',
                'benefits' => [
                    'Clears stubborn black, blue, green, and red inks',
                    'Requires 50% fewer sessions than traditional lasers',
                    'Protects surrounding skin tissue from thermal damage',
                    'Continuous cryo-cooling ensures patient comfort'
                ],
                'procedure_steps' => [
                    'Clinical high-res photography and ink evaluation',
                    'Topical numbing cream application',
                    'Picosecond laser delivery with cryogenic air cooling',
                    'Post-procedure soothing barrier dressing'
                ],
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Subtle Facial Contouring & Dermal Fillers',
                'slug' => 'facial-contouring-fillers',
                'category' => 'aesthetic-treatments',
                'icon' => 'flower-2',
                'featured_image' => 'https://images.pexels.com/photos/14438367/pexels-photo-14438367.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'short_description' => 'Premium hyaluronic acid injectables for anatomical cheek sculpting, tear trough rejuvenation, and lip harmony.',
                'description' => 'Administered exclusively by board-certified dermatologists using gentle micro-cannula techniques. We adhere to conservative micro-dosing for effortlessly graceful, natural-looking volume.',
                'duration' => '45 Minutes',
                'price_starting_at' => '₹18,000',
                'benefits' => [
                    'Instant structural refinement and contouring',
                    'Reversible, 100% biocompatible hyaluronic acid',
                    'Smooth, seamless results lasting 12 to 18 months',
                    'Minimal bruising with micro-cannula technique'
                ],
                'procedure_steps' => [
                    '3D Facial anatomy and proportions consultation',
                    'Topical local anesthetic',
                    'Precise micro-cannula filler placement',
                    'Gentle sculpting and symmetry check'
                ],
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Triple-Wavelength Painless Laser Hair Reduction',
                'slug' => 'laser-hair-reduction',
                'category' => 'laser',
                'icon' => 'zap',
                'featured_image' => 'https://images.pexels.com/photos/7446659/pexels-photo-7446659.jpeg?auto=compress&cs=tinysrgb&w=1200',
                'short_description' => 'Alexandrite, Diode, and Nd:YAG triple wavelength laser with -5°C sapphire cooling for silky smooth skin.',
                'description' => 'Clinically proven permanent reduction of unwanted hair across all Indian skin tones. Contact sapphire ice-cooling eliminates heat sensations while destroying hair follicle roots.',
                'duration' => '30–60 Minutes',
                'price_starting_at' => '₹2,999',
                'benefits' => [
                    'Safe and effective across Fitzpatrick skin types I–VI',
                    'Continuous ice cooling ensures pain-free comfort',
                    'Eliminates ingrown hairs, folliculitis, and razor burns',
                    'Up to 90% permanent hair reduction after a full course'
                ],
                'procedure_steps' => [
                    'Skin and hair texture assessment',
                    'Chilled conductive ultrasound gel application',
                    'In-motion rapid multi-pass laser glide',
                    'Soothing aloe vera hydrating balm'
                ],
                'is_featured' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($servicesData as $svc) {
            $service = Service::create($svc);
            SeoMeta::create([
                'model_type' => Service::class,
                'model_id' => $service->id,
                'meta_title' => $service->title . ' in Bandra Mumbai | Lumique Aesthetic Clinic',
                'meta_description' => $service->short_description,
                'canonical_url' => url('/services/' . $service->slug),
                'og_title' => $service->title,
                'og_description' => $service->short_description,
                'og_image' => $service->featured_image,
            ]);
        }

        // 7. YouTube Videos Module
        $videos = [
            [
                'title' => 'HydraFacial MD Complete Clinical Walkthrough',
                'slug' => 'hydrafacial-md-walkthrough',
                'youtube_url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
                'youtube_video_id' => 'M7lc1UVf-VE',
                'thumbnail' => 'https://img.youtube.com/vi/M7lc1UVf-VE/hqdefault.jpg',
                'description' => 'Dr. Alisha Vance demonstrates the 4-step patented vortex cleansing and antioxidant infusion process.',
                'category' => 'skin',
                'is_featured' => true,
                'sort_order' => 1,
                'published_at' => now(),
            ],
            [
                'title' => 'Hollywood Carbon Laser Peel Live Demo',
                'slug' => 'carbon-laser-peel-demo',
                'youtube_url' => 'https://www.youtube.com/watch?v=kXYiU_JCYtU',
                'youtube_video_id' => 'kXYiU_JCYtU',
                'thumbnail' => 'https://img.youtube.com/vi/kXYiU_JCYtU/hqdefault.jpg',
                'description' => 'Experience the photoacoustic pore-tightening power of Q-switched laser technology on active acne.',
                'category' => 'laser',
                'is_featured' => true,
                'sort_order' => 2,
                'published_at' => now(),
            ],
            [
                'title' => 'How GFC & PRP Reverse Hair Loss',
                'slug' => 'gfc-prp-hair-loss-explained',
                'youtube_url' => 'https://www.youtube.com/watch?v=21X5lGlDOfg',
                'youtube_video_id' => '21X5lGlDOfg',
                'thumbnail' => 'https://img.youtube.com/vi/21X5lGlDOfg/hqdefault.jpg',
                'description' => 'Dr. Siddharth Kapoor explains the science of concentrated growth factors and follicular stimulation.',
                'category' => 'hair',
                'is_featured' => true,
                'sort_order' => 3,
                'published_at' => now(),
            ],
            [
                'title' => 'Picosecond Laser Ink Shattering Demonstration',
                'slug' => 'pico-laser-tattoo-demo',
                'youtube_url' => 'https://www.youtube.com/watch?v=rT9qC-dZ5-A',
                'youtube_video_id' => 'rT9qC-dZ5-A',
                'thumbnail' => 'https://img.youtube.com/vi/rT9qC-dZ5-A/hqdefault.jpg',
                'description' => 'Watch the instantaneous photoacoustic dispersion of dense dark ink without blistering or burning.',
                'category' => 'tattoo-removal',
                'is_featured' => true,
                'sort_order' => 4,
                'published_at' => now(),
            ],
        ];

        foreach ($videos as $v) {
            Video::create($v);
        }

        // 8. Gallery & Before/After
        $gallery = Gallery::create([
            'title' => 'Clinical Case Results & Before/After Transformations',
            'slug' => 'clinical-results',
            'category' => 'all',
            'description' => 'Real, unretouched transformations achieved at Lumique Aesthetic Clinic, Mumbai.',
            'status' => true,
            'sort_order' => 1,
        ]);

        $galleryItems = [
            [
                'gallery_id' => $gallery->id,
                'title' => 'Acne Scar Revision & Skin Smoothing',
                'image_before' => '/storage/gallery/acne_scar_before.jpg',
                'image_after' => '/storage/gallery/acne_scar_after.jpg',
                'category' => 'skin',
                'treatment_name' => 'HydraFacial MD + Carbon Laser',
                'sort_order' => 1,
            ],
            [
                'gallery_id' => $gallery->id,
                'title' => 'Crown Density Regrowth (After 4 GFC Sessions)',
                'image_before' => '/storage/gallery/hair_growth_before.jpg',
                'image_after' => '/storage/gallery/hair_growth_after.jpg',
                'category' => 'hair',
                'treatment_name' => 'Advanced GFC Hair Therapy',
                'sort_order' => 2,
            ],
            [
                'gallery_id' => $gallery->id,
                'title' => 'Dark Tribal Tattoo Clearance (After 4 Sessions)',
                'image_before' => '/storage/gallery/tattoo_clearance_before.jpg',
                'image_after' => '/storage/gallery/tattoo_clearance_after.jpg',
                'category' => 'tattoo-removal',
                'treatment_name' => 'Picosecond Laser Removal',
                'sort_order' => 3,
            ],
            [
                'gallery_id' => $gallery->id,
                'title' => 'Subtle Tear Trough & Cheek Contour Refinement',
                'image_before' => '/storage/gallery/teartrough_before.jpg',
                'image_after' => '/storage/gallery/teartrough_after.jpg',
                'category' => 'aesthetic-treatments',
                'treatment_name' => 'Dermal Filler Sculpting',
                'sort_order' => 4,
            ],
        ];

        foreach ($galleryItems as $item) {
            GalleryItem::create($item);
        }

        // 9. Testimonials
        $testimonials = [
            [
                'name' => 'Priya Nair',
                'designation' => 'Fashion Stylist, Bandra',
                'treatment_taken' => 'HydraFacial MD & Carbon Laser',
                'photo' => 'https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=300',
                'rating' => 5,
                'content' => 'Dr. Alisha Vance and her team are exceptional. The HydraFacial and Carbon Laser treatments completely transformed my skin texture before my bridal shoots. The clinic ambiance in Bandra feels like a tranquil luxury retreat.',
                'source' => 'Google Review (Verified Patient)',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Rohan Merchant',
                'designation' => 'Managing Director, Mumbai',
                'treatment_taken' => 'PRP & GFC Hair Restoration',
                'photo' => 'https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&w=300',
                'rating' => 5,
                'content' => 'I was struggling with acute hairline thinning for two years. Within 3 sessions of GFC with Dr. Kapoor, my hair shedding completely ceased and visible density returned. Highly recommended for genuine medical expertise without false promises.',
                'source' => 'Google Review (Verified Patient)',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Ananya Sharma',
                'designation' => 'Architect, South Mumbai',
                'treatment_taken' => 'Picosecond Tattoo Removal & Lip Tint',
                'photo' => 'https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=300',
                'rating' => 5,
                'content' => 'Pico laser tattoo removal at Lumique was practically painless compared to another clinic I visited earlier. My forearm ink faded away with zero scarring. The professionalism and hygiene standards are world-class.',
                'source' => 'Google Review (Verified Patient)',
                'is_featured' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create($t);
        }

        // 10. FAQ Categories & FAQs
        $faqCatGeneral = FaqCategory::create(['name' => 'General Clinic & Appointments', 'slug' => 'general-appointments', 'sort_order' => 1]);
        $faqCatSkin = FaqCategory::create(['name' => 'Skin & Laser Therapies', 'slug' => 'skin-laser', 'sort_order' => 2]);
        $faqCatHair = FaqCategory::create(['name' => 'Hair Restoration & GFC', 'slug' => 'hair-restoration', 'sort_order' => 3]);

        $faqs = [
            [
                'category_id' => $faqCatGeneral->id,
                'question' => 'How do I schedule an appointment at Lumique Aesthetic Clinic?',
                'answer' => 'You can conveniently book online through our universal appointment booking form, call our front desk directly at +91 88795 50581, or connect instantly with our concierge via WhatsApp. Our team will assist in scheduling a consultation at your preferred time slot.',
                'sort_order' => 1,
            ],
            [
                'category_id' => $faqCatGeneral->id,
                'question' => 'Where is the clinic located in Mumbai?',
                'answer' => 'We are located on the Ground Floor of Kenilworth Mall on Linking Road, Bandra West, Mumbai. Valet parking and accessible elevator entrances are available.',
                'sort_order' => 2,
            ],
            [
                'category_id' => $faqCatSkin->id,
                'question' => 'Is there any downtime after HydraFacial or Carbon Laser Peels?',
                'answer' => 'No downtime at all! Both HydraFacial MD and the Hollywood Carbon Laser Peel are non-invasive red-carpet procedures. You can resume your daily routine or apply light makeup immediately afterward.',
                'sort_order' => 3,
            ],
            [
                'category_id' => $faqCatHair->id,
                'question' => 'How many PRP / GFC sessions will I need to see results?',
                'answer' => 'Most patients begin noticing a substantial decrease in daily shedding after 1 to 2 sessions. For visible regrowth and density improvement, a full course of 4 to 6 sessions spaced 4 weeks apart is clinically recommended.',
                'sort_order' => 4,
            ],
        ];

        foreach ($faqs as $f) {
            Faq::create($f);
        }

        // 11. Blog Categories, Posts & Tags
        $blogCatSkin = BlogCategory::create(['name' => 'Skin Science', 'slug' => 'skin-science', 'description' => 'Clinical insights on dermatological treatments and skincare actives.']);
        $blogCatHair = BlogCategory::create(['name' => 'Hair Care', 'slug' => 'hair-care', 'description' => 'Expert guides on trichology, shedding prevention, and regrowth.']);

        $tagRetinol = BlogTag::create(['name' => 'Retinol', 'slug' => 'retinol']);
        $tagHA = BlogTag::create(['name' => 'Hyaluronic Acid', 'slug' => 'hyaluronic-acid']);
        $tagPRP = BlogTag::create(['name' => 'PRP Therapy', 'slug' => 'prp-therapy']);

        $post1 = BlogPost::create([
            'category_id' => $blogCatSkin->id,
            'author_id' => $adminUser->id,
            'title' => 'Hyaluronic Acid vs Retinol: When and How to Use Both in Your Routine',
            'slug' => 'hyaluronic-acid-vs-retinol-skincare-guide',
            'excerpt' => 'Understand how these two powerhouse ingredients work, why they complement each other, and the optimal layering sequence for Indian skin.',
            'content' => "### Introduction to Active Skincare\nIn modern dermatology, active ingredients are the foundation of effective preventive and corrective skincare. Two of the most widely researched and clinically validated molecules are Hyaluronic Acid and Retinol.\n\nWhile both promise transformative results, they address entirely different biological mechanisms in the skin.\n\n### What Is Hyaluronic Acid?\nHyaluronic acid (HA) is a naturally occurring glycosaminoglycan found throughout the body's connective tissue. A single molecule of HA can hold up to 1,000 times its molecular weight in water.\n\n- **Primary Role:** Hydration, barrier restoration, and dermal plumpness.\n- **Best For:** Dehydrated skin, dullness, fine dehydration lines, and post-procedure recovery.\n- **When to Apply:** Morning and evening on damp skin.\n\n### What Is Retinol?\nRetinol is a derivative of Vitamin A that accelerates cellular turnover and stimulates fibroblast cells to synthesize fresh collagen and elastin.\n\n- **Primary Role:** Cellular renewal, acne clearance, and smoothing deep wrinkles.\n- **Best For:** Photoaging, irregular texture, hyperpigmentation, and loss of skin elasticity.\n- **When to Apply:** Exclusively at night, followed by broad-spectrum sunscreen the next morning.\n\n### How to Safely Combine Both\n1. Cleanse thoroughly with a gentle pH-balanced cleanser.\n2. Apply Hyaluronic Acid serum onto slightly damp skin to lock in moisture.\n3. Allow the skin to dry completely (3-5 minutes).\n4. Apply a pea-sized amount of Retinol.\n5. Finish with a nourishing lipid-rich barrier cream to seal the active ingredients.",
            'featured_image' => 'https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=800',
            'status' => 'published',
            'published_at' => now()->subDays(5),
            'read_time_minutes' => 4,
            'view_count' => 142,
        ]);
        $post1->tags()->attach([$tagRetinol->id, $tagHA->id]);

        $post2 = BlogPost::create([
            'category_id' => $blogCatHair->id,
            'author_id' => $adminUser->id,
            'title' => 'Preventing and Reversing Monsoon Hair Fall in Mumbai: Expert Medical Insights',
            'slug' => 'preventing-treating-hair-fall-summer-monsoon',
            'excerpt' => 'Discover why high humidity and coastal weather trigger acute telogen effluvium and how in-clinic therapies can help restore density.',
            'content' => "### Understanding Monsoon Shedding in Coastal Cities\nSeasonal hair shedding, medically known as acute telogen effluvium, is one of the most common reasons patients visit our Mumbai clinic during transitional weather.\n\nHumidity shifts, scalp microbiome imbalances, and nutritional micro-deficiencies can prematurely push hair follicles from the active growth (anagen) phase into the shedding (telogen) phase.\n\n### Proven In-Clinic Interventions\n1. **Growth Factor Concentrate (GFC):** Highly purified autologous growth factors injected directly at root depth.\n2. **Low-Level Laser Light Therapy (LLLT):** Photobiomodulation stimulates mitochondrial ATP production in dermal papilla cells.\n3. **Medical Scalp Detox:** Removes sebum plugs and recalibrates the scalp microbiome.",
            'featured_image' => 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=800',
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'read_time_minutes' => 5,
            'view_count' => 98,
        ]);
        $post2->tags()->attach([$tagPRP->id]);

        // 12. Menus & Navigation Structure
        $headerMenu = Menu::create([
            'name' => 'Main Header Navigation',
            'location' => 'header',
            'description' => 'Primary luxury navigation header',
            'status' => true,
        ]);

        $menuItems = [
            ['menu_id' => $headerMenu->id, 'title' => 'Home', 'url' => '/', 'sort_order' => 1],
            ['menu_id' => $headerMenu->id, 'title' => 'About Us', 'url' => '/about', 'sort_order' => 2],
            ['menu_id' => $headerMenu->id, 'title' => 'Treatments', 'url' => '/services', 'sort_order' => 3],
            ['menu_id' => $headerMenu->id, 'title' => 'Videos', 'url' => '/videos', 'sort_order' => 4],
            ['menu_id' => $headerMenu->id, 'title' => 'Gallery', 'url' => '/gallery', 'sort_order' => 5],
            ['menu_id' => $headerMenu->id, 'title' => 'Blog', 'url' => '/blog', 'sort_order' => 6],
            ['menu_id' => $headerMenu->id, 'title' => 'Contact', 'url' => '/contact', 'sort_order' => 7],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }

        // 13. Dynamic CMS Pages & Section Types
        $sectionTypes = [
            ['name' => 'Hero Banner', 'key' => 'hero', 'description' => 'Luxury full-width hero header with CTA buttons and clinic badges'],
            ['name' => 'Clinical Statistics', 'key' => 'stats', 'description' => 'Key numbers (15,000+ procedures, 12+ years experience, 99.4% satisfaction)'],
            ['name' => 'Doctor Profile Spotlight', 'key' => 'doctor_spotlight', 'description' => 'Lead physician background and medical philosophy'],
            ['name' => 'Treatments Showcase Grid', 'key' => 'treatments_grid', 'description' => 'Interactive service cards with category filters'],
            ['name' => 'Video Gallery', 'key' => 'video_gallery', 'description' => 'Embedded clinical procedures and patient stories'],
            ['name' => 'Before & After Slider', 'key' => 'before_after', 'description' => 'Comparative transformation showcase'],
            ['name' => 'Testimonials Carousel', 'key' => 'testimonials', 'description' => 'Verified patient experiences and star ratings'],
            ['name' => 'FAQ Accordion', 'key' => 'faq', 'description' => 'Structured questions and answers'],
            ['name' => 'Appointment Booking CTA', 'key' => 'booking_cta', 'description' => 'Call to action banner prompting consultation booking'],
            ['name' => 'Contact & Google Map', 'key' => 'contact_map', 'description' => 'Address, interactive map, and inquiry form'],
        ];

        foreach ($sectionTypes as $st) {
            SectionType::create($st);
        }

        // Create Core Pages
        $homePage = Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'subtitle' => 'Advanced Dermatology & Aesthetic Care Designed Around You',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        $aboutPage = Page::create([
            'title' => 'About Lumique Aesthetic Clinic',
            'slug' => 'about',
            'subtitle' => 'Where Medical Rigor Meets Artistry in Aesthetic Enhancement',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 2,
        ]);

        $contactPage = Page::create([
            'title' => 'Contact & Clinic Location',
            'slug' => 'contact',
            'subtitle' => 'Visit Our Bandra West Sanctuary or Connect with Our Concierge',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 3,
        ]);

        // Home Page Dynamic Sections
        PageSection::create([
            'page_id' => $homePage->id,
            'section_type_key' => 'hero',
            'title' => 'Advanced Dermatology & Aesthetic Care Designed Around You',
            'subtitle' => 'Skin · Hair · Laser · Aesthetic',
            'content' => 'Personalized skin, hair, laser, and aesthetic treatments delivered by board-certified specialists in a serene, luxurious clinical environment in Bandra West, Mumbai.',
            'image' => 'https://images.pexels.com/photos/7446659/pexels-photo-7446659.jpeg?auto=compress&cs=tinysrgb&w=1920',
            'settings' => [
                'tag_text' => 'Lumique Aesthetic Clinic',
                'stats' => [
                    ['number' => '15k+', 'label' => 'Happy Patients'],
                    ['number' => '50+', 'label' => 'Treatments'],
                    ['number' => '12+', 'label' => 'Years Experience'],
                ],
            ],
            'sort_order' => 1,
            'status' => true,
        ]);

        PageSection::create([
            'page_id' => $homePage->id,
            'section_type_key' => 'snapshot',
            'title' => 'Clinic Snapshot',
            'settings' => [
                'items' => [
                    ['label' => 'Visit Lumique', 'title' => 'Your confidence, cared for.', 'desc' => 'A calm, elevated clinic sanctuary built around your goals.'],
                    ['label' => 'Call Us', 'title' => '+91 88795 50581', 'desc' => 'Personal guidance from our dermatological team.'],
                    ['label' => 'Opening Hours', 'title' => 'Mon – Sat: 9AM – 7PM', 'desc' => 'Sunday: By Special Appointment'],
                    ['label' => 'Start Your Journey', 'title' => 'Ready when you are.', 'cta' => 'Book a consultation', 'dark' => true],
                ],
            ],
            'sort_order' => 2,
            'status' => true,
        ]);

        PageSection::create([
            'page_id' => $homePage->id,
            'section_type_key' => 'why_us',
            'title' => 'Care that feels personal, results that feel natural',
            'subtitle' => 'Why Lumique',
            'content' => 'Every treatment at Lumique is tailored to your skin biology and goals — supported by cutting-edge medical technology and empathetic physicians.',
            'settings' => [
                'pillars' => [
                    ['icon' => 'shield-check', 'title' => 'Safety-First', 'desc' => 'Treatment Approach'],
                    ['icon' => 'microscope', 'title' => 'Advanced Tech', 'desc' => 'US-FDA Approved Systems'],
                    ['icon' => 'heart-handshake', 'title' => 'Personalized Plans', 'desc' => 'Tailored to You'],
                    ['icon' => 'sparkles', 'title' => 'Natural Results', 'desc' => 'Subtle & Elegant'],
                ],
            ],
            'sort_order' => 3,
            'status' => true,
        ]);

        PageSection::create([
            'page_id' => $homePage->id,
            'section_type_key' => 'how_it_works',
            'title' => 'How Lumique Works',
            'subtitle' => 'Your Journey',
            'content' => 'A simple, guided path from your initial consultation to confident, lasting results.',
            'settings' => [
                'steps' => [
                    ['step' => '01', 'icon' => 'calendar', 'title' => 'Book Consultation', 'desc' => 'Reserve your appointment online or call us directly to select a comfortable slot.'],
                    ['step' => '02', 'icon' => 'stethoscope', 'title' => 'Personal Assessment', 'desc' => 'Your specialist reviews your skin/hair type and concerns to formulate a tailored plan.'],
                    ['step' => '03', 'icon' => 'microscope', 'title' => 'Expert Procedure', 'desc' => 'Receive treatment in a medical-grade, serene environment using advanced technology.'],
                    ['step' => '04', 'icon' => 'sparkles', 'title' => 'Lasting Radiance', 'desc' => 'We guide your aftercare protocol so your results stay natural, fresh, and sustained.'],
                ],
            ],
            'sort_order' => 4,
            'status' => true,
        ]);

        PageSection::create([
            'page_id' => $homePage->id,
            'section_type_key' => 'cta_banner',
            'title' => 'Ready to Transform Your Skin, Hair, and Confidence?',
            'subtitle' => 'Start Your Journey',
            'content' => 'Book your consultation today and discover personalized treatments designed around you in Bandra West, Mumbai.',
            'settings' => [
                'primary_btn' => 'Book an Appointment',
                'secondary_btn' => 'Call Us Directly',
            ],
            'sort_order' => 5,
            'status' => true,
        ]);

        // Banner Seeding
        Banner::create([
            'title' => 'Advanced Dermatology & Aesthetic Care Designed Around You',
            'subtitle' => 'Skin · Hair · Laser · Aesthetic',
            'badge_text' => 'Lumique Aesthetic Clinic',
            'button_text' => 'Book an Appointment',
            'button_url' => '#book',
            'secondary_button_text' => 'Explore Treatments',
            'secondary_button_url' => '/services',
            'image' => 'https://images.pexels.com/photos/7446659/pexels-photo-7446659.jpeg?auto=compress&cs=tinysrgb&w=1920',
            'status' => true,
            'sort_order' => 1,
        ]);

        // 14. Demo Inquiries, Leads, Follow-ups for CRM
        $inquiry1 = Inquiry::create([
            'name' => 'Meera Joshi',
            'email' => 'meera.joshi@example.com',
            'phone' => '+91 98201 44552',
            'subject' => 'HydraFacial MD Consultation for Wedding',
            'message' => 'Hi Dr. Vance, I am getting married next month and would like to understand the best skin prep package including HydraFacial and Carbon Laser.',
            'service_id' => 1,
            'service_name' => 'Medical HydraFacial MD®',
            'preferred_date' => now()->addDays(3),
            'preferred_time' => '11:00 AM',
            'source' => 'Website Contact Form',
            'type' => 'appointment',
            'status' => 'new',
            'priority' => 'high',
            'assigned_to' => $adminUser->id,
            'ip_address' => '127.0.0.1',
        ]);

        $lead1 = Lead::create([
            'inquiry_id' => $inquiry1->id,
            'lead_source_id' => 1,
            'name' => 'Meera Joshi',
            'email' => 'meera.joshi@example.com',
            'phone' => '+91 98201 44552',
            'service_id' => 1,
            'service_name' => 'Medical HydraFacial MD®',
            'status' => 'new',
            'priority' => 'high',
            'assigned_to' => $adminUser->id,
            'preferred_date' => now()->addDays(3),
            'preferred_time' => '11:00 AM',
            'estimated_value' => 25000.00,
            'notes' => 'Prospective bridal package client. Interested in multi-session glow plan.',
        ]);

        LeadNote::create([
            'lead_id' => $lead1->id,
            'user_id' => $adminUser->id,
            'note' => 'Inquiry received via website appointment booking form. Assigned to Dr. Vance for personalized treatment consultation.',
        ]);

        LeadFollowUp::create([
            'lead_id' => $lead1->id,
            'assigned_to' => $adminUser->id,
            'follow_up_date' => now()->addDay()->toDateString(),
            'follow_up_time' => '10:30:00',
            'note' => 'Call patient to confirm Saturday 11:00 AM slot and discuss bridal pre-care steps.',
            'status' => 'pending',
        ]);

        LeadActivity::create([
            'lead_id' => $lead1->id,
            'user_id' => $adminUser->id,
            'activity_type' => 'lead_created',
            'description' => 'Lead created automatically from website appointment request.',
            'properties' => ['service' => 'Medical HydraFacial MD®', 'channel' => 'Website'],
        ]);

        // 15. Seed Core Page SEO Metas
        $coreSeoData = [
            [
                'path' => '/',
                'meta_title' => 'Lumique Aesthetic Clinic | Premier Dermatology, Laser & Hair Care Mumbai',
                'meta_description' => 'Bespoke aesthetic clinic in Bandra West, Mumbai specializing in Medical HydraFacial MD, Pico Laser Tattoo Removal, Carbon Peels, GFC Hair Restoration, and Dermal Fillers.',
                'meta_keywords' => 'dermatologist bandra, hydrafacial mumbai, laser clinic mumbai, prp hair clinic, skin clinic bandra west',
                'canonical_url' => 'http://127.0.0.1:8000/',
                'og_title' => 'Lumique Aesthetic Clinic | Bespoke Luxury Dermatology Bandra West',
                'og_description' => 'Experience personalized, US-FDA approved clinical skin, laser, and hair therapies tailored to you.',
                'og_image' => '/images/logo.jpeg',
                'robots' => 'index, follow',
            ],
            [
                'path' => '/about',
                'meta_title' => 'About Lumique Clinic | Dr. Alisha Vance, MD & Specialist Team',
                'meta_description' => 'Discover our patient-first medical philosophy, state-of-the-art diagnostic protocols, and board-certified aesthetic practitioners in Bandra West, Mumbai.',
                'meta_keywords' => 'about lumique clinic, dr alisha vance, aesthetic dermatologist mumbai, best skin doctor bandra',
                'canonical_url' => 'http://127.0.0.1:8000/about',
                'og_title' => 'About Lumique Aesthetic Clinic Mumbai',
                'og_description' => 'Board-certified clinical experts delivering subtle, elegant, and safe aesthetic transformations.',
                'og_image' => '/images/logo.jpeg',
                'robots' => 'index, follow',
            ],
            [
                'path' => '/services',
                'meta_title' => 'Clinical Treatments & Procedures Catalog | Lumique Clinic',
                'meta_description' => 'Explore our comprehensive range of US-FDA approved clinical skin rejuvenation, laser treatments, trichology hair restoration, and facial contouring.',
                'meta_keywords' => 'skin treatments mumbai, laser hair removal bandra, carbon laser peel mumbai, hair loss treatment',
                'canonical_url' => 'http://127.0.0.1:8000/services',
                'og_title' => 'Clinical Treatments Catalog | Lumique Aesthetic Clinic',
                'og_description' => 'US-FDA approved clinical procedures for skin, hair, laser, and aesthetic medicine in Mumbai.',
                'og_image' => '/images/logo.jpeg',
                'robots' => 'index, follow',
            ],
            [
                'path' => '/videos',
                'meta_title' => 'Procedure Videos & Treatment Demonstrations | Lumique Clinic',
                'meta_description' => 'Watch in-depth procedural demonstrations, doctor explanations, and patient treatment journeys at Lumique Aesthetic Clinic.',
                'meta_keywords' => 'treatment videos, hydrafacial demonstration, laser hair removal video mumbai',
                'canonical_url' => 'http://127.0.0.1:8000/videos',
                'og_title' => 'Procedure Videos & Clinical Demonstrations | Lumique Clinic',
                'og_description' => 'Behind-the-scenes procedural insights and medical guidance from our aesthetic specialists.',
                'og_image' => '/images/logo.jpeg',
                'robots' => 'index, follow',
            ],
            [
                'path' => '/gallery',
                'meta_title' => 'Clinical Results Gallery & Case Studies | Lumique Clinic',
                'meta_description' => 'Real clinical before-and-after results across laser skin resurfacing, acne scar revision, and hair density regrowth.',
                'meta_keywords' => 'before after skin results, laser hair results, prp hair results mumbai',
                'canonical_url' => 'http://127.0.0.1:8000/gallery',
                'og_title' => 'Results Gallery | Lumique Aesthetic Clinic Mumbai',
                'og_description' => 'Real patient case studies and validated clinical outcomes from our Bandra West sanctuary.',
                'og_image' => '/images/logo.jpeg',
                'robots' => 'index, follow',
            ],
            [
                'path' => '/blog',
                'meta_title' => 'The Dermatology Journal & Clinical Insights | Lumique Clinic',
                'meta_description' => 'Evidence-based articles on skincare actives, laser technology, hair loss science, and preventive aesthetic medicine.',
                'meta_keywords' => 'skincare blog, dermatology tips mumbai, active ingredients guide, hair care advice',
                'canonical_url' => 'http://127.0.0.1:8000/blog',
                'og_title' => 'The Lumique Journal | Aesthetic Medicine & Dermatology Insights',
                'og_description' => 'Doctor-written clinical insights on skin biology, active ingredients, and aesthetic science.',
                'og_image' => '/images/logo.jpeg',
                'robots' => 'index, follow',
            ],
            [
                'path' => '/contact',
                'meta_title' => 'Contact Lumique Aesthetic Clinic | Book Consultation Bandra West',
                'meta_description' => 'Schedule your private consultation at our Bandra West sanctuary on Linking Road, Mumbai. Phone, WhatsApp, and location directions.',
                'meta_keywords' => 'contact lumique, book dermatologist bandra, skin clinic linking road mumbai',
                'canonical_url' => 'http://127.0.0.1:8000/contact',
                'og_title' => 'Contact Lumique Aesthetic Clinic Bandra West Mumbai',
                'og_description' => 'Connect with our patient care team to reserve your personalized skin or hair consultation.',
                'og_image' => '/images/logo.jpeg',
                'robots' => 'index, follow',
            ],
        ];

        foreach ($coreSeoData as $seo) {
            SeoMeta::updateOrCreate(['path' => $seo['path']], $seo);
        }
    }
}
