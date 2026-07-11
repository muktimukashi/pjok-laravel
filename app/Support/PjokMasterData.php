<?php

namespace App\Support;

use App\Models\PjokRecord;

class PjokMasterData
{
    public static function load(): array
    {
        $defaults = self::defaults();
        $data = [];

        foreach (array_keys($defaults) as $type) {
            $records = PjokRecord::query()
                ->where('type', $type)
                ->orderBy('id')
                ->get()
                ->map(fn (PjokRecord $record) => $record->payload ?: [])
                ->filter()
                ->values()
                ->all();

            $data[$type] = count($records) > 0 ? $records : $defaults[$type];
        }

        return $data;
    }

    public static function seedDefaults(): void
    {
        foreach (self::defaults() as $type => $records) {
            foreach ($records as $index => $payload) {
                $code = self::recordCode($type, $payload, $index);

                PjokRecord::query()->updateOrCreate(
                    ['type' => $type, 'code' => $code],
                    [
                        'name' => $payload['name'] ?? $payload['className'] ?? $payload['materi'] ?? $code,
                        'payload' => $payload,
                    ],
                );
            }
        }
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

    private static function recordCode(string $type, array $payload, int $index): string
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
