function renderDashboardContent() {
  const dashboardContent = document.getElementById("dashboardContent");
  if (!dashboardContent) return;

  const maxAdminTotal = Math.max(studentRecords.length, teacherRecords.length, principalRecords.length, classRecords.length, academicYearRecords.length, 1);
  const adminChartItems = [
    { label: "Siswa", value: studentRecords.length },
    { label: "Guru", value: teacherRecords.length },
    { label: "Kepsek", value: principalRecords.length },
    { label: "Kelas", value: classRecords.length },
    { label: "Tahun", value: academicYearRecords.length }
  ];
  const teacherSummary = `
    <div class="card" style="margin-top:18px;">
      <div class="section-head">
        <div>
          <h3>Dashboard Admin</h3>
          <p>Grafik ringkasan master data dan akademik.</p>
        </div>
      </div>
      <div class="chart dashboard-chart">
        ${adminChartItems.map((item) => `<div class="chart-bar" style="height:${Math.max(18, (item.value / maxAdminTotal) * 100)}%;"><span>${item.value}</span><small>${item.label}</small></div>`).join("")}
      </div>
    </div>
  `;

  const studentSummary = `
    <div class="card" style="margin-top:18px;">
      <div class="section-head"><div><h3>Dashboard Siswa</h3><p>Ringkasan tahun, semester, dan nilai total semester aktif.</p></div><button class="btn btn-soft btn-sm" onclick="goTo('recap')">Link Rekap</button></div>
      <div class="student-score-grid">
        <div class="student-score-box"><span>Tahun Penilaian</span><strong>${appState.activeStudent ? appState.activeStudent.year : "-"}</strong></div>
        <div class="student-score-box"><span>Semester</span><strong>${appState.activeStudent ? appState.activeStudent.semester : "-"}</strong></div>
        <div class="student-score-box"><span>Nilai Total</span><strong>${appState.activeStudent ? calculateAverageScore(appState.activeStudent) : "-"}</strong></div>
      </div>
    </div>
  `;

  const kepsekSummary = `
    <div class="grid stats-grid" style="margin-top:18px;">
      <div class="card stat-card"><div><h3>Rata-rata Nilai</h3><strong>81.0</strong></div><div class="icon-box bg-sky">NA</div></div>
      <div class="card stat-card"><div><h3>Penilaian Selesai</h3><strong>78%</strong></div><div class="icon-box bg-green">78</div></div>
      <div class="card stat-card"><div><h3>Belum Selesai</h3><strong>22%</strong></div><div class="icon-box bg-yellow">22</div></div>
      <div class="card stat-card"><div><h3>Kelas Tercakup</h3><strong>${classRecords.length}</strong></div><div class="icon-box bg-purple">K</div></div>
    </div>
    <div class="card" style="margin-top:18px;">
      <div class="section-head">
        <div><h3>Grafik Kemajuan Nilai Siswa</h3><p>Rata-rata nilai akhir per kelas dalam bentuk perbandingan progres.</p></div>
        
      </div>
      <div class="chart" style="height:320px;">
        <div class="chart-bar" style="height:58%"><small>5A</small></div>
        <div class="chart-bar" style="height:71%"><small>6A</small></div>
        <div class="chart-bar" style="height:66%"><small>5B</small></div>
        <div class="chart-bar" style="height:79%"><small>6B</small></div>
        <div class="chart-bar" style="height:73%"><small>6C</small></div>
      </div>
    </div>
    <div class="grid two-col" style="margin-top:18px;">
      <div class="card">
        <div class="section-head">
          <div><h3>Statistik Penilaian Guru</h3><p>Monitoring apakah penilaian sudah selesai dilakukan oleh guru.</p></div>
          
        </div>
        <div class="progress-item"><header><span>Kognitif</span><span>86%</span></header><div class="bar"><span style="width:86%"></span></div></div>
        <div class="progress-item"><header><span>Afektif</span><span>74%</span></header><div class="bar"><span style="width:74%"></span></div></div>
        <div class="progress-item"><header><span>Psikomotor</span><span>69%</span></header><div class="bar"><span style="width:69%"></span></div></div>
        <div class="progress-item"><header><span>Absensi</span><span>92%</span></header><div class="bar"><span style="width:92%"></span></div></div>
      </div>
    </div>
    <div class="card" style="margin-top:18px;">
      <div class="section-head">
        <div><h3>Hasil Penilaian per Kelas</h3><p>Ringkasan hasil penilaian dalam persentase untuk masing-masing kelas.</p></div>
        
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Kelas</th><th>Rata-rata</th><th>Penilaian Selesai</th><th>Belum Selesai</th><th>Predikat</th></tr></thead>
          <tbody>
            <tr><td>5A</td><td>84%</td><td>92%</td><td>8%</td><td><span class="badge badge-green">Sangat Baik</span></td></tr>
            <tr><td>6A</td><td>78%</td><td>81%</td><td>19%</td><td><span class="badge badge-blue">Baik</span></td></tr>
            <tr><td>5B</td><td>81%</td><td>88%</td><td>12%</td><td><span class="badge badge-green">Sangat Baik</span></td></tr>
            <tr><td>6B</td><td>75%</td><td>79%</td><td>21%</td><td><span class="badge badge-blue">Baik</span></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  `;

  const superadminSummary = `
    <div class="grid stats-grid" style="margin-top:18px;">
      <div class="card stat-card"><div><h3>Total Akun</h3><strong>5</strong><small>Akun aktif</small></div><div class="icon-box bg-sky">U</div></div>
      <div class="card stat-card"><div><h3>Role Tersedia</h3><strong>5</strong><small>Superadmin, Admin, Guru, Kepsek, Siswa</small></div><div class="icon-box bg-green">R</div></div>
      <div class="card stat-card"><div><h3>Log Aktivitas</h3><strong>${auditLogRecords.length}</strong><small>Riwayat sistem</small></div><div class="icon-box bg-yellow">L</div></div>
      <div class="card stat-card"><div><h3>Status Sistem</h3><strong>Siap</strong><small>Monitoring akses aktif</small></div><div class="icon-box bg-purple">S</div></div>
    </div>
    <div class="grid quick-actions" style="margin-top:18px;">
      <div class="action-card" onclick="goTo('userRole')"><div class="icon-box bg-sky">R</div><strong>User Role</strong><span>Kelola role dan hak akses pengguna.</span></div>
      <div class="action-card" onclick="goTo('addUser')"><div class="icon-box bg-green">+</div><strong>Add User</strong><span>Tambahkan akun baru untuk guru, kepsek, atau siswa.</span></div>
      <div class="action-card" onclick="goTo('userList')"><div class="icon-box bg-purple">U</div><strong>User List</strong><span>Lihat, edit, reset, dan hapus akun pengguna.</span></div>
      <div class="action-card" onclick="goTo('audit')"><div class="icon-box bg-yellow">L</div><strong>Audit Log</strong><span>Lihat jejak aktivitas sistem dan perubahan data.</span></div>
    </div>
  `;

  if (appState.role === "Admin") {
    dashboardContent.innerHTML = teacherSummary;
    return;
  }

  if (appState.role === "Guru PJOK") {
    dashboardContent.innerHTML = guruSummary;
    return;
  }

  if (appState.role === "Siswa") {
    dashboardContent.innerHTML = studentSummary;
    return;
  }

  if (appState.role === "Kepala Sekolah") {
    dashboardContent.innerHTML = kepsekSummary;
    return;
  }

  if (appState.role === "Superadmin") {
    dashboardContent.innerHTML = superadminSummary;
    return;
  }

  dashboardContent.innerHTML = `<div class="card" style="margin-top:18px;"><p>Ringkasan dashboard untuk peran ${appState.role}.</p></div>`;
}

