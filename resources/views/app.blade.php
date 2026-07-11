<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MADEP PJOK - Aplikasi Asesmen Digital PJOK</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/style.css') }}" />
</head>
<body>
  @php($currentRole = auth()->user()->role)
  <section id="appPage" class="app-shell">
    <aside id="sidebar" class="sidebar">
      <div class="app-logo">
        <div class="logo-icon logo-image-wrap">
          <img src="{{ asset('assets/logo.png') }}" alt="Logo MADEP PJOK" class="app-logo-image" />
        </div>
        <div>
          <strong>MADEP PJOK</strong>
          <span>Sistem Asesmen PJOK</span>
        </div>
        <button class="sidebar-close" type="button" onclick="closeSidebar()" aria-label="Tutup menu">&times;</button>
      </div>

      <div class="menu-label">Menu Utama</div>
      <div class="menu-item active" data-page="dashboard">Dashboard</div>
      @if($currentRole === 'superadmin')
      <div class="menu-item superadmin-menu hidden" data-page="userRole">User Role</div>
      <div class="menu-item superadmin-menu hidden" data-page="addUser">Add User</div>
      <div class="menu-item superadmin-menu hidden" data-page="userList">User List</div>
      <div class="menu-item superadmin-menu hidden" data-page="audit">Audit Log</div>
      @endif
      @if($currentRole === 'guru')
      <div class="menu-item guru-menu hidden" data-page="attendance">Absensi Siswa</div>
      <div class="menu-item guru-menu hidden" data-page="assessmentPlan">Rencana Asesmen</div>
      <div class="menu-group guru-menu hidden">Asesmen</div>
      <div class="menu-item menu-sub guru-menu hidden" data-page="affective">Afektif</div>
      <div class="menu-item menu-sub guru-menu hidden" data-page="cognitive">Kognitif</div>
      <div class="menu-item menu-sub guru-menu hidden" data-page="psychomotor">Psikomotor</div>
      <div class="menu-group guru-menu hidden">Rekapitulasi</div>
      <div class="menu-item menu-sub guru-menu hidden" data-page="recap">Nilai Siswa</div>
      <div class="menu-item menu-sub guru-menu hidden" data-page="criteriaRecap">Kriteria Penilaian</div>
      @endif
      @if($currentRole === 'siswa')
      <div class="menu-item menu-sub guru-menu hidden" data-page="recap">Nilai Siswa</div>
      @endif
      @if($currentRole === 'admin')
      <div class="menu-group admin-menu hidden">Master Data</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="students">Data Siswa</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="teachers">Data Guru</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="principals">Data Kepala Sekolah</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="classes">Data Kelas</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="academicYears">Tahun Ajaran</div>
      <div class="menu-group admin-menu hidden">Akademik</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="studentPlacement">Penempatan Siswa</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="classPromotion">Kenaikan Kelas</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="teacherAssignment">Penugasan Guru PJOK</div>
      <div class="menu-item menu-sub admin-menu hidden" data-page="principalPeriod">Periode Kepala Sekolah</div>
      <div class="menu-item admin-menu hidden" data-page="settings">Pengaturan</div>
      @endif
    </aside>

    <main class="main">
      <header class="topbar">
        <div style="display:flex; align-items:center; gap:14px;">
          <button id="mobileToggle" class="mobile-toggle" onclick="toggleSidebar()" aria-label="Buka menu" aria-expanded="false">&#9776;</button>
          <div class="page-title">
            <h2 id="pageTitle">Dashboard</h2>
            <p id="pageDesc">Ringkasan data asesmen digital PJOK semester aktif.</p>
          </div>
        </div>
        <div class="user-card">
          <div class="avatar" id="avatar">A</div>
          <div>
            <strong id="activeRole">Admin</strong><br>
            <small id="periodLabel" style="color:var(--muted);">2025/2026 - Semester Ganjil</small>
          </div>
          <button class="btn btn-outline" type="button" onclick="logout()">Keluar</button>
        </div>
      </header>
      <section id="dashboard" class="page active">
        <div class="card hero-card">
          <div class="hero-center">
            <div class="hero-logo logo-image-wrap">
              <img src="{{ asset('assets/logo.png') }}" alt="Logo MADEP PJOK" class="app-logo-image" />
            </div>
            <h3>MADEP PJOK</h3>
            <p>Manajemen Data Evaluasi Pembelajaran Pendidikan Jasmani Olahraga dan Kesehatan</p>
          </div>
        </div>
        <div id="dashboardContent"></div>
      </section>

      <section id="userRole" class="page">
        <div class="card">
          <div class="section-head">
            <div><h3>User Role</h3><p>Atur role dan status akses pengguna sistem.</p></div>
            <button class="btn btn-primary" type="button" data-user-role-save>Simpan Role</button>
          </div>
          <div class="grid form-grid">
            <div class="field"><label>Pilih User</label><select id="roleUserSelect"></select></div>
            <div class="field"><label>Role</label><select id="roleUserRole"><option value="superadmin">Superadmin</option><option value="admin">Admin</option><option value="guru">Guru PJOK</option><option value="kepsek">Kepala Sekolah</option><option value="siswa">Siswa</option></select></div>
            <div class="field"><label>Status Akses</label><select id="roleUserStatus"><option>Aktif</option><option>Nonaktif</option></select></div>
          </div>
        </div>
      </section>

      <section id="addUser" class="page">
        <div class="card">
          <div class="section-head">
            <div><h3>Add User</h3><p>Tambah akun baru untuk admin, guru, kepala sekolah, atau siswa.</p></div>
            <button class="btn btn-primary" type="button" data-user-save>Tambah User</button>
          </div>
          <div class="grid form-grid">
            <input id="userFormId" type="hidden" />
            <div class="field"><label>Nama Lengkap</label><input id="userName" placeholder="Nama pengguna" /></div>
            <div class="field"><label>Email</label><input id="userEmail" type="email" placeholder="email@sekolah.id" /></div>
            <div class="field"><label>Role</label><select id="userRole"><option value="admin">Admin</option><option value="guru">Guru PJOK</option><option value="kepsek">Kepala Sekolah</option><option value="siswa">Siswa</option><option value="superadmin">Superadmin</option></select></div>
            <div class="field"><label>Status</label><select id="userStatus"><option>Aktif</option><option>Nonaktif</option></select></div>
            <div class="field"><label>Password</label><input id="userPassword" type="password" placeholder="Minimal 6 karakter" /></div>
          </div>
        </div>
      </section>

      <section id="userList" class="page">
        <div class="card">
          <div class="section-head">
            <div><h3>User List</h3><p>Daftar akun pengguna yang terdaftar di sistem.</p></div>
            <button class="btn btn-soft" type="button" data-user-refresh>Refresh</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>No</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
              </thead>
              <tbody id="userTableBody"></tbody>
            </table>
          </div>
        </div>
      </section>
      <section id="teachers" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Data Guru</h3><p>Kelola data guru aktif.</p></div><button class="btn btn-primary" type="button" data-admin-new="teacher">Tambah Guru</button></div>
          <div id="teacherForm" class="master-form hidden"><div class="grid form-grid"><div class="field"><label>NIP</label><input id="teacherNip" /></div><div class="field"><label>Nama Lengkap</label><input id="teacherName" /></div><div class="field"><label>Jenis Kelamin</label><select id="teacherGender"><option>Laki-laki</option><option>Perempuan</option></select></div><div class="field"><label>Email</label><input id="teacherEmail" type="email" /></div><div class="field"><label>Status</label><select id="teacherStatus"><option>Aktif</option><option>Nonaktif</option></select></div></div><div class="form-actions-right"><button class="btn btn-soft" type="button" data-admin-close="teacherForm">Tutup</button><button class="btn btn-primary" type="button" data-admin-save="teacher">Simpan Guru</button></div></div>
          <div class="admin-toolbar"><input id="teacherSearch" placeholder="Search guru..." data-admin-filter="teacher" /><select id="teacherStatusFilter" data-admin-filter="teacher"><option value="">Semua Status</option><option>Aktif</option><option>Nonaktif</option></select></div>
          <div class="table-wrap"><table><thead><tr><th>No</th><th>NIP</th><th>Nama Lengkap</th><th>Jenis Kelamin</th><th>Email</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="teacherTableBody"></tbody></table></div><div id="teacherPagination" class="pagination-bar"></div>
        </div>
      </section>

      <section id="principals" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Data Kepala Sekolah</h3><p>Kelola data kepala sekolah.</p></div><button class="btn btn-primary" type="button" data-admin-new="principal">Tambah Kepala Sekolah</button></div>
          <div id="principalForm" class="master-form hidden"><div class="grid form-grid"><div class="field"><label>NIP</label><input id="principalNip" /></div><div class="field"><label>Nama Lengkap</label><input id="principalName" /></div><div class="field"><label>Jenis Kelamin</label><select id="principalGender"><option>Laki-laki</option><option>Perempuan</option></select></div><div class="field"><label>Email</label><input id="principalEmail" type="email" /></div><div class="field"><label>Status</label><select id="principalStatus"><option>Aktif</option><option>Nonaktif</option></select></div></div><div class="form-actions-right"><button class="btn btn-soft" type="button" data-admin-close="principalForm">Tutup</button><button class="btn btn-primary" type="button" data-admin-save="principal">Simpan Kepala Sekolah</button></div></div>
          <div class="admin-toolbar"><input id="principalSearch" placeholder="Search kepala sekolah..." data-admin-filter="principal" /><select id="principalStatusFilter" data-admin-filter="principal"><option value="">Semua Status</option><option>Aktif</option><option>Nonaktif</option></select></div>
          <div class="table-wrap"><table><thead><tr><th>No</th><th>NIP</th><th>Nama Lengkap</th><th>Jenis Kelamin</th><th>Email</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="principalTableBody"></tbody></table></div><div id="principalPagination" class="pagination-bar"></div>
        </div>
      </section>

      <section id="classes" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Data Kelas</h3><p>Kelola daftar kelas aktif.</p></div><button class="btn btn-primary" type="button" data-toggle-form="classForm">Tambah Kelas</button></div>
          <div id="classForm" class="master-form hidden"><div class="field"><label>Nama Kelas</label><input id="className" placeholder="Contoh: Kelas 5A" /></div><div class="form-actions-right"><button class="btn btn-soft" type="button" data-toggle-form="classForm">Tutup</button><button id="classSubmitButton" class="btn btn-primary" type="button" data-add-master="class">Tambah</button></div></div>
          <div class="table-wrap" style="margin-top:18px;"><table><thead><tr><th>No</th><th>Nama Kelas</th><th>Aksi</th></tr></thead><tbody id="classTableBody"></tbody></table></div>
        </div>
      </section>

      <section id="academicYears" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Tahun Ajaran</h3><p>Kelola tahun ajaran aktif.</p></div><button class="btn btn-primary" type="button" data-admin-new="year">Tambah Tahun Ajaran</button></div>
          <div id="yearForm" class="master-form hidden"><div class="grid form-grid"><div class="field"><label>Tahun Ajaran</label><input id="yearName" placeholder="2026/2027" /></div><div class="field"><label>Status</label><select id="yearStatus"><option>Aktif</option><option>Nonaktif</option></select></div></div><div class="form-actions-right"><button class="btn btn-soft" type="button" data-admin-close="yearForm">Tutup</button><button class="btn btn-primary" type="button" data-admin-save="year">Simpan Tahun Ajaran</button></div></div>
          <div class="table-wrap"><table><thead><tr><th>No</th><th>Tahun Ajaran</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="yearTableBody"></tbody></table></div>
        </div>
      </section>

      <section id="students" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Data Siswa</h3><p>Kelola data siswa berdasarkan kelas dan status.</p></div><button class="btn btn-primary" type="button" data-admin-new="student">Tambah Siswa</button></div>
          <div id="studentForm" class="master-form hidden"><div class="grid form-grid"><div class="field"><label>NIS</label><input id="studentNisn" /></div><div class="field"><label>Nama Lengkap</label><input id="studentName" /></div><div class="field"><label>Jenis Kelamin</label><select id="studentGender"><option>Laki-laki</option><option>Perempuan</option></select></div><div class="field"><label>Email</label><input id="studentEmail" type="email" /></div><div class="field"><label>Status</label><select id="studentStatus"><option>Aktif</option><option>Nonaktif</option></select></div></div><div class="form-actions-right"><button class="btn btn-soft" type="button" data-admin-close="studentForm">Tutup</button><button class="btn btn-primary" type="button" data-admin-save="student">Simpan Siswa</button></div></div>
          <div class="admin-toolbar"><input id="studentSearch" placeholder="Search siswa..." data-admin-filter="student" /><select id="studentClassFilter" data-admin-filter="student"><option value="">Semua Kelas</option></select><select id="studentStatusFilter" data-admin-filter="student"><option value="">Semua Status</option><option>Aktif</option><option>Nonaktif</option></select></div>
          <div class="table-wrap"><table><thead><tr><th>No</th><th>NIS</th><th>Nama Lengkap</th><th>Jenis Kelamin</th><th>Email</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="studentTableBody"></tbody></table></div><div id="studentPagination" class="pagination-bar"></div>
        </div>
      </section>

      <section id="studentPlacement" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Penempatan Siswa</h3><p>Pilih siswa, kelas, dan tahun ajaran untuk penempatan akademik.</p></div><button class="btn btn-primary" type="button" onclick="saveStudentPlacement()">Simpan Penempatan</button></div>
          <div class="grid form-grid"><div class="field"><label>Tahun Ajaran</label><select id="placementYear"></select></div><div class="field"><label>Kelas</label><select id="placementClass"></select></div><div class="field"><label>Search Siswa</label><input id="placementStudentSearch" placeholder="Cari nama atau NISN..." /></div></div>
          <div class="table-wrap"><table><thead><tr><th>Pilih</th><th>NISN</th><th>Nama Siswa</th><th>Status</th></tr></thead><tbody id="placementStudentBody"></tbody></table></div><div id="placementStudentPagination" class="pagination-bar"></div>
          <details class="assessment-detail-panel placement-list-panel">
            <summary>Daftar Penempatan</summary>
            <div class="placement-panel-content">
              <p class="panel-helper-text">Riwayat penempatan siswa per kelas dan tahun ajaran.</p>
              <div class="admin-toolbar"><input id="placementSearch" placeholder="Search nama siswa, NISN, kelas..." /></div>
              <div class="table-wrap"><table><thead><tr><th>Nama Siswa</th><th>Kelas</th><th>Tahun Ajaran</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="placementTableBody"></tbody></table></div><div id="placementPagination" class="pagination-bar"></div>
            </div>
          </details>
        </div>
      </section>
      <section id="classPromotion" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Kenaikan Kelas</h3><p>Kelola simulasi kenaikan kelas siswa per tahun ajaran.</p></div><button class="btn btn-primary" type="button" onclick="saveClassPromotion()">Simpan Kenaikan</button></div>
          <div class="grid form-grid"><div class="field"><label>Tahun Ajaran</label><select id="promotionYear"></select></div><div class="field"><label>Kelas Asal</label><select id="promotionFromClass"></select></div><div class="field"><label>Kelas Tujuan</label><select id="promotionToClass"></select></div></div>
          <div class="table-wrap"><table><thead><tr><th>No</th><th>NIS</th><th>Nama Siswa</th><th>Kelas Asal</th><th>Kelas Tujuan</th><th>Status</th></tr></thead><tbody id="promotionTableBody"></tbody></table></div>
        </div>
      </section>

      <section id="teacherAssignment" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Penugasan Guru PJOK</h3><p>Atur guru PJOK yang bertanggung jawab pada setiap kelas.</p></div><button class="btn btn-primary" type="button" onclick="saveTeacherAssignment()">Simpan Penugasan</button></div>
          <div class="grid form-grid"><div class="field"><label>Tahun Ajaran</label><select id="assignmentYear"></select></div><div class="field"><label>Guru PJOK</label><select id="assignmentTeacher"></select></div><div class="field"><label>Kelas</label><select id="assignmentClass"></select></div></div>
          <div class="table-wrap"><table><thead><tr><th>No</th><th>Guru PJOK</th><th>NIP</th><th>Kelas</th><th>Tahun Ajaran</th><th>Status</th></tr></thead><tbody id="teacherAssignmentTableBody"></tbody></table></div>
        </div>
      </section>

      <section id="principalPeriod" class="page">
        <div class="card admin-data-page">
          <div class="section-head"><div><h3>Periode Kepala Sekolah</h3><p>Atur periode jabatan kepala sekolah yang aktif.</p></div><button class="btn btn-primary" type="button" onclick="savePrincipalPeriod()">Simpan Periode</button></div>
          <div class="grid form-grid"><div class="field"><label>Kepala Sekolah</label><select id="periodPrincipal"></select></div><div class="field"><label>Tahun Mulai</label><input id="periodStart" value="2025" /></div><div class="field"><label>Tahun Selesai</label><input id="periodEnd" value="2029" /></div></div>
          <div class="table-wrap"><table><thead><tr><th>No</th><th>Kepala Sekolah</th><th>NIP</th><th>Periode</th><th>Status</th></tr></thead><tbody id="principalPeriodTableBody"></tbody></table></div>
        </div>
      </section>
      <section id="assessmentPlan" class="page">
        <div class="card">
          <div class="section-head">
            <div><h3>Rencana Asesmen</h3><p>Siapkan materi, tujuan, indikator/aspek, dan kriteria penilaian sebelum mengisi nilai siswa.</p></div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              <button class="btn btn-outline btn-sm" type="button" onclick="newAssessmentPlan()">Tambah</button>
            </div>
          </div>

          <details id="assessmentPlanPanel" class="assessment-detail-panel">
            <summary>Detail Rencana Asesmen</summary>
            <div class="grid form-grid">
              <div class="field"><label>Tahun Ajaran</label><select id="planYear"><option>2025/2026</option><option>2026/2027</option></select></div>
              <div class="field"><label>Semester</label><select id="planSemester"><option>Ganjil</option><option>Genap</option></select></div>
              <div class="field"><label>Kelas</label><select id="planClass"><option>Kelas 5A</option><option>Kelas 6A</option></select></div>
              <div class="field"><label>Jenis Asesmen</label><select id="planType"><option>Afektif</option><option>Kognitif</option><option>Psikomotor</option></select></div>
              <div class="field"><label>Pertemuan</label><select id="planMeeting"></select></div>
              <div class="field"><label>Indikator/Aspek</label><select id="planAspect"></select></div>
              <div class="field"><label>Materi/Konten</label><input id="planMateri" placeholder="Contoh: Passing bola" /></div>
              <div class="field"><label>Tujuan Pembelajaran</label><input id="planTujuan" placeholder="Contoh: Siswa mampu ..." /></div>
            </div>
            <div class="detail-subhead"><h4>Kriteria Penilaian</h4><p>Isi kriteria nilai 1 sampai 5 untuk rencana pertemuan ini.</p></div>
            <div class="criteria-grid criteria-grid-wide">
              <div class="criteria-col"><span>1</span><input class="plan-criteria-input" data-score="1" placeholder="Kriteria nilai 1" required /></div>
              <div class="criteria-col"><span>2</span><input class="plan-criteria-input" data-score="2" placeholder="Kriteria nilai 2" required /></div>
              <div class="criteria-col"><span>3</span><input class="plan-criteria-input" data-score="3" placeholder="Kriteria nilai 3" required /></div>
              <div class="criteria-col"><span>4</span><input class="plan-criteria-input" data-score="4" placeholder="Kriteria nilai 4" required /></div>
              <div class="criteria-col"><span>5</span><input class="plan-criteria-input" data-score="5" placeholder="Kriteria nilai 5" required /></div>
            </div>
            <div class="assessment-detail-actions">
              <button class="btn btn-primary" type="button" onclick="saveAssessmentPlan()">Simpan Rencana</button>
            </div>
          </details>

          <div class="section-head list-section-head">
            <div><h3>Daftar Rencana Asesmen</h3><p>Filter atau cari data, lalu edit/hapus dari baris yang dipilih.</p></div>
          </div>
          <details class="plan-filter-panel">
            <summary>Filter & Search</summary>
            <div class="grid plan-filter-grid">
              <div class="field"><label>Tahun</label><select id="planFilterYear"><option value="">Semua Tahun</option></select></div>
              <div class="field"><label>Semester</label><select id="planFilterSemester"><option value="">Semua Semester</option><option>Ganjil</option><option>Genap</option></select></div>
              <div class="field"><label>Kelas</label><select id="planFilterClass"><option value="">Semua Kelas</option></select></div>
              <div class="field"><label>Jenis</label><select id="planFilterType"><option value="">Semua Jenis</option></select></div>
              <div class="field"><label>Pertemuan</label><select id="planFilterMeeting"><option value="">Semua Pertemuan</option></select></div>
              <div class="field"><label>Cari</label><input id="planSearch" placeholder="Materi, tujuan, indikator..." /></div>
            </div>
          </details>
          <div class="table-wrap" style="margin-top:14px;">
            <table>
              <thead><tr><th>No</th><th>Tahun</th><th>Semester</th><th>Kelas</th><th>Jenis</th><th>Pertemuan</th><th>Indikator/Aspek</th><th>Materi</th><th>Aksi</th></tr></thead>
              <tbody id="assessmentPlanTableBody"></tbody>
            </table>
          </div>
          <div id="assessmentPlanPagination" class="pagination-bar"></div>
        </div>
      </section>
      <section id="attendance" class="page">
        <div class="card">
          <div class="section-head"><div><h3>Absensi Siswa</h3><p>Filter tanggal, tahun ajaran, semester, kelas, dan pertemuan, lalu tampilkan daftar siswa di bawah.</p></div><button class="btn btn-primary">Simpan</button></div>
          <div class="grid attendance-filter-grid">
            <div class="field"><label>Tanggal</label><input id="attendanceDate" type="date" /></div>
            <div class="field"><label>Tahun Ajaran</label><select id="attendanceYear"><option>2025/2026</option><option>2026/2027</option></select></div>
            <div class="field"><label>Semester</label><select id="attendanceSemester"><option>Ganjil</option><option>Genap</option></select></div>
            <div class="field"><label>Kelas</label><select id="attendanceClass"><option>Kelas 5A</option><option>Kelas 6A</option></select></div>
            <div class="field"><label>Pertemuan</label><select id="attendanceMeeting"></select></div>
            <div class="field"><label>Aksi</label><button class="btn btn-soft" style="width:100%;">Tampilkan</button></div>
          </div>
          <div class="attendance-legend">
            <span class="legend-pill legend-h">H = Hadir</span>
            <span class="legend-pill legend-s">S = Sakit</span>
            <span class="legend-pill legend-i">I = Izin</span>
            <span class="legend-pill legend-a">A = Alfa</span>
          </div>
          <div class="table-wrap">
            <table class="attendance-table">
              <thead><tr><th>No</th><th>Nama Siswa</th><th>H</th><th>S</th><th>I</th><th>A</th><th>Catatan</th></tr></thead>
              <tbody>
                <tr><td>1</td><td>Siswa A</td><td><button class="status-choice active status-h">H</button></td><td><button class="status-choice status-s">S</button></td><td><button class="status-choice status-i">I</button></td><td><button class="status-choice status-a">A</button></td><td><input placeholder="Catatan"></td></tr>
                <tr><td>2</td><td>Siswa B</td><td><button class="status-choice active status-h">H</button></td><td><button class="status-choice status-s">S</button></td><td><button class="status-choice status-i">I</button></td><td><button class="status-choice status-a">A</button></td><td><input placeholder="Catatan"></td></tr>
              </tbody>
            </table>
          </div>
          <div class="form-actions-right">
            <button class="btn btn-soft">Back</button>
            <button class="btn btn-primary" type="button">Simpan</button>
          </div>
        </div>
      </section>

      <section id="cognitive" class="page assessment-page" data-type="Kognitif">
        <div class="card assessment-card">
          <div class="section-head"><div><h3>Asesmen Kognitif</h3><p>Pilih tahun, semester, kelas, dan pertemuan. Detail otomatis dari Rencana Asesmen.</p></div><button class="btn btn-primary" type="button" data-save-assessment>Simpan</button></div>
          <details class="assessment-detail-panel" open>
            <summary>Detail Asesmen</summary>
            <div class="grid form-grid">
              <div class="field"><label>Tanggal</label><input id="cognitiveDate" type="date" /></div>
            <div class="field"><label>Tahun Ajaran</label><select class="assessment-year"><option>2025/2026</option><option>2026/2027</option></select></div>
            <div class="field"><label>Semester</label><select class="assessment-semester"><option>Ganjil</option><option>Genap</option></select></div>
            <div class="field"><label>Pilih Kelas</label><select class="assessment-class"><option>Kelas 5A</option><option>Kelas 6A</option></select></div>
              <div class="field"><label>Pertemuan</label><select id="cognitiveMeeting"></select></div>
              <div class="field"><label>Materi/Konten</label><input id="cognitiveMateri" value="" readonly /></div>
              <div class="field"><label>Tujuan Pembelajaran</label><input id="cognitiveTujuan" value="" readonly /></div>
              <div class="field"><label>Bentuk Tugas</label><select><option>Tes Objektif/Tertulis</option><option>Tes Esai</option><option>Tes Lisan</option><option>Portofolio</option><option>Proyek</option></select></div>
            </div>
            <div class="detail-subhead"><h4>Kriteria Penilaian</h4><p>Terisi otomatis dari Rencana Asesmen.</p></div>
            <div class="criteria-grid">
              <div class="criteria-col"><span>1</span><input class="criteria-input" readonly data-score="1" value="Belum Memahami" required /></div>
              <div class="criteria-col"><span>2</span><input class="criteria-input" readonly data-score="2" value="Mulai Memahami" required /></div>
              <div class="criteria-col"><span>3</span><input class="criteria-input" readonly data-score="3" value="Memahami" required /></div>
              <div class="criteria-col"><span>4</span><input class="criteria-input" readonly data-score="4" value="Lebih Memahami" required /></div>
              <div class="criteria-col"><span>5</span><input class="criteria-input" readonly data-score="5" value="Sangat Memahami" required /></div>
            </div>
          </details>
          <div class="table-wrap">
            <table class="assessment-table">
              <thead><tr><th>No</th><th>Nama Siswa</th><th>Skor 1</th><th>Skor 2</th><th>Skor 3</th><th>Skor 4</th><th>Skor 5</th><th>Nilai</th><th>Catatan</th></tr></thead>
              <tbody>
                <tr><td>1</td><td>Siswa A</td><td><button class="score-choice-btn">1</button></td><td><button class="score-choice-btn">2</button></td><td><button class="score-choice-btn">3</button></td><td><button class="score-choice-btn active">4</button></td><td><button class="score-choice-btn">5</button></td><td><input value="4" /></td><td><input placeholder="Catatan"></td></tr>
                <tr><td>2</td><td>Siswa B</td><td><button class="score-choice-btn">1</button></td><td><button class="score-choice-btn">2</button></td><td><button class="score-choice-btn active">3</button></td><td><button class="score-choice-btn">4</button></td><td><button class="score-choice-btn">5</button></td><td><input value="3" /></td><td><input placeholder="Catatan"></td></tr>
              </tbody>
            </table>
          </div>
          <div class="form-actions-right">
            <button class="btn btn-soft">Back</button>
            <button class="btn btn-primary" type="button" data-save-assessment>Simpan</button>
          </div>
        </div>
      </section>
      <section id="affective" class="page assessment-page" data-type="Afektif">
        <div class="card assessment-card">
          <div class="section-head"><div><h3>Asesmen Afektif</h3><p>Pilih tahun, semester, kelas, dan pertemuan. Detail otomatis dari Rencana Asesmen.</p></div><button class="btn btn-primary" type="button" data-save-assessment>Simpan</button></div>
          <details class="assessment-detail-panel" open>
            <summary>Detail Asesmen</summary>
            <div class="grid form-grid">
              <div class="field"><label>Tanggal</label><input id="affectiveDate" type="date" /></div>
            <div class="field"><label>Tahun Ajaran</label><select class="assessment-year"><option>2025/2026</option><option>2026/2027</option></select></div>
            <div class="field"><label>Semester</label><select class="assessment-semester"><option>Ganjil</option><option>Genap</option></select></div>
            <div class="field"><label>Pilih Kelas</label><select class="assessment-class"><option>Kelas 5A</option><option>Kelas 6A</option></select></div>
              <div class="field"><label>Pertemuan</label><select id="affectiveMeeting"></select></div>
              <div class="field"><label>Materi/Konten</label><input id="affectiveMateri" value="" readonly /></div>
              <div class="field"><label>Tujuan Pembelajaran</label><input id="affectiveTujuan" value="" readonly /></div>
              <div class="field"><label>Indikator Sikap</label><select id="affectiveIndikator" class="assessment-aspect" required></select></div>
            </div>
            <div class="detail-subhead"><h4>Kriteria Penilaian</h4><p>Terisi otomatis dari Rencana Asesmen.</p></div>
            <div class="criteria-grid criteria-grid-wide">
              <div class="criteria-col"><span>1</span><input class="criteria-input" readonly data-score="1" value="Belum Berkembang" required /></div>
              <div class="criteria-col"><span>2</span><input class="criteria-input" readonly data-score="2" value="Mulai Berkembang" required /></div>
              <div class="criteria-col"><span>3</span><input class="criteria-input" readonly data-score="3" value="Berkembang" required /></div>
              <div class="criteria-col"><span>4</span><input class="criteria-input" readonly data-score="4" value="Cakap" required /></div>
              <div class="criteria-col"><span>5</span><input class="criteria-input" readonly data-score="5" value="Mahir" required /></div>
            </div>
          </details>
          <div class="table-wrap">
            <table class="assessment-table">
              <thead><tr><th>No</th><th>Nama Siswa</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>Catatan</th></tr></thead>
              <tbody>
                <tr><td>1</td><td>Siswa A</td><td><button class="score-choice-btn">1</button></td><td><button class="score-choice-btn">2</button></td><td><button class="score-choice-btn">3</button></td><td><button class="score-choice-btn active">4</button></td><td><button class="score-choice-btn">5</button></td><td><input placeholder="Catatan"></td></tr>
                <tr><td>2</td><td>Siswa B</td><td><button class="score-choice-btn">1</button></td><td><button class="score-choice-btn active">2</button></td><td><button class="score-choice-btn">3</button></td><td><button class="score-choice-btn">4</button></td><td><button class="score-choice-btn">5</button></td><td><input placeholder="Catatan"></td></tr>
              </tbody>
            </table>
          </div>
          <div class="form-actions-right">
            <button class="btn btn-soft">Back</button>
            <button class="btn btn-primary" type="button" data-save-assessment>Simpan</button>
          </div>
        </div>
      </section>
      <section id="psychomotor" class="page assessment-page" data-type="Psikomotor">
        <div class="card assessment-card">
          <div class="section-head"><div><h3>Asesmen Psikomotor</h3><p>Pilih tahun, semester, kelas, dan pertemuan. Detail otomatis dari Rencana Asesmen.</p></div><button class="btn btn-primary" type="button" data-save-assessment>Simpan</button></div>
          <details class="assessment-detail-panel" open>
            <summary>Detail Asesmen</summary>
            <div class="grid form-grid">
              <div class="field"><label>Tanggal</label><input id="psychomotorDate" type="date" /></div>
            <div class="field"><label>Tahun Ajaran</label><select class="assessment-year"><option>2025/2026</option><option>2026/2027</option></select></div>
            <div class="field"><label>Semester</label><select class="assessment-semester"><option>Ganjil</option><option>Genap</option></select></div>
            <div class="field"><label>Pilih Kelas</label><select class="assessment-class"><option>Kelas 5A</option><option>Kelas 6A</option></select></div>
              <div class="field"><label>Pertemuan</label><select id="psychomotorMeeting"></select></div>
              <div class="field"><label>Materi/Konten</label><input id="psychomotorMateri" value="" readonly /></div>
              <div class="field"><label>Tujuan Pembelajaran</label><input id="psychomotorTujuan" value="" readonly /></div>
              <div class="field"><label>Aspek Penilaian</label><select id="psychomotorIndikator" class="assessment-aspect" required></select></div>
            </div>
            <div class="detail-subhead"><h4>Kriteria Penilaian</h4><p>Terisi otomatis dari Rencana Asesmen.</p></div>
            <div class="criteria-grid criteria-grid-wide">
              <div class="criteria-col"><span>1</span><input class="criteria-input" readonly data-score="1" value="Belum Berkembang" required /></div>
              <div class="criteria-col"><span>2</span><input class="criteria-input" readonly data-score="2" value="Mulai Berkembang" required /></div>
              <div class="criteria-col"><span>3</span><input class="criteria-input" readonly data-score="3" value="Berkembang" required /></div>
              <div class="criteria-col"><span>4</span><input class="criteria-input" readonly data-score="4" value="Cakap" required /></div>
              <div class="criteria-col"><span>5</span><input class="criteria-input" readonly data-score="5" value="Mahir" required /></div>
            </div>
          </details>
          <div class="table-wrap">
            <table class="assessment-table">
              <thead><tr><th>No</th><th>Nama Siswa</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>Catatan</th></tr></thead>
              <tbody>
                <tr><td>1</td><td>Siswa A</td><td><button class="score-choice-btn">1</button></td><td><button class="score-choice-btn">2</button></td><td><button class="score-choice-btn">3</button></td><td><button class="score-choice-btn active">4</button></td><td><button class="score-choice-btn">5</button></td><td><input placeholder="Catatan"></td></tr>
                <tr><td>2</td><td>Siswa B</td><td><button class="score-choice-btn">1</button></td><td><button class="score-choice-btn active">2</button></td><td><button class="score-choice-btn">3</button></td><td><button class="score-choice-btn">4</button></td><td><button class="score-choice-btn">5</button></td><td><input placeholder="Catatan"></td></tr>
              </tbody>
            </table>
          </div>
          <div class="form-actions-right">
            <button class="btn btn-soft">Back</button>
            <button class="btn btn-primary" type="button" data-save-assessment>Simpan</button>
          </div>
        </div>
      </section>

      <section id="recap" class="page">
        <div class="card">
          <div class="section-head">
            <div>
              <h3 id="recapTitle">Rekapitulasi Nilai</h3>
              <p id="recapDesc">Filter tahun ajaran, semester, dan jenis rekap, lalu download ke Excel.</p>
            </div>
          </div>
          <div id="studentRecapNotice" class="notice-box hidden">Siswa hanya dapat melihat nilai pribadi pada semester aktif.</div>
          <div id="recapFilterBar" class="recap-filter-bar">
            <div class="field"><label>Tahun Ajaran</label><select id="recapYearFilter"><option value="">Semua Tahun</option></select></div>
            <div class="field"><label>Semester</label><select id="recapSemesterFilter"><option value="">Semua Semester</option><option>Ganjil</option><option>Genap</option></select></div>
            <div class="field"><label>Jenis Rekap</label><select id="recapTypeFilter"><option value="final">Nilai Akhir</option><option value="attendance">Absensi</option><option value="affective">Afektif</option><option value="cognitive">Kognitif</option><option value="psychomotor">Psikomotor</option></select></div>
            <div class="field"><label>Aksi</label><button class="btn btn-primary" type="button" onclick="exportRecapExcel()">Download Excel</button></div>
          </div>
          <div class="recap-preview card-soft">
            <div class="section-head">
              <div>
                <h4>Preview Rekap</h4>
                <p>Data mengikuti filter yang dipilih.</p>
              </div>
            </div>
            <div class="table-wrap">
              <table>
                <thead id="recapTableHead"></thead>
                <tbody id="recapTableBody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <section id="criteriaRecap" class="page">
        <div class="card">
          <div class="section-head">
            <div>
              <h3>Rekap Kriteria Penilaian</h3>
              <p>Daftar kriteria penilaian setiap pertemuan dan semester.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              <button class="btn btn-soft btn-sm" onclick="renderCriteriaRecapTable()">Refresh</button>
              <button class="btn btn-primary btn-sm" onclick="exportCriteriaRecapExcel()">Export Excel</button>
            </div>
          </div>
          <div class="grid criteria-filter-grid">
            <div class="field"><label>Tahun</label><select id="criteriaYearFilter"><option value="">Semua Tahun</option></select></div>
            <div class="field"><label>Semester</label><select id="criteriaSemesterFilter"><option value="">Semua Semester</option><option>Ganjil</option><option>Genap</option></select></div>
            <div class="field"><label>Kelas</label><select id="criteriaClassFilter"><option value="">Semua Kelas</option></select></div>
            <div class="field"><label>Jenis Asesmen</label><select id="criteriaTypeFilter"><option value="">Semua Jenis</option></select></div>
            <div class="field"><label>Pertemuan</label><select id="criteriaMeetingFilter"><option value="">Semua Pertemuan</option></select></div>
            <div class="field"><label>Aksi</label><button class="btn btn-soft" type="button" style="width:100%;" onclick="renderCriteriaRecapTable()">Tampilkan</button></div>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>No</th><th>Tahun</th><th>Semester</th><th>Kelas</th><th>Jenis Asesmen</th><th>Pertemuan</th><th>Aspek/Indikator</th><th>Nilai 1</th><th>Nilai 2</th><th>Nilai 3</th><th>Nilai 4</th><th>Nilai 5</th></tr></thead>
              <tbody id="criteriaRecapTableBody"></tbody>
            </table>
          </div>
          <div id="criteriaRecapPagination" class="pagination-bar"></div>
        </div>
      </section>

      <section id="settings" class="page">
        <div class="card">
          <div class="section-head"><div><h3>Pengaturan</h3><p>Atur tahun ajaran, semester, dan bobot nilai.</p></div><button class="btn btn-primary">Simpan Pengaturan</button></div>
          <div class="grid form-grid">
            <div class="field"><label>Tahun Ajaran</label><select><option>2025/2026</option><option>2026/2027</option></select></div>
            <div class="field"><label>Semester</label><select><option>Ganjil</option><option>Genap</option></select></div>
            <div class="field"><label>Bobot Afektif</label><input value="25%" /></div>
            <div class="field"><label>Bobot Kognitif</label><input value="25%" /></div>
            <div class="field"><label>Bobot Kehadiran</label><input value="10%" /></div>
            <div class="field"><label>Bobot Psikomotor</label><input value="40%" /></div>
            <div class="field"><label>Catatan</label><input value="Pengaturan ini digunakan untuk kebutuhan presentasi dan penyesuaian sistem." /></div>
          </div>
        </div>
      </section>

      <section id="audit" class="page">
        <div class="card">
          <div class="section-head">
            <div>
              <h3>Audit Log</h3>
              <p>Riwayat aktivitas sistem, termasuk login, tambah data, dan pembukaan rekap.</p>
            </div>
            <button class="btn btn-soft">Refresh</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>No</th>
                  <th>Waktu</th>
                  <th>User</th>
                  <th>Role</th>
                  <th>Aksi</th>
                  <th>Menu</th>
                  <th>Detail</th>
                </tr>
              </thead>
              <tbody id="auditTableBody"></tbody>
            </table>
          </div>
        </div>
      </section>

      <div class="footer-note">MADEP PJOK | Aplikasi Asesmen Digital PJOK</div>
    </main>
  </section>

  <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
  </form>
  <script>
    window.authUser = {{ Illuminate\Support\Js::from([
      'name' => auth()->user()->name,
      'email' => auth()->user()->email,
      'role' => auth()->user()->role,
    ]) }};
    window.initialData = {{ Illuminate\Support\Js::from($initialData ?? []) }};
    window.csrfToken = {{ Illuminate\Support\Js::from(csrf_token()) }};
  </script>
  <script src="{{ asset('assets/js/data.js') }}"></script>
  <script src="{{ asset('assets/js/state.js') }}"></script>
  <script src="{{ asset('assets/js/utils.js') }}"></script>
  <script src="{{ asset('assets/js/roles.js') }}"></script>
  <script src="{{ asset('assets/js/recap.js') }}"></script>
  <script src="{{ asset('assets/js/assessment.js') }}"></script>
  <script src="{{ asset('assets/js/roster.js') }}"></script>
  <script src="{{ asset('assets/js/dashboard.js') }}"></script>
  <script src="{{ asset('assets/js/admin.js') }}"></script>
  <script src="{{ asset('assets/js/user-management.js') }}"></script>
  <script src="{{ asset('assets/js/audit.js') }}"></script>
  <script src="{{ asset('assets/js/navigation.js') }}"></script>
  <script src="{{ asset('assets/js/auth.js') }}"></script>
  <script src="{{ asset('assets/js/forms.js') }}"></script>
  <script src="{{ asset('assets/script.js') }}"></script>
  <script src="{{ asset('assets/js/events.js') }}"></script>
</body>
</html>

