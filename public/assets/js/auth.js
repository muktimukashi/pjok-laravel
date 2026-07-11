function login() {
  const form = document.querySelector('#loginPage form');
  if (form) {
    form.submit();
    return;
  }

  window.location.href = "/login";
}
function logout() {
  const form = document.getElementById("logoutForm");
  if (form) {
    form.submit();
    return;
  }

  window.location.href = "/login";
}

function handleLoginKeydown(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    login();
  }
}

function updateMobileToggleIcon() {
  const sidebar = document.getElementById("sidebar");
  const toggle = document.getElementById("mobileToggle");
  if (!sidebar || !toggle) return;
  const isOpen = sidebar.classList.contains("open");
  toggle.innerHTML = isOpen ? "&times;" : "&#9776;";
  toggle.setAttribute("aria-label", isOpen ? "Tutup menu" : "Buka menu");
  toggle.setAttribute("aria-expanded", String(isOpen));
}

function closeSidebar() {
  document.getElementById("sidebar").classList.remove("open");
  updateMobileToggleIcon();
}
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
  updateMobileToggleIcon();
}


