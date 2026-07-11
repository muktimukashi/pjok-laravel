function getInputValue(id) {
  return document.getElementById(id)?.value.trim() || "";
}
function getStudentRecord(identity) {
  const key = String(identity || "").toLowerCase();
  return studentRecords.find((student) => student.id === identity || String(student.email || "").toLowerCase() === key) || studentRecords[0];
}
const masterRecordSources = {
  studentRecords: () => studentRecords,
  teacherRecords: () => teacherRecords,
  principalRecords: () => principalRecords,
  classRecords: () => classRecords,
  academicYearRecords: () => academicYearRecords,
  teacherAssignmentRecords: () => teacherAssignmentRecords,
  principalPeriodRecords: () => principalPeriodRecords,
  placementRecords: () => placementRecords,
  criteriaRecords: () => criteriaRecords,
  assessmentRecords: () => assessmentRecords
};

async function persistMasterRecords(types) {
  const uniqueTypes = [...new Set(types)].filter((type) => masterRecordSources[type]);
  if (!uniqueTypes.length) return;

  try {
    await Promise.all(uniqueTypes.map(async (type) => {
      const response = await fetch('/records/sync', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.csrfToken || ''
        },
        body: JSON.stringify({ type, records: masterRecordSources[type]() })
      });

      if (!response.ok) {
        throw new Error(`Gagal menyimpan ${type}`);
      }
    }));
  } catch (error) {
    console.error(error);
    alert('Data sudah berubah di layar, tapi gagal disimpan ke database. Coba login ulang atau refresh lalu ulangi.');
  }
}

