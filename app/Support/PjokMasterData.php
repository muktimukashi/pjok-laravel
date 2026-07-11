<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\PjokRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PjokMasterData
{
    public static function load(): array
    {
        $defaults = self::defaults();

        return [
            'classRecords' => self::loadClasses($defaults['classRecords']),
            'studentRecords' => self::loadStudents($defaults['studentRecords']),
            'teacherRecords' => self::loadTeachers('Guru PJOK', $defaults['teacherRecords']),
            'principalRecords' => self::loadTeachers('Kepala Sekolah', $defaults['principalRecords']),
            'academicYearRecords' => self::loadAcademicYears($defaults['academicYearRecords']),
            'teacherAssignmentRecords' => self::loadGeneric('teacherAssignmentRecords', $defaults['teacherAssignmentRecords']),
            'principalPeriodRecords' => self::loadGeneric('principalPeriodRecords', $defaults['principalPeriodRecords']),
            'placementRecords' => self::loadGeneric('placementRecords', $defaults['placementRecords']),
            'criteriaRecords' => self::loadGeneric('criteriaRecords', $defaults['criteriaRecords']),
            'assessmentRecords' => self::loadAssessments($defaults['assessmentRecords']),
        ];
    }

    public static function syncRecords(string $type, array $records): void
    {
        match ($type) {
            'classRecords' => self::syncClasses($records),
            'studentRecords' => self::syncStudents($records),
            'teacherRecords' => self::syncTeachers('Guru PJOK', $records),
            'principalRecords' => self::syncTeachers('Kepala Sekolah', $records),
            'academicYearRecords' => self::syncAcademicYears($records),
            'assessmentRecords' => self::syncAssessments($records),
            default => self::syncGeneric($type, $records),
        };
    }

    public static function seedDefaults(): void
    {
        foreach (self::defaults() as $type => $records) {
            self::syncRecords($type, $records);
        }
    }

    private static function loadClasses(array $fallback): array
    {
        if (! Schema::hasTable('classes')) return $fallback;

        $records = SchoolClass::query()->orderBy('name')->get()->map(fn (SchoolClass $record) => [
            'name' => $record->name,
        ])->values()->all();

        return $records ?: $fallback;
    }

    private static function loadAcademicYears(array $fallback): array
    {
        if (! Schema::hasTable('academic_years')) return $fallback;

        $records = AcademicYear::query()->orderBy('name')->get()->map(fn (AcademicYear $record) => [
            'name' => $record->name,
            'status' => $record->status,
        ])->values()->all();

        return $records ?: $fallback;
    }

    private static function loadStudents(array $fallback): array
    {
        if (! Schema::hasTable('students')) return $fallback;

        $records = Student::query()->orderBy('name')->get()->map(fn (Student $student) => [
            'id' => $student->student_id,
            'name' => $student->name,
            'gender' => $student->gender,
            'email' => $student->email,
            'status' => $student->status,
            'className' => $student->class_name,
            'year' => $student->year,
            'semester' => $student->semester,
            'attendance' => (int) $student->attendance,
            'cognitive' => (float) $student->cognitive,
            'affective' => (float) $student->affective,
            'psychomotor' => (float) $student->psychomotor,
            'finalScore' => (float) $student->final_score,
            'predicate' => $student->predicate,
            'predicateClass' => $student->predicate_class,
        ])->values()->all();

        return $records ?: $fallback;
    }

    private static function loadTeachers(string $role, array $fallback): array
    {
        if (! Schema::hasTable('teachers')) return $fallback;

        $records = Teacher::query()->where('role', $role)->orderBy('name')->get()->map(fn (Teacher $teacher) => [
            'nip' => $teacher->nip,
            'name' => $teacher->name,
            'gender' => $teacher->gender,
            'email' => $teacher->email,
            'status' => $teacher->status,
            'role' => $teacher->role,
        ])->values()->all();

        return $records ?: $fallback;
    }

    private static function loadAssessments(array $fallback): array
    {
        if (! Schema::hasTable('assessments')) return $fallback;

        $records = Assessment::query()->orderBy('year')->orderBy('semester')->orderBy('class_name')->orderBy('meeting')->get()->map(fn (Assessment $assessment) => [
            'year' => $assessment->year,
            'semester' => $assessment->semester,
            'className' => $assessment->class_name,
            'meeting' => $assessment->meeting,
            'type' => $assessment->type,
            'materi' => $assessment->materi,
            'tujuan' => $assessment->tujuan,
            'aspect' => $assessment->aspect,
            'criteria' => $assessment->criteria ?: [],
        ])->values()->all();

        return $records ?: $fallback;
    }

    private static function loadGeneric(string $type, array $fallback): array
    {
        if (! Schema::hasTable('pjok_records')) return $fallback;

        $records = PjokRecord::query()
            ->where('type', $type)
            ->orderBy('id')
            ->get()
            ->map(fn (PjokRecord $record) => $record->payload ?: [])
            ->filter()
            ->values()
            ->all();

        return $records ?: $fallback;
    }

    private static function syncClasses(array $records): void
    {
        if (! Schema::hasTable('classes')) return;

        DB::transaction(function () use ($records): void {
            SchoolClass::query()->delete();
            foreach ($records as $record) {
                if (! empty($record['name'])) SchoolClass::query()->create(['name' => $record['name']]);
            }
        });
    }

    private static function syncAcademicYears(array $records): void
    {
        if (! Schema::hasTable('academic_years')) return;

        DB::transaction(function () use ($records): void {
            AcademicYear::query()->delete();
            foreach ($records as $record) {
                if (! empty($record['name'])) AcademicYear::query()->create([
                    'name' => $record['name'],
                    'status' => $record['status'] ?? 'Aktif',
                ]);
            }
        });
    }

    private static function syncStudents(array $records): void
    {
        if (! Schema::hasTable('students')) return;

        DB::transaction(function () use ($records): void {
            Student::query()->delete();
            foreach ($records as $record) {
                if (empty($record['id']) || empty($record['name'])) continue;
                Student::query()->create([
                    'student_id' => $record['id'],
                    'name' => $record['name'],
                    'gender' => $record['gender'] ?? null,
                    'email' => $record['email'] ?? null,
                    'status' => $record['status'] ?? 'Aktif',
                    'class_name' => $record['className'] ?? null,
                    'year' => $record['year'] ?? null,
                    'semester' => $record['semester'] ?? null,
                    'attendance' => (int) ($record['attendance'] ?? 0),
                    'cognitive' => (float) ($record['cognitive'] ?? 0),
                    'affective' => (float) ($record['affective'] ?? 0),
                    'psychomotor' => (float) ($record['psychomotor'] ?? 0),
                    'final_score' => (float) ($record['finalScore'] ?? 0),
                    'predicate' => $record['predicate'] ?? null,
                    'predicate_class' => $record['predicateClass'] ?? null,
                ]);
            }
        });
    }

    private static function syncTeachers(string $role, array $records): void
    {
        if (! Schema::hasTable('teachers')) return;

        DB::transaction(function () use ($role, $records): void {
            Teacher::query()->where('role', $role)->delete();
            foreach ($records as $record) {
                if (empty($record['nip']) || empty($record['name'])) continue;
                Teacher::query()->create([
                    'nip' => $record['nip'],
                    'name' => $record['name'],
                    'gender' => $record['gender'] ?? null,
                    'email' => $record['email'] ?? null,
                    'status' => $record['status'] ?? 'Aktif',
                    'role' => $role,
                ]);
            }
        });
    }

    private static function syncAssessments(array $records): void
    {
        if (! Schema::hasTable('assessments')) return;

        DB::transaction(function () use ($records): void {
            Assessment::query()->delete();
            foreach ($records as $record) {
                Assessment::query()->create([
                    'year' => $record['year'] ?? '2025/2026',
                    'semester' => $record['semester'] ?? 'Ganjil',
                    'class_name' => $record['className'] ?? '-',
                    'meeting' => (string) ($record['meeting'] ?? '1'),
                    'type' => $record['type'] ?? 'Afektif',
                    'materi' => $record['materi'] ?? null,
                    'tujuan' => $record['tujuan'] ?? null,
                    'aspect' => $record['aspect'] ?? null,
                    'criteria' => $record['criteria'] ?? [],
                ]);
            }
        });
    }

    private static function syncGeneric(string $type, array $records): void
    {
        if (! Schema::hasTable('pjok_records')) return;

        DB::transaction(function () use ($type, $records): void {
            PjokRecord::query()->where('type', $type)->delete();
            foreach (array_values($records) as $index => $payload) {
                $code = self::recordCode($type, $payload, $index);
                PjokRecord::query()->create([
                    'type' => $type,
                    'code' => $code,
                    'name' => $payload['name'] ?? $payload['className'] ?? $payload['materi'] ?? $code,
                    'payload' => $payload,
                ]);
            }
        });
    }

    public static function defaults(): array
    {
        $classRecords = [];
        foreach (range(1, 6) as $grade) {
            foreach (['A', 'B', 'C'] as $section) {
                $classRecords[] = ['name' => "Kelas {$grade}{$section}"];
            }
        }

        $firstNames = ['Andi', 'Siti', 'Bima', 'Dewi', 'Rizky', 'Nadia', 'Fajar', 'Aulia', 'Bagas', 'Citra'];
        $lastNames = ['Pratama', 'Rahmawati', 'Saputra', 'Lestari', 'Maulana', 'Putri', 'Kurniawan', 'Safitri', 'Nugraha', 'Permata'];
        $studentRecords = [];

        foreach (array_slice($classRecords, 0, 6) as $classIndex => $classRecord) {
            $grade = preg_replace('/\D/', '', $classRecord['name']) ?: '1';
            $section = substr($classRecord['name'], -1);

            foreach (range(1, 8) as $studentIndex) {
                $id = $grade . (ord($section) - 64) . str_pad((string) $studentIndex, 3, '0', STR_PAD_LEFT);
                $attendance = 82 + (($classIndex + $studentIndex) % 17);
                $cognitive = round(3.1 + (($classIndex + $studentIndex) % 18) / 10, 1);
                $affective = round(3.2 + (($classIndex + $studentIndex + 3) % 17) / 10, 1);
                $psychomotor = round(3.3 + (($classIndex + $studentIndex + 5) % 16) / 10, 1);
                $finalScore = round(($attendance * 0.1) + ($affective * 20 * 0.25) + ($cognitive * 20 * 0.25) + ($psychomotor * 20 * 0.4), 1);

                $studentRecords[] = [
                    'id' => $id,
                    'name' => $firstNames[$studentIndex - 1] . ' ' . $lastNames[($classIndex + $studentIndex - 1) % count($lastNames)],
                    'gender' => $studentIndex % 2 === 1 ? 'Laki-laki' : 'Perempuan',
                    'email' => strtolower($id) . '@siswa.sekolah.id',
                    'status' => 'Aktif',
                    'className' => $classRecord['name'],
                    'year' => '2025/2026',
                    'semester' => 'Ganjil',
                    'attendance' => $attendance,
                    'cognitive' => $cognitive,
                    'affective' => $affective,
                    'psychomotor' => $psychomotor,
                    'finalScore' => $finalScore,
                    'predicate' => $finalScore >= 86 ? 'Sangat Baik' : ($finalScore >= 76 ? 'Baik' : 'Cukup'),
                    'predicateClass' => $finalScore >= 86 ? 'badge-green' : ($finalScore >= 76 ? 'badge-blue' : 'badge-yellow'),
                ];
            }
        }

        $teacherRecords = [
            ['nip' => '198804122010011001', 'name' => 'Pak Ahmad Fauzi', 'gender' => 'Laki-laki', 'email' => 'ahmad.fauzi@sekolah.id', 'status' => 'Aktif', 'role' => 'Guru PJOK'],
            ['nip' => '199006182014022002', 'name' => 'Bu Rina Kartika', 'gender' => 'Perempuan', 'email' => 'rina.kartika@sekolah.id', 'status' => 'Aktif', 'role' => 'Guru PJOK'],
        ];
        $principalRecords = [
            ['nip' => '197705122001122001', 'name' => 'Drs. Hendra Prasetyo', 'gender' => 'Laki-laki', 'email' => 'kepsek@sekolah.id', 'status' => 'Aktif', 'role' => 'Kepala Sekolah'],
        ];
        $academicYearRecords = [
            ['name' => '2025/2026', 'status' => 'Aktif'],
            ['name' => '2026/2027', 'status' => 'Nonaktif'],
        ];
        $teacherAssignmentRecords = array_map(function (array $classRecord) use ($teacherRecords) {
            $grade = (int) (preg_replace('/\D/', '', $classRecord['name']) ?: 1);
            $teacher = $grade <= 3 ? $teacherRecords[0] : $teacherRecords[1];
            return ['teacherNip' => $teacher['nip'], 'className' => $classRecord['name'], 'year' => '2025/2026', 'status' => 'Aktif'];
        }, $classRecords);
        $principalPeriodRecords = [[
            'principalNip' => $principalRecords[0]['nip'],
            'startYear' => '2025',
            'endYear' => '2029',
            'status' => 'Aktif',
        ]];
        $placementRecords = array_map(fn (array $student) => [
            'studentId' => $student['id'],
            'className' => $student['className'],
            'year' => $student['year'],
            'status' => $student['status'],
        ], $studentRecords);
        $criteriaRecords = [
            ['year' => '2025/2026', 'semester' => 'Ganjil', 'className' => 'Kelas 5A', 'meeting' => '1', 'type' => 'Afektif', 'aspect' => 'Sportifitas', 'criteria' => [1 => 'Belum Berkembang', 2 => 'Mulai Berkembang', 3 => 'Berkembang', 4 => 'Cakap', 5 => 'Mahir']],
            ['year' => '2025/2026', 'semester' => 'Ganjil', 'className' => 'Kelas 5A', 'meeting' => '1', 'type' => 'Kognitif', 'aspect' => 'Tes Objektif/ Tes Tertulis', 'criteria' => [1 => 'Belum Memahami', 2 => 'Mulai Memahami', 3 => 'Memahami', 4 => 'Lebih Memahami', 5 => 'Sangat Memahami']],
            ['year' => '2025/2026', 'semester' => 'Ganjil', 'className' => 'Kelas 5A', 'meeting' => '1', 'type' => 'Psikomotor', 'aspect' => 'Proses Gerak', 'criteria' => [1 => 'Belum Berkembang', 2 => 'Mulai Berkembang', 3 => 'Berkembang', 4 => 'Cakap', 5 => 'Mahir']],
        ];
        $assessmentRecords = [
            ['year' => '2025/2026', 'semester' => 'Ganjil', 'className' => 'Kelas 5A', 'meeting' => '1', 'type' => 'Afektif', 'materi' => 'Permainan bola kecil: kerja sama tim', 'tujuan' => 'Siswa menunjukkan sportivitas dan tanggung jawab saat bermain.', 'aspect' => 'Sportifitas', 'criteria' => [1 => 'Sering melanggar aturan dan belum menerima keputusan permainan', 2 => 'Mulai mengikuti aturan dengan banyak arahan guru', 3 => 'Mengikuti aturan dan menerima hasil permainan dengan cukup baik', 4 => 'Konsisten sportif serta menghargai teman dan lawan', 5 => 'Menjadi teladan sportivitas dan membantu menjaga suasana permainan']],
            ['year' => '2025/2026', 'semester' => 'Ganjil', 'className' => 'Kelas 5A', 'meeting' => '1', 'type' => 'Kognitif', 'materi' => 'Aturan dasar permainan kasti', 'tujuan' => 'Siswa memahami aturan, posisi pemain, dan strategi sederhana permainan kasti.', 'aspect' => 'Tes Objektif/ Tes Tertulis', 'criteria' => [1 => 'Belum Memahami', 2 => 'Mulai Memahami', 3 => 'Memahami', 4 => 'Lebih Memahami', 5 => 'Sangat Memahami']],
            ['year' => '2025/2026', 'semester' => 'Ganjil', 'className' => 'Kelas 5A', 'meeting' => '1', 'type' => 'Psikomotor', 'materi' => 'Teknik melempar dan menangkap bola kasti', 'tujuan' => 'Siswa mampu melakukan gerak dasar lempar tangkap dengan koordinasi yang baik.', 'aspect' => 'Koordinasi', 'criteria' => [1 => 'Gerakan belum terkoordinasi dan bola sering tidak terarah', 2 => 'Koordinasi mulai muncul namun masih sering kehilangan kontrol', 3 => 'Koordinasi cukup baik dengan beberapa kesalahan kecil', 4 => 'Koordinasi baik, lemparan dan tangkapan cukup stabil', 5 => 'Koordinasi sangat baik, gerakan efektif dan konsisten']],
        ];

        return compact('classRecords', 'studentRecords', 'teacherRecords', 'principalRecords', 'academicYearRecords', 'teacherAssignmentRecords', 'principalPeriodRecords', 'placementRecords', 'criteriaRecords', 'assessmentRecords');
    }

    public static function recordCode(string $type, array $payload, int $index): string
    {
        return match ($type) {
            'studentRecords' => $payload['id'] ?? (string) $index,
            'teacherRecords', 'principalRecords' => $payload['nip'] ?? (string) $index,
            'classRecords', 'academicYearRecords' => $payload['name'] ?? (string) $index,
            'teacherAssignmentRecords' => ($payload['teacherNip'] ?? $index) . '-' . ($payload['className'] ?? $index) . '-' . ($payload['year'] ?? $index),
            'principalPeriodRecords' => ($payload['principalNip'] ?? $index) . '-' . ($payload['startYear'] ?? $index),
            'placementRecords' => ($payload['studentId'] ?? $index) . '-' . ($payload['year'] ?? $index),
            'criteriaRecords', 'assessmentRecords' => implode('-', [$payload['year'] ?? $index, $payload['semester'] ?? $index, $payload['className'] ?? $index, $payload['meeting'] ?? $index, $payload['type'] ?? $index, $payload['aspect'] ?? $index]),
            default => (string) $index,
        };
    }
}
