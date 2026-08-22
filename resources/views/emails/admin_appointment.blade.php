<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Appointment Request</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f4f2; margin: 0; padding: 20px; color: #222; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-top: 5px solid #D4AF37; }
        .header { background: #14080B; color: #D4AF37; padding: 25px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; letter-spacing: 1px; }
        .badge { display: inline-block; background: #7A1C2E; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-top: 8px; }
        .content { padding: 30px; }
        .field { margin-bottom: 15px; border-bottom: 1px solid #f0eae7; padding-bottom: 10px; }
        .field-label { font-size: 12px; text-transform: uppercase; color: #7A1C2E; font-weight: bold; margin-bottom: 3px; }
        .field-value { font-size: 15px; color: #333; }
        .date-box { background: #faf6f5; padding: 15px; border-radius: 8px; border: 1px solid #ebdcd5; margin: 15px 0; }
        .footer { background: #faf6f5; padding: 15px; text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>LUMIQUE AESTHETIC CLINIC</h2>
            <div class="badge">NEW APPOINTMENT REQUEST</div>
        </div>
        <div class="content">
            <div class="field">
                <div class="field-label">Patient Name</div>
                <div class="field-value"><strong>{{ $inquiry->name }}</strong></div>
            </div>
            <div class="field">
                <div class="field-label">Contact Information</div>
                <div class="field-value">
                    Email: <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a><br>
                    Phone: <a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a>
                </div>
            </div>
            <div class="field">
                <div class="field-label">Requested Procedure / Category</div>
                <div class="field-value"><strong>{{ $inquiry->service_name ?: 'Comprehensive Skin & Aesthetic Consultation' }}</strong></div>
            </div>
            <div class="date-box">
                <div class="field-label">Requested Date & Time</div>
                <div class="field-value" style="font-size: 16px; color: #7A1C2E; font-weight: bold;">
                    📅 {{ $inquiry->preferred_date ? $inquiry->preferred_date->format('l, F d, Y') : 'Earliest Available' }} &bull; ⏰ {{ $inquiry->preferred_time ?: 'Flexible Slot' }}
                </div>
            </div>
            @if($inquiry->message)
            <div class="field">
                <div class="field-label">Additional Patient Notes</div>
                <div class="field-value" style="font-style: italic;">{{ $inquiry->message }}</div>
            </div>
            @endif
            <div class="field">
                <div class="field-label">System Source</div>
                <div class="field-value">{{ $inquiry->source }} &bull; IP: {{ $inquiry->ip_address }}</div>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Lumique Aesthetic Clinic &bull; Bandra West, Mumbai &bull; Internal CRM Notification
        </div>
    </div>
</body>
</html>
