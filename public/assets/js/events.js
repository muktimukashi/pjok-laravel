document.addEventListener("click", (event) => {
  const adminNewButton = event.target.closest("[data-admin-new]");
  if (adminNewButton) {
    newAdminRecord(adminNewButton.dataset.adminNew);
    return;
  }

  const adminCloseButton = event.target.closest("[data-admin-close]");
  if (adminCloseButton) {
    const form = document.getElementById(adminCloseButton.dataset.adminClose);
    if (form) form.classList.add("hidden");
    return;
  }

  const adminSaveButton = event.target.closest("[data-admin-save]");
  if (adminSaveButton) {
    saveAdminRecord(adminSaveButton.dataset.adminSave);
    return;
  }

  const adminEditButton = event.target.closest("[data-admin-edit]");
  if (adminEditButton) {
    const [type, index] = adminEditButton.dataset.adminEdit.split(":");
    editAdminRecord(type, Number(index));
    return;
  }

  const adminDeleteButton = event.target.closest("[data-admin-delete]");
  if (adminDeleteButton) {
    const [type, index] = adminDeleteButton.dataset.adminDelete.split(":");
    deleteAdminRecord(type, Number(index));
    return;
  }

  const adminDetailButton = event.target.closest("[data-admin-detail]");
  if (adminDetailButton) {
    const [type, index] = adminDetailButton.dataset.adminDetail.split(":");
    detailAdminRecord(type, Number(index));
    return;
  }

  const adminPageButton = event.target.closest("[data-admin-page]");
  if (adminPageButton) {
    const [type, direction] = adminPageButton.dataset.adminPage.split(":");
    adminPages[type] = Math.max(1, (adminPages[type] || 1) + (direction === "next" ? 1 : -1));
    renderAdminTable(type);
    return;
  }

  const placementStudentPageButton = event.target.closest("[data-placement-student-page]");
  if (placementStudentPageButton) {
    placementStudentPage = Math.max(1, placementStudentPage + (placementStudentPageButton.dataset.placementStudentPage === "next" ? 1 : -1));
    renderPlacement();
    return;
  }

  const placementPageButton = event.target.closest("[data-placement-page]");
  if (placementPageButton) {
    placementPage = Math.max(1, placementPage + (placementPageButton.dataset.placementPage === "next" ? 1 : -1));
    renderPlacement();
    return;
  }
  const placementEditButton = event.target.closest("[data-placement-edit]");
  if (placementEditButton) {
    const item = placementRecords[Number(placementEditButton.dataset.placementEdit)];
    if (item) {
      const yearSelect = document.getElementById("placementYear");
      const classSelect = document.getElementById("placementClass");
      if (yearSelect) yearSelect.value = item.year;
      if (classSelect) classSelect.value = item.className;
      document.querySelectorAll(".placement-check").forEach((checkbox) => {
        checkbox.checked = checkbox.value === item.studentId;
      });
    }
    return;
  }

  const placementDeleteButton = event.target.closest("[data-placement-delete]");
  if (placementDeleteButton) {
    placementRecords.splice(Number(placementDeleteButton.dataset.placementDelete), 1);
    renderMasterLists();
    persistMasterRecords(["placementRecords"]);
    return;
  }
  const toggleButton = event.target.closest("[data-toggle-form]");
  if (toggleButton) {
    toggleMasterForm(toggleButton.dataset.toggleForm);
    return;
  }

  const editClassButton = event.target.closest("[data-edit-class]");
  if (editClassButton) {
    editClassRecord(Number(editClassButton.dataset.editClass));
    return;
  }
  const deleteClassButton = event.target.closest("[data-delete-class]");
  if (deleteClassButton) {
    deleteClassRecord(Number(deleteClassButton.dataset.deleteClass));
    return;
  }

  const addButton = event.target.closest("[data-add-master]");
  if (addButton) {
    addMasterRecord(addButton.dataset.addMaster);
    return;
  }

  const criteriaPageButton = event.target.closest("[data-criteria-page]");
  if (criteriaPageButton) {
    changeCriteriaRecapPage(criteriaPageButton.dataset.criteriaPage);
    return;
  }
  const loadPlanButton = event.target.closest("[data-load-plan]");
  if (loadPlanButton) {
    loadAssessmentPlanByIndex(Number(loadPlanButton.dataset.loadPlan));
    return;
  }

  const deletePlanButton = event.target.closest("[data-delete-plan]");
  if (deletePlanButton) {
    deleteAssessmentPlanByIndex(Number(deletePlanButton.dataset.deletePlan));
    return;
  }

  const planPageButton = event.target.closest("[data-plan-page]");
  if (planPageButton) {
    changeAssessmentPlanPage(planPageButton.dataset.planPage);
    return;
  }

  const newAssessmentButton = event.target.closest("[data-new-assessment]");
  if (newAssessmentButton) {
    const page = newAssessmentButton.closest(".assessment-page");
    if (page) clearAssessmentRecord(page);
    return;
  }

  const editAssessmentButton = event.target.closest("[data-edit-assessment]");
  if (editAssessmentButton) {
    const page = editAssessmentButton.closest(".assessment-page");
    if (page) editAssessmentRecord(page);
    return;
  }

  const deleteAssessmentButton = event.target.closest("[data-delete-assessment]");
  if (deleteAssessmentButton) {
    const page = deleteAssessmentButton.closest(".assessment-page");
    if (page) deleteAssessmentRecord(page);
    return;
  }

  const saveAssessmentButton = event.target.closest("[data-save-assessment]");
  if (saveAssessmentButton) {
    const page = saveAssessmentButton.closest(".assessment-page");
    if (page) saveAssessmentRecord(page);
    return;
  }

  const menuItem = event.target.closest(".menu-item");
  if (menuItem && !menuItem.classList.contains("hidden")) {
    goTo(menuItem.dataset.page);
  }

  if (event.target.closest(".status-choice")) {
    const button = event.target.closest("button");
    const row = button.closest("tr");
    row.querySelectorAll(".status-choice").forEach((item) => item.classList.remove("active"));
    button.classList.add("active");
  }

  if (event.target.closest(".score-choice-btn")) {
    const button = event.target.closest("button");
    const row = button.closest("tr");
    row.querySelectorAll(".score-choice-btn").forEach((item) => item.classList.remove("active"));
    button.classList.add("active");
  }
});

document.addEventListener("change", (event) => {
  if (event.target.matches("#studentCsvInput")) {
    importStudentCsv(event.target.files?.[0]);
    event.target.value = "";
    return;
  }

  if (event.target.matches("#promotionYear, #promotionFromClass, #promotionToClass, #assignmentYear, #assignmentTeacher, #assignmentClass, #periodPrincipal, #periodStart, #periodEnd, #attendanceClass, .assessment-class")) {
    if (event.target.matches("#promotionFromClass")) {
      const target = document.getElementById("promotionToClass");
      if (target) target.value = getNextClassName(event.target.value);
    }
    if (["studentPlacement", "classPromotion", "teacherAssignment", "principalPeriod"].includes(appState.currentPage)) {
      renderAcademicMenus();
    }
    if (appState.currentPage === "attendance") {
      renderAttendanceRoster();
    }
    if (["affective", "cognitive", "psychomotor"].includes(appState.currentPage)) {
      document.querySelectorAll(".assessment-page").forEach(renderAssessmentRoster);
    }
  }

  const adminFilter = event.target.closest("[data-admin-filter]");
  if (adminFilter) {
    const type = adminFilter.dataset.adminFilter;
    adminPages[type] = 1;
    renderAdminTable(type);
  }

  if (event.target.matches(".assessment-year, .assessment-semester, .assessment-class, #recapYearFilter, #recapSemesterFilter, #recapTypeFilter, #planYear, #planSemester, #planType, #planClass, #planMeeting, #planFilterYear, #planFilterSemester, #planFilterClass, #planFilterType, #planFilterMeeting, #criteriaYearFilter, #criteriaSemesterFilter, #criteriaClassFilter, #criteriaTypeFilter, #criteriaMeetingFilter, select[id$='Meeting']")) {
    if (["affective", "cognitive", "psychomotor", "recap", "criteriaRecap"].includes(appState.currentPage)) {
      syncAssessmentRecords();
    }
    if (appState.currentPage === "recap") {
      renderRecapTable();
    }
    if (appState.currentPage === "criteriaRecap") {
      renderCriteriaFilterOptions();
      if (event.target.matches("#criteriaYearFilter, #criteriaSemesterFilter, #criteriaClassFilter, #criteriaTypeFilter, #criteriaMeetingFilter")) {
        criteriaRecapPage = 1;
      }
      renderCriteriaRecapTable();
    }
    if (appState.currentPage === "assessmentPlan" && event.target.matches("#planFilterYear, #planFilterSemester, #planFilterClass, #planFilterType, #planFilterMeeting")) {
      assessmentPlanPage = 1;
      renderAssessmentPlanTable();
    }
    if (appState.currentPage === "assessmentPlan" && event.target.matches("#planYear, #planSemester, #planType, #planClass, #planMeeting")) loadAssessmentPlanForm();
  }
});

if (appState.role === "Siswa") {
  appState.activeStudent = getStudentRecord(appState.identity);
}

updateSidebarByRole();
document.getElementById("activeRole").textContent = appState.activeStudent ? appState.activeStudent.name : appState.role;
document.getElementById("avatar").textContent = appState.activeStudent ? appState.activeStudent.name.charAt(0) : appState.role.charAt(0);
updateRoleView();
showPage(getDefaultPage(appState.role));

document.addEventListener("input", (event) => {
  if (event.target.matches("#placementStudentSearch")) {
    placementStudentPage = 1;
    renderPlacement();
  }
  if (event.target.matches("#placementSearch")) {
    placementPage = 1;
    renderPlacement();
  }

  const adminFilter = event.target.closest("[data-admin-filter]");
  if (adminFilter) {
    const type = adminFilter.dataset.adminFilter;
    adminPages[type] = 1;
    renderAdminTable(type);
  }
});

document.getElementById("planSearch")?.addEventListener("input", () => {
  assessmentPlanPage = 1;
  renderAssessmentPlanTable();
});
document.getElementById("loginIdentity")?.addEventListener("keydown", handleLoginKeydown);
document.getElementById("loginPassword")?.addEventListener("keydown", handleLoginKeydown);

