/* ==========================================================================
   Lumique Aesthetic Clinic - Admin Dashboard Engine
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  initAdminTabs();
  renderAdminDashboard();
  renderAdminAppointments();
  renderAdminTreatments();
  renderAdminBlog();
  renderAdminDoctor();
  renderAdminSettings();
  initAdminSidebar();
  if (window.lucide && typeof window.lucide.createIcons === 'function') {
    window.lucide.createIcons();
  }
});

function initAdminSidebar() {
  const toggleBtn = document.getElementById('admin-sidebar-toggle');
  const sidebar = document.querySelector('.admin-sidebar');
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  }
}

function initAdminTabs() {
  const navBtns = document.querySelectorAll('.admin-nav-item');
  const panels = document.querySelectorAll('.admin-tab-panel');

  navBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.getAttribute('data-tab');
      navBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      panels.forEach(panel => {
        panel.style.display = panel.id === `tab-${target}` ? 'block' : 'none';
      });

      const sidebar = document.querySelector('.admin-sidebar');
      if (sidebar) sidebar.classList.remove('open');

      if (window.lucide) window.lucide.createIcons();
    });
  });
}

function renderAdminDashboard() {
  const appointments = window.LumiqueStore ? window.LumiqueStore.getAppointments() : [];
  const { treatments, blogPosts } = window.LUMIQUE_DATA;

  const totalApts = appointments.length;
  const newApts = appointments.filter(a => a.status === 'new').length;
  const confirmedApts = appointments.filter(a => a.status === 'confirmed').length;

  const countTotalEl = document.getElementById('stat-total-appointments');
  const countNewEl = document.getElementById('stat-new-appointments');
  const countTreatmentsEl = document.getElementById('stat-total-treatments');
  const countArticlesEl = document.getElementById('stat-total-articles');

  if (countTotalEl) countTotalEl.textContent = totalApts;
  if (countNewEl) countNewEl.textContent = newApts;
  if (countTreatmentsEl) countTreatmentsEl.textContent = treatments.length;
  if (countArticlesEl) countArticlesEl.textContent = blogPosts.length;

  // Recent Appointments Mini-Table
  const recentTable = document.getElementById('dashboard-recent-appointments');
  if (recentTable) {
    if (!appointments.length) {
      recentTable.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--color-charcoal-muted); padding: 2rem;">No appointments yet.</td></tr>';
      return;
    }
    recentTable.innerHTML = appointments.slice(0, 5).map(apt => `
      <tr>
        <td><strong>${apt.name}</strong><br><small style="color: var(--color-charcoal-muted);">${apt.phone}</small></td>
        <td>${apt.treatment_title || 'General Consultation'}</td>
        <td>${apt.preferred_date || 'N/A'}<br><small style="color: var(--color-charcoal-muted);">${apt.preferred_time || ''}</small></td>
        <td><span class="badge badge-${apt.status}">${apt.status}</span></td>
        <td>
          <button class="btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;" onclick="openAppointmentModal('${apt.id}')">View</button>
        </td>
      </tr>
    `).join('');
  }
}

function renderAdminAppointments() {
  const container = document.getElementById('admin-appointments-table-body');
  if (!container || !window.LumiqueStore) return;

  const appointments = window.LumiqueStore.getAppointments();

  if (!appointments.length) {
    container.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--color-charcoal-muted); padding: 3rem;">No appointments booked yet.</td></tr>';
    return;
  }

  container.innerHTML = appointments.map(apt => `
    <tr>
      <td>
        <strong>${apt.name}</strong><br>
        <small style="color: var(--color-charcoal-muted);">${apt.phone} ${apt.email ? `• ${apt.email}` : ''}</small>
      </td>
      <td>${apt.treatment_title || 'Consultation'}</td>
      <td>${apt.preferred_date || 'Not set'}<br><small style="color: var(--color-charcoal-muted);">${apt.preferred_time || ''}</small></td>
      <td>
        <select onchange="updateAptStatus('${apt.id}', this.value)" style="padding: 0.35rem 0.5rem; font-size: 0.8rem; border-radius: 4px; border: 1px solid var(--color-border); background-color: #fff;">
          <option value="new" ${apt.status === 'new' ? 'selected' : ''}>New</option>
          <option value="contacted" ${apt.status === 'contacted' ? 'selected' : ''}>Contacted</option>
          <option value="confirmed" ${apt.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
          <option value="completed" ${apt.status === 'completed' ? 'selected' : ''}>Completed</option>
          <option value="cancelled" ${apt.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
        </select>
      </td>
      <td>
        <button class="btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; margin-right: 0.25rem;" onclick="openAppointmentModal('${apt.id}')">Details</button>
        <button class="btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; color: var(--color-crimson); border-color: var(--color-crimson);" onclick="deleteApt('${apt.id}')">Delete</button>
      </td>
    </tr>
  `).join('');
}

window.updateAptStatus = function(id, newStatus) {
  if (window.LumiqueStore) {
    window.LumiqueStore.updateAppointmentStatus(id, newStatus);
    renderAdminDashboard();
    renderAdminAppointments();
  }
};

window.deleteApt = function(id) {
  if (confirm('Are you sure you want to delete this appointment?')) {
    if (window.LumiqueStore) {
      window.LumiqueStore.deleteAppointment(id);
      renderAdminDashboard();
      renderAdminAppointments();
    }
  }
};

window.openAppointmentModal = function(id) {
  const appointments = window.LumiqueStore.getAppointments();
  const apt = appointments.find(a => a.id === id);
  if (!apt) return;

  const modal = document.getElementById('appointment-detail-modal');
  const content = document.getElementById('appointment-modal-content');
  if (!modal || !content) return;

  content.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
      <div>
        <h3 class="heading-3" style="font-size: 1.35rem;">${apt.name}</h3>
        <p style="font-size: 0.85rem; color: var(--color-charcoal-muted);">${new Date(apt.created_at).toLocaleString()}</p>
      </div>
      <span class="badge badge-${apt.status}">${apt.status}</span>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
      <div><strong>Phone:</strong> <a href="tel:${apt.phone}" style="color: var(--color-crimson);">${apt.phone}</a></div>
      <div><strong>Email:</strong> ${apt.email || 'None provided'}</div>
      <div><strong>Treatment:</strong> ${apt.treatment_title || 'General Consultation'}</div>
      <div><strong>Date / Time:</strong> ${apt.preferred_date || 'Flexible'} (${apt.preferred_time || 'Any'})</div>
    </div>
    <div style="background-color: var(--color-ivory); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
      <strong style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--color-charcoal-muted); margin-bottom: 0.5rem;">Patient Message</strong>
      <p style="font-size: 0.9rem; line-height: 1.6;">${apt.message || 'No additional message.'}</p>
    </div>
    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
      <a href="https://wa.me/${apt.phone.replace(/[^0-9]/g, '')}" target="_blank" class="btn-primary" style="font-size: 0.8rem; padding: 0.5rem 1rem; background-color: #25D366; border-color: #25D366;">WhatsApp Patient</a>
      <button class="btn-secondary" style="font-size: 0.8rem; padding: 0.5rem 1rem;" onclick="closeModal()">Close</button>
    </div>
  `;

  modal.classList.add('open');
};

window.closeModal = function() {
  const modal = document.getElementById('appointment-detail-modal');
  if (modal) modal.classList.remove('open');
};

function renderAdminTreatments() {
  const container = document.getElementById('admin-treatments-table-body');
  if (!container || !window.LUMIQUE_DATA) return;

  const { treatments, categories } = window.LUMIQUE_DATA;

  container.innerHTML = treatments.map(t => {
    const cat = categories.find(c => c.id === t.category_id);
    return `
      <tr>
        <td>
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <img src="${t.hero_image}" alt="${t.title}" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;">
            <div>
              <strong>${t.title}</strong><br>
              <small style="color: var(--color-charcoal-muted);">${t.slug}</small>
            </div>
          </div>
        </td>
        <td><span class="badge" style="background-color: var(--color-soft-red); color: var(--color-crimson);">${cat ? cat.name : 'General'}</span></td>
        <td>${t.is_featured ? '⭐ Yes' : 'No'}</td>
        <td>
          <a href="treatment-detail.html?slug=${t.slug}" target="_blank" class="btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;">View Live</a>
        </td>
      </tr>
    `;
  }).join('');
}

function renderAdminBlog() {
  const container = document.getElementById('admin-blog-table-body');
  if (!container || !window.LUMIQUE_DATA) return;

  const { blogPosts } = window.LUMIQUE_DATA;

  container.innerHTML = blogPosts.map(p => `
    <tr>
      <td>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <img src="${p.featured_image}" alt="${p.title}" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;">
          <div>
            <strong>${p.title}</strong><br>
            <small style="color: var(--color-charcoal-muted);">${p.author}</small>
          </div>
        </div>
      </td>
      <td><span class="badge" style="background-color: var(--color-soft-red); color: var(--color-crimson);">${p.category}</span></td>
      <td>${p.published_at}</td>
      <td>
        <a href="blog-post.html?slug=${p.slug}" target="_blank" class="btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;">View Article</a>
      </td>
    </tr>
  `).join('');
}

function renderAdminDoctor() {
  const form = document.getElementById('admin-doctor-form');
  if (!form || !window.LUMIQUE_DATA) return;

  const doc = window.LUMIQUE_DATA.doctor;

  form.querySelector('[name="name"]').value = doc.name || '';
  form.querySelector('[name="title"]').value = doc.title || '';
  form.querySelector('[name="qualifications"]').value = doc.qualifications || '';
  form.querySelector('[name="introduction"]').value = doc.introduction || '';
  form.querySelector('[name="specializations"]').value = doc.specializations || '';
  form.querySelector('[name="treatment_philosophy"]').value = doc.treatment_philosophy || '';

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    doc.name = form.querySelector('[name="name"]').value;
    doc.title = form.querySelector('[name="title"]').value;
    doc.qualifications = form.querySelector('[name="qualifications"]').value;
    doc.introduction = form.querySelector('[name="introduction"]').value;
    doc.specializations = form.querySelector('[name="specializations"]').value;
    doc.treatment_philosophy = form.querySelector('[name="treatment_philosophy"]').value;

    alert('Doctor Profile successfully updated!');
  });
}

function renderAdminSettings() {
  const form = document.getElementById('admin-settings-form');
  if (!form || !window.LUMIQUE_DATA) return;

  const s = window.LUMIQUE_DATA.settings;

  form.querySelector('[name="clinic_name"]').value = s.clinic_name || '';
  form.querySelector('[name="phone"]').value = s.phone || '';
  form.querySelector('[name="whatsapp"]').value = s.whatsapp || '';
  form.querySelector('[name="email"]').value = s.email || '';
  form.querySelector('[name="address"]').value = s.address || '';
  form.querySelector('[name="working_hours"]').value = s.working_hours || '';

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    s.clinic_name = form.querySelector('[name="clinic_name"]').value;
    s.phone = form.querySelector('[name="phone"]').value;
    s.whatsapp = form.querySelector('[name="whatsapp"]').value;
    s.email = form.querySelector('[name="email"]').value;
    s.address = form.querySelector('[name="address"]').value;
    s.working_hours = form.querySelector('[name="working_hours"]').value;

    alert('Clinic Settings successfully updated!');
  });
}
