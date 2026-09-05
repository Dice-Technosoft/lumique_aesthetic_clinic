@extends('layouts.admin')

@section('title', 'Administrator Profile')
@section('breadcrumb_parent', 'Account')
@section('breadcrumb_current', 'Administrator Profile')
@section('page_title', 'Administrator Profile & Security Credentials')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="admin-panel-card">
        <div class="panel-card-header" style="border-bottom: 1px solid var(--color-border); padding-bottom: 1.25rem; margin-bottom: 1.75rem;">
            <div>
                <h3>Administrator Account & Security Profile</h3>
                <small class="text-muted">Update your administrator name, profile photo, contact email, phone number, and security password</small>
            </div>
            @php
                $currentAvatar = $user->avatar_url ?? null;
                $currentAvatarSrc = !empty($currentAvatar) ? (str_starts_with($currentAvatar, 'http') || str_starts_with($currentAvatar, '/') ? $currentAvatar : asset('storage/' . $currentAvatar)) : null;
                $pName = $user->name ?? 'Admin';
                $pParts = preg_split('/\s+/', trim($pName));
                $userInitials = count($pParts) >= 2
                    ? strtoupper(substr($pParts[0], 0, 1) . substr(end($pParts), 0, 1))
                    : strtoupper(substr($pName, 0, 2));
            @endphp
            <div id="headerAvatarWrap">
                @if($currentAvatarSrc)
                    <img src="{{ $currentAvatarSrc }}" alt="{{ $user->name }}" class="user-avatar-circle" id="headerAvatarImg" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid var(--color-gold-bright);">
                @else
                    <div class="user-avatar-circle" id="headerAvatarInitials" style="width: 48px; height: 48px; font-size: 1.15rem;">
                        {{ $userInitials }}
                    </div>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="login-alert alert-success" style="margin-bottom: 1.5rem; background-color: rgba(46, 125, 50, 0.15); border: 1px solid rgba(46, 125, 50, 0.35); color: #2e7d32; padding: 0.85rem 1rem; border-radius: 6px;">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="login-alert alert-error" style="margin-bottom: 1.5rem; background-color: rgba(200, 16, 30, 0.1); border: 1px solid rgba(200, 16, 30, 0.3); color: var(--color-crimson); padding: 0.85rem 1rem; border-radius: 6px;">
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm" enctype="multipart/form-data" onsubmit="handleProfileSubmit(event)">
            @csrf

            <!-- Section 1: Profile Photo & Basic Information -->
            <div class="settings-group-section" style="margin-bottom: 2rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1.75rem;">
                <h4 class="settings-group-title" style="margin-bottom: 1.25rem; font-size: 0.85rem; color: var(--color-crimson); letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700;">PROFILE PHOTO & BASIC INFORMATION</h4>

                <!-- Profile Photo Upload -->
                <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem; background: var(--color-bg-light); padding: 1.25rem; border-radius: 10px; border: 1px solid var(--color-border);">
                    <div style="position: relative;">
                        @if($currentAvatarSrc)
                            <img src="{{ $currentAvatarSrc }}" alt="Profile Photo" id="avatarPreviewImg" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-gold-bright); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        @else
                            <div id="avatarPreviewInitials" class="user-avatar-circle" style="width: 80px; height: 80px; font-size: 1.85rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                {{ $userInitials }}
                            </div>
                            <img src="" alt="Profile Photo" id="avatarPreviewImg" style="display: none; width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-gold-bright); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <label for="prof_avatar" style="font-weight: 600; font-size: 0.88rem; display: block; margin-bottom: 0.35rem; color: var(--color-charcoal);">Upload Administrator Photo</label>
                        <input type="file" id="prof_avatar" name="avatar" accept="image/*" class="form-control" style="font-size: 0.85rem; padding: 0.45rem 0.65rem;" onchange="previewAvatar(event)">
                        <small class="text-muted" style="display: block; margin-top: 0.35rem;">Supports JPG, PNG, WEBP, GIF (Max 3MB). Appears on top-bar navigation.</small>
                    </div>
                </div>

                <div class="settings-inputs-grid">
                    <div class="form-group">
                        <label for="prof_name">Full Name *</label>
                        <input type="text" id="prof_name" name="name" value="{{ old('name', $user->name) }}" required class="form-control" placeholder="Dr. Alisha Vance">
                    </div>

                    <div class="form-group">
                        <label for="prof_email">Admin Email Address *</label>
                        <input type="email" id="prof_email" name="email" value="{{ old('email', $user->email) }}" required class="form-control" placeholder="admin@lumiqueclinic.com">
                        <small class="text-muted">Used for admin portal sign-in and system alerts</small>
                    </div>

                    <div class="form-group">
                        <label for="prof_phone">Contact Phone Number</label>
                        <input type="text" id="prof_phone" name="phone" value="{{ old('phone', $user->phone ?? '+91 88795 50581') }}" class="form-control" placeholder="+91 88795 50581">
                        <small class="text-muted">Primary phone for 2FA and internal contact</small>
                    </div>
                </div>
            </div>

            <!-- Section 2: Security & Password Update -->
            <div class="settings-group-section" style="margin-bottom: 2rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1.75rem;">
                <h4 class="settings-group-title" style="margin-bottom: 1rem; font-size: 0.85rem; color: var(--color-crimson); letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700;">SECURITY CREDENTIALS (PASSWORD UPDATE)</h4>
                <p class="text-muted" style="font-size: 0.82rem; margin-bottom: 1.25rem;">Leave password fields blank if you do not wish to change your current login password.</p>

                <div class="settings-inputs-grid">
                    <div class="form-group">
                        <label for="prof_password">New Password</label>
                        <input type="password" id="prof_password" name="password" minlength="6" class="form-control" placeholder="Minimum 6 characters">
                    </div>

                    <div class="form-group">
                        <label for="prof_password_confirmation">Confirm New Password</label>
                        <input type="password" id="prof_password_confirmation" name="password_confirmation" minlength="6" class="form-control" placeholder="Re-enter new password">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-gold btn-sm">Return to Dashboard</a>
                <button type="submit" id="saveProfileBtn" class="btn btn-gold btn-sm">Update Profile & Credentials</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('avatarPreviewImg');
                const previewInitials = document.getElementById('avatarPreviewInitials');
                if (previewImg) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                }
                if (previewInitials) {
                    previewInitials.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    }

    async function handleProfileSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('saveProfileBtn');
        btn.disabled = true;
        btn.innerText = 'Updating...';

        const form = document.getElementById('profileForm');
        const formData = new FormData(form);

        try {
            const res = await fetch('{{ route("admin.profile.update") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                showToast(data.message || 'Profile updated successfully in database!', 'success');
                // Clear password inputs
                document.getElementById('prof_password').value = '';
                document.getElementById('prof_password_confirmation').value = '';
                
                // Update avatar and username live in topbar
                if (data.data) {
                    const topUserName = document.querySelector('.user-name');
                    const topUserEmail = document.querySelector('.user-role');
                    const topAvatarCircle = document.querySelector('.admin-user-pill .user-avatar-circle');
                    if (topUserName) topUserName.innerText = data.data.name;
                    if (topUserEmail) topUserEmail.innerText = data.data.email;

                    const nameParts = data.data.name.trim().split(/\s+/);
                    const initials = nameParts.length >= 2 
                        ? (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase()
                        : data.data.name.substring(0, 2).toUpperCase();

                    const headerInitials = document.getElementById('headerAvatarInitials');
                    if (headerInitials) headerInitials.innerText = initials;
                    const previewInitials = document.getElementById('avatarPreviewInitials');
                    if (previewInitials) previewInitials.innerText = initials;

                    if (data.data.avatar_url) {
                        const avatarUrl = data.data.avatar_url.startsWith('http') || data.data.avatar_url.startsWith('/') ? data.data.avatar_url : '/storage/' + data.data.avatar_url;
                        if (topAvatarCircle) {
                            if (topAvatarCircle.tagName === 'IMG') {
                                topAvatarCircle.src = avatarUrl;
                            } else {
                                topAvatarCircle.outerHTML = `<img src="${avatarUrl}" alt="${data.data.name}" class="user-avatar-circle" style="object-fit: cover; border: 1.5px solid var(--color-gold-bright);">`;
                            }
                        }
                    } else if (topAvatarCircle && topAvatarCircle.tagName !== 'IMG') {
                        topAvatarCircle.innerText = initials;
                    }
                }
            } else {
                showToast(data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Validation failed'), 'error');
            }
        } catch(err) {
            showToast('Network error updating profile', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Update Profile & Credentials';
        }
    }
</script>
@endsection
