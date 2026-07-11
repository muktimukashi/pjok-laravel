function getInputValue(id) {
  return document.getElementById(id)?.value.trim() || "";
}
function getStudentRecord(identity) {
  const key = String(identity || "").toLowerCase();
  return studentRecords.find((student) => student.id === identity || String(student.email || "").toLowerCase() === key) || studentRecords[0];
}

