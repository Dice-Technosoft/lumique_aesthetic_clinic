@php
    $emailLogo = !empty($settings['logo_url']) ? (str_starts_with($settings['logo_url'], 'http') ? $settings['logo_url'] : url($settings['logo_url'])) : null;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thank You - {{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}</title>
    <style>
        body { font-family: 'Playfair Display', Georgia, 'Segoe UI', serif; background-color: #f7f4f2; margin: 0; padding: 20px; color: #222; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-top: 5px solid #7A1C2E; }
        .header { background: #14080B; color: #D4AF37; padding: 35px 25px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: normal; letter-spacing: 2px; }
        .header p { margin: 8px 0 0; font-size: 13px; color: #e5d7cb; font-family: sans-serif; letter-spacing: 1px; text-transform: uppercase; }
        .content { padding: 35px; font-family: 'Segoe UI', sans-serif; font-size: 15px; line-height: 1.7; color: #3a3a3a; }
        .highlight { color: #7A1C2E; font-weight: 600; }
        .quote-box { background: #faf6f5; padding: 20px; border-radius: 8px; border-left: 4px solid #D4AF37; margin: 25px 0; font-style: italic; }
        .btn-holder { text-align: center; margin: 30px 0; }
        .btn { display: inline-block; background: #7A1C2E; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 30px; font-weight: 600; font-size: 14px; letter-spacing: 0.5px; }
        .footer { background: #14080B; color: #a89f91; padding: 25px; text-align: center; font-family: sans-serif; font-size: 12px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @if($emailLogo)
                <img src="{{ $emailLogo }}" alt="{{ $settings['site_name'] ?? 'Lumique' }}" style="height: 44px; width: auto; margin-bottom: 8px; border-radius: 4px;">
            @endif
            <h1>{{ strtoupper($settings['site_name'] ?? 'LUMIQUE') }}</h1>
            <p>{{ $settings['tagline'] ?? 'Aesthetic Clinic • Bandra West, Mumbai' }}</p>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $inquiry->name }}</strong>,</p>
            <p>Thank you for reaching out to <strong>Lumique Aesthetic Clinic</strong>. We have received your inquiry regarding our medical aesthetics and dermatological care.</p>
            
            <p>Our medical concierge and patient care team are reviewing your message and will reach out to you within our operational hours to assist with answers, treatment guidance, or personalized consultation scheduling.</p>

            <div class="quote-box">
                "Our philosophy is grounded in delivering authentic, graceful results tailored to your unique anatomical harmony."
                <div style="margin-top: 8px; font-size: 13px; font-weight: bold; color: #7A1C2E;">— Dr. Alisha Vance, MD, DVD</div>
            </div>

            <p>If you require immediate assistance or wish to speak with our concierge directly:</p>
            <div class="btn-holder">
                <a href="https://wa.me/918879550581?text=Hello%20Lumique%20Clinic,%20I%20recently%20submitted%20an%20inquiry." class="btn">Connect on WhatsApp</a>
            </div>

            <p style="margin-top: 25px;">Warm regards,<br>
            <strong>The Patient Concierge Team</strong><br>
            Lumique Aesthetic Clinic, Mumbai</p>
        </div>
        <div class="footer">
            Ground Floor, Kenilworth Mall, Linking Road, Bandra West, Mumbai 400050<br>
            Phone: +91 88795 50581 &bull; Email: info@lumiqueclinic.com<br>
            &copy; {{ date('Y') }} Lumique Aesthetic Clinic. All rights reserved.
        </div>
    </div>
</body>
</html>
