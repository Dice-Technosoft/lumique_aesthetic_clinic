@php
    $siteFavicon = !empty($settings['favicon_url']) ? (str_starts_with($settings['favicon_url'], 'http') || str_starts_with($settings['favicon_url'], '/') ? $settings['favicon_url'] : asset('storage/' . $settings['favicon_url'])) : '/images/favicon.png';
    $siteLogo = !empty($settings['logo_url']) ? (str_starts_with($settings['logo_url'], 'http') || str_starts_with($settings['logo_url'], '/') ? $settings['logo_url'] : asset('storage/' . $settings['logo_url'])) : '/images/logo.jpeg';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Portal') | {{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}</title>
    
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" href="{{ $siteFavicon }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/css/admin.css">
    @yield('styles')
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Collapsible Admin Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                    <img src="{{ $siteLogo }}" alt="{{ $settings['site_name'] ?? 'Lumique' }}" class="admin-brand-logo">
                    <div class="brand-text-group">
                        <span class="brand-main">LUMIQUE</span>
                        <span class="brand-sub">ADMIN PORTAL</span>
                    </div>
                </a>
                <button class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle Sidebar" title="Collapse / Expand Sidebar">
                    <span class="toggle-icon-arrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                    </span>
                </button>
            </div>

            <div class="sidebar-menu-scroll">
                <ul class="sidebar-nav">
                    <li class="nav-category">MAIN</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}" data-title="Dashboard">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
                            </span>
                            <span class="nav-label">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-category">CLINICAL & SERVICES</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" data-title="Treatment Categories">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                            </span>
                            <span class="nav-label">Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.services') }}" class="nav-link {{ request()->routeIs('admin.services*') ? 'active' : '' }}" data-title="Treatments & Services">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><path d="M12 2l2.4 7.4 7.6 2.6-7.6 2.6L12 22l-2.4-7.4L2 12l7.6-2.6z"></path></svg>
                            </span>
                            <span class="nav-label">Treatments & Services</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.doctors') }}" class="nav-link {{ request()->routeIs('admin.doctors*') ? 'active' : '' }}" data-title="Specialists & Doctors">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"></path><path d="M8 15v1a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6v-4"></path><circle cx="20" cy="10" r="2"></circle></svg>
                            </span>
                            <span class="nav-label">Specialists & Doctors</span>
                        </a>
                    </li>

                    <li class="nav-category">PATIENT CRM</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.inquiries') }}" class="nav-link {{ request()->routeIs('admin.inquiries*') ? 'active' : '' }}" data-title="Contact Inquiries">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </span>
                            <span class="nav-label">Inquiries</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.leads') }}" class="nav-link {{ request()->routeIs('admin.leads*') ? 'active' : '' }}" data-title="Appointments">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"></path></svg>
                            </span>
                            <span class="nav-label">Appointments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.testimonials') }}" class="nav-link {{ request()->routeIs('admin.testimonials*') ? 'active' : '' }}" data-title="Patient Testimonials">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            </span>
                            <span class="nav-label">Patient Stories</span>
                        </a>
                    </li>

                    <li class="nav-category">MEDIA & CONTENT CMS</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.gallery') }}" class="nav-link {{ request()->routeIs('admin.gallery*') ? 'active' : '' }}" data-title="Results Gallery">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            </span>
                            <span class="nav-label">Results Gallery</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.videos') }}" class="nav-link {{ request()->routeIs('admin.videos*') ? 'active' : '' }}" data-title="Videos & Media">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                            </span>
                            <span class="nav-label">Videos & Media</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.blogs') }}" class="nav-link {{ request()->routeIs('admin.blogs*') ? 'active' : '' }}" data-title="Blog & Articles">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            </span>
                            <span class="nav-label">Blog & Journal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.about') }}" class="nav-link {{ request()->routeIs('admin.about*') ? 'active' : '' }}" data-title="About & Story CMS">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            </span>
                            <span class="nav-label">About & Story CMS</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.faqs') }}" class="nav-link {{ request()->routeIs('admin.faqs*') ? 'active' : '' }}" data-title="FAQs Management">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            </span>
                            <span class="nav-label">FAQs Management</span>
                        </a>
                    </li>

                    <li class="nav-category">SYSTEM CONFIG</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}" data-title="Admin Profile">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </span>
                            <span class="nav-label">Admin Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" data-title="Site & Email Settings">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            </span>
                            <span class="nav-label">Site & Email Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.seo') }}" class="nav-link {{ request()->routeIs('admin.seo*') ? 'active' : '' }}" data-title="SEO Manager">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                            </span>
                            <span class="nav-label">SEO Manager</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Bottom Brand Logo (Displayed at the bottom downside when sidebar is closed) -->
            <div class="sidebar-bottom-brand">
                <a href="{{ route('admin.dashboard') }}" title="Lumique Aesthetic Clinic">
                    <img src="{{ $siteLogo }}" alt="{{ $settings['site_name'] ?? 'Lumique' }}" class="bottom-brand-logo">
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="admin-main">
            <!-- Top Navigation Bar -->
            <header class="admin-topbar">
                <div class="topbar-left" style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-sidebar-open" id="mobileSidebarOpen">☰</button>
                    <div>
                        <nav class="admin-breadcrumbs" aria-label="breadcrumb">
                            <ol style="display: flex; align-items: center; list-style: none; padding: 0; margin: 0 0 2px; font-size: 0.72rem; color: var(--color-charcoal-muted); gap: 6px;">
                                <li><a href="{{ route('admin.dashboard') }}" style="color: var(--color-charcoal-muted); text-decoration: none;">Admin</a></li>
                                <li><span style="opacity: 0.4;">/</span></li>
                                @hasSection('breadcrumb_parent')
                                    <li><span style="color: var(--color-charcoal-muted);">@yield('breadcrumb_parent')</span></li>
                                    <li><span style="opacity: 0.4;">/</span></li>
                                @endif
                                <li style="color: var(--color-crimson); font-weight: 600;">@yield('breadcrumb_current', 'Dashboard')</li>
                            </ol>
                        </nav>
                        <h2 class="topbar-page-title" style="margin: 0; line-height: 1.2;">@yield('page_title', 'Clinic Overview')</h2>
                    </div>
                </div>
                <div class="topbar-right">
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-gold btn-sm" style="display: inline-flex; align-items: center; gap: 0.45rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        <span>Preview Public Site</span>
                    </a>
                    <a href="{{ route('admin.profile') }}" class="admin-user-pill" style="text-decoration: none; cursor: pointer;" title="Edit Administrator Profile">
                        @php
                            $adminUser = auth()->user() ?? \App\Models\User::first();
                            $adminName = $adminUser?->name ?? 'Administrator';
                            $adminEmail = $adminUser?->email ?? 'Super Admin';
                            $userAvatar = $adminUser?->avatar_url ?? null;
                            $userAvatarSrc = !empty($userAvatar) ? (str_starts_with($userAvatar, 'http') || str_starts_with($userAvatar, '/') ? $userAvatar : asset('storage/' . $userAvatar)) : null;
                            $nameParts = preg_split('/\s+/', trim($adminName));
                            $adminInitials = count($nameParts) >= 2
                                ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
                                : strtoupper(substr($adminName, 0, 2));
                        @endphp
                        @if($userAvatarSrc)
                            <img src="{{ $userAvatarSrc }}" alt="{{ $adminName }}" class="user-avatar-circle" style="object-fit: cover; border: 1.5px solid var(--color-gold-bright);">
                        @else
                            <div class="user-avatar-circle">{{ $adminInitials }}</div>
                        @endif
                        <div class="user-text">
                            <span class="user-name">{{ $adminName }}</span>
                            <span class="user-role">{{ $adminEmail }}</span>
                        </div>
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(200, 16, 30, 0.1); color: var(--color-crimson); border: 1px solid rgba(200, 16, 30, 0.3);" title="Sign out of portal">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content Body -->
            <div class="admin-content-body">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Lucide Icons & Scripts -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const mobileOpen = document.getElementById('mobileSidebarOpen');

        // Check LocalStorage preference
        if (localStorage.getItem('admin_sidebar_collapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('admin_sidebar_collapsed', sidebar.classList.contains('collapsed'));
            });
        }
        if (mobileOpen) {
            mobileOpen.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
            });
        }

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }

        // Floating Luxury Flyout Tooltip on Collapsed Sidebar
        const floatingTooltip = document.createElement('div');
        floatingTooltip.id = 'sidebar-floating-tooltip';
        document.body.appendChild(floatingTooltip);

        document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
            link.addEventListener('mouseenter', () => {
                if (sidebar.classList.contains('collapsed')) {
                    const title = link.getAttribute('data-title') || link.querySelector('.nav-label')?.innerText.trim() || '';
                    if (!title) return;
                    const rect = link.getBoundingClientRect();
                    floatingTooltip.textContent = title;
                    floatingTooltip.style.top = `${rect.top + rect.height / 2}px`;
                    floatingTooltip.style.left = `${rect.right + 12}px`;
                    floatingTooltip.classList.add('show');
                }
            });
            link.addEventListener('mouseleave', () => {
                floatingTooltip.classList.remove('show');
            });
        });

        // Global Bottom-Right Toast Notification System
        window.showToast = function(message, type = 'success', duration = 3500) {
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container-bottom-right';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            toast.className = `admin-toast toast-${type}`;
            const icon = type === 'success' ? '✓' : (type === 'error' ? '✕' : 'ℹ');
            toast.innerHTML = `
                <span class="toast-icon">${icon}</span>
                <div class="toast-body">${message}</div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('toast-show');
            }, 10);
            setTimeout(() => {
                toast.classList.remove('toast-show');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        };

        // Global Luxury Delete Confirmation Modal Handler
        let activeDeleteCallback = null;

        window.confirmDeleteModal = function(title, itemName, callback) {
            document.getElementById('globalDeleteModalTitle').innerText = title || 'Confirm Deletion';
            const safeName = (itemName || 'this record').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            document.getElementById('globalDeleteModalMessage').innerHTML = `Are you sure you want to delete <strong>"${safeName}"</strong>? This action cannot be undone.`;
            activeDeleteCallback = callback;
            document.getElementById('globalDeleteModal').classList.add('open');
        };

        window.closeGlobalDeleteModal = function() {
            document.getElementById('globalDeleteModal').classList.remove('open');
            activeDeleteCallback = null;
        };

        document.addEventListener('DOMContentLoaded', function() {
            const confirmBtn = document.getElementById('globalDeleteConfirmBtn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', async function() {
                    if (activeDeleteCallback) {
                        const originalText = confirmBtn.innerText;
                        confirmBtn.disabled = true;
                        confirmBtn.innerText = 'Deleting...';
                        try {
                            await activeDeleteCallback();
                        } catch(err) {
                            console.error(err);
                        } finally {
                            confirmBtn.disabled = false;
                            confirmBtn.innerText = originalText;
                            closeGlobalDeleteModal();
                        }
                    }
                });
            }
        });
    </script>

    <!-- Global Luxury Delete Confirmation Modal -->
    <div class="modal-overlay" id="globalDeleteModal" style="z-index: 100000;">
        <div class="modal-card delete-confirm-modal-card" style="max-width: 440px; text-align: center; padding: 2.25rem;">
            <button type="button" class="modal-close" onclick="closeGlobalDeleteModal()">&times;</button>
            <div style="width: 58px; height: 58px; border-radius: 50%; background: rgba(139, 21, 56, 0.1); color: var(--color-crimson, #8b1538); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 id="globalDeleteModalTitle" style="font-family: var(--font-serif); font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--color-charcoal);">Confirm Deletion</h3>
            <p id="globalDeleteModalMessage" class="text-muted" style="font-size: 0.9rem; margin-bottom: 1.75rem; line-height: 1.5;">
                Are you sure you want to delete this record? This action cannot be undone.
            </p>
            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                <button type="button" class="btn btn-outline-gold btn-sm" style="min-width: 100px;" onclick="closeGlobalDeleteModal()">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="globalDeleteConfirmBtn" style="min-width: 115px;">Yes, Delete</button>
            </div>
        </div>
    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
