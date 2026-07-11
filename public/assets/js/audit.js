function renderAuditLogTable() {
  const tableBody = document.getElementById("auditTableBody");
  if (!tableBody) return;

  tableBody.innerHTML = auditLogRecords
    .map(
      (item, index) => `
        <tr>
          <td>${index + 1}</td>
          <td>${item.time}</td>
          <td>${item.user}</td>
          <td>${item.role}</td>
          <td>${item.action}</td>
          <td>${item.page}</td>
          <td>${item.detail}</td>
        </tr>
      `
    )
    .join("");
}

