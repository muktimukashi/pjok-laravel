function normalizeScore(value) {
  const number = Number(value) || 0;
  return number <= 5 ? number * 20 : number;
}

function calculateAverageScore(student) {
  const scores = [
    Number(student.attendance) || 0,
    normalizeScore(student.affective),
    normalizeScore(student.psychomotor),
    normalizeScore(student.cognitive)
  ];
  const total = scores.reduce((sum, score) => sum + score, 0) / scores.length;
  return Number(total.toFixed(1));
}

function getScorePredicate(score) {
  if (score >= 86) return { text: "Sangat Baik", className: "badge-green" };
  if (score >= 76) return { text: "Baik", className: "badge-blue" };
  if (score >= 66) return { text: "Cukup", className: "badge-yellow" };
  return { text: "Perlu Bimbingan", className: "badge-red" };
}
function renderRecapFilterOptions() {
  const yearFilter = document.getElementById("recapYearFilter");
  if (!yearFilter) return;

  const currentYear = yearFilter.value;
  const years = [...new Set(studentRecords.map((student) => student.year).filter(Boolean))];
  yearFilter.innerHTML = `<option value="">Semua Tahun</option>${years.map((year) => `<option>${year}</option>`).join("")}`;
  yearFilter.value = years.includes(currentYear) ? currentYear : "";
}

function getRecapRecords() {
  const yearFilter = document.getElementById("recapYearFilter")?.value || "";
  const semesterFilter = document.getElementById("recapSemesterFilter")?.value || "";
  const records = appState.role === "Siswa" && appState.activeStudent ? [appState.activeStudent] : studentRecords;

  return records.filter((student) => {
    const matchYear = !yearFilter || student.year === yearFilter;
    const matchSemester = !semesterFilter || student.semester === semesterFilter;
    return matchYear && matchSemester;
  });
}
function getRecapType() {
  return document.getElementById("recapTypeFilter")?.value || "final";
}

function getRecapColumns(type = getRecapType()) {
  const baseColumns = [
    { label: "No", value: (_student, index) => index + 1 },
    { label: "Nama", value: (student) => student.name },
    { label: "Kelas", value: (student) => student.className },
    { label: "Tahun", value: (student) => student.year || "2025/2026" },
    { label: "Semester", value: (student) => student.semester || "Ganjil" }
  ];
  const scoreColumns = {
    attendance: [{ label: "Kehadiran", value: (student) => student.attendance || 0, score: (student) => Number(student.attendance) || 0 }],
    affective: [{ label: "Afektif", value: (student) => student.affective, score: (student) => normalizeScore(student.affective) }],
    cognitive: [{ label: "Kognitif", value: (student) => student.cognitive, score: (student) => normalizeScore(student.cognitive) }],
    psychomotor: [{ label: "Psikomotor", value: (student) => student.psychomotor, score: (student) => normalizeScore(student.psychomotor) }]
  };

  if (type !== "final") {
    const selected = scoreColumns[type] || scoreColumns.attendance;
    return [
      ...baseColumns,
      ...selected,
      { label: "Keterangan", value: (student) => getScorePredicate(selected[0].score(student)).text, badge: (student) => getScorePredicate(selected[0].score(student)) }
    ];
  }

  return [
    ...baseColumns,
    { label: "Kehadiran", value: (student) => student.attendance || 0 },
    { label: "Afektif", value: (student) => student.affective },
    { label: "Psikomotor", value: (student) => student.psychomotor },
    { label: "Kognitif", value: (student) => student.cognitive },
    { label: "Nilai Total", value: (student) => calculateAverageScore(student), strong: true },
    { label: "Keterangan", value: (student) => getScorePredicate(calculateAverageScore(student)).text, badge: (student) => getScorePredicate(calculateAverageScore(student)) }
  ];
}

function renderRecapTable() {
  const tableHead = document.getElementById("recapTableHead");
  const tableBody = document.getElementById("recapTableBody");
  if (!tableHead || !tableBody) return;

  renderRecapFilterOptions();
  const records = getRecapRecords();
  const columns = getRecapColumns();
  tableHead.innerHTML = `<tr>${columns.map((column) => `<th>${column.label}</th>`).join("")}</tr>`;
  tableBody.innerHTML = records
    .map((student, index) => `
      <tr>
        ${columns
          .map((column) => {
            const value = column.value(student, index);
            if (column.badge) {
              const badge = column.badge(student);
              return `<td><span class="badge ${badge.className}">${badge.text}</span></td>`;
            }
            return `<td>${column.strong ? `<strong>${value}</strong>` : value}</td>`;
          })
          .join("")}
      </tr>
    `)
    .join("");
}

function exportRecapExcel() {
  const records = getRecapRecords();
  const columns = getRecapColumns();
  const rows = records.map((student, index) => columns.map((column) => column.value(student, index)));
  const tableRows = [columns.map((column) => column.label), ...rows]
    .map((row) => `<tr>${row.map((cell) => `<td>${escapeExcelCell(cell)}</td>`).join("")}</tr>`)
    .join("");
  const worksheet = `<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><table>${tableRows}</table></body></html>`;
  const blob = new Blob([worksheet], { type: "application/vnd.ms-excel;charset=utf-8;" });
  const link = document.createElement("a");
  const date = new Date().toISOString().slice(0, 10);

  link.href = URL.createObjectURL(blob);
  link.download = `rekapitulasi-nilai-${getRecapType()}-${date}.xls`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(link.href);
}

