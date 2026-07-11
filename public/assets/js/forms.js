function resetClassForm() {
  editingClassIndex = null;
  const input = document.getElementById("className");
  const submitButton = document.getElementById("classSubmitButton");
  if (input) input.value = "";
  if (submitButton) submitButton.textContent = "Tambah";
}

function editClassRecord(index) {
  const record = classRecords[index];
  const form = document.getElementById("classForm");
  const input = document.getElementById("className");
  const submitButton = document.getElementById("classSubmitButton");
  if (!record || !form || !input) return;

  editingClassIndex = index;
  input.value = record.name;
  form.classList.remove("hidden");
  if (submitButton) submitButton.textContent = "Simpan Perubahan";
}

function renameClassReferences(oldName, newName) {
  studentRecords.forEach((student) => {
    if (student.className === oldName) student.className = newName;
  });
  criteriaRecords.forEach((record) => {
    if (record.className === oldName) record.className = newName;
  });
  assessmentRecords.forEach((record) => {
    if (record.className === oldName) record.className = newName;
  });
}
function toggleMasterForm(formId) {
  const form = document.getElementById(formId);
  if (form) form.classList.toggle("hidden");
  if (formId === "classForm" && form?.classList.contains("hidden")) resetClassForm();
}

function addMasterRecord(type) {
  if (type === "teacher") {
    const name = getInputValue("teacherName");
    const nip = getInputValue("teacherNip");
    const principal = getInputValue("principalName");
    const principalNip = getInputValue("principalNip");

    if (name) {
      teacherRecords.push({ name, nip, role: "Guru PJOK" });
    }

    if (principal) {
      teacherRecords.push({ name: principal, nip: principalNip, role: "Kepala Sekolah" });
    }

    ["teacherName", "teacherNip", "principalName", "principalNip"].forEach((id) => {
      const input = document.getElementById(id);
      if (input) input.value = "";
    });

    toggleMasterForm("teacherForm");
    renderMasterLists();
    renderDashboardContent();
    persistMasterRecords(["teacherRecords"]);
    return;
  }

  if (type === "class") {
    const className = document.getElementById("className").value.trim();
    if (!className) return;

    if (editingClassIndex !== null && classRecords[editingClassIndex]) {
      const oldName = classRecords[editingClassIndex].name;
      classRecords[editingClassIndex].name = className;
      renameClassReferences(oldName, className);
    } else {
      classRecords.push({ name: className });
    }

    persistMasterRecords(["classRecords", "studentRecords", "criteriaRecords", "assessmentRecords"]);
    resetClassForm();
    toggleMasterForm("classForm");
    renderMasterLists();
    renderDashboardContent();
    renderRecapTable();
    renderAssessmentPlanFilterOptions();
    renderAssessmentPlanTable();
    renderCriteriaFilterOptions();
    renderCriteriaRecapTable();
    return;
  }

  if (type === "student") {
    const className = document.getElementById("studentClass").value;
    const name = getInputValue("studentName");
    const nisn = getInputValue("studentNisn");
    if (!name || !nisn) return;

    studentRecords.push({ id: nisn, name, className, year: "2025/2026", semester: "Ganjil", attendance: 0, cognitive: 0, affective: 0, psychomotor: 0, finalScore: 0, predicate: "Aktif", predicateClass: "badge-green" });
    ["studentName", "studentNisn", "studentUsername"].forEach((id) => {
      const input = document.getElementById(id);
      if (input) input.value = "";
    });

    toggleMasterForm("studentForm");
    renderMasterLists();
    renderDashboardContent();
    renderRecapTable();
    persistMasterRecords(["studentRecords", "placementRecords"]);
    return;
  }
}

function setAdminFormVisible(type, visible = true) {
  const form = document.getElementById(`${type}Form`);
  if (form) form.classList.toggle("hidden", !visible);
}

function resetAdminForm(type) {
  editingAdmin[type] = null;
  const ids = type === "student" ? ["studentNisn", "studentName", "studentEmail"] : type === "teacher" ? ["teacherNip", "teacherName", "teacherEmail"] : type === "principal" ? ["principalNip", "principalName", "principalEmail"] : ["yearName"];
  ids.forEach((id) => { const input = document.getElementById(id); if (input) input.value = ""; });
}

function newAdminRecord(type) {
  resetAdminForm(type);
  setAdminFormVisible(type, true);
}

function editAdminRecord(type, index) {
  const records = type === "year" ? academicYearRecords : getAdminRecords(type);
  const record = records[index];
  if (!record) return;
  editingAdmin[type] = index;
  setAdminFormVisible(type, true);
  if (type === "student") {
    document.getElementById("studentNisn").value = record.id || "";
    document.getElementById("studentName").value = record.name || "";
    document.getElementById("studentGender").value = record.gender || "Laki-laki";
    document.getElementById("studentEmail").value = record.email || "";
    document.getElementById("studentStatus").value = record.status || "Aktif";
  } else if (type === "teacher") {
    document.getElementById("teacherNip").value = record.nip || "";
    document.getElementById("teacherName").value = record.name || "";
    document.getElementById("teacherGender").value = record.gender || "Laki-laki";
    document.getElementById("teacherEmail").value = record.email || "";
    document.getElementById("teacherStatus").value = record.status || "Aktif";
  } else if (type === "principal") {
    document.getElementById("principalNip").value = record.nip || "";
    document.getElementById("principalName").value = record.name || "";
    document.getElementById("principalGender").value = record.gender || "Laki-laki";
    document.getElementById("principalEmail").value = record.email || "";
    document.getElementById("principalStatus").value = record.status || "Aktif";
  } else if (type === "year") {
    document.getElementById("yearName").value = record.name || "";
    document.getElementById("yearStatus").value = record.status || "Aktif";
  }
}

function saveAdminRecord(type) {
  if (type === "student") {
    const record = { id: getInputValue("studentNisn"), name: getInputValue("studentName"), gender: getInputValue("studentGender"), email: getInputValue("studentEmail"), status: getInputValue("studentStatus") || "Aktif" };
    if (!record.id || !record.name) return;
    if (editingAdmin.student !== null) {
      studentRecords[editingAdmin.student] = { ...studentRecords[editingAdmin.student], ...record };
    } else {
      studentRecords.push({ ...record, className: classRecords[0]?.name || "-", year: "2025/2026", semester: "Ganjil", attendance: 0, cognitive: 0, affective: 0, psychomotor: 0, finalScore: 0, predicate: "Aktif", predicateClass: "badge-green" });
    }
    ensurePlacementRecords();
    persistMasterRecords(["studentRecords", "placementRecords"]);
  } else if (type === "teacher") {
    const record = { nip: getInputValue("teacherNip"), name: getInputValue("teacherName"), gender: getInputValue("teacherGender"), email: getInputValue("teacherEmail"), status: getInputValue("teacherStatus") || "Aktif", role: "Guru PJOK" };
    if (!record.nip || !record.name) return;
    editingAdmin.teacher !== null ? teacherRecords[editingAdmin.teacher] = record : teacherRecords.push(record);
    persistMasterRecords(["teacherRecords"]);
  } else if (type === "principal") {
    const record = { nip: getInputValue("principalNip"), name: getInputValue("principalName"), gender: getInputValue("principalGender"), email: getInputValue("principalEmail"), status: getInputValue("principalStatus") || "Aktif" };
    if (!record.nip || !record.name) return;
    editingAdmin.principal !== null ? principalRecords[editingAdmin.principal] = record : principalRecords.push(record);
    persistMasterRecords(["principalRecords"]);
  } else if (type === "year") {
    const record = { name: getInputValue("yearName"), status: getInputValue("yearStatus") || "Aktif" };
    if (!record.name) return;
    editingAdmin.year !== null ? academicYearRecords[editingAdmin.year] = record : academicYearRecords.push(record);
    persistMasterRecords(["academicYearRecords"]);
  }
  resetAdminForm(type);
  setAdminFormVisible(type, false);
  renderMasterLists();
  syncRoleDataViews();
}

function deleteAdminRecord(type, index) {
  if (type === "student") {
    const [removed] = studentRecords.splice(index, 1);
    if (removed) {
      for (let i = placementRecords.length - 1; i >= 0; i -= 1) {
        if (placementRecords[i].studentId === removed.id) placementRecords.splice(i, 1);
      }
    }
    persistMasterRecords(["studentRecords", "placementRecords"]);
  }
  if (type === "teacher") {
    const [removed] = teacherRecords.splice(index, 1);
    if (removed) {
      for (let i = teacherAssignmentRecords.length - 1; i >= 0; i -= 1) {
        if (teacherAssignmentRecords[i].teacherNip === removed.nip) teacherAssignmentRecords.splice(i, 1);
      }
    }
    persistMasterRecords(["teacherRecords", "teacherAssignmentRecords"]);
  }
  if (type === "principal") {
    const [removed] = principalRecords.splice(index, 1);
    if (removed) {
      for (let i = principalPeriodRecords.length - 1; i >= 0; i -= 1) {
        if (principalPeriodRecords[i].principalNip === removed.nip) principalPeriodRecords.splice(i, 1);
      }
    }
    persistMasterRecords(["principalRecords", "principalPeriodRecords"]);
  }
  if (type === "year") {
    academicYearRecords.splice(index, 1);
    persistMasterRecords(["academicYearRecords"]);
  }
  renderMasterLists();
  syncRoleDataViews();
}

function deleteClassRecord(index) {
  const record = classRecords[index];
  if (!record) return;
  classRecords.splice(index, 1);
  renameClassReferences(record.name, classRecords[0]?.name || "-");
  renderMasterLists();
  renderDashboardContent();
  persistMasterRecords(["classRecords", "studentRecords", "criteriaRecords", "assessmentRecords"]);
}

function detailAdminRecord(type, index) {
  const record = getAdminRecords(type)[index];
  if (!record) return;
  alert(Object.entries(record).map(([key, value]) => `${key}: ${value}`).join("\n"));
}

async function importStudentCsv(file) {
  if (!file) return;

  const formData = new FormData();
  formData.append("csv", file);

  try {
    const response = await fetch("/students/import-csv", {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "X-CSRF-TOKEN": window.csrfToken || ""
      },
      body: formData
    });

    const result = await response.json().catch(() => ({}));
    if (!response.ok) {
      throw new Error(result.message || "Import CSV gagal.");
    }

    studentRecords = result.studentRecords || studentRecords;
    ensurePlacementRecords();
    await persistMasterRecords(["placementRecords"]);
    adminPages.student = 1;
    renderMasterLists();
    syncRoleDataViews();
    alert(`Import selesai. ${result.imported || 0} siswa diproses, ${result.skipped || 0} baris dilewati.`);
  } catch (error) {
    console.error(error);
    alert(error.message || "Import CSV gagal. Periksa format file dan coba lagi.");
  }
}
function saveStudentPlacement() {
  const year = document.getElementById("placementYear")?.value || "2025/2026";
  const className = document.getElementById("placementClass")?.value || "Kelas 5A";
  document.querySelectorAll(".placement-check:checked").forEach((checkbox) => {
    const existing = placementRecords.find((item) => item.studentId === checkbox.value && item.year === year);
    if (existing) existing.className = className;
    else placementRecords.push({ studentId: checkbox.value, className, year, status: "Aktif" });
    const student = studentRecords.find((item) => item.id === checkbox.value);
    if (student) { student.className = className; student.year = year; }
  });
  renderMasterLists();
  syncRoleDataViews();
  persistMasterRecords(["placementRecords", "studentRecords"]);
}

function saveClassPromotion() {
  const fromClass = document.getElementById("promotionFromClass")?.value || "";
  const toClass = document.getElementById("promotionToClass")?.value || "";
  const year = document.getElementById("promotionYear")?.value || "2025/2026";
  if (!fromClass || !toClass) return;
  studentRecords.forEach((student) => {
    if (student.className === fromClass) {
      student.className = toClass;
      student.year = year;
      const existing = placementRecords.find((item) => item.studentId === student.id && item.year === year);
      if (existing) existing.className = toClass;
      else placementRecords.push({ studentId: student.id, className: toClass, year, status: student.status || "Aktif" });
    }
  });
  renderMasterLists();
  syncRoleDataViews();
  persistMasterRecords(["studentRecords", "placementRecords"]);
}
function saveTeacherAssignment() {
  const year = document.getElementById("assignmentYear")?.value || "2025/2026";
  const className = document.getElementById("assignmentClass")?.value || classRecords[0]?.name || "";
  const teacherIndex = Number(document.getElementById("assignmentTeacher")?.value || 0);
  const teacher = teacherRecords[teacherIndex] || teacherRecords[0];
  if (!className || !teacher) return;
  const existing = teacherAssignmentRecords.find((item) => item.className === className && item.year === year);
  if (existing) existing.teacherNip = teacher.nip;
  else teacherAssignmentRecords.push({ teacherNip: teacher.nip, className, year, status: "Aktif" });
  renderAcademicMenus();
  persistMasterRecords(["teacherAssignmentRecords"]);
}

function savePrincipalPeriod() {
  const principalIndex = Number(document.getElementById("periodPrincipal")?.value || 0);
  const principal = principalRecords[principalIndex] || principalRecords[0];
  const startYear = document.getElementById("periodStart")?.value || "2025";
  const endYear = document.getElementById("periodEnd")?.value || "2029";
  if (!principal) return;
  const existing = principalPeriodRecords.find((item) => item.principalNip === principal.nip);
  if (existing) { existing.startYear = startYear; existing.endYear = endYear; existing.status = "Aktif"; }
  else principalPeriodRecords.push({ principalNip: principal.nip, startYear, endYear, status: "Aktif" });
  renderAcademicMenus();
  persistMasterRecords(["principalPeriodRecords"]);
}

