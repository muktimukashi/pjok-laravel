function getAdminRecords(type) {
  if (type === "student") return studentRecords;
  if (type === "teacher") return teacherRecords;
  if (type === "principal") return principalRecords;
  return [];
}

function badgeStatus(status) {
  return `<span class="badge ${status === "Aktif" ? "badge-green" : "badge-red"}">${status || "Aktif"}</span>`;
}

function getFilteredAdminRecords(type) {
  const records = getAdminRecords(type);
  const search = document.getElementById(`${type}Search`)?.value.trim().toLowerCase() || "";
  const status = document.getElementById(`${type}StatusFilter`)?.value || "";
  const classFilter = type === "student" ? document.getElementById("studentClassFilter")?.value || "" : "";

  return records.map((record, index) => ({ record, index })).filter(({ record }) => {
    const haystack = [record.id, record.nip, record.name, record.gender, record.email, record.status, record.className].join(" ").toLowerCase();
    return (!search || haystack.includes(search)) && (!status || record.status === status) && (!classFilter || record.className === classFilter);
  });
}

function renderAdminPagination(type, totalRecords) {
  const target = document.getElementById(`${type}Pagination`);
  if (!target) return;
  const totalPages = Math.max(1, Math.ceil(totalRecords / 10));
  adminPages[type] = Math.min(Math.max(adminPages[type] || 1, 1), totalPages);
  const start = totalRecords ? (adminPages[type] - 1) * 10 + 1 : 0;
  const end = Math.min((adminPages[type] || 1) * 10, totalRecords);
  target.innerHTML = `<span>Menampilkan ${start}-${end} dari ${totalRecords} data</span><div class="pagination-actions"><button class="btn btn-soft btn-sm" type="button" data-admin-page="${type}:prev" ${adminPages[type] === 1 ? "disabled" : ""}>‹</button><strong>Halaman ${adminPages[type]} / ${totalPages}</strong><button class="btn btn-soft btn-sm" type="button" data-admin-page="${type}:next" ${adminPages[type] === totalPages ? "disabled" : ""}>›</button></div>`;
}

function renderAdminTable(type) {
  const tbody = document.getElementById(`${type}TableBody`);
  if (!tbody) return;
  const filtered = getFilteredAdminRecords(type);
  const page = adminPages[type] || 1;
  const rows = filtered.slice((page - 1) * 10, page * 10);
  tbody.innerHTML = rows.length ? rows.map(({ record, index }, rowIndex) => {
    const number = (page - 1) * 10 + rowIndex + 1;
    const idValue = type === "student" ? record.id : record.nip;
    return `<tr><td>${number}</td><td>${idValue}</td><td>${record.name}</td><td>${record.gender || "-"}</td><td>${record.email || "-"}</td><td>${badgeStatus(record.status)}</td><td><button class="btn btn-soft btn-sm" data-admin-detail="${type}:${index}">Rincian</button> <button class="btn btn-outline btn-sm" data-admin-edit="${type}:${index}">Ubah</button> <button class="btn btn-red btn-sm" data-admin-delete="${type}:${index}">Hapus</button></td></tr>`;
  }).join("") : `<tr><td colspan="7">Data belum ditemukan.</td></tr>`;
  renderAdminPagination(type, filtered.length);
}

function renderYearTable() {
  const tbody = document.getElementById("yearTableBody");
  if (!tbody) return;
  tbody.innerHTML = academicYearRecords.map((year, index) => `<tr><td>${index + 1}</td><td>${year.name}</td><td>${badgeStatus(year.status)}</td><td><button class="btn btn-outline btn-sm" data-admin-edit="year:${index}">Ubah</button> <button class="btn btn-red btn-sm" data-admin-delete="year:${index}">Hapus</button></td></tr>`).join("");
}

function ensurePlacementRecords() {
  studentRecords.forEach((student) => {
    const hasPlacement = placementRecords.some((item) => item.studentId === student.id && item.year === student.year);
    if (!hasPlacement) {
      placementRecords.push({ studentId: student.id, className: student.className, year: student.year || "2025/2026", status: student.status || "Aktif" });
    }
  });
}
function renderSimplePagination(targetId, page, totalRecords, datasetName) {
  const target = document.getElementById(targetId);
  if (!target) return;
  const totalPages = Math.max(1, Math.ceil(totalRecords / 10));
  const start = totalRecords ? (page - 1) * 10 + 1 : 0;
  const end = Math.min(page * 10, totalRecords);
  target.innerHTML = `<span>Menampilkan ${start}-${end} dari ${totalRecords} data</span><div class="pagination-actions"><button class="btn btn-soft btn-sm" type="button" data-${datasetName}-page="prev" ${page === 1 ? "disabled" : ""}>‹</button><strong>Halaman ${page} / ${totalPages}</strong><button class="btn btn-soft btn-sm" type="button" data-${datasetName}-page="next" ${page === totalPages ? "disabled" : ""}>›</button></div>`;
}

function renderPlacement() {
  ensurePlacementRecords();
  const yearSelect = document.getElementById("placementYear");
  const classSelect = document.getElementById("placementClass");
  if (yearSelect) { const current = yearSelect.value; yearSelect.innerHTML = academicYearRecords.map((year) => `<option>${year.name}</option>`).join(""); if (current) yearSelect.value = current; }
  if (classSelect) { const current = classSelect.value; classSelect.innerHTML = classRecords.map((kelas) => `<option>${kelas.name}</option>`).join(""); if (current) classSelect.value = current; }

  const studentSearch = document.getElementById("placementStudentSearch")?.value.trim().toLowerCase() || "";
  const filteredStudents = studentRecords.filter((student) => [student.id, student.name, student.className, student.status].join(" ").toLowerCase().includes(studentSearch));
  const studentTotalPages = Math.max(1, Math.ceil(filteredStudents.length / 10));
  placementStudentPage = Math.min(placementStudentPage, studentTotalPages);
  const studentRows = filteredStudents.slice((placementStudentPage - 1) * 10, placementStudentPage * 10);
  const studentBody = document.getElementById("placementStudentBody");
  if (studentBody) studentBody.innerHTML = studentRows.length ? studentRows.map((student) => `<tr><td><input type="checkbox" class="placement-check" value="${student.id}"></td><td>${student.id}</td><td>${student.name}</td><td>${badgeStatus(student.status)}</td></tr>`).join("") : `<tr><td colspan="4">Data siswa tidak ditemukan.</td></tr>`;
  renderSimplePagination("placementStudentPagination", placementStudentPage, filteredStudents.length, "placement-student");

  const placementSearch = document.getElementById("placementSearch")?.value.trim().toLowerCase() || "";
  const filteredPlacements = placementRecords.map((item, index) => ({ item, index, student: studentRecords.find((record) => record.id === item.studentId) })).filter(({ item, student }) => [item.studentId, item.className, item.year, item.status, student?.name].join(" ").toLowerCase().includes(placementSearch));
  const placementTotalPages = Math.max(1, Math.ceil(filteredPlacements.length / 10));
  placementPage = Math.min(placementPage, placementTotalPages);
  const placementRows = filteredPlacements.slice((placementPage - 1) * 10, placementPage * 10);
  const placementBody = document.getElementById("placementTableBody");
  if (placementBody) placementBody.innerHTML = placementRows.length ? placementRows.map(({ item, index, student }) => `<tr><td>${student?.name || item.studentId}</td><td>${item.className}</td><td>${item.year}</td><td>${badgeStatus(item.status)}</td><td><button class="btn btn-outline btn-sm" data-placement-edit="${index}">Ubah</button> <button class="btn btn-red btn-sm" data-placement-delete="${index}">Hapus</button></td></tr>`).join("") : `<tr><td colspan="5">Data penempatan tidak ditemukan.</td></tr>`;
  renderSimplePagination("placementPagination", placementPage, filteredPlacements.length, "placement");
}

function getNextClassName(className) {
  const match = className.match(/Kelas\s+(\d)([A-C])/);
  if (!match) return className;
  const nextGrade = Math.min(Number(match[1]) + 1, 6);
  return `Kelas ${nextGrade}${match[2]}`;
}

function renderAcademicMenus() {
  const yearOptions = academicYearRecords.map((year) => `<option>${year.name}</option>`).join("");
  const classOptions = classRecords.map((kelas) => `<option>${kelas.name}</option>`).join("");
  const teacherOptions = teacherRecords.map((teacher, index) => `<option value="${index}">${teacher.name}</option>`).join("");
  const principalOptions = principalRecords.map((principal, index) => `<option value="${index}">${principal.name}</option>`).join("");

  ["promotionYear", "assignmentYear"].forEach((id) => {
    const select = document.getElementById(id);
    if (select) { const current = select.value; select.innerHTML = yearOptions; if (current) select.value = current; }
  });
  ["promotionFromClass", "promotionToClass", "assignmentClass"].forEach((id) => {
    const select = document.getElementById(id);
    if (select) { const current = select.value; select.innerHTML = classOptions; if (current) select.value = current; }
  });
  const assignmentTeacher = document.getElementById("assignmentTeacher");
  if (assignmentTeacher) { const current = assignmentTeacher.value; assignmentTeacher.innerHTML = teacherOptions; if (current) assignmentTeacher.value = current; }
  const periodPrincipal = document.getElementById("periodPrincipal");
  if (periodPrincipal) { const current = periodPrincipal.value; periodPrincipal.innerHTML = principalOptions; if (current) periodPrincipal.value = current; }

  const fromClass = document.getElementById("promotionFromClass")?.value || classRecords[0]?.name || "Kelas 1A";
  const toClassSelect = document.getElementById("promotionToClass");
  if (toClassSelect && !toClassSelect.value) toClassSelect.value = getNextClassName(fromClass);
  const toClass = toClassSelect?.value || getNextClassName(fromClass);
  const promotionStudents = studentRecords.filter((student) => student.className === fromClass).slice(0, 20);
  const promotionBody = document.getElementById("promotionTableBody");
  if (promotionBody) {
    promotionBody.innerHTML = promotionStudents.map((student, index) => `<tr><td>${index + 1}</td><td>${student.id}</td><td>${student.name}</td><td>${fromClass}</td><td>${toClass}</td><td>${badgeStatus("Aktif")}</td></tr>`).join("");
  }

  const assignmentBody = document.getElementById("teacherAssignmentTableBody");
  if (assignmentBody) {
    assignmentBody.innerHTML = teacherAssignmentRecords.map((assignment, index) => {
      const teacher = teacherRecords.find((item) => item.nip === assignment.teacherNip) || teacherRecords[0];
      return `<tr><td>${index + 1}</td><td>${teacher?.name || "-"}</td><td>${assignment.teacherNip}</td><td>${assignment.className}</td><td>${assignment.year}</td><td>${badgeStatus(assignment.status)}</td></tr>`;
    }).join("");
  }

  const periodBody = document.getElementById("principalPeriodTableBody");
  if (periodBody) {
    periodBody.innerHTML = principalPeriodRecords.map((period, index) => {
      const principal = principalRecords.find((item) => item.nip === period.principalNip) || principalRecords[0];
      return `<tr><td>${index + 1}</td><td>${principal?.name || "-"}</td><td>${period.principalNip}</td><td>${period.startYear} - ${period.endYear}</td><td>${badgeStatus(period.status)}</td></tr>`;
    }).join("");
  }
}
function renderMasterLists() {
  ["student", "teacher", "principal"].forEach(renderAdminTable);
  renderYearTable();
  renderPlacement();
  renderAcademicMenus();
  renderAttendanceRoster();
  document.querySelectorAll(".assessment-page").forEach(renderAssessmentRoster);
  const classTableBody = document.getElementById("classTableBody");
  const assessmentClassSelects = document.querySelectorAll(".assessment-class");
  const attendanceClassSelect = document.getElementById("attendanceClass");
  const studentClassFilter = document.getElementById("studentClassFilter");
  if (classTableBody) classTableBody.innerHTML = classRecords.map((kelas, index) => `<tr><td>${index + 1}</td><td>${kelas.name}</td><td><button class="btn btn-outline btn-sm" type="button" data-edit-class="${index}">Ubah</button> <button class="btn btn-red btn-sm" type="button" data-delete-class="${index}">Hapus</button></td></tr>`).join("");
  const classOptions = classRecords.map((kelas) => `<option>${kelas.name}</option>`).join("");
  assessmentClassSelects.forEach((select) => { const current = select.value; select.innerHTML = classOptions; if (current) select.value = current; });
  if (attendanceClassSelect) { const current = attendanceClassSelect.value; attendanceClassSelect.innerHTML = classOptions; if (current) attendanceClassSelect.value = current; }
  if (studentClassFilter) { const current = studentClassFilter.value; studentClassFilter.innerHTML = `<option value="">Semua Kelas</option>${classOptions}`; studentClassFilter.value = current; }
  syncAssessmentPlanOptions();
}

