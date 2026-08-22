@extends('layouts.admin')

@section('title', 'About Page & Clinic Story CMS - Lumique Admin')
@section('breadcrumb_parent', 'Website CMS')
@section('breadcrumb_current', 'About & Clinic Story')
@section('page_title', 'About Page CMS & Clinic Story')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">
        <div>
            <h3 style="font-family: var(--font-serif); font-size: 1.3rem; margin-bottom: 0.25rem;">About Page & Clinic Story CMS</h3>
            <small class="text-muted">Manage the 2 clinic imagery assets, story narrative, mission, vision, and hero content</small>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('about') }}" target="_blank" class="btn btn-outline-gold btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none;">
                <span>View Live About Page</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            </a>
            <button type="button" class="btn btn-gold btn-sm" onclick="document.getElementById('aboutCmsForm').requestSubmit()">Save Changes</button>
        </div>
    </div>

    <form id="aboutCmsForm" onsubmit="handleAboutCmsSubmit(event)" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            
            <!-- LEFT COLUMN: The 2 Clinic Images with Live Visual Preview -->
            <div style="background: var(--color-ivory); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--color-border);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                    <span style="font-size: 1.25rem;">🖼️</span>
                    <h4 style="font-size: 1.05rem; margin: 0; color: var(--color-charcoal);">Clinic Story Images (2 Assets)</h4>
                </div>
                <p style="font-size: 0.8rem; color: var(--color-charcoal-muted); margin-bottom: 1.25rem;">
                    Upload high-resolution clinical ambience and doctor consultation photos. The live preview updates instantly.
                </p>

                <!-- Primary Image: Clinic Ambience (4:5 Portrait) -->
                <div class="form-group mb-4">
                    <label for="file_about_image_1" style="font-weight: 600; font-size: 0.875rem; color: var(--color-charcoal); display: block; margin-bottom: 0.35rem;">
                        1. Clinic Ambience Photo (Primary 4:5 Image) *
                    </label>
                    <input type="file" id="file_about_image_1" name="file_about_image_1" accept="image/*" class="form-control form-control-sm" onchange="previewAboutImage(this, 'live_about_img_1', 'setting_about_image_1')" style="margin-bottom: 0.5rem;">
                    <input type="text" id="setting_about_image_1" name="about_image_1" value="{{ $settings['about_image_1'] ?? '' }}" class="form-control form-control-sm" placeholder="Image URL or uploaded path" oninput="updateLiveImg('live_about_img_1', this.value)">
                </div>

                <!-- Secondary Image: Doctor Consultation (Floating) -->
                <div class="form-group mb-4">
                    <label for="file_about_image_2" style="font-weight: 600; font-size: 0.875rem; color: var(--color-charcoal); display: block; margin-bottom: 0.35rem;">
                        2. Doctor Consultation Photo (Secondary Floating Image) *
                    </label>
                    <input type="file" id="file_about_image_2" name="file_about_image_2" accept="image/*" class="form-control form-control-sm" onchange="previewAboutImage(this, 'live_about_img_2', 'setting_about_image_2')" style="margin-bottom: 0.5rem;">
                    <input type="text" id="setting_about_image_2" name="about_image_2" value="{{ $settings['about_image_2'] ?? '' }}" class="form-control form-control-sm" placeholder="Image URL or uploaded path" oninput="updateLiveImg('live_about_img_2', this.value)">
                </div>

                <!-- Live Visual Arrangement Preview -->
                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(0,0,0,0.08);">
                    <small style="font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-crimson); font-size: 0.72rem; display: block; margin-bottom: 0.75rem;">
                        LIVE LAYOUT PREVIEW (AS SEEN ON WEBSITE)
                    </small>
                    <div style="position: relative; width: 100%; max-width: 320px; margin: 0 auto; padding-bottom: 2rem;">
                        <div style="border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 2px solid #fff;">
                            <img id="live_about_img_1" 
                                 src="{{ $settings['about_image_1'] ?? 'https://images.pexels.com/photos/11024139/pexels-photo-11024139.jpeg?auto=compress&cs=tinysrgb&w=800' }}" 
                                 alt="Primary Ambience" 
                                 style="width: 100%; aspect-ratio: 4/5; object-fit: cover; display: block;">
                        </div>
                        <div style="position: absolute; bottom: 0; right: -15px; width: 130px; height: 130px; border: 4px solid #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 12px 28px rgba(0,0,0,0.22); background: #000;">
                            <img id="live_about_img_2" 
                                 src="{{ $settings['about_image_2'] ?? 'https://images.pexels.com/photos/7108264/pexels-photo-7108264.jpeg?auto=compress&cs=tinysrgb&w=400' }}" 
                                 alt="Secondary Consultation" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Story Narrative, Mission & Vision -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Section 1: Our Story Narrative -->
                <div style="background: #ffffff; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--color-border);">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                        <span style="font-size: 1.25rem;">✨</span>
                        <h4 style="font-size: 1.05rem; margin: 0; color: var(--color-charcoal);">Our Story & Clinic Narrative</h4>
                    </div>

                    <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label for="setting_about_story_subtitle">Story Subtitle</label>
                            <input type="text" id="setting_about_story_subtitle" name="about_story_subtitle" value="{{ $settings['about_story_subtitle'] ?? 'Our Story' }}" class="form-control" placeholder="e.g. Our Story">
                        </div>
                        <div class="form-group" style="flex: 1.5;">
                            <label for="setting_about_story_title">Story Main Heading *</label>
                            <input type="text" id="setting_about_story_title" name="about_story_title" value="{{ $settings['about_story_title'] ?? 'Aesthetic Medicine Refined' }}" required class="form-control" placeholder="e.g. Aesthetic Medicine Refined">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="setting_about_story_p1">Story Paragraph 1 *</label>
                        <textarea id="setting_about_story_p1" name="about_story_p1" rows="3" required class="form-control" placeholder="We believe that true aesthetic confidence does not stem from dramatic alterations...">{{ $settings['about_story_p1'] ?? 'We believe that true aesthetic confidence does not stem from dramatic alterations, but rather from celebrating and rejuvenating your authentic self.' }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label for="setting_about_story_p2">Story Paragraph 2 *</label>
                        <textarea id="setting_about_story_p2" name="about_story_p2" rows="3" required class="form-control" placeholder="From multi-wavelength laser technologies to autologous hair therapy...">{{ $settings['about_story_p2'] ?? 'From multi-wavelength laser technologies to autologous hair therapy and gentle facial contouring, every protocol is backed by strict medical guidelines, cutting-edge equipment, and continuous patient dialogue.' }}</textarea>
                    </div>
                </div>

                <!-- Section 2: Mission & Vision Cards -->
                <div style="background: #ffffff; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--color-border);">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                        <span style="font-size: 1.25rem;">🎯</span>
                        <h4 style="font-size: 1.05rem; margin: 0; color: var(--color-charcoal);">Our Mission & Vision Cards</h4>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <!-- Mission -->
                        <div style="background: var(--color-ivory); padding: 1rem; border-radius: 6px; border: 1px solid var(--color-border);">
                            <div class="form-group mb-2">
                                <label for="setting_about_mission_title">Mission Title</label>
                                <input type="text" id="setting_about_mission_title" name="about_mission_title" value="{{ $settings['about_mission_title'] ?? 'Our Mission' }}" class="form-control form-control-sm">
                            </div>
                            <div class="form-group mb-0">
                                <label for="setting_about_mission_desc">Mission Statement</label>
                                <textarea id="setting_about_mission_desc" name="about_mission_desc" rows="3" class="form-control form-control-sm">{{ $settings['about_mission_desc'] ?? 'Deliver safe, natural, and personalized dermatological outcomes.' }}</textarea>
                            </div>
                        </div>

                        <!-- Vision -->
                        <div style="background: var(--color-ivory); padding: 1rem; border-radius: 6px; border: 1px solid var(--color-border);">
                            <div class="form-group mb-2">
                                <label for="setting_about_vision_title">Vision Title</label>
                                <input type="text" id="setting_about_vision_title" name="about_vision_title" value="{{ $settings['about_vision_title'] ?? 'Our Vision' }}" class="form-control form-control-sm">
                            </div>
                            <div class="form-group mb-0">
                                <label for="setting_about_vision_desc">Vision Statement</label>
                                <textarea id="setting_about_vision_desc" name="about_vision_desc" rows="3" class="form-control form-control-sm">{{ $settings['about_vision_desc'] ?? 'Be the most trusted, evidence-based aesthetic clinic in Mumbai.' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Hero Banner Narrative -->
                <div style="background: #ffffff; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--color-border);">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                        <span style="font-size: 1.25rem;">🌟</span>
                        <h4 style="font-size: 1.05rem; margin: 0; color: var(--color-charcoal);">About Hero Header Banner</h4>
                    </div>

                    <div class="form-group mb-3">
                        <label for="setting_about_hero_title">Hero Main Title</label>
                        <input type="text" id="setting_about_hero_title" name="about_hero_title" value="{{ $settings['about_hero_title'] ?? 'A Clinic Built on Trust, Medical Science & Compassionate Artistry' }}" class="form-control">
                    </div>

                    <div class="form-group mb-0">
                        <label for="setting_about_hero_description">Hero Narrative Description</label>
                        <textarea id="setting_about_hero_description" name="about_hero_description" rows="2" class="form-control">{{ $settings['about_hero_description'] ?? 'Lumique Aesthetic Clinic was founded with a singular vision: to bring together board-certified dermatological science with refined aesthetic artistry in an uplifting, patient-first sanctuary in Bandra West, Mumbai.' }}</textarea>
                    </div>
                </div>

            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.25rem; border-top: 1px solid var(--color-border);">
            <button type="submit" id="saveAboutBtn" class="btn btn-gold">
                <span>Save Story & Clinic Media</span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewAboutImage(input, previewImgId, hiddenInputId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewImgId);
                if (img) img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateLiveImg(previewImgId, url) {
        if (url) {
            const img = document.getElementById(previewImgId);
            if (img) img.src = url;
        }
    }

    async function handleAboutCmsSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveAboutBtn');
        btn.disabled = true;
        btn.innerHTML = '<span>Saving...</span>';

        const formData = new FormData(document.getElementById('aboutCmsForm'));

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
                showToast('About Page Story, Images & Vision saved successfully!', 'success');
                setTimeout(() => { location.reload(); }, 800);
            } else {
                showToast(data.message || 'Error saving about page settings', 'error');
            }
        } catch(err) {
            console.error(err);
            showToast('Network error saving settings', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span>Save Story & Clinic Media</span>';
        }
    }
</script>
@endsection
