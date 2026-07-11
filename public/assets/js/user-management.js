const userRoleLabels = {
  superadmin: 'Superadmin',
  admin: 'Admin',
  guru: 'Guru PJOK',
  kepsek: 'Kepala Sekolah',
  siswa: 'Siswa'
};

function getUserPayload(includePassword = true) {
  const payload = {
    name: getInputValue('userName'),
    email: getInputValue('userEmail'),
    role: getInputValue('userRole'),
    status: getInputValue('userStatus') || 'Aktif'
  };
  const password = getInputValue('userPassword');
  if (includePassword || password) payload.password = password;
  return payload;
}

function resetUserForm() {
  ['userFormId', 'userName', 'userEmail', 'userPassword'].forEach((id) => {
    const input = document.getElementById(id);
    if (input) input.value = '';
  });
  const role = document.getElementById('userRole');
  const status = document.getElementById('userStatus');
  if (role) role.value = 'admin';
  if (status) status.value = 'Aktif';
}

async function requestUsers(url, options = {}) {
  const response = await fetch(url, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': window.csrfToken || '',
      ...(options.headers || {})
    }
  });

  if (!response.ok) {
    const data = await response.json().catch(() => ({}));
    throw new Error(data.message || 'Gagal menyimpan user.');
  }

  userRecords = await response.json();
  renderUserManagement();
}

async function refreshUsers() {
  await requestUsers('/users', { method: 'GET', headers: { 'Content-Type': 'application/json' } });
}

async function saveUserRecord() {
  const id = getInputValue('userFormId');
  const payload = getUserPayload(!id);
  if (!payload.name || !payload.email || (!id && !payload.password)) return;

  await requestUsers(id ? `/users/${id}` : '/users', {
    method: id ? 'PUT' : 'POST',
    body: JSON.stringify(payload)
  });
  resetUserForm();
}

function editUserRecord(index) {
  const record = userRecords[index];
  if (!record) return;
  document.getElementById('userFormId').value = record.id || '';
  document.getElementById('userName').value = record.name || '';
  document.getElementById('userEmail').value = record.email || '';
  document.getElementById('userRole').value = record.role || 'siswa';
  document.getElementById('userStatus').value = record.status || 'Aktif';
  document.getElementById('userPassword').value = '';
  goTo('addUser');
}

async function deleteUserRecord(index) {
  const record = userRecords[index];
  if (!record || !confirm(`Hapus user ${record.name}?`)) return;
  await requestUsers(`/users/${record.id}`, { method: 'DELETE' });
}

function syncRoleEditorFromSelectedUser() {
  const select = document.getElementById('roleUserSelect');
  if (!select) return;
  const record = userRecords.find((user) => String(user.id) === select.value) || userRecords[0];
  if (!record) return;
  document.getElementById('roleUserRole').value = record.role || 'siswa';
  document.getElementById('roleUserStatus').value = record.status || 'Aktif';
}

async function saveSelectedUserRole() {
  const id = document.getElementById('roleUserSelect')?.value;
  const record = userRecords.find((user) => String(user.id) === String(id));
  if (!record) return;

  await requestUsers(`/users/${record.id}`, {
    method: 'PUT',
    body: JSON.stringify({
      name: record.name,
      email: record.email,
      role: document.getElementById('roleUserRole')?.value || record.role,
      status: document.getElementById('roleUserStatus')?.value || record.status
    })
  });
}

function renderUserRoleEditor() {
  const select = document.getElementById('roleUserSelect');
  if (!select) return;
  const current = select.value;
  select.innerHTML = userRecords.map((user) => `<option value="${user.id}">${user.name} - ${user.email}</option>`).join('');
  if (current) select.value = current;
  syncRoleEditorFromSelectedUser();
}

function renderUserTable() {
  const tbody = document.getElementById('userTableBody');
  if (!tbody) return;
  tbody.innerHTML = userRecords.length ? userRecords.map((user, index) => `
    <tr>
      <td>${index + 1}</td>
      <td>${user.name}</td>
      <td>${user.email}</td>
      <td>${userRoleLabels[user.role] || user.role}</td>
      <td>${badgeStatus(user.status)}</td>
      <td>
        <button class="btn btn-outline btn-sm" type="button" data-user-edit="${index}">Ubah</button>
        <button class="btn btn-red btn-sm" type="button" data-user-delete="${index}">Hapus</button>
      </td>
    </tr>`).join('') : '<tr><td colspan="6">Data user belum tersedia.</td></tr>';
}

function renderUserManagement() {
  renderUserRoleEditor();
  renderUserTable();
}

document.addEventListener('click', async (event) => {
  try {
    if (event.target.closest('[data-user-save]')) {
      await saveUserRecord();
      return;
    }
    if (event.target.closest('[data-user-role-save]')) {
      await saveSelectedUserRole();
      return;
    }
    if (event.target.closest('[data-user-refresh]')) {
      await refreshUsers();
      return;
    }
    const editButton = event.target.closest('[data-user-edit]');
    if (editButton) {
      editUserRecord(Number(editButton.dataset.userEdit));
      return;
    }
    const deleteButton = event.target.closest('[data-user-delete]');
    if (deleteButton) {
      await deleteUserRecord(Number(deleteButton.dataset.userDelete));
    }
  } catch (error) {
    console.error(error);
    alert(error.message || 'Aksi user gagal.');
  }
});

document.addEventListener('change', (event) => {
  if (event.target.matches('#roleUserSelect')) syncRoleEditorFromSelectedUser();
});

