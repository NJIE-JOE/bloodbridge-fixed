/* ============================================================
   BDMS — Hospital Portal (vanilla JS, no frameworks)
   ============================================================ */

/* ---------- icons (inline SVG, no external icon library) ---------- */
const ICONS = {
  droplet: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="{fill}" stroke="{stroke}" stroke-width="2"><path d="M12 2s7 7.6 7 12.5A7 7 0 0 1 5 14.5C5 9.6 12 2 12 2Z"/></svg>',
  dashboard: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>',
  calendar: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
  syringe: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 2 4 4M17 7l-1.5-1.5M14.5 4.5 3 16l-1 5 5-1L18.5 8.5M9 11l4 4"/></svg>',
  swap: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m17 2 4 4-4 4M3 11V9a4 4 0 0 1 4-4h14M7 22l-4-4 4-4M21 13v2a4 4 0 0 1-4 4H3"/></svg>',
  plus: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>',
  minus: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round"><path d="M5 12h14"/></svg>',
  arrowLeft: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>',
  logout: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>',
  chevron: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
  clock: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>',
  user: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>',
  check: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.9-9.1M22 4 12 14.01l-3-3"/></svg>',
  alert: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/></svg>',
  eye: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>',
  eyeOff: '<svg width="{s}" height="{s}" viewBox="0 0 24 24" fill="none" stroke="{stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.9 4.24A9.1 9.1 0 0 1 12 4c7 0 11 7 11 7a13.2 13.2 0 0 1-1.67 2.68M6.6 6.6C3.3 8.4 1 12 1 12s4 7 11 7a9.7 9.7 0 0 0 5.4-1.6M1 1l22 22M9.5 9.5a3 3 0 0 0 4.2 4.2"/></svg>',
};
function icon(name, size, color) {
  const tpl = ICONS[name] || "";
  const stroke = color || "currentColor";
  return tpl.replaceAll("{s}", size || 16).replaceAll("{stroke}", stroke).replaceAll("{fill}", name === "droplet" ? stroke : "none");
}

/* ---------- static data ---------- */
const BLOOD_GROUPS = ["O+", "O-", "A+", "A-", "B+", "B-", "AB+", "AB-"];

const REQUIRED_FIELDS = ["name", "address", "city", "license", "email", "password"];
const FIELD_LABELS = { name: "Hospital name", address: "Street address", city: "City", license: "License number", email: "Email", password: "Password" };

/* ---------- app state ---------- */
const state = {
  view: "auth", // 'auth' | 'portal'
  authMode: "signup", // 'signup' | 'login'
  hospital: null,
  form: { name: "", address: "", city: "", license: "", email: "", password: "" },
  errors: {},
  showPassword: false,
  section: null, // active operations card

  inventory: { "O+": 34, "O-": 11, "A+": 26, "A-": 9, "B+": 19, "B-": 6, "AB+": 8, "AB-": 3 },
  donors: [
    { id: "d1", name: "Elvis Mballa", bloodGroup: "O-", phone: "+237 677 20 14 92" },
    { id: "d2", name: "Bih Achu", bloodGroup: "A+", phone: "+237 691 43 08 21" },
    { id: "d3", name: "Fritz Njoya", bloodGroup: "B+", phone: "+237 655 17 92 60" },
    { id: "d4", name: "Larissa Tabi", bloodGroup: "AB+", phone: "+237 699 34 71 05" },
    { id: "d5", name: "Guy Fokou", bloodGroup: "O+", phone: "+237 675 88 26 47" },
  ],
  appointments: [
    { id: "ap1", donorId: "d2", donorName: "Bih Achu", bloodGroup: "A+", date: "2026-08-21", time: "09:30", status: "scheduled" },
    { id: "ap2", donorId: "d3", donorName: "Fritz Njoya", bloodGroup: "B+", date: "2026-08-21", time: "11:00", status: "scheduled" },
  ],
  requests: [
    { id: "r1", recipientName: "Ward 4B — E. Ateba", bloodGroup: "O-", unitsNeeded: 3, urgency: "critical", status: "matched" },
    { id: "r2", recipientName: "ICU — S. Ngu", bloodGroup: "A+", unitsNeeded: 2, urgency: "high", status: "matched" },
    { id: "r3", recipientName: "Surgery — M. Bissa", bloodGroup: "AB-", unitsNeeded: 1, urgency: "standard", status: "matched" },
  ],
  bookingDraft: { donorId: "", date: "", time: "" },
  toast: null,
  toastTimer: null,
};

const CARD_META = [
  { key: "dashboard", title: "Hospital Dashboard", desc: "Inventory overview, upcoming appointments, pending requests", i: "dashboard" },
  { key: "inventory", title: "Inventory Management", desc: "View and adjust blood units on hand per blood group", i: "droplet" },
  { key: "appointments", title: "Appointment Scheduling", desc: "Book donor appointments and check for scheduling conflicts", i: "calendar" },
  { key: "extraction", title: "Extraction & Donation Log", desc: "Record extraction, complete donations, update donor history", i: "syringe" },
  { key: "requests", title: "Fulfil Matched Requests", desc: "Transfer units to a recipient and close out the request", i: "swap" },
];

/* ---------- helpers ---------- */
function notify(msg, tone) {
  state.toast = { msg, tone: tone || "success" };
  render();
  clearTimeout(state.toastTimer);
  state.toastTimer = setTimeout(() => { state.toast = null; render(); }, 2800);
}

function pillClass(tone) {
  return "pill pill-" + (tone || "neutral");
}

/* ============================================================
   RENDER
   ============================================================ */
function render() {
  const root = document.getElementById("app");
  root.innerHTML = state.view === "auth" ? renderAuth() : renderPortal();
  bindEvents();
}

/* ---------- AUTH screen ---------- */
function renderAuth() {
  const isSignup = state.authMode === "signup";
  const f = state.form;
  const err = state.errors;

  const field = (key, label, opts = {}) => {
    const type = opts.password ? (state.showPassword ? "text" : "password") : (opts.type || "text");
    const eyeBtn = opts.password
      ? `<button type="button" class="field-eye" data-toggle-pw="1">${icon(state.showPassword ? "eyeOff" : "eye", 19)}</button>`
      : "";
    return `
      <div class="field ${err[key] ? "has-error" : ""}">
        <div class="field-label-row"><span class="field-label">${label}</span></div>
        <div class="field-input-wrap">
          <input class="field-input" data-field="${key}" type="${type}" placeholder="${opts.placeholder || ""}" value="${(f[key] || "").replace(/"/g, "&quot;")}" />
          ${eyeBtn}
        </div>
        ${err[key] ? `<p class="field-error-msg">This field is required.</p>` : ""}
      </div>`;
  };

  const signupFields = `
    ${field("name", "Name of hospital", { placeholder: "Buea Regional Hospital" })}
    ${field("city", "City", { placeholder: "Buea" })}
    ${field("address", "Street address", { placeholder: "Mile 8, Muea" })}
    ${field("license", "License number", { placeholder: "CM-HL-04521" })}
    ${field("email", "Email", { type: "email", placeholder: "admin@hospital.cm" })}
    ${field("password", "Password", { password: true, placeholder: "••••••••" })}
    <div class="auth-actions">
      <button type="button" class="btn-primary" data-action="register">Register</button>
      <p class="auth-switch">Already have an account? <button type="button" class="link-btn" data-action="switch-login">Log in</button></p>
    </div>`;

  const loginFields = `
    ${field("email", "Email", { type: "email", placeholder: "admin@hospital.cm" })}
    ${field("password", "Password", { password: true, placeholder: "••••••••" })}
    <div class="auth-actions">
      <button type="button" class="btn-primary" data-action="login">Log in</button>
      <p class="auth-switch">New hospital? <button type="button" class="link-btn" data-action="switch-signup">Register here</button></p>
    </div>`;

  return `
    <div class="auth-page">
      <div class="auth-wrap">
        <div class="auth-card">
          <div class="auth-brand">${icon("droplet", 22, "#6B0F14")}<span>BDMS · Cameroon</span></div>
          <h1 class="auth-title">${isSignup ? "Register your hospital" : "Hospital login"}</h1>
          <p class="auth-subtitle">${isSignup ? "Join the Blood Donation Management System." : "Sign in to your hospital account."}</p>
          <div>${isSignup ? signupFields : loginFields}</div>
        </div>
        <p class="auth-footnote">Donor and recipient sign-up live elsewhere in BDMS.</p>
      </div>
    </div>
    ${state.toast ? renderToast() : ""}`;
}

/* ---------- PORTAL ---------- */
function renderPortal() {
  const h = state.hospital;
  return `
    <div class="portal-page">
      <div class="portal-header">
        <div class="portal-header-inner">
          <div class="brand-block">
            ${icon("droplet", 20, "#6B0F14")}
            <div>
              <p class="brand-name">${h.name}</p>
              <p class="brand-sub">${h.city}, Cameroon · License ${h.license}</p>
            </div>
          </div>
          <button type="button" class="logout-btn" data-action="logout">${icon("logout", 15)} Log out</button>
        </div>
      </div>
      <div class="portal-main">
        ${state.section ? renderSection() : renderOpsGrid()}
      </div>
    </div>
    ${state.toast ? renderToast() : ""}`;
}

function renderOpsGrid() {
  const card = (c) => `
    <button type="button" class="op-card" data-open-section="${c.key}">
      <div class="op-card-top">
        <div class="op-icon-box">${icon(c.i, 19, "#6B0F14")}</div>
        <span class="chevron">${icon("chevron", 16)}</span>
      </div>
      <p class="op-card-title">${c.title}</p>
      <p class="op-card-desc">${c.desc}</p>
    </button>`;
  return `
    <div class="ops-heading">${icon("droplet", 18, "#6B0F14")}<h1>Operations</h1></div>
    <p class="ops-sub">Five workflows for running blood supply at ${state.hospital.name}.</p>
    <div class="ops-grid-3">${CARD_META.slice(0, 3).map(card).join("")}</div>
    <div class="ops-grid-2">${CARD_META.slice(3).map(card).join("")}</div>`;
}

function renderSection() {
  const backBtn = `<button type="button" class="back-btn" data-action="back">${icon("arrowLeft", 15)} Back to operations</button>`;
  let body = "";
  if (state.section === "dashboard") body = sectionDashboard();
  if (state.section === "inventory") body = sectionInventory();
  if (state.section === "appointments") body = sectionAppointments();
  if (state.section === "extraction") body = sectionExtraction();
  if (state.section === "requests") body = sectionRequests();
  return backBtn + body;
}

function sectionDashboard() {
  const totalUnits = Object.values(state.inventory).reduce((a, b) => a + b, 0);
  const upcoming = state.appointments.filter((a) => a.status === "scheduled");
  const pending = state.requests.filter((r) => r.status === "matched");

  const bgStrip = BLOOD_GROUPS.map((g) => `
    <div class="bg-tile"><p class="bg-tile-label">${g}</p><p class="bg-tile-value">${state.inventory[g]}</p></div>`).join("");

  const upcomingRows = upcoming.length
    ? upcoming.map((a) => `
      <div class="row-item">
        <div class="row-left">${icon("user", 15, "#B9A6A4")}<span class="name">${a.donorName}</span><span class="${pillClass("neutral")}">${a.bloodGroup}</span></div>
        <div class="row-right">${a.date} · ${a.time}</div>
      </div>`).join("")
    : `<div class="empty-row">No appointments scheduled.</div>`;

  const pendingRows = pending.length
    ? pending.map((r) => `
      <div class="row-item">
        <div class="row-left"><span class="name">${r.recipientName}</span><span class="${pillClass(r.urgency)}">${r.urgency}</span></div>
        <div class="row-right">${r.unitsNeeded}× ${r.bloodGroup}</div>
      </div>`).join("")
    : `<div class="empty-row">No open requests.</div>`;

  return `
    <div class="section-shell">
      <div class="section-heading">${icon("droplet", 18, "#6B0F14")}<h2>Hospital Dashboard</h2></div>
      <p class="section-desc">Overview of inventory, upcoming appointments, and pending requests.</p>
      <div class="stat-grid">
        <div class="stat-block"><p class="stat-label">Total units on hand</p><p class="stat-value">${totalUnits}</p><p class="stat-sub">across 8 blood groups</p></div>
        <div class="stat-block"><p class="stat-label">Upcoming appointments</p><p class="stat-value">${upcoming.length}</p><p class="stat-sub">scheduled extractions</p></div>
        <div class="stat-block"><p class="stat-label">Pending requests</p><p class="stat-value">${pending.length}</p><p class="stat-sub">awaiting fulfilment</p></div>
      </div>
      <p class="sub-heading">Inventory snapshot</p>
      <div class="bg-strip">${bgStrip}</div>
      <p class="sub-heading">Upcoming appointments</p>
      <div class="row-list">${upcomingRows}</div>
      <p class="sub-heading">Pending requests</p>
      <div class="row-list" style="margin-bottom:0">${pendingRows}</div>
    </div>`;
}

function sectionInventory() {
  const cards = BLOOD_GROUPS.map((g) => `
    <div class="inv-card">
      <div class="inv-group">${icon("droplet", 13, "#7A6866")} ${g}</div>
      <div class="inv-value">${state.inventory[g]}</div>
      <div class="inv-controls">
        <button type="button" class="inv-btn" data-adjust="${g}:-1">${icon("minus", 15)}</button>
        <button type="button" class="inv-btn plus" data-adjust="${g}:1">${icon("plus", 15)}</button>
      </div>
      ${state.inventory[g] <= 8 ? `<div class="inv-low"><span class="${pillClass("critical")}">Low stock</span></div>` : ""}
    </div>`).join("");
  return `
    <div class="section-shell">
      <div class="section-heading">${icon("droplet", 18, "#6B0F14")}<h2>Inventory Management</h2></div>
      <p class="section-desc">View and adjust blood units on hand per blood group.</p>
      <div class="inv-grid">${cards}</div>
    </div>`;
}

function sectionAppointments() {
  const donorOptions = state.donors.map((d) => `<option value="${d.id}" ${state.bookingDraft.donorId === d.id ? "selected" : ""}>${d.name} — ${d.bloodGroup}</option>`).join("");
  const scheduled = state.appointments.filter((a) => a.status === "scheduled");
  const rows = scheduled.length
    ? scheduled.map((a) => `
      <div class="row-item">
        <div class="row-left">${icon("user", 15, "#B9A6A4")}<span class="name">${a.donorName}</span><span class="${pillClass("neutral")}">${a.bloodGroup}</span></div>
        <div class="row-right">${icon("clock", 13)} ${a.date} · ${a.time}</div>
      </div>`).join("")
    : `<div class="empty-row">No appointments scheduled.</div>`;

  return `
    <div class="section-shell">
      <div class="section-heading">${icon("calendar", 18, "#6B0F14")}<h2>Appointment Scheduling</h2></div>
      <p class="section-desc">Book donor extraction appointments and catch scheduling conflicts.</p>
      <p class="sub-heading">Book an appointment</p>
      <div class="booking-box">
        <label><span class="lbl">Available donor</span>
          <select id="booking-donor"><option value="">Select donor…</option>${donorOptions}</select>
        </label>
        <label><span class="lbl">Date</span><input id="booking-date" type="date" value="${state.bookingDraft.date}" /></label>
        <label><span class="lbl">Time</span><input id="booking-time" type="time" value="${state.bookingDraft.time}" /></label>
        <button type="button" class="booking-submit" data-action="book">Book appointment (conflict check applied)</button>
      </div>
      <p class="sub-heading">Scheduled extractions</p>
      <div class="row-list" style="margin-bottom:0">${rows}</div>
    </div>`;
}

function sectionExtraction() {
  const scheduled = state.appointments.filter((a) => a.status === "scheduled");
  const completed = state.appointments.filter((a) => a.status === "completed");

  const scheduledRows = scheduled.length
    ? scheduled.map((a) => `
      <div class="extract-row">
        <div class="extract-left">
          ${icon("syringe", 16, "#B9A6A4")}
          <span class="name">${a.donorName}</span>
          <span class="${pillClass("neutral")}">${a.bloodGroup}</span>
          <span class="extract-date">${a.date} · ${a.time}</span>
        </div>
        <button type="button" class="btn-small" data-record="${a.id}">Record extraction</button>
      </div>`).join("")
    : `<div class="empty-row">No extractions awaiting completion.</div>`;

  const completedRows = completed.length
    ? completed.map((a) => `
      <div class="row-item">
        <div class="row-left">${icon("check", 15, "#1E8E5A")}<span class="name">${a.donorName}</span><span class="${pillClass("neutral")}">${a.bloodGroup}</span></div>
        <div class="completed-note">Donor history & inventory updated</div>
      </div>`).join("")
    : `<div class="empty-row">No completed donations yet.</div>`;

  return `
    <div class="section-shell">
      <div class="section-heading">${icon("syringe", 18, "#6B0F14")}<h2>Extraction & Donation Log</h2></div>
      <p class="section-desc">Record the extraction, complete the donation, and update donor history and inventory.</p>
      <div class="row-list" style="margin-bottom:0">${scheduledRows}</div>
      <p class="sub-heading mt">Completed donations</p>
      <div class="row-list" style="margin-bottom:0">${completedRows}</div>
    </div>`;
}

function sectionRequests() {
  const rows = state.requests.map((r) => `
    <div class="extract-row">
      <div class="extract-left">
        ${icon("swap", 16, "#B9A6A4")}
        <div>
          <div class="name">${r.recipientName}</div>
          <div class="extract-date">${r.unitsNeeded} unit(s) · ${r.bloodGroup} · ${state.inventory[r.bloodGroup]} on hand</div>
        </div>
        <span class="${pillClass(r.urgency)}">${r.urgency}</span>
      </div>
      ${r.status === "matched"
        ? `<button type="button" class="btn-small" data-transfer="${r.id}">Transfer &amp; close</button>`
        : `<span class="${pillClass("success")}">Closed</span>`}
    </div>`).join("");

  return `
    <div class="section-shell">
      <div class="section-heading">${icon("swap", 18, "#6B0F14")}<h2>Fulfil Matched Requests</h2></div>
      <p class="section-desc">Transfer units to a recipient and close out the request.</p>
      <div class="row-list" style="margin-bottom:0">${rows}</div>
    </div>`;
}

function renderToast() {
  const t = state.toast;
  return `<div class="toast ${t.tone}">${icon(t.tone === "critical" ? "alert" : "check", 16)} ${t.msg}</div>`;
}

/* ============================================================
   EVENTS
   ============================================================ */
function bindEvents() {
  const root = document.getElementById("app");

  // text/email/password inputs on the auth screen
  root.querySelectorAll("[data-field]").forEach((input) => {
    input.addEventListener("input", (e) => {
      const key = e.target.getAttribute("data-field");
      state.form[key] = e.target.value;
      if (state.errors[key]) {
        state.errors[key] = false;
        // re-render just enough to clear the error style without losing focus:
        // simplest robust approach is a full render + restore focus/caret.
        const pos = e.target.selectionStart;
        render();
        const again = document.querySelector(`[data-field="${key}"]`);
        if (again) { again.focus(); again.setSelectionRange(pos, pos); }
      }
    });
  });

  root.querySelectorAll("[data-toggle-pw]").forEach((btn) => {
    btn.addEventListener("click", () => { state.showPassword = !state.showPassword; render(); });
  });

  const actionMap = {
    register: () => submitAuth("signup"),
    login: () => submitAuth("login"),
    "switch-login": () => { state.authMode = "login"; state.errors = {}; render(); },
    "switch-signup": () => { state.authMode = "signup"; state.errors = {}; render(); },
    logout: () => { state.view = "auth"; state.section = null; render(); },
    back: () => { state.section = null; render(); },
    book: () => bookAppointment(),
  };
  root.querySelectorAll("[data-action]").forEach((el) => {
    const a = el.getAttribute("data-action");
    if (actionMap[a]) el.addEventListener("click", actionMap[a]);
  });

  root.querySelectorAll("[data-open-section]").forEach((el) => {
    el.addEventListener("click", () => { state.section = el.getAttribute("data-open-section"); render(); });
  });

  root.querySelectorAll("[data-adjust]").forEach((el) => {
    el.addEventListener("click", () => {
      const [group, delta] = el.getAttribute("data-adjust").split(":");
      adjustInventory(group, parseInt(delta, 10));
    });
  });

  root.querySelectorAll("[data-record]").forEach((el) => {
    el.addEventListener("click", () => recordExtraction(el.getAttribute("data-record")));
  });

  root.querySelectorAll("[data-transfer]").forEach((el) => {
    el.addEventListener("click", () => transferUnits(el.getAttribute("data-transfer")));
  });

  const donorSel = document.getElementById("booking-donor");
  const dateInp = document.getElementById("booking-date");
  const timeInp = document.getElementById("booking-time");
  if (donorSel) donorSel.addEventListener("change", (e) => { state.bookingDraft.donorId = e.target.value; });
  if (dateInp) dateInp.addEventListener("input", (e) => { state.bookingDraft.date = e.target.value; });
  if (timeInp) timeInp.addEventListener("input", (e) => { state.bookingDraft.time = e.target.value; });
}

/* ---------- actions ---------- */
function submitAuth(mode) {
  const fieldsToCheck = mode === "signup" ? REQUIRED_FIELDS : ["email", "password"];
  const nextErrors = {};
  const missingLabels = [];
  fieldsToCheck.forEach((k) => {
    if (!state.form[k] || !state.form[k].trim()) {
      nextErrors[k] = true;
      missingLabels.push(FIELD_LABELS[k]);
    }
  });
  if (missingLabels.length) {
    state.errors = nextErrors;
    render();
    notify(`Missing: ${missingLabels.join(", ")}.`, "critical");
    return;
  }
  state.errors = {};
  state.hospital = {
    ...state.form,
    name: state.form.name || "Your Hospital",
    city: state.form.city || "—",
    license: state.form.license || "—",
  };
  state.view = "portal";
  state.section = null;
  render();
  notify(`Welcome, ${state.form.name || "hospital"}. BDMS registration complete.`);
}

function adjustInventory(group, delta) {
  state.inventory[group] = Math.max(0, state.inventory[group] + delta);
  render();
}

function bookAppointment() {
  const { donorId, date, time } = state.bookingDraft;
  if (!donorId || !date || !time) {
    notify("Choose a donor, date, and time to book.", "critical");
    return;
  }
  const conflict = state.appointments.some((a) => a.status === "scheduled" && a.date === date && a.time === time);
  if (conflict) {
    notify("Conflict: that extraction slot is already booked.", "critical");
    return;
  }
  const donor = state.donors.find((d) => d.id === donorId);
  state.appointments.push({
    id: "ap" + Date.now(),
    donorId, donorName: donor.name, bloodGroup: donor.bloodGroup,
    date, time, status: "scheduled",
  });
  state.bookingDraft = { donorId: "", date: "", time: "" };
  render();
  notify(`Appointment booked with ${donor.name}.`);
}

function recordExtraction(id) {
  const appt = state.appointments.find((a) => a.id === id);
  if (!appt) return;
  appt.status = "completed";
  state.inventory[appt.bloodGroup] += 1;
  render();
  notify(`Extraction recorded for ${appt.donorName}. Donor history and inventory updated.`);
}

function transferUnits(id) {
  const req = state.requests.find((r) => r.id === id);
  if (!req) return;
  if (state.inventory[req.bloodGroup] < req.unitsNeeded) {
    notify(`Not enough ${req.bloodGroup} units on hand to fulfil this request.`, "critical");
    return;
  }
  state.inventory[req.bloodGroup] -= req.unitsNeeded;
  req.status = "fulfilled";
  render();
  notify(`${req.unitsNeeded} unit(s) of ${req.bloodGroup} transferred. Request closed.`);
}

/* ---------- boot ---------- */
document.addEventListener("DOMContentLoaded", render);
