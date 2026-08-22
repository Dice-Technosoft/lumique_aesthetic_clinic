/* ==========================================================================
   Lumique Aesthetic Clinic - Core Data & Local Storage Engine
   ========================================================================== */

const LUMIQUE_DATA = {
  settings: {
    clinic_name: 'Lumique Aesthetic Clinic',
    tagline: 'Advanced Dermatology & Aesthetic Care Designed Around You',
    phone: '+91 88795 50581',
    whatsapp: '+918879550581',
    email: 'info@lumiqueclinic.com',
    address: 'Ground Floor, Kenilworth Mall, Linking Road, Bandra West, Mumbai, Maharashtra 400050, India',
    working_hours: 'Monday – Saturday: 9:00 AM – 7:00 PM\nSunday: Closed',
    logo_img: 'images/logo.jpeg',
    map_embed: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3771.4411132644265!2d72.83354927596535!3d19.04432175296839!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c9197779a513%3A0x6b1070e28f3cb295!2sLinking%20Rd%2C%20Bandra%20West%2C%20Mumbai%2C%20Maharashtra%20400050!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin'
  },

  doctor: {
    name: 'Dr. Alisha Vance, MD, DVD',
    title: 'Lead Dermatologist & Aesthetic Specialist',
    photo: 'https://images.pexels.com/photos/32160039/pexels-photo-32160039.jpeg?auto=compress&cs=tinysrgb&w=800',
    introduction: 'Dr. Vance brings over a decade of specialized experience in clinical dermatology, laser aesthetics, and non-surgical facial rejuvenation. She is dedicated to delivering authentic, natural results tailored to each patient in our Mumbai sanctuary.',
    professional_profile: 'Board-certified Dermatologist with fellowship training in Advanced Aesthetic Medicine and Cutaneous Laser Surgery from premier international medical institutions.',
    qualifications: 'MBBS, MD (Dermatology, Venereology & Leprosy), Fellowship in Aesthetic Medicine (FACD)',
    experience: '12+ Years of clinical and aesthetic practice with over 15,000 successful procedures in Mumbai.',
    specializations: 'Non-surgical Facial Contouring, Laser Skin Resurfacing, Hair Restoration, Acne Scar Revision, Melasma & Pigmentation Management',
    treatment_philosophy: 'We believe that aesthetic medicine should enhance your innate beauty rather than alter it. Every treatment plan is crafted with meticulous medical precision and utmost patient comfort.',
    clinic_approach: 'Comprehensive 360° skin diagnostics, individualized treatment plans, strict medical hygiene protocols, and dedicated aftercare support.'
  },

  categories: [
    {
      id: 'skin',
      slug: 'skin',
      name: 'Skin Treatments',
      icon: 'sparkles',
      description: 'Comprehensive medical and aesthetic solutions for glowing, clear, and youthful skin.',
      image: 'https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=900',
      media: {
        photos: [
          { title: 'Clinical HydraFacial Step', url: 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Vortex Deep Cleansing & Exfoliation' },
          { title: 'LED Phototherapy Treatment', url: 'https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Collagen-Stimulating Red Light Therapy' },
          { title: 'Chemical Peel Application', url: 'https://images.pexels.com/photos/7108264/pexels-photo-7108264.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Medical-Grade Glycolic Smoothing' }
        ],
        videos: [
          { title: 'HydraFacial Glow Experience', duration: '1:45', thumbnail: 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800', videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-facial-massage-treatment-at-a-spa-41484-large.mp4' },
          { title: 'Skin Glow Diagnostics', duration: '2:10', thumbnail: 'https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=800', videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-woman-applying-a-face-mask-41486-large.mp4' }
        ],
        beforeAfter: [
          { before: 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=600', after: 'https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=600', label: 'Acne Scar Smoothing & Radiance (After 3 Sessions)' }
        ]
      }
    },
    {
      id: 'hair',
      slug: 'hair',
      name: 'Hair Restoration',
      icon: 'scissors',
      description: 'Advanced clinical therapies and transplantation techniques for dense, natural hair growth.',
      image: 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=900',
      media: {
        photos: [
          { title: 'GFC / PRP Therapy Setup', url: 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Autologous Growth Factor Isolation' },
          { title: 'Digital Trichoscopy Scan', url: 'https://images.pexels.com/photos/11024139/pexels-photo-11024139.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'High-Magnification Follicle Analysis' }
        ],
        videos: [
          { title: 'PRP Scalp Restoration Protocol', duration: '2:20', thumbnail: 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=800', videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-hairdresser-styling-a-womans-hair-41481-large.mp4' }
        ],
        beforeAfter: [
          { before: 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=600', after: 'https://images.pexels.com/photos/11024139/pexels-photo-11024139.jpeg?auto=compress&cs=tinysrgb&w=600', label: 'Crown Density Restoration (After 4 Sessions)' }
        ]
      }
    },
    {
      id: 'laser',
      slug: 'laser',
      name: 'Laser Treatments',
      icon: 'zap',
      description: 'State-of-the-art US-FDA approved laser technologies for hair removal, pigmentation, and scars.',
      image: 'https://images.pexels.com/photos/4586726/pexels-photo-4586726.jpeg?auto=compress&cs=tinysrgb&w=900',
      media: {
        photos: [
          { title: 'Triple Wavelength Laser', url: 'https://images.pexels.com/photos/4586726/pexels-photo-4586726.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Sapphire Ice-Cooling Tip' },
          { title: 'Carbon Laser Peeling', url: 'https://images.pexels.com/photos/7446659/pexels-photo-7446659.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Q-Switched Nd:YAG Laser Pass' }
        ],
        videos: [
          { title: 'Hollywood Laser Peel Live Walkthrough', duration: '1:50', thumbnail: 'https://images.pexels.com/photos/4586726/pexels-photo-4586726.jpeg?auto=compress&cs=tinysrgb&w=800', videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-facial-massage-treatment-at-a-spa-41484-large.mp4' }
        ],
        beforeAfter: [
          { before: 'https://images.pexels.com/photos/4586726/pexels-photo-4586726.jpeg?auto=compress&cs=tinysrgb&w=600', after: 'https://images.pexels.com/photos/7446659/pexels-photo-7446659.jpeg?auto=compress&cs=tinysrgb&w=600', label: 'Laser Hair Reduction (After 5 Sessions)' }
        ]
      }
    },
    {
      id: 'tattoo-removal',
      slug: 'tattoo-removal',
      name: 'Tattoo Removal',
      icon: 'eraser',
      description: 'Safe, high-precision Picosecond laser removal for all ink colors with zero skin scarring.',
      image: 'https://images.pexels.com/photos/7446683/pexels-photo-7446683.jpeg?auto=compress&cs=tinysrgb&w=900',
      media: {
        photos: [
          { title: 'Picosecond Handpiece', url: 'https://images.pexels.com/photos/7446683/pexels-photo-7446683.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Photoacoustic Ink Dispersion' }
        ],
        videos: [
          { title: 'Picosecond Laser Pulse Demonstration', duration: '1:30', thumbnail: 'https://images.pexels.com/photos/7446683/pexels-photo-7446683.jpeg?auto=compress&cs=tinysrgb&w=800', videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-facial-massage-treatment-at-a-spa-41484-large.mp4' }
        ],
        beforeAfter: [
          { before: 'https://images.pexels.com/photos/7446683/pexels-photo-7446683.jpeg?auto=compress&cs=tinysrgb&w=600', after: 'https://images.pexels.com/photos/3985332/pexels-photo-3985332.jpeg?auto=compress&cs=tinysrgb&w=600', label: 'Dark Ink Clearance (After 4 Sessions)' }
        ]
      }
    },
    {
      id: 'aesthetic-treatments',
      slug: 'aesthetic-treatments',
      name: 'Aesthetic Enhancements',
      icon: 'flower-2',
      description: 'Bespoke injectables, dermal fillers, and anti-aging treatments for graceful enhancement.',
      image: 'https://images.pexels.com/photos/14438367/pexels-photo-14438367.jpeg?auto=compress&cs=tinysrgb&w=900',
      media: {
        photos: [
          { title: 'Micro-Cannula Facial Contouring', url: 'https://images.pexels.com/photos/14438367/pexels-photo-14438367.jpeg?auto=compress&cs=tinysrgb&w=800', caption: 'Anatomical Precision Injections' }
        ],
        videos: [
          { title: 'Subtle Facial Enhancement Consultation', duration: '2:40', thumbnail: 'https://images.pexels.com/photos/14438367/pexels-photo-14438367.jpeg?auto=compress&cs=tinysrgb&w=800', videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-woman-applying-a-face-mask-41486-large.mp4' }
        ],
        beforeAfter: [
          { before: 'https://images.pexels.com/photos/14438367/pexels-photo-14438367.jpeg?auto=compress&cs=tinysrgb&w=600', after: 'https://images.pexels.com/photos/32160039/pexels-photo-32160039.jpeg?auto=compress&cs=tinysrgb&w=600', label: 'Tear Trough & Jawline Refinement' }
        ]
      }
    }
  ],

  treatments: [
    {
      id: 'hydrafacial-glow',
      slug: 'hydrafacial-glow',
      category_id: 'skin',
      title: 'Medical HydraFacial MD',
      short_intro: 'Deep vortex cleansing, gentle exfoliation, painless extractions, and antioxidant infusion.',
      hero_image: 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=1200',
      is_featured: true,
      who_is_it_for: 'Suitable for all skin types seeking instant glow, deep pore unclogging, hydration, and improved texture.',
      benefits: '• Instant radiant and dewy complexion\n• Thorough blackhead & congestion extraction\n• Zero downtime or post-treatment redness\n• Long-lasting deep dermal hydration',
      procedure_overview: 'A 4-step patented vortex technology cleanses, peels, extracts, and hydrates using customized medical serums rich in peptides and hyaluronic acid.',
      treatment_process: '1. Gentle lymphatic drainage & soothing cleanse\n2. Light glycolic & salicylic acid exfoliation\n3. Automated painless suction extraction\n4. Targeted antioxidant & hyaluronic peptide infusion',
      expected_results: 'Immediate visible glow, refined pores, and softened fine lines noticeable right after the first session.',
      recovery_info: 'No downtime. You can immediately return to your normal daily activities or makeup.',
      num_sessions: 'Recommended every 4 to 6 weeks for optimal skin health maintenance.',
      faqs: [
        {
          question: 'Is HydraFacial painful?',
          answer: 'Not at all. Patients frequently describe the treatment as soothing and pleasant, akin to a cool paintbrush moving across the face.'
        },
        {
          question: 'How long do the results last?',
          answer: 'The luminous glow and refined texture typically last 1 to 4 weeks depending on your skin type and home skincare regimen.'
        }
      ]
    },
    {
      id: 'carbon-laser-peel',
      slug: 'carbon-laser-peel',
      category_id: 'laser',
      title: 'Hollywood Carbon Laser Peel',
      short_intro: 'Revolutionary laser facial that reduces acne, shrinks large pores, and boosts collagen production.',
      hero_image: 'https://images.pexels.com/photos/4586726/pexels-photo-4586726.jpeg?auto=compress&cs=tinysrgb&w=1200',
      is_featured: true,
      who_is_it_for: 'Ideal for oily, acne-prone skin, enlarged pores, superficial pigmentation, and dull uneven skin tone.',
      benefits: '• Significantly diminishes acne and excess sebum\n• Tightens enlarged pores\n• Stimulates deep collagen remodeling\n• Leaves skin silky smooth and photo-ready',
      procedure_overview: 'A liquid carbon layer is applied to the skin to absorb impurities. An advanced Q-switched Nd:YAG laser then vaporizes the carbon particles, carrying away dead cells and debris.',
      treatment_process: '1. Deep skin sanitization\n2. Application of medical-grade carbon lotion\n3. Laser pulses targeting and fragmenting carbon particles\n4. Calming peptide mask and broad-spectrum sun protection',
      expected_results: 'Immediate oil control, brighter complexion, and progressive skin firming over repeated sessions.',
      recovery_info: 'Mild transient warmth or light erythema that subsides within 1-2 hours.',
      num_sessions: '3 to 6 sessions spaced 2 to 3 weeks apart for maximum clinical results.',
      faqs: [
        {
          question: 'Can I do this before a special event?',
          answer: 'Yes! Often known as the Red Carpet Laser Facial, it provides an immediate porcelain-like finish with zero downtime.'
        }
      ]
    },
    {
      id: 'prp-hair-restoration',
      slug: 'prp-hair-restoration',
      category_id: 'hair',
      title: 'Advanced PRP / GFC Hair Restoration',
      short_intro: 'Autologous concentrated growth factor therapy to halt hair thinning and reactivate dormant follicles.',
      hero_image: 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=1200',
      is_featured: true,
      who_is_it_for: 'Individuals experiencing androgenetic alopecia, male/female pattern hair loss, or post-stress hair thinning.',
      benefits: '• Strengthens existing hair root shafts\n• Stimulates natural hair density and volume\n• Completely 100% bio-compatible with zero allergic risk\n• Fast 45-minute lunch break procedure',
      procedure_overview: 'A small sample of your blood is drawn and spun in a specialized centrifuge to isolate platelet-rich plasma packed with active growth factors, which are gently injected into thinning scalp zones.',
      treatment_process: '1. Blood collection and centrifugation\n2. Scalp numbing with topical anesthetic cream\n3. Micro-injection of concentrated growth factors\n4. Low-level laser light stimulation',
      expected_results: 'Reduction in hair fall within 3-4 weeks, followed by noticeable thickness and new hair sprout growth in 3-4 months.',
      recovery_info: 'Mild scalp tenderness for 12 hours. Washing hair is allowed the next morning.',
      num_sessions: '4 to 6 sessions spaced 4 weeks apart, followed by maintenance every 6 months.',
      faqs: [
        {
          question: 'Does PRP hurt?',
          answer: 'We apply a high-strength topical numbing cream before the procedure to ensure minimal to no discomfort.'
        }
      ]
    },
    {
      id: 'pico-laser-tattoo-removal',
      slug: 'pico-laser-tattoo-removal',
      category_id: 'tattoo-removal',
      title: 'Picosecond Laser Tattoo Removal',
      short_intro: 'Ultra-short picosecond pulses break ink into micro-particles with minimal sessions and zero scarring.',
      hero_image: 'https://images.pexels.com/photos/7446683/pexels-photo-7446683.jpeg?auto=compress&cs=tinysrgb&w=1200',
      is_featured: true,
      who_is_it_for: 'Anyone looking to completely remove or fade an unwanted tattoo for cover-up without scarring.',
      benefits: '• Clears stubborn dark, blue, green, and red ink\n• Requires 50% fewer sessions than older Q-switch lasers\n• Leaves surrounding skin tissue completely intact\n• Minimal thermal discomfort with cryogenic skin cooling',
      procedure_overview: 'Photo-acoustic shockwaves shatter ink pigments into dust-like microscopic particles that the body’s lymphatic system naturally eliminates over time.',
      treatment_process: '1. Clinical photography & tattoo assessment\n2. Numbing cream application\n3. Cryo-chilled picosecond laser pass\n4. Soothing antibacterial ointment dressing',
      expected_results: 'Progressive fading visible with each treatment cycle.',
      recovery_info: 'Minor frosting/redness for 3-5 days. Keep the treated area clean and moisturized.',
      num_sessions: '4 to 8 sessions spaced 6 to 8 weeks apart depending on ink depth and age.',
      faqs: [
        {
          question: 'Will it leave a scar?',
          answer: 'Picosecond lasers utilize mechanical photoacoustic energy rather than high thermal heat, virtually eliminating scar risks.'
        }
      ]
    },
    {
      id: 'dermal-fillers-sculpt',
      slug: 'dermal-fillers-sculpt',
      category_id: 'aesthetic-treatments',
      title: 'Subtle Facial Contouring & Dermal Fillers',
      short_intro: 'Hyaluronic acid dermal fillers to restore volume in cheeks, jawline, lips, and under-eye hollows.',
      hero_image: 'https://images.pexels.com/photos/14438367/pexels-photo-14438367.jpeg?auto=compress&cs=tinysrgb&w=1200',
      is_featured: true,
      who_is_it_for: 'Patients seeking subtle volume restoration, lip definition, under-eye tear trough correction, or jawline harmonization.',
      benefits: '• Immediate, natural-looking facial harmony\n• Reversible and biocompatible hyaluronic acid\n• Stimulates natural collagen synthesis\n• Results last from 9 to 18 months',
      procedure_overview: 'Expert physician injection using ultra-fine micro-cannulas for smooth, natural volume enhancement with minimal swelling.',
      treatment_process: '1. Comprehensive facial anatomy mapping\n2. Topical local numbing\n3. Precise micro-cannula placement\n4. Gentle sculpting and symmetry evaluation',
      expected_results: 'Immediate structural rejuvenation and youthfulness that settles into seamless natural beauty within 7 days.',
      recovery_info: 'Mild swelling or pinpoint bruising may occur for 2-3 days.',
      num_sessions: 'Single treatment with a complimentary 2-week follow-up touch-up if needed.',
      faqs: [
        {
          question: 'Will my face look unnatural or overdone?',
          answer: 'Our core philosophy is graceful elegance. We specialize in micro-dosing and anatomical symmetry so results look effortlessly refreshed.'
        }
      ]
    },
    {
      id: 'laser-hair-reduction',
      slug: 'laser-hair-reduction',
      category_id: 'laser',
      title: 'Triple-Wavelength Painless Laser Hair Reduction',
      short_intro: 'Triple wavelength (Alexandrite, Diode, Nd:YAG) technology with Ice-Cooling for permanent hair reduction.',
      hero_image: 'https://images.pexels.com/photos/7446659/pexels-photo-7446659.jpeg?auto=compress&cs=tinysrgb&w=1200',
      is_featured: true,
      who_is_it_for: 'Men and women seeking permanent reduction of unwanted hair on face, arms, legs, back, or full body.',
      benefits: '• Safe on all skin tones (Fitzpatrick types I–VI)\n• Continuous contact cooling prevents pain\n• Eliminates painful ingrown hairs and razor bumps\n• Up to 90% permanent hair reduction',
      procedure_overview: 'Synchronized triple-wavelength lasers target hair follicles at different anatomical depths while contact cooling shields the epidermis.',
      treatment_process: '1. Skin analysis & shaving preparation\n2. Cooling conductive gel application\n3. In-motion high-speed laser sweeps\n4. Post-laser soothing aloe moisturizer',
      expected_results: 'Fine, slow-growing hair after session 1; lasting reduction after completing the recommended course.',
      recovery_info: 'Zero downtime. Avoid direct sun exposure and hot saunas for 24 hours.',
      num_sessions: '6 to 8 sessions spaced 4 to 6 weeks apart.',
      faqs: [
        {
          question: 'Is it completely painless?',
          answer: 'With our advanced -5°C sapphire cooling crystal tip, most clients feel only a pleasant warm or cool sensation.'
        }
      ]
    }
  ],

  blogPosts: [
    {
      id: 'post-1',
      slug: 'hyaluronic-acid-vs-retinol-skincare-guide',
      title: 'Hyaluronic Acid vs Retinol: When and How to Use Both in Your Routine',
      category: 'Skin Science',
      category_slug: 'skin-care',
      excerpt: 'Understand how these two powerhouse ingredients work, why they complement each other, and the optimal layering sequence for Indian skin.',
      featured_image: 'https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=800',
      author: 'Dr. Alisha Vance',
      published_at: '2026-07-28',
      content: `### Introduction to Active Skincare
In modern dermatology, active ingredients are the foundation of effective preventive and corrective skincare. Two of the most widely researched and clinically validated molecules are Hyaluronic Acid and Retinol.

While both promise transformative results, they address entirely different biological mechanisms in the skin.

---

### What Is Hyaluronic Acid?
Hyaluronic acid (HA) is a naturally occurring glycosaminoglycan found throughout the body's connective tissue. A single molecule of HA can hold up to 1,000 times its molecular weight in water.

- **Primary Role:** Hydration, barrier restoration, and dermal plumpness.
- **Best For:** Dehydrated skin, dullness, fine dehydration lines, and post-procedure recovery.
- **When to Apply:** Morning and evening on damp skin.

---

### What Is Retinol?
Retinol is a derivative of Vitamin A that accelerates cellular turnover and stimulates fibroblast cells to synthesize fresh collagen and elastin.

- **Primary Role:** Cellular renewal, acne clearance, and smoothing deep wrinkles.
- **Best For:** Photoaging, irregular texture, hyperpigmentation, and loss of skin elasticity.
- **When to Apply:** Exclusively at night, followed by broad-spectrum sunscreen the next morning.

---

### How to Safely Combine Both
1. Cleanse thoroughly with a gentle pH-balanced cleanser.
2. Apply Hyaluronic Acid serum onto slightly damp skin to lock in moisture.
3. Allow the skin to dry completely (3-5 minutes).
4. Apply a pea-sized amount of Retinol.
5. Finish with a nourishing lipid-rich barrier cream to seal the active ingredients.

Consistent application and daily SPF 50+ protection are essential to safeguard your glowing results.`
    },
    {
      id: 'post-2',
      slug: 'preventing-treating-hair-fall-summer-monsoon',
      title: 'Preventing and Reversing Monsoon Hair Fall in Mumbai: Expert Medical Insights',
      category: 'Hair Care',
      category_slug: 'hair-care',
      excerpt: 'Discover why high humidity and coastal weather trigger acute telogen effluvium and how in-clinic therapies can help.',
      featured_image: 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=800',
      author: 'Dr. Alisha Vance',
      published_at: '2026-08-05',
      content: `### Understanding Monsoon Shedding in Coastal Cities
Seasonal hair shedding, medically known as acute telogen effluvium, is one of the most common reasons patients visit our Mumbai clinic during transitional weather.

Humidity shifts, scalp microbiome imbalances, and nutritional micro-deficiencies can prematurely push hair follicles from the active growth (anagen) phase into the shedding (telogen) phase.

---

### Clinical Signs to Watch For
- More than 100 strands lost daily during washing or brushing.
- Visible widening of the central hair partition.
- Scalp itching or excessive sebum accumulation at follicle roots.

---

### Proven In-Clinic Interventions
1. **Growth Factor Concentrate (GFC):** Highly purified autologous growth factors injected directly at root depth.
2. **Low-Level Laser Light Therapy (LLLT):** Photobiomodulation stimulates mitochondrial ATP production in dermal papilla cells.
3. **Medical Scalp Detox:** Removes sebum plugs and recalibrates the scalp microbiome.

Book a trichoscopy analysis with our specialists in Bandra West to pinpoint root causes and formulate an effective recovery regimen.`
    },
    {
      id: 'post-3',
      slug: 'why-picosecond-laser-is-gold-standard-tattoo-removal',
      title: 'Why Picosecond Technology is the Gold Standard in Tattoo & Pigment Removal',
      category: 'Laser Science',
      category_slug: 'laser-treatments',
      excerpt: 'How trillionths-of-a-second laser pulses break down complex inks without thermal damage to surrounding tissue.',
      featured_image: 'https://images.pexels.com/photos/7446683/pexels-photo-7446683.jpeg?auto=compress&cs=tinysrgb&w=800',
      author: 'Dr. Alisha Vance',
      published_at: '2026-08-14',
      content: `### The Evolution of Tattoo Laser Removal
For decades, nanosecond Q-switched lasers were the standard for tattoo removal. While effective, they relied heavily on thermal heat to shatter ink pigments, occasionally resulting in skin blistering or pigmentary alterations.

Picosecond technology represents a quantum leap in laser physics.

---

### The Photoacoustic Shockwave Advantage
A picosecond laser delivers pulses measured in trillionths of a second (10^-12 seconds). This speed creates an intense photomechanical shockwave rather than heat.

- **Dust-Sized Particles:** Shatters pigment into minuscule particles that are easily filtered by white blood cells.
- **Zero Thermal Scarring:** Surrounding healthy skin tissue is completely preserved.
- **Fewer Sessions:** Reduces the total required treatment sessions by up to 50%.

Consult our laser dermatology team in Mumbai to evaluate your tattoo and receive a customized clearance roadmap.`
    }
  ],

  testimonials: [
    {
      name: 'Priya Shah',
      treatment: 'HydraFacial & Skin Rejuvenation',
      text: 'From the first consultation at the Bandra clinic, I felt heard and cared for. The results are subtle, fresh and completely natural — exactly what I wanted. Lumique sets the gold standard for clinical aesthetics in Mumbai.'
    },
    {
      name: 'Arjun Mehta',
      treatment: 'PRP Hair Restoration',
      text: 'The medical team explained every step of the GFC therapy clearly. After 4 sessions I have noticeable thickness and my hairline has stabilized. Highly recommend Dr. Vance!'
    },
    {
      name: 'Neha Roy',
      treatment: 'Laser Hair Reduction',
      text: 'A truly luxury clinic experience. The cooling technology made the laser treatments completely painless, and the clinic ambience is serene and spotless.'
    }
  ]
};

// Storage Utilities for Appointments and Content Management
class LumiqueStore {
  static getAppointments() {
    try {
      const stored = localStorage.getItem('lumique_appointments');
      if (stored) return JSON.parse(stored);
    } catch (e) {
      console.error('Failed to read appointments from localStorage', e);
    }
    // Default mock appointments for demonstration
    return [
      {
        id: 'apt-101',
        name: 'Pooja Sharma',
        phone: '+91 98200 12345',
        email: 'pooja.s@example.com',
        treatment_id: 'hydrafacial-glow',
        treatment_title: 'Medical HydraFacial MD',
        preferred_date: '2026-08-22',
        preferred_time: 'morning',
        message: 'Looking for a skin refresher before my anniversary weekend.',
        status: 'confirmed',
        created_at: new Date(Date.now() - 3600000 * 24 * 2).toISOString()
      },
      {
        id: 'apt-102',
        name: 'Rohan Patel',
        phone: '+91 98190 67890',
        email: 'rohan.p@example.com',
        treatment_id: 'prp-hair-restoration',
        treatment_title: 'Advanced PRP / GFC Hair Restoration',
        preferred_date: '2026-08-25',
        preferred_time: 'afternoon',
        message: 'Consultation regarding hair thinning at the crown.',
        status: 'new',
        created_at: new Date(Date.now() - 3600000 * 5).toISOString()
      }
    ];
  }

  static saveAppointment(appointment) {
    const appointments = this.getAppointments();
    const newApt = {
      id: 'apt-' + Date.now(),
      created_at: new Date().toISOString(),
      status: 'new',
      ...appointment
    };
    appointments.unshift(newApt);
    localStorage.setItem('lumique_appointments', JSON.stringify(appointments));
    return newApt;
  }

  static updateAppointmentStatus(id, newStatus) {
    const appointments = this.getAppointments();
    const apt = appointments.find(a => a.id === id);
    if (apt) {
      apt.status = newStatus;
      localStorage.setItem('lumique_appointments', JSON.stringify(appointments));
    }
    return appointments;
  }

  static deleteAppointment(id) {
    let appointments = this.getAppointments();
    appointments = appointments.filter(a => a.id !== id);
    localStorage.setItem('lumique_appointments', JSON.stringify(appointments));
    return appointments;
  }
}

window.LUMIQUE_DATA = LUMIQUE_DATA;
window.LumiqueStore = LumiqueStore;
