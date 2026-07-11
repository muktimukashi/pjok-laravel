function getStudentsByClass(className) {
  return studentRecords.filter((student) => student.className === className && student.status !== "Nonaktif");
}

function getSelectedClassFromPage(page) {
  return page?.querySelector(".assessment-class")?.value || document.getElementById("attendanceClass")?.value || classRecords[0]?.name || "";
}

function renderAttendanceRoster() {
  const className = document.getElementById("attendanceClass")?.value || classRecords[0]?.name || "";
  const tbody = document.querySelector(".attendance-table tbody");
  if (!tbody) return;
  const students = getStudentsByClass(className);
  tbody.innerHTML = students.length ? students.map((student, index) => `
    <tr data-student-id="${student.id}">
      <td>${index + 1}</td>
      <td>${student.name}</td>
      <td><button class="status-choice active status-h">H</button></td>
      <td><button class="status-choice status-s">S</button></td>
      <td><button class="status-choice status-i">I</button></td>
      <td><button class="status-choice status-a">A</button></td>
      <td><input placeholder="Catatan"></td>
    </tr>
  `).join("") : `<tr><td colspan="7">Belum ada siswa di ${className}.</td></tr>`;
}

function renderAssessmentRoster(page) {
  if (!page) return;
  const type = page.dataset.type;
  const className = getSelectedClassFromPage(page);
  const tbody = page.querySelector(".assessment-table tbody");
  if (!tbody) return;
  const students = getStudentsByClass(className);
  tbody.innerHTML = students.length ? students.map((student, index) => {
    const currentScore = Math.max(1, Math.min(5, Math.round(Number(student[type === "Afektif" ? "affective" : type === "Kognitif" ? "cognitive" : "psychomotor"]) || 4)));
    const scoreButtons = [1, 2, 3, 4, 5].map((score) => `<td><button class="score-choice-btn ${score === currentScore ? "active" : ""}">${score}</button></td>`).join("");
    const scoreInput = type === "Kognitif" ? `<td><input value="${currentScore}" /></td>` : "";
    return `<tr data-student-id="${student.id}"><td>${index + 1}</td><td>${student.name}</td>${scoreButtons}${scoreInput}<td><input placeholder="Catatan"></td></tr>`;
  }).join("") : `<tr><td colspan="${type === "Kognitif" ? 9 : 8}">Belum ada siswa di ${className}.</td></tr>`;
}

function syncRoleDataViews() {
  renderAttendanceRoster();
  document.querySelectorAll(".assessment-page").forEach(renderAssessmentRoster);
  renderRecapFilterOptions();
  renderRecapTable();
  renderDashboardContent();
}
function renderAssessmentForms() {
  document.querySelectorAll(".assessment-page").forEach((page) => {
    const type = page.dataset.type;
    const meta = assessmentData[type];
    if (!meta) return;

    const meeting = page.querySelector(`#${meta.meetingId}`);
    if (meeting) {
      meeting.innerHTML = Array.from({ length: 18 }, (_, i) => `<option>${i + 1}</option>`).join("");
    }

    const aspectSelect = meta.aspectId ? page.querySelector(`#${meta.aspectId}`) : null;
    if (aspectSelect && meta.options) {
      aspectSelect.innerHTML = meta.options.map((option) => `<option>${option}</option>`).join("");
    }

    const cognitiveTask = page.querySelector("select:not(.assessment-year):not(.assessment-semester):not(.assessment-class):not(.assessment-aspect):not([id$='Meeting'])");
    if (type === "Kognitif" && cognitiveTask) {
      cognitiveTask.innerHTML = cognitiveOptions.map((option) => `<option>${option}</option>`).join("");
    }

    renderAssessmentRoster(page);

    const dateField = page.querySelector(`#${meta.dateId}`);
    if (dateField && !dateField.value) {
      dateField.value = new Date().toISOString().slice(0, 10);
    }
  });

  const attendanceMeeting = document.getElementById("attendanceMeeting");
  if (attendanceMeeting) {
    attendanceMeeting.innerHTML = Array.from({ length: 18 }, (_, i) => `<option>${i + 1}</option>`).join("");
  }

  const attendanceDate = document.getElementById("attendanceDate");
  if (attendanceDate && !attendanceDate.value) {
    attendanceDate.value = new Date().toISOString().slice(0, 10);
  }

  const criteriaMeetingFilter = document.getElementById("criteriaMeetingFilter");
  if (criteriaMeetingFilter) {
    criteriaMeetingFilter.innerHTML = `<option value="">Semua Pertemuan</option>${Array.from({ length: 18 }, (_, i) => `<option>${i + 1}</option>`).join("")}`;
  }
}

