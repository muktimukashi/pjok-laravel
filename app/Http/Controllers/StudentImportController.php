<?php

namespace App\Http\Controllers;

use App\Models\PjokRecord;
use App\Models\Student;
use App\Support\PjokMasterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class StudentImportController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize('create', PjokRecord::class);

        abort_unless(Schema::hasTable('students'), 500, 'Tabel siswa belum tersedia. Jalankan migrasi database terlebih dahulu.');

        $validated = $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $rows = $this->readCsv($validated['csv']->getRealPath());

        if (count($rows) < 2) {
            throw ValidationException::withMessages(['csv' => 'CSV harus punya header dan minimal satu baris data siswa.']);
        }

        $headers = array_map(fn ($header) => $this->normalizeHeader($header), array_shift($rows));
        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $record = $this->mapRow($headers, $row);

            if (empty($record['student_id']) || empty($record['name'])) {
                $skipped++;
                continue;
            }

            Student::query()->updateOrCreate(
                ['student_id' => $record['student_id']],
                $record,
            );
            $imported++;
        }

        return response()->json([
            'ok' => true,
            'imported' => $imported,
            'skipped' => $skipped,
            'studentRecords' => PjokMasterData::load()['studentRecords'],
        ]);
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        abort_unless($handle, 422, 'File CSV tidak bisa dibaca.');

        $rows = [];
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if ($row === [null] || implode('', $row) === '') continue;
            $rows[] = array_map(fn ($value) => trim((string) $value), $row);
        }
        fclose($handle);

        return $rows;
    }

    private function mapRow(array $headers, array $row): array
    {
        $data = [];
        foreach ($headers as $index => $header) {
            $data[$header] = $row[$index] ?? null;
        }

        $studentId = $data['nis'] ?? $data['nisn'] ?? $data['student_id'] ?? $data['id'] ?? null;

        return [
            'student_id' => $studentId,
            'name' => $data['nama'] ?? $data['nama_lengkap'] ?? $data['name'] ?? null,
            'gender' => $data['jenis_kelamin'] ?? $data['gender'] ?? null,
            'email' => $data['email'] ?? null,
            'status' => ($data['status'] ?? null) ?: 'Aktif',
            'class_name' => $data['kelas'] ?? $data['class'] ?? $data['class_name'] ?? null,
            'year' => $data['tahun_ajaran'] ?? $data['tahun'] ?? $data['year'] ?? null,
            'semester' => $data['semester'] ?? null,
            'attendance' => (int) ($data['kehadiran'] ?? $data['attendance'] ?? 0),
            'cognitive' => (float) ($data['kognitif'] ?? $data['cognitive'] ?? 0),
            'affective' => (float) ($data['afektif'] ?? $data['affective'] ?? 0),
            'psychomotor' => (float) ($data['psikomotor'] ?? $data['psychomotor'] ?? 0),
            'final_score' => (float) ($data['nilai_akhir'] ?? $data['final_score'] ?? 0),
            'predicate' => $data['predikat'] ?? $data['predicate'] ?? null,
            'predicate_class' => $data['kelas_predikat'] ?? $data['predicate_class'] ?? null,
        ];
    }

    private function normalizeHeader(string $header): string
    {
        $normalized = strtolower(trim($header, " \t\n\r\0\x0B\xEF\xBB\xBF"));
        $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized) ?: '';

        return trim($normalized, '_');
    }
}

