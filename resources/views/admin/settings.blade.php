@extends('layouts.admin')

@section('title', 'Site & System Settings')
@section('breadcrumb_parent', 'System Config')
@section('breadcrumb_current', 'Clinic & Brand Settings')
@section('page_title', 'Clinic Site & System Configurations')

@section('content')
<div class="admin-panel-card">
    <form onsubmit="handleSettingsSubmit(event)" id="settingsForm" enctype="multipart/form-data">
        @foreach($groupedSettings as $groupName => $settingsInGroup)
        @if(in_array(strtolower($groupName), ['theme', 'seo', 'email']))
            @continue
        @endif
        <div class="settings-group-section">
            <h3 class="settings-group-title">{{ $groupName === 'about_page' ? 'ABOUT PAGE CMS & CLINIC STORY' : strtoupper($groupName) . ' CONFIGURATION' }}</h3>
            
            <div class="settings-inputs-grid">
                @foreach($settingsInGroup as $setting)
                @if(strtolower($groupName) === 'contact' && !in_array($setting->key, ['email', 'phone', 'whatsapp', 'address', 'working_hours']))
                    @continue
                @endif
                <div class="form-group" style="{{ in_array($setting->key, ['about_hero_description', 'about_story_p1', 'about_story_p2']) ? 'grid-column: span 2;' : '' }}">
                    <label for="setting_{{ $setting->key }}">
                        @if($setting->key === 'email')
                            Clinic Contact Email
                        @elseif($setting->key === 'phone')
                            Phone Number
                        @elseif($setting->key === 'whatsapp')
                            WhatsApp Number
                        @elseif($setting->key === 'address')
                            Clinic Address
                        @elseif($setting->key === 'working_hours')
                            Working Hours
                        @elseif($setting->key === 'site_name')
                            Clinic Name
                        @elseif($setting->key === 'tagline')
                            Clinic Tagline
                        @elseif($setting->key === 'logo_url')
                            Clinic Brand Logo
                        @elseif($setting->key === 'favicon_url')
                            Favicon Icon
                        @elseif($setting->key === 'about_hero_title')
                            About Hero Main Title
                        @elseif($setting->key === 'about_hero_description')
                            About Hero Narrative Description
                        @elseif($setting->key === 'about_story_subtitle')
                            Clinic Story Subtitle
                        @elseif($setting->key === 'about_story_title')
                            Clinic Story Main Heading
                        @elseif($setting->key === 'about_story_p1')
                            Clinic Story (Paragraph 1)
                        @elseif($setting->key === 'about_story_p2')
                            Clinic Story (Paragraph 2)
                        @elseif($setting->key === 'about_image_1')
                            Clinic Ambience Photo (Primary 4:5 Portrait)
                        @elseif($setting->key === 'about_image_2')
                            Doctor Consultation Photo (Secondary Floating)
                        @elseif($setting->key === 'about_mission_title')
                            Mission Title
                        @elseif($setting->key === 'about_mission_desc')
                            Mission Description
                        @elseif($setting->key === 'about_vision_title')
                            Vision Title
                        @elseif($setting->key === 'about_vision_desc')
                            Vision Description
                        @else
                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                        @endif
                    </label>
                    
                    @if($setting->type === 'image' || in_array($setting->key, ['logo_url', 'favicon_url']))
                        <div style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 1rem; background: var(--color-ivory); padding: 0.75rem; border-radius: 6px; border: 1px solid var(--color-border);">
                            <img id="preview_{{ $setting->key }}" 
                                 src="{{ !empty($setting->value) ? (str_starts_with($setting->value, 'http') || str_starts_with($setting->value, '/') ? $setting->value : asset('storage/' . $setting->value)) : '/images/logo.jpeg' }}" 
                                 alt="Preview" 
                                 style="max-height: 48px; max-width: 140px; object-fit: contain; background: #ffffff; padding: 4px; border-radius: 4px; border: 1px solid #eee;">
                            <div style="flex: 1;">
                                <input type="file" id="file_{{ $setting->key }}" name="file_{{ $setting->key }}" accept="image/*,.ico,.svg" class="form-control form-control-sm" onchange="previewSettingImage(this, 'preview_{{ $setting->key }}')">
                            </div>
                        </div>
                        <input type="hidden" id="setting_{{ $setting->key }}" name="{{ $setting->key }}" value="{{ $setting->value }}">
                    @elseif($setting->type === 'text')
                        <textarea id="setting_{{ $setting->key }}" name="{{ $setting->key }}" rows="3" class="form-control">{{ $setting->value }}</textarea>
                    @elseif($setting->type === 'boolean')
                        <select id="setting_{{ $setting->key }}" name="{{ $setting->key }}" class="form-control">
                            <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Disabled</option>
                        </select>
                    @else
                        <input type="text" id="setting_{{ $setting->key }}" name="{{ $setting->key }}" value="{{ $setting->value }}" class="form-control">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="settings-save-bar" style="display: flex; align-items: center; justify-content: flex-end; gap: 1rem;">
            <button type="submit" id="saveSettingsBtn" class="btn btn-gold btn-sm">Save Settings</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewSettingImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                if (img) img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function handleSettingsSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveSettingsBtn');
        btn.disabled = true;
        btn.innerText = 'Saving...';

        const formData = new FormData(document.getElementById('settingsForm'));

        try {
            const res = await fetch('/api/v1/admin/settings', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                showToast('Settings saved successfully!', 'success');
                setTimeout(() => { location.reload(); }, 1000);
            } else {
                showToast(data.message || 'Error saving settings', 'error');
            }
        } catch(err) {
            showToast('Network error saving settings', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Save Settings';
        }
    }
</script>
@endsection
