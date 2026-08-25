<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patient Follow-up Scheduled</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f4f2; margin: 0; padding: 20px; color: #222; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-top: 5px solid #8B1538; }
        .header { background: #14080B; color: #D4AF37; padding: 25px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; letter-spacing: 1px; }
        .badge { display: inline-block; background: #8B1538; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-top: 8px; }
        .content { padding: 30px; }
        .field { margin-bottom: 15px; border-bottom: 1px solid #f0eae7; padding-bottom: 10px; }
        .field-label { font-size: 12px; text-transform: uppercase; color: #8B1538; font-weight: bold; margin-bottom: 3px; }
        .field-value { font-size: 15px; color: #333; }
        .date-box { background: #faf4f5; padding: 15px; border-radius: 8px; border: 1px solid #ebd4d9; margin: 15px 0; }
        .footer { background: #faf6f5; padding: 15px; text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>LUMIQUE AESTHETIC CLINIC</h2>
            <div class="badge">PATIENT FOLLOW-UP SCHEDULED</div>
        </div>
        <div class="content">
            <div class="field">
                <div class="field-label">Patient / Customer Name</div>
                <div class="field-value"><strong>{{ $lead->name }}</strong></div>
            </div>
            <div class="field">
                <div class="field-label">Contact Information</div>
                <div class="field-value">
                    Email: <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a><br>
                    Phone: <a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>
                </div>
            </div>
            <div class="field">
                <div class="field-label">Treatment / Procedure</div>
                <div class="field-value"><strong>{{ $lead->service_name ?: ($lead->service->title ?? 'General Consultation') }}</strong></div>
            </div>
            <div class="date-box">
                <div class="field-label">Follow-Up Scheduled Date & Time</div>
                <div class="field-value" style="font-size: 16px; color: #8B1538; font-weight: bold;">
                    📅 {{ $followUp->follow_up_date ? $followUp->follow_up_date->format('l, F d, Y') : 'Date Pending' }} 
                    @if($followUp->follow_up_time)
                        &bull; ⏰ {{ $followUp->follow_up_time }}
                    @endif
                </div>
            </div>
            @if($followUp->note)
            <div class="field">
                <div class="field-label">Follow-Up Objective / Note</div>
                <div class="field-value" style="font-style: italic; background: #fffdfa; padding: 8px 12px; border-left: 3px solid #D4AF37; border-radius: 4px;">
                    "{{ $followUp->note }}"
                </div>
            </div>
            @endif
            <div class="field">
                <div class="field-label">CRM Record ID</div>
                <div class="field-value">Appointment #{{ $lead->id }} (Follow-up #{{ $followUp->id }})</div>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Lumique Aesthetic Clinic &bull; Bandra West, Mumbai &bull; Internal CRM Notification
        </div>
    </div>
</body>
</html>
