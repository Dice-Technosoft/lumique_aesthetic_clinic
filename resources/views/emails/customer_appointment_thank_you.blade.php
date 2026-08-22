@php
    $emailLogo = !empty($settings['logo_url']) ? (str_starts_with($settings['logo_url'], 'http') ? $settings['logo_url'] : url($settings['logo_url'])) : null;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Request Received - {{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}</title>
    <style>
        body { font-family: 'Playfair Display', Georgia, 'Segoe UI', serif; background-color: #f7f4f2; margin: 0; padding: 20px; color: #222; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-top: 5px solid #D4AF37; }
        .header { background: #14080B; color: #D4AF37; padding: 35px 25px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: normal; letter-spacing: 2px; }
        .header p { margin: 8px 0 0; font-size: 13px; color: #e5d7cb; font-family: sans-serif; letter-spacing: 1px; text-transform: uppercase; }
        .content { padding: 35px; font-family: 'Segoe UI', sans-serif; font-size: 15px; line-height: 1.7; color: #3a3a3a; }
        .details-box { background: #faf6f5; padding: 20px; border-radius: 8px; border: 1px solid #ebdcd5; margin: 25px 0; }
        .details-row { display: flex; margin-bottom: 8px; font-size: 14px; }
        .details-label { width: 140px; color: #7A1C2E; font-weight: 600; }
        .notice-box { background: #fff8eb; border-left: 4px solid #D4AF37; padding: 15px; border-radius: 4px; font-size: 13.5px; color: #6e5425; margin: 20px 0; }
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
            <p>Thank you for requesting a consultation at <strong>Lumique Aesthetic Clinic</strong>. We have received your booking details successfully.</p>
            
            <div class="details-box">
                <div style="font-weight: bold; color: #7A1C2E; margin-bottom: 12px; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Summary of Requested Session</div>
                <div><strong>Procedure / Interest:</strong> {{ $inquiry->service_name ?: 'Aesthetic Consultation' }}</div>
                <div><strong>Requested Date:</strong> {{ $inquiry->preferred_date ? $inquiry->preferred_date->format('l, F d, Y') : 'Earliest Available' }}</div>
                <div><strong>Preferred Time Slot:</strong> {{ $inquiry->preferred_time ?: 'Flexible' }}</div>
                <div><strong>Clinic Location:</strong> Kenilworth Mall, Linking Road, Bandra West, Mumbai</div>
            </div>

            <div class="notice-box">
                <strong>Please Note:</strong> This email confirms that our front desk has received your request. Our medical concierge will call or message you on WhatsApp shortly to confirm slot availability and provide pre-treatment recommendations.
            </div>

            <p>If you wish to make changes or connect directly with our front desk:</p>
            <div class="btn-holder">
                <a href="https://wa.me/918879550581?text=Hello%20Lumique%20Clinic,%20I%20have%20requested%20an%20appointment%20for%20{{ urlencode($inquiry->name) }}." class="btn">Chat with Concierge</a>
            </div>

            <p style="margin-top: 25px;">We look forward to welcoming you to our clinic sanctuary.<br>
            <strong>Dr. Alisha Vance & The Lumique Medical Team</strong></p>
        </div>
        <div class="footer">
            Ground Floor, Kenilworth Mall, Linking Road, Bandra West, Mumbai 400050<br>
            Phone: +91 88795 50581 &bull; Email: info@lumiqueclinic.com<br>
            &copy; {{ date('Y') }} Lumique Aesthetic Clinic. All rights reserved.
        </div>
    </div>
</body>
</html>
