<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Appointment Request</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f6f3f0; margin: 0; padding: 24px; color: #222; }
        .card { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.07); border: 1px solid #e8e0d8; }
        .header { background: #14080B; color: #D4AF37; padding: 24px 20px; text-align: center; }
        .header h2 { margin: 0 0 6px 0; font-size: 20px; letter-spacing: 1.5px; font-weight: 700; }
        .badge { display: inline-block; background: #7A1C2E; color: #ffffff; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; letter-spacing: 0.5px; }
        .content { padding: 24px 28px; }
        .field { margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f2ece8; }
        .field-label { font-size: 11px; text-transform: uppercase; color: #7A1C2E; font-weight: 700; margin-bottom: 3px; letter-spacing: 0.5px; }
        .field-value { font-size: 15px; color: #2a2a2a; }
        .date-box { background: #faf4f5; padding: 14px 16px; border-radius: 8px; border: 1px solid #ebd4d9; margin: 16px 0; }
        .message-box { background: #faf5f5; padding: 12px 14px; border-radius: 6px; border-left: 3px solid #7A1C2E; font-style: italic; color: #444; font-size: 14px; margin-top: 4px; }
        .wa-btn-wrapper { text-align: center; margin: 24px 0 10px 0; }
        .wa-btn { display: inline-block; background-color: #25D366; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 30px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.35); }
        .footer { background: #faf7f5; padding: 14px; text-align: center; font-size: 11px; color: #888; border-top: 1px solid #ede4dc; }
    </style>
</head>
<body>
    @php
        $rawPhone = preg_replace('/[^0-9]/', '', $inquiry->phone);
        if (strlen($rawPhone) === 10) {
            $whatsappPhone = '91' . $rawPhone;
        } else {
            $whatsappPhone = $rawPhone;
        }
        $slotDate = $inquiry->preferred_date ? $inquiry->preferred_date->format('M d, Y') : 'Date Pending';
        $slotTime = $inquiry->preferred_time ? ' at ' . $inquiry->preferred_time : '';
        $whatsappMsg = urlencode("Hello " . $inquiry->name . ", this is Lumique Aesthetic Clinic confirming your appointment for " . ($inquiry->service_name ?: 'consultation') . " scheduled on " . $slotDate . $slotTime . ".");
    @endphp

    <div class="card">
        <div class="header">
            <h2>LUMIQUE AESTHETIC CLINIC</h2>
            <div class="badge">NEW APPOINTMENT BOOKING</div>
        </div>

        <div class="content">
            <div class="field">
                <div class="field-label">Patient Name</div>
                <div class="field-value"><strong>{{ $inquiry->name }}</strong></div>
            </div>

            <div class="field">
                <div class="field-label">Contact Information</div>
                <div class="field-value">
                    📞 Phone: <a href="tel:{{ $inquiry->phone }}" style="color: #222; text-decoration: none; font-weight: 600;">{{ $inquiry->phone }}</a><br>
                    ✉️ Email: <a href="mailto:{{ $inquiry->email }}" style="color: #7A1C2E; text-decoration: none;">{{ $inquiry->email }}</a>
                </div>
            </div>

            <div class="field">
                <div class="field-label">Requested Procedure / Treatment</div>
                <div class="field-value" style="color: #7A1C2E; font-weight: 600;">{{ $inquiry->service_name ?: 'Comprehensive Skin & Aesthetic Consultation' }}</div>
            </div>

            <div class="date-box">
                <div class="field-label">Appointment Date & Time Slot</div>
                <div class="field-value" style="font-size: 15px; color: #7A1C2E; font-weight: 700;">
                    📅 {{ $inquiry->preferred_date ? $inquiry->preferred_date->format('l, F d, Y') : 'Earliest Available' }} 
                    @if($inquiry->preferred_time)
                        &bull; ⏰ {{ $inquiry->preferred_time }}
                    @endif
                </div>
            </div>

            @if($inquiry->message)
            <div class="field" style="border-bottom: none;">
                <div class="field-label">Patient Intake Notes</div>
                <div class="message-box">"{{ $inquiry->message }}"</div>
            </div>
            @endif

            <!-- Direct Connect on WhatsApp Button -->
            <div class="wa-btn-wrapper">
                <a href="https://wa.me/{{ $whatsappPhone }}?text={{ $whatsappMsg }}" target="_blank" class="wa-btn">
                    💬 Connect on WhatsApp
                </a>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Lumique Aesthetic Clinic &bull; Bandra West, Mumbai &bull; Internal CRM Notification
        </div>
    </div>
</body>
</html>
