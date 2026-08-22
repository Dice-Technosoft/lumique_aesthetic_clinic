<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Website Inquiry</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f4f2; margin: 0; padding: 20px; color: #222; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-top: 5px solid #7A1C2E; }
        .header { background: #14080B; color: #D4AF37; padding: 25px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; letter-spacing: 1px; }
        .content { padding: 30px; }
        .field { margin-bottom: 15px; border-bottom: 1px solid #f0eae7; padding-bottom: 10px; }
        .field-label { font-size: 12px; text-transform: uppercase; color: #7A1C2E; font-weight: bold; margin-bottom: 3px; }
        .field-value { font-size: 15px; color: #333; }
        .message-box { background: #faf6f5; padding: 15px; border-radius: 6px; border-left: 3px solid #7A1C2E; font-style: italic; }
        .footer { background: #faf6f5; padding: 15px; text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>LUMIQUE AESTHETIC CLINIC</h2>
            <p style="margin: 5px 0 0; font-size: 13px; color: #e0d0c0;">New Website Inquiry Notification</p>
        </div>
        <div class="content">
            <div class="field">
                <div class="field-label">Patient Name</div>
                <div class="field-value">{{ $inquiry->name }}</div>
            </div>
            <div class="field">
                <div class="field-label">Contact Details</div>
                <div class="field-value">
                    Email: <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a><br>
                    Phone: <a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a>
                </div>
            </div>
            @if($inquiry->subject)
            <div class="field">
                <div class="field-label">Subject</div>
                <div class="field-value">{{ $inquiry->subject }}</div>
            </div>
            @endif
            @if($inquiry->service_name)
            <div class="field">
                <div class="field-label">Interested Service / Treatment</div>
                <div class="field-value">{{ $inquiry->service_name }}</div>
            </div>
            @endif
            @if($inquiry->message)
            <div class="field">
                <div class="field-label">Patient Message</div>
                <div class="message-box">{{ $inquiry->message }}</div>
            </div>
            @endif
            <div class="field">
                <div class="field-label">Submission Details</div>
                <div class="field-value">Source: {{ $inquiry->source }} &bull; {{ $inquiry->created_at->format('M d, Y h:i A') }}</div>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Lumique Aesthetic Clinic &bull; Bandra West, Mumbai &bull; Internal CRM Alert
        </div>
    </div>
</body>
</html>
