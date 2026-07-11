const roleMenus = {
  Superadmin: ["dashboard", "userRole", "addUser", "userList", "audit"],
  Admin: ["dashboard", "students", "teachers", "principals", "classes", "academicYears", "studentPlacement", "classPromotion", "teacherAssignment", "principalPeriod", "settings"],
  "Guru PJOK": ["dashboard", "students", "attendance", "assessmentPlan", "affective", "cognitive", "psychomotor", "recap", "criteriaRecap"],
  Siswa: ["dashboard", "recap"],
  "Kepala Sekolah": ["dashboard"]
};

const auditLogRecords = [
  { time: "2026-06-03 08:15", user: "superadmin", role: "Superadmin", action: "Login", page: "Dashboard", detail: "Masuk ke aplikasi dengan akses penuh." },
  { time: "2026-06-03 08:20", user: "admin", role: "Admin", action: "Tambah Data Guru", page: "Data Guru", detail: "Menambahkan guru PJOK baru ke master data." },
  { time: "2026-06-03 09:05", user: "guru", role: "Guru PJOK", action: "Input Asesmen", page: "Asesmen Kognitif", detail: "Mengisi nilai siswa kelas 5A pertemuan 1." },
  { time: "2026-06-03 09:40", user: "kepsek", role: "Kepala Sekolah", action: "Lihat Dashboard", page: "Dashboard", detail: "Membuka monitoring hasil penilaian per kelas." }
];

const assessmentData = {
  Kognitif: { dateId: "cognitiveDate", meetingId: "cognitiveMeeting", aspectId: null, options: cognitiveOptions },
  Afektif: { dateId: "affectiveDate", meetingId: "affectiveMeeting", aspectId: "affectiveIndikator", options: affectiveOptions },
  Psikomotor: { dateId: "psychomotorDate", meetingId: "psychomotorMeeting", aspectId: "psychomotorIndikator", options: psychomotorOptions }
};

const roleAliases = {
  admin: "Admin",
  user: "Siswa",
  superadmin: "Superadmin",
  guru: "Guru PJOK",
  "guru pjok": "Guru PJOK",
  siswa: "Siswa",
  kepsek: "Kepala Sekolah",
  "kepala sekolah": "Kepala Sekolah"
};

function normalizeRole(role) {
  const rawRole = String(role || "siswa").trim();
  return roleAliases[rawRole.toLowerCase()] || rawRole;
}

const authUser = window.authUser || {};
const appState = {
  role: normalizeRole(authUser.role),
  identity: authUser.email || "",
  activeStudent: null,
  currentPage: "dashboard"
};

function getDefaultPage(role) {
  return "dashboard";
}

function getAllowedPages(role) {
  return roleMenus[role] || ["dashboard"];
}

function isAllowed(pageId) {
  return getAllowedPages(appState.role).includes(pageId);
}

