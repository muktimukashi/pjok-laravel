function collectCriteriaFromAssessmentPages() {
  const liveRecords = [];
  document.querySelectorAll(".assessment-page").forEach((page) => {
    const type = page.dataset.type;
    const year = page.querySelector(".assessment-year")?.value || "2025/2026";
    const semester = page.querySelector(".assessment-semester")?.value || "Ganjil";
    const className = page.querySelector(".assessment-class")?.value || "Kelas 5A";
    const meeting = page.querySelector("select[id$='Meeting']")?.value || "1";
    const aspect = page.querySelector(".assessment-aspect")?.value || page.querySelector("select:not(.assessment-year):not(.assessment-semester):not(.assessment-class):not([id$='Meeting'])")?.value || "Kriteria Umum";
    const criteriaInputs = page.querySelectorAll(".criteria-input");
    const criteria = collectCriteriaInputs(page);

    if (criteriaInputs.length) {
      liveRecords.push({ year, semester, className, meeting, type, aspect, criteria });
    }
  });

  return liveRecords;
}

function getAssessmentContext(page) {
  const type = page.dataset.type;
  const year = page.querySelector(".assessment-year")?.value || "2025/2026";
  const semester = page.querySelector(".assessment-semester")?.value || "Ganjil";
  const className = page.querySelector(".assessment-class")?.value || "Kelas 5A";
  const meeting = page.querySelector("select[id$='Meeting']")?.value || "1";
  return { type, year, semester, className, meeting };
}

function getAssessmentKey(context) {
  return [context.type, context.year, context.semester, context.className, context.meeting].join("|");
}

function collectCriteriaInputs(page) {
  const criteria = {};
  page.querySelectorAll(".criteria-input").forEach((input) => {
    const score = input.dataset.score;
    criteria[score] = input.value.trim();
  });
  return criteria;
}

function findAssessmentRecord(context) {
  const key = getAssessmentKey(context);
  return assessmentRecords.find((record) => getAssessmentKey(record) === key);
}

function saveAssessmentRecord(page) {
  const context = getAssessmentContext(page);
  const record = {
    ...context,
    materi: page.querySelector("input[id$='Materi']")?.value.trim() || "",
    tujuan: page.querySelector("input[id$='Tujuan']")?.value.trim() || "",
    aspect: page.querySelector(".assessment-aspect")?.value || page.querySelector("select:not(.assessment-year):not(.assessment-semester):not(.assessment-class):not([id$='Meeting'])")?.value || "Kriteria Umum",
    criteria: collectCriteriaInputs(page)
  };
  const existingIndex = assessmentRecords.findIndex((item) => getAssessmentKey(item) === getAssessmentKey(record));

  if (existingIndex >= 0) {
    assessmentRecords[existingIndex] = record;
  } else {
    assessmentRecords.push(record);
  }

  renderCriteriaFilterOptions();
  renderCriteriaRecapTable();
}

function loadAssessmentRecord(page) {
  const context = getAssessmentContext(page);
  const record = findAssessmentRecord(context);
  const materiField = page.querySelector("input[id$='Materi']");
  const tujuanField = page.querySelector("input[id$='Tujuan']");
  const aspectField = page.querySelector(".assessment-aspect");

  if (materiField) materiField.value = record?.materi || "";
  if (tujuanField) tujuanField.value = record?.tujuan || "";
  if (aspectField && record?.aspect) aspectField.value = record.aspect;

  page.querySelectorAll(".criteria-input").forEach((input) => {
    const score = input.dataset.score;
    input.value = record?.criteria?.[score] || "";
  });
}

function openAssessmentPanels(page) {
  page.querySelectorAll(".assessment-detail-panel, .criteria-panel").forEach((panel) => {
    panel.open = true;
  });
}

function clearAssessmentRecord(page) {
  page.querySelectorAll("input[id$='Materi'], input[id$='Tujuan']").forEach((input) => {
    input.value = "";
  });
  page.querySelectorAll(".criteria-input").forEach((input) => {
    const score = input.dataset.score;
    input.value = input.defaultValue || defaultCriteria[score];
  });
  openAssessmentPanels(page);
}

function editAssessmentRecord(page) {
  loadAssessmentRecord(page);
  openAssessmentPanels(page);
}

function deleteAssessmentRecord(page) {
  const context = getAssessmentContext(page);
  const key = getAssessmentKey(context);
  const existingIndex = assessmentRecords.findIndex((record) => getAssessmentKey(record) === key);

  if (existingIndex >= 0) {
    assessmentRecords.splice(existingIndex, 1);
  }

  clearAssessmentRecord(page);
  renderCriteriaFilterOptions();
  renderCriteriaRecapTable();
}
function syncAssessmentRecords() {
  document.querySelectorAll(".assessment-page").forEach(loadAssessmentRecord);
}
function renderCriteriaFilterOptions() {
  const yearFilter = document.getElementById("criteriaYearFilter");
  const classFilter = document.getElementById("criteriaClassFilter");
  const typeFilter = document.getElementById("criteriaTypeFilter");
  const meetingFilter = document.getElementById("criteriaMeetingFilter");
  const records = [...criteriaRecords, ...assessmentRecords, ...collectCriteriaFromAssessmentPages()];

  if (yearFilter) {
    const current = yearFilter.value;
    const years = [...new Set(records.map((record) => record.year).filter(Boolean))];
    yearFilter.innerHTML = `<option value="">Semua Tahun</option>${years.map((year) => `<option>${year}</option>`).join("")}`;
    yearFilter.value = years.includes(current) ? current : "";
  }

  if (classFilter) {
    const current = classFilter.value;
    const classes = [...new Set([...classRecords.map((kelas) => kelas.name), ...records.map((record) => record.className)].filter(Boolean))];
    classFilter.innerHTML = `<option value="">Semua Kelas</option>${classes.map((className) => `<option>${className}</option>`).join("")}`;
    classFilter.value = classes.includes(current) ? current : "";
  }

  if (typeFilter) {
    const current = typeFilter.value;
    const types = [...new Set(["Afektif", "Kognitif", "Psikomotor", ...records.map((record) => record.type)].filter(Boolean))];
    typeFilter.innerHTML = `<option value="">Semua Jenis</option>${types.map((type) => `<option>${type}</option>`).join("")}`;
    typeFilter.value = types.includes(current) ? current : "";
  }

  if (meetingFilter) {
    const current = meetingFilter.value;
    const meetings = getAssessmentPlanMeetings();
    meetingFilter.innerHTML = `<option value="">Semua Pertemuan</option>${meetings.map((meeting) => `<option>${meeting}</option>`).join("")}`;
    meetingFilter.value = current;
  }
}
function getCriteriaRecapRecords() {
  const yearFilter = document.getElementById("criteriaYearFilter")?.value || "";
  const semesterFilter = document.getElementById("criteriaSemesterFilter")?.value || "";
  const classFilter = document.getElementById("criteriaClassFilter")?.value || "";
  const typeFilter = document.getElementById("criteriaTypeFilter")?.value || "";
  const meetingFilter = document.getElementById("criteriaMeetingFilter")?.value || "";
  const liveRecords = collectCriteriaFromAssessmentPages();
  const allRecords = [...criteriaRecords, ...assessmentRecords, ...liveRecords];

  return allRecords.filter((record) => {
    const matchYear = !yearFilter || record.year === yearFilter;
    const matchSemester = !semesterFilter || record.semester === semesterFilter;
    const matchClass = !classFilter || record.className === classFilter;
    const matchType = !typeFilter || record.type === typeFilter;
    const matchMeeting = !meetingFilter || record.meeting === meetingFilter;
    return matchYear && matchSemester && matchClass && matchType && matchMeeting;
  });
}

function renderCriteriaRecapPagination(totalRecords) {
  const pagination = document.getElementById("criteriaRecapPagination");
  if (!pagination) return;

  const totalPages = Math.max(1, Math.ceil(totalRecords / 10));
  criteriaRecapPage = Math.min(Math.max(criteriaRecapPage, 1), totalPages);
  const start = totalRecords ? (criteriaRecapPage - 1) * 10 + 1 : 0;
  const end = Math.min(criteriaRecapPage * 10, totalRecords);

  pagination.innerHTML = `
    <span>Menampilkan ${start}-${end} dari ${totalRecords} data</span>
    <div class="pagination-actions">
      <button class="btn btn-soft btn-sm" type="button" data-criteria-page="prev" ${criteriaRecapPage === 1 ? "disabled" : ""}>‹</button>
      <strong>Halaman ${criteriaRecapPage} / ${totalPages}</strong>
      <button class="btn btn-soft btn-sm" type="button" data-criteria-page="next" ${criteriaRecapPage === totalPages ? "disabled" : ""}>›</button>
    </div>
  `;
}

function renderCriteriaRecapTable() {
  const tableBody = document.getElementById("criteriaRecapTableBody");
  if (!tableBody) return;

  const filteredRecords = getCriteriaRecapRecords();
  const pageRecords = filteredRecords.slice((criteriaRecapPage - 1) * 10, criteriaRecapPage * 10);
  tableBody.innerHTML = pageRecords.length
    ? pageRecords
        .map(
          (record, index) => `
            <tr>
              <td>${(criteriaRecapPage - 1) * 10 + index + 1}</td>
              <td>${record.year}</td>
              <td>${record.semester}</td>
              <td>${record.className}</td>
              <td>${record.type}</td>
              <td>${record.meeting}</td>
              <td>${record.aspect}</td>
              <td>${record.criteria[1] || "-"}</td>
              <td>${record.criteria[2] || "-"}</td>
              <td>${record.criteria[3] || "-"}</td>
              <td>${record.criteria[4] || "-"}</td>
              <td>${record.criteria[5] || "-"}</td>
            </tr>
          `
        )
        .join("")
    : `<tr><td colspan="12">Data kriteria penilaian belum ditemukan.</td></tr>`;

  renderCriteriaRecapPagination(filteredRecords.length);
}

function changeCriteriaRecapPage(direction) {
  const totalPages = Math.max(1, Math.ceil(getCriteriaRecapRecords().length / 10));
  if (direction === "prev") criteriaRecapPage = Math.max(1, criteriaRecapPage - 1);
  if (direction === "next") criteriaRecapPage = Math.min(totalPages, criteriaRecapPage + 1);
  renderCriteriaRecapTable();
}
function escapeExcelCell(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function exportCriteriaRecapExcel() {
  const records = getCriteriaRecapRecords();
  const headers = ["No", "Tahun", "Semester", "Kelas", "Jenis Asesmen", "Pertemuan", "Aspek/Indikator", "Nilai 1", "Nilai 2", "Nilai 3", "Nilai 4", "Nilai 5"];
  const rows = records.map((record, index) => [
    index + 1,
    record.year,
    record.semester,
    record.className,
    record.type,
    record.meeting,
    record.aspect,
    record.criteria[1],
    record.criteria[2],
    record.criteria[3],
    record.criteria[4],
    record.criteria[5]
  ]);
  const tableRows = [headers, ...rows]
    .map((row) => `<tr>${row.map((cell) => `<td>${escapeExcelCell(cell)}</td>`).join("")}</tr>`)
    .join("");
  const worksheet = `<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><table>${tableRows}</table></body></html>`;
  const blob = new Blob([worksheet], { type: "application/vnd.ms-excel;charset=utf-8;" });
  const link = document.createElement("a");
  const date = new Date().toISOString().slice(0, 10);

  link.href = URL.createObjectURL(blob);
  link.download = `rekap-kriteria-penilaian-${date}.xls`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(link.href);
}
function getAssessmentOptions(type) {
  if (type === "Afektif") return affectiveOptions;
  if (type === "Kognitif") return cognitiveOptions;
  return psychomotorOptions;
}

function collectPlanCriteriaInputs() {
  const criteria = {};
  document.querySelectorAll(".plan-criteria-input").forEach((input) => {
    const score = input.dataset.score;
    criteria[score] = input.value.trim();
  });
  return criteria;
}

function getAssessmentPlanContext() {
  return {
    type: document.getElementById("planType")?.value || "Afektif",
    year: document.getElementById("planYear")?.value || "2025/2026",
    semester: document.getElementById("planSemester")?.value || "Ganjil",
    className: document.getElementById("planClass")?.value || "Kelas 5A",
    meeting: document.getElementById("planMeeting")?.value || ""
  };
}

function syncPlanAspectOptions() {
  const type = document.getElementById("planType")?.value || "Afektif";
  const aspectSelect = document.getElementById("planAspect");
  if (!aspectSelect) return;

  const current = aspectSelect.value;
  const options = getAssessmentOptions(type);
  aspectSelect.innerHTML = options.map((option) => `<option>${option}</option>`).join("");
  aspectSelect.value = options.includes(current) ? current : options[0];
}

function getAssessmentPlanMeetings() {
  const meetings = [...new Set(assessmentRecords.map((record) => record.meeting).filter(Boolean))];
  return meetings.sort((a, b) => Number(a) - Number(b));
}

function getNextAssessmentPlanMeeting(context = getAssessmentPlanContext()) {
  const meetings = assessmentRecords
    .filter((record) => record.year === context.year && record.semester === context.semester && record.className === context.className && record.type === context.type)
    .map((record) => Number(record.meeting))
    .filter((meeting) => Number.isFinite(meeting));
  const lastMeeting = meetings.length ? Math.max(...meetings) : 0;
  return String(lastMeeting + 1);
}

function ensurePlanMeetingOption(meeting) {
  const meetingSelect = document.getElementById("planMeeting");
  if (!meetingSelect || !meeting) return;
  const exists = Array.from(meetingSelect.options).some((option) => option.value === meeting || option.textContent === meeting);
  if (!exists) meetingSelect.insertAdjacentHTML("beforeend", `<option>${meeting}</option>`);
  meetingSelect.value = meeting;
}
function clearAssessmentPlanDetail() {
  const materi = document.getElementById("planMateri");
  const tujuan = document.getElementById("planTujuan");
  if (materi) materi.value = "";
  if (tujuan) tujuan.value = "";
  document.querySelectorAll(".plan-criteria-input").forEach((input) => {
    input.value = "";
  });
}
function loadAssessmentPlanForm() {
  syncPlanAspectOptions();
  const context = getAssessmentPlanContext();
  const record = findAssessmentRecord(context);
  const materi = document.getElementById("planMateri");
  const tujuan = document.getElementById("planTujuan");
  const aspect = document.getElementById("planAspect");

  if (materi) materi.value = record?.materi || "";
  if (tujuan) tujuan.value = record?.tujuan || "";
  if (aspect && record?.aspect) aspect.value = record.aspect;
  document.querySelectorAll(".plan-criteria-input").forEach((input) => {
    const score = input.dataset.score;
    input.value = record?.criteria?.[score] || "";
  });
}

function openAssessmentPlanPanel() {
  const panel = document.getElementById("assessmentPlanPanel");
  if (panel) panel.open = true;
}

function newAssessmentPlan() {
  syncAssessmentPlanOptions();
  const nextMeeting = getNextAssessmentPlanMeeting();
  ensurePlanMeetingOption(nextMeeting);
  syncPlanAspectOptions();
  clearAssessmentPlanDetail();
  openAssessmentPlanPanel();
}

function saveAssessmentPlan() {
  const context = getAssessmentPlanContext();
  if (!context.meeting) {
    alert("Pilih pertemuan dulu sebelum menyimpan rencana asesmen.");
    return;
  }

  const record = {
    ...context,
    materi: document.getElementById("planMateri")?.value.trim() || "",
    tujuan: document.getElementById("planTujuan")?.value.trim() || "",
    aspect: document.getElementById("planAspect")?.value || "Kriteria Umum",
    criteria: collectPlanCriteriaInputs()
  };
  const existingIndex = assessmentRecords.findIndex((item) => getAssessmentKey(item) === getAssessmentKey(record));

  if (existingIndex >= 0) assessmentRecords[existingIndex] = record;
  else assessmentRecords.push(record);

  syncAssessmentPlanOptions();
  ensurePlanMeetingOption(context.meeting);
  renderAssessmentPlanFilterOptions();
  renderAssessmentPlanTable();
  syncAssessmentRecords();
  renderCriteriaFilterOptions();
  renderCriteriaRecapTable();
}

function editAssessmentPlan() {
  loadAssessmentPlanForm();
  openAssessmentPlanPanel();
}

function deleteAssessmentPlan() {
  const context = getAssessmentPlanContext();
  const key = getAssessmentKey(context);
  const index = assessmentRecords.findIndex((record) => getAssessmentKey(record) === key);
  if (index >= 0) assessmentRecords.splice(index, 1);
  newAssessmentPlan();
  renderAssessmentPlanFilterOptions();
  renderAssessmentPlanTable();
  syncAssessmentRecords();
  renderCriteriaFilterOptions();
  renderCriteriaRecapTable();
}

function deleteAssessmentPlanByIndex(index) {
  if (!assessmentRecords[index]) return;
  assessmentRecords.splice(index, 1);
  const totalPages = Math.max(1, Math.ceil(getFilteredAssessmentPlanRecords().length / 10));
  assessmentPlanPage = Math.min(assessmentPlanPage, totalPages);
  renderAssessmentPlanFilterOptions();
  renderAssessmentPlanTable();
  syncAssessmentRecords();
  renderCriteriaFilterOptions();
  renderCriteriaRecapTable();
}

function renderAssessmentPlanFilterOptions() {
  const yearFilter = document.getElementById("planFilterYear");
  const classFilter = document.getElementById("planFilterClass");
  const typeFilter = document.getElementById("planFilterType");
  const meetingFilter = document.getElementById("planFilterMeeting");
  const records = assessmentRecords;

  if (yearFilter) {
    const current = yearFilter.value;
    const years = [...new Set(records.map((record) => record.year).filter(Boolean))];
    yearFilter.innerHTML = `<option value="">Semua Tahun</option>${years.map((year) => `<option>${year}</option>`).join("")}`;
    yearFilter.value = years.includes(current) ? current : "";
  }

  if (classFilter) {
    const current = classFilter.value;
    const classes = [...new Set([...classRecords.map((kelas) => kelas.name), ...records.map((record) => record.className)].filter(Boolean))];
    classFilter.innerHTML = `<option value="">Semua Kelas</option>${classes.map((className) => `<option>${className}</option>`).join("")}`;
    classFilter.value = classes.includes(current) ? current : "";
  }

  if (typeFilter) {
    const current = typeFilter.value;
    const types = [...new Set(["Afektif", "Kognitif", "Psikomotor", ...records.map((record) => record.type)].filter(Boolean))];
    typeFilter.innerHTML = `<option value="">Semua Jenis</option>${types.map((type) => `<option>${type}</option>`).join("")}`;
    typeFilter.value = types.includes(current) ? current : "";
  }

  if (meetingFilter) {
    const current = meetingFilter.value;
    const meetings = getAssessmentPlanMeetings();
    meetingFilter.innerHTML = `<option value="">Semua Pertemuan</option>${meetings.map((meeting) => `<option>${meeting}</option>`).join("")}`;
    meetingFilter.value = current;
  }
}

function getFilteredAssessmentPlanRecords() {
  const yearFilter = document.getElementById("planFilterYear")?.value || "";
  const semesterFilter = document.getElementById("planFilterSemester")?.value || "";
  const classFilter = document.getElementById("planFilterClass")?.value || "";
  const typeFilter = document.getElementById("planFilterType")?.value || "";
  const meetingFilter = document.getElementById("planFilterMeeting")?.value || "";
  const search = document.getElementById("planSearch")?.value.trim().toLowerCase() || "";

  return assessmentRecords
    .map((record, index) => ({ record, index }))
    .filter(({ record }) => {
      const haystack = [record.year, record.semester, record.className, record.type, record.meeting, record.aspect, record.materi, record.tujuan, ...Object.values(record.criteria || {})].join(" ").toLowerCase();
      const matchYear = !yearFilter || record.year === yearFilter;
      const matchSemester = !semesterFilter || record.semester === semesterFilter;
      const matchClass = !classFilter || record.className === classFilter;
      const matchType = !typeFilter || record.type === typeFilter;
      const matchMeeting = !meetingFilter || record.meeting === meetingFilter;
      const matchSearch = !search || haystack.includes(search);
      return matchYear && matchSemester && matchClass && matchType && matchMeeting && matchSearch;
    });
}

function renderAssessmentPlanPagination(totalRecords) {
  const pagination = document.getElementById("assessmentPlanPagination");
  if (!pagination) return;

  const totalPages = Math.max(1, Math.ceil(totalRecords / 10));
  assessmentPlanPage = Math.min(Math.max(assessmentPlanPage, 1), totalPages);
  const start = totalRecords ? (assessmentPlanPage - 1) * 10 + 1 : 0;
  const end = Math.min(assessmentPlanPage * 10, totalRecords);

  pagination.innerHTML = `
    <span>Menampilkan ${start}-${end} dari ${totalRecords} data</span>
    <div class="pagination-actions">
      <button class="btn btn-soft btn-sm" type="button" data-plan-page="prev" ${assessmentPlanPage === 1 ? "disabled" : ""}>‹</button>
      <strong>Halaman ${assessmentPlanPage} / ${totalPages}</strong>
      <button class="btn btn-soft btn-sm" type="button" data-plan-page="next" ${assessmentPlanPage === totalPages ? "disabled" : ""}>›</button>
    </div>
  `;
}

function renderAssessmentPlanTable() {
  const tableBody = document.getElementById("assessmentPlanTableBody");
  if (!tableBody) return;

  const filteredRecords = getFilteredAssessmentPlanRecords();
  const pageRecords = filteredRecords.slice((assessmentPlanPage - 1) * 10, assessmentPlanPage * 10);

  tableBody.innerHTML = pageRecords.length
    ? pageRecords
        .map(({ record, index }, rowIndex) => `
          <tr>
            <td>${(assessmentPlanPage - 1) * 10 + rowIndex + 1}</td>
            <td>${record.year}</td>
            <td>${record.semester}</td>
            <td>${record.className}</td>
            <td>${record.type}</td>
            <td>${record.meeting}</td>
            <td>${record.aspect}</td>
            <td>${record.materi || "-"}</td>
            <td><button class="btn btn-outline btn-sm" type="button" data-load-plan="${index}">Edit</button> <button class="btn btn-red btn-sm" type="button" data-delete-plan="${index}">Hapus</button></td>
          </tr>
        `)
        .join("")
    : `<tr><td colspan="9">Data rencana asesmen belum ditemukan.</td></tr>`;

  renderAssessmentPlanPagination(filteredRecords.length);
}

function changeAssessmentPlanPage(direction) {
  const totalPages = Math.max(1, Math.ceil(getFilteredAssessmentPlanRecords().length / 10));
  if (direction === "prev") assessmentPlanPage = Math.max(1, assessmentPlanPage - 1);
  if (direction === "next") assessmentPlanPage = Math.min(totalPages, assessmentPlanPage + 1);
  renderAssessmentPlanTable();
}

function loadAssessmentPlanByIndex(index) {
  const record = assessmentRecords[index];
  if (!record) return;
  document.getElementById("planYear").value = record.year;
  document.getElementById("planSemester").value = record.semester;
  document.getElementById("planClass").value = record.className;
  document.getElementById("planType").value = record.type;
  document.getElementById("planMeeting").value = record.meeting;
  syncPlanAspectOptions();
  loadAssessmentPlanForm();
  openAssessmentPlanPanel();
}
function syncAssessmentPlanOptions() {
  const classSelect = document.getElementById("planClass");
  const meetingSelect = document.getElementById("planMeeting");
  if (classSelect) {
    const current = classSelect.value;
    classSelect.innerHTML = classRecords.map((kelas) => `<option>${kelas.name}</option>`).join("");
    if (current) classSelect.value = current;
  }
  if (meetingSelect) {
    const current = meetingSelect.value;
    const meetings = getAssessmentPlanMeetings();
    meetingSelect.innerHTML = `<option value="">Pilih Pertemuan</option>${meetings.map((meeting) => `<option>${meeting}</option>`).join("")}`;
    meetingSelect.value = meetings.includes(current) ? current : "";
  }
  syncPlanAspectOptions();
}

