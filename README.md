# Lumique Aesthetic Clinic

[![Website Status](https://img.shields.io/badge/Status-Live-success?style=flat-square)](#)
[![Tech Stack](https://img.shields.io/badge/Built%20With-HTML5%20%7C%20CSS3%20%7C%20Vanilla%20JS-crimson?style=flat-square)](#)
[![Location](https://img.shields.io/badge/Location-Bandra%20West%2C%20Mumbai-gold?style=flat-square)](#)

A bespoke, luxury web application for **Lumique Aesthetic Clinic** — a premier dermatology, hair restoration, laser therapy, and medical aesthetic center based in Bandra West, Mumbai.

Built entirely with standard **HTML5, Vanilla CSS3, and modern JavaScript (ES6+)**, optimized for ultra-fast load times, seamless compatibility with local Apache/XAMPP environments, and zero build tool overhead.

---

## 🌟 Key Features

- **💎 Luxury Aesthetic & Design System**:
  - Curated color palette (Crimson, Burgundy, Antique Gold, Soft Red, Ivory, and Deep Charcoal).
  - Modern glassmorphism with high-contrast frosted cards (`backdrop-filter`).
  - Smooth scroll reveal animations and refined typography with *Playfair Display* & *Inter*.

- **🔄 Adaptive Header with Dynamic Scroll Transition**:
  - Transparent floating header on dark hero banners that dynamically transitions to a frosted glass header upon scrolling.
  - Inline vector SVG social media icons (Facebook, Instagram, YouTube, WhatsApp).

- **📅 Universal Interactive Appointment Booking Modal**:
  - Popup booking modal accessible from every page (`openAppointmentModal()`).
  - Full client-side validation, appointment scheduling (Date & Time slots), and anti-spam honeypot.
  - Client-side persistence via `LumiqueStore` connected with local storage.

- **🎥 Rich Treatments Media Showcase**:
  - Filter bar supporting **"All Treatments"** and individual categories (Skin, Hair, Laser, Tattoo Removal, Aesthetic Enhancements).
  - High-resolution procedure photos.
  - Embedded clinical video cards with custom video player modal (`openVideoModal`).
  - Side-by-side comparative Before & After outcome galleries.

- **📍 Mumbai Clinic Integration**:
  - Accurate physical address in Linking Road, Bandra West, Mumbai.
  - Interactive Google Maps embed and direct WhatsApp concierge integration.

- **📊 Admin Portal (`admin.html`)**:
  - Real-time appointment management dashboard (view, filter, confirm, cancel).
  - Clinic operational settings editor (hours, contact info, lead doctor details).
  - Treatments and blog article inventory manager.

- **🔍 Complete SEO & Social Sharing (Open Graph / Twitter)**:
  - Vector SVG and PNG favicon integration.
  - Full Open Graph metadata and Twitter Cards for rich link previews across WhatsApp, Facebook, LinkedIn, and Google Search.

---

## 📁 Project Structure

```
lumique_aesthetic_clinic/
├── images/
│   ├── favicon.svg             # Crisp vector SVG favicon
│   └── logo.jpeg               # High-res clinic logo & social preview
├── css/
│   ├── style.css               # Master luxury design system & responsive styles
│   └── admin.css               # Admin dashboard layout & components
├── js/
│   ├── data.js                 # Central store, clinic dataset & LumiqueStore engine
│   ├── main.js                 # Global header scroll reveals, mobile drawer & fallbacks
│   ├── appointments.js         # Universal booking modal & video modal player
│   ├── treatments.js           # Treatments catalog, category filters & detail viewer
│   └── blog.js                 # Educational articles catalog & live search
├── index.html                  # Homepage (Hero, Stats, Doctor Highlight, CTA)
├── about.html                  # Doctor Profile & Clinic Philosophy
├── treatments.html             # Treatments Catalog with Photos & Videos
├── treatment-detail.html       # Single Treatment Deep-Dive & FAQs
├── blog.html                   # Educational Skincare Articles & Search
├── blog-post.html              # Article Reader
├── contact.html                # Clinic Location, Google Map & Consultation Form
├── admin.html                  # Interactive Clinic Management Portal
└── README.md                   # Project Documentation
```

---

## 🚀 Getting Started

### Option 1: Run via XAMPP / Apache (Recommended)
1. Clone or move the repository into your XAMPP `htdocs` directory:
   ```bash
   # Path example on macOS:
   /Applications/XAMPP/xamppfiles/htdocs/lumique_aesthetic_clinic
   
   # Path example on Windows:
   C:\xampp\htdocs\lumique_aesthetic_clinic
   ```
2. Start the Apache server in the XAMPP Control Panel.
3. Open your browser and navigate to:
   ```
   http://localhost/lumique_aesthetic_clinic/
   ```

### Option 2: Run via Live Server (VS Code)
1. Open the project folder in VS Code.
2. Right-click `index.html` and select **"Open with Live Server"**.

### Option 3: Direct Browser Open
Simply double-click `index.html` to open directly in Chrome, Safari, Firefox, or Edge.

---

## 📄 Pages Guide

| Page | URL | Description |
| :--- | :--- | :--- |
| **Home** | `index.html` | Hero banner, key statistics, procedure snapshot, doctor profile, testimonials & CTA |
| **About Doctor** | `about.html` | Dr. Alisha Vance profile, clinical credentials, philosophy & safety standards |
| **Treatments** | `treatments.html` | Categorized treatment catalog with photo/video galleries and "All" filter |
| **Treatment Detail**| `treatment-detail.html` | In-depth procedure steps, benefits, recovery timeline & interactive FAQs |
| **Educational Blog**| `blog.html` | Dermatological articles with instant keyword search & category filters |
| **Article Reader** | `blog-post.html` | Full markdown-rendered skincare articles and related reading |
| **Contact & Visit** | `contact.html` | Bandra West clinic details, Google Maps embed, operating hours & booking form |
| **Admin Portal** | `admin.html` | Booking requests dashboard, status management, and clinic data controls |

---

## 🏥 Clinic Information

- **Clinic Name**: Lumique Aesthetic Clinic
- **Lead Specialist**: Dr. Alisha Vance, MD (Dermatology, Venereology & Leprosy)
- **Address**: Ground Floor, Kenilworth Mall, Linking Road, Bandra West, Mumbai, Maharashtra 400050, India
- **Phone**: [+91 88795 50581](tel:+918879550581)
- **WhatsApp Concierge**: [+91 88795 50581](https://wa.me/918879550581)
- **Working Hours**: Monday – Saturday: 9:00 AM – 7:00 PM (Sunday Closed)

---

## 📜 License

&copy; 2026 Lumique Aesthetic Clinic. All rights reserved.
