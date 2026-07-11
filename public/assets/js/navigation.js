function updateSidebarByRole() {
  const allowed = new Set(getAllowedPages(appState.role));
  document.querySelectorAll(".menu-item").forEach((item) => {
    const page = item.dataset.page;
    item.classList.toggle("hidden", !allowed.has(page));
    item.classList.remove("active");
  });
  document.querySelectorAll(".menu-group").forEach((group) => {
    const isGuruGroup = group.classList.contains("guru-menu");
    const isAdminGroup = group.classList.contains("admin-menu");
    group.classList.toggle("hidden", (isGuruGroup && appState.role !== "Guru PJOK") || (isAdminGroup && appState.role !== "Admin"));
  });
  const dashboardItem = document.querySelector('[data-page="dashboard"]');
  if (dashboardItem && !dashboardItem.classList.contains("hidden")) {
    dashboardItem.classList.add("active");
  }
}

function updateRoleView() {
  const recapTitle = document.getElementById("recapTitle");
  const recapDesc = document.getElementById("recapDesc");
  const recapFilterBar = document.getElementById("recapFilterBar");
  const studentNotice = document.getElementById("studentRecapNotice");
  const periodLabel = document.getElementById("periodLabel");

  if (appState.role === "Siswa" && appState.activeStudent) {
    recapTitle.textContent = "Nilai Saya";
    recapDesc.textContent = `Rekap nilai pribadi ${appState.activeStudent.name} pada semester aktif.`;
    if (recapFilterBar) recapFilterBar.classList.add("hidden");
    if (studentNotice) studentNotice.classList.remove("hidden");
    document.getElementById("activeRole").textContent = appState.activeStudent.name;
    if (periodLabel) periodLabel.textContent = `NIS ${appState.activeStudent.id} - 2025/2026 Semester Ganjil`;
  } else {
    recapTitle.textContent = "Rekapitulasi Nilai";
    recapDesc.textContent = "Filter tahun ajaran, semester, dan jenis rekap, lalu download ke Excel.";
    if (recapFilterBar) recapFilterBar.classList.remove("hidden");
    if (studentNotice) studentNotice.classList.add("hidden");
    if (periodLabel) periodLabel.textContent = "2025/2026 - Semester Ganjil";
  }

}

const renderedPages = new Set();

function renderPageData(pageId, force = false) {
  if (!force && renderedPages.has(pageId)) return;

  if (pageId === "dashboard") {
    renderDashboardContent();
  } else if (["students", "teachers", "principals", "classes", "academicYears", "studentPlacement", "classPromotion", "teacherAssignment", "principalPeriod"].includes(pageId)) {
    renderMasterLists();
  } else if (pageId === "attendance") {
    renderAcademicMenus();
    renderAttendanceRoster();
  } else if (["affective", "cognitive", "psychomotor"].includes(pageId)) {
    renderAssessmentForms();
    renderAcademicMenus();
    document.querySelectorAll(".assessment-page").forEach(renderAssessmentRoster);
  } else if (pageId === "assessmentPlan") {
    syncAssessmentPlanOptions();
    renderAssessmentPlanFilterOptions();
    renderAssessmentPlanTable();
  } else if (pageId === "recap") {
    renderRecapFilterOptions();
    renderRecapTable();
  } else if (pageId === "criteriaRecap") {
    renderCriteriaFilterOptions();
    renderCriteriaRecapTable();
  } else if (["userRole", "addUser", "userList"].includes(pageId)) {
    renderUserManagement();
  } else if (pageId === "audit") {
    renderAuditLogTable();
  }

  renderedPages.add(pageId);
}

function showPage(pageId) {
  document.querySelectorAll(".page").forEach((page) => page.classList.remove("active"));
  const target = document.getElementById(pageId);
  if (target) target.classList.add("active");

  document.querySelectorAll(".menu-item").forEach((item) => item.classList.remove("active"));
  const activeMenu = document.querySelector(`[data-page="${pageId}"]`);
  if (activeMenu && !activeMenu.classList.contains("hidden")) {
    activeMenu.classList.add("active");
  }

  const meta = pageMeta[pageId] || pageMeta.dashboard;
  document.getElementById("pageTitle").textContent = meta[0];
  document.getElementById("pageDesc").textContent = meta[1];
  appState.currentPage = pageId;
  renderPageData(pageId);
  document.getElementById("sidebar").classList.remove("open");
  updateMobileToggleIcon();
}

function goTo(pageId) {
  if (!isAllowed(pageId)) {
    showPage(getDefaultPage(appState.role));
    return;
  }

  showPage(pageId);
}

