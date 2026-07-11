const pageMeta = {
  dashboard: ["Dashboard", "Ringkasan data evaluasi pembelajaran pendidikan jasmani olahraga dan kesehatan."],
  userRole: ["User Role", "Pengaturan role dan akses pengguna sistem."],
  addUser: ["Add User", "Tambah akun pengguna baru ke dalam sistem."],
  userList: ["User List", "Daftar akun pengguna yang sudah terdaftar."],
  teachers: ["Data Guru", "Kelola identitas guru dan kepala sekolah."],
  classes: ["Data Kelas", "Kelola daftar kelas aktif dan data pendukung."],
  audit: ["Audit Log", "Riwayat aktivitas sistem dan jejak perubahan data."],
  settings: ["Pengaturan", "Tahun ajaran, semester, dan bobot nilai."],
  principals: ["Data Kepala Sekolah", "Kelola data kepala sekolah."],
  academicYears: ["Tahun Ajaran", "Kelola tahun ajaran aktif."],
  studentPlacement: ["Penempatan Siswa", "Atur penempatan siswa ke kelas dan tahun ajaran."],
  classPromotion: ["Kenaikan Kelas", "Kelola proses kenaikan kelas siswa."],
  teacherAssignment: ["Penugasan Guru PJOK", "Atur guru PJOK penanggung jawab setiap kelas."],
  principalPeriod: ["Periode Kepala Sekolah", "Atur periode jabatan kepala sekolah."],
  assessmentPlan: ["Rencana Asesmen", "Setup materi, tujuan, indikator, dan kriteria penilaian per pertemuan."],
  criteriaRecap: ["Rekap Kriteria Penilaian", "Kriteria penilaian per pertemuan dan semester."],
};

const initialData = window.initialData || {};
let classRecords = initialData.classRecords || [];
let studentRecords = initialData.studentRecords || [];
let teacherRecords = initialData.teacherRecords || [];
let principalRecords = initialData.principalRecords || [];
let academicYearRecords = initialData.academicYearRecords || [];
let teacherAssignmentRecords = initialData.teacherAssignmentRecords || [];
let principalPeriodRecords = initialData.principalPeriodRecords || [];
let placementRecords = initialData.placementRecords || [];
let criteriaRecords = initialData.criteriaRecords || [];
let assessmentRecords = initialData.assessmentRecords || [];

const affectiveOptions = [
  "Beriman",
  "Bergotong royong",
  "Kemandirian",
  "Bernalar Kritis",
  "Kreatif",
  "Sportifitas",
  "Tanggung Jawab",
  "Kejujuran",
  "Kerjasama",
  "Kedisplinan",
  "Sikap hormat (Respect)",
  "Percaya diri/ Motivasi"
];

const cognitiveOptions = [
  "Tes Objektif/ Tes Tertulis",
  "Tes Esai",
  "Tes Lisan",
  "Portofolio",
  "Proyek"
];

const psychomotorOptions = [
  "Proses Gerak",
  "Ketepatan",
  "Kelincahan",
  "Kecepatan",
  "Daya tahan",
  "Kekuatan",
  "Kelenturan",
  "Keseimbangan",
  "Koordinasi",
  "Proyek",
  "Portofolio",
  "Bermain Peran"
];

const defaultCriteria = {
  1: "Belum Berkembang",
  2: "Mulai Berkembang",
  3: "Berkembang",
  4: "Cakap",
  5: "Mahir"
};
