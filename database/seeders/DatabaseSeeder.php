<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset Database (Matikan Foreign Key Check)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'users', 'roles', 'permissions', 'fakultas', 'program_studis', 
            'gedung', 'master_ruangans', 'tahun_akademiks', 'master_sesi_waktus',
            'kurikulum', 'mata_kuliahs', 'prasyarat_mk', 'dosens', 'stafs', 
            'mahasiswas', 'kelas', 'jadwal_perkuliahans', 'krs_mahasiswas',
            'realisasi_perkuliahan', 'presensi_mahasiswa', 'materi_kuliah',
            'tugas_kuliah', 'pengumpulan_tugas', 'kelas_dosen'
        ];

        foreach ($tables as $table) {
            // Cek jika tabel ada sebelum truncate untuk menghindari error
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $password = Hash::make('password123');
        $faker = \Faker\Factory::create('id_ID');

        // ==========================================
        // 1. MASTER UTAMA (Roles, Permissions, Tahun, Sesi)
        // ==========================================
        
        echo "Seeding Roles & Permissions...\n";
        $roles = ['Admin', 'Dosen', 'Mahasiswa', 'Staf'];
        $roleIds = [];
        foreach ($roles as $r) {
            $roleIds[$r] = DB::table('roles')->insertGetId([
                'nama_role' => $r, 
                'deskripsi' => "Role untuk $r",
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // 15 Permissions
        for ($i = 1; $i <= 15; $i++) {
            DB::table('permissions')->insert([
                'nama_aksi' => 'aksi_' . $i,
                'nama_menu' => 'Menu ' . ceil($i / 3),
                'deskripsi' => 'Hak akses simulasi ke-' . $i,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        echo "Seeding Tahun Akademik & Sesi...\n";
        // 5 Tahun Akademik
        $tahunList = ['20221', '20222', '20231', '20232', '20241'];
        $statusList = ['Selesai', 'Selesai', 'Selesai', 'Selesai', 'Aktif'];
        $taIds = [];
        foreach ($tahunList as $idx => $kode) {
            $taIds[] = DB::table('tahun_akademiks')->insertGetId([
                'kode_tahun' => $kode,
                'nama_tahun' => 'Tahun Ajaran ' . substr($kode, 0, 4) . ' ' . (substr($kode, 4, 1) == '1' ? 'Ganjil' : 'Genap'),
                'tanggal_mulai' => Carbon::createFromDate(substr($kode, 0, 4), substr($kode, 4, 1) == '1' ? 9 : 2, 1),
                'tanggal_selesai' => Carbon::createFromDate(substr($kode, 0, 4) + 1, substr($kode, 4, 1) == '1' ? 1 : 7, 31),
                'status' => $statusList[$idx],
                'created_at' => now(), 'updated_at' => now()
            ]);
        }
        $activeTaId = end($taIds);

        // 12 Sesi Waktu
        $sesiIds = [];
        for ($i = 1; $i <= 12; $i++) {
            $start = 7 + $i; // Mulai jam 08:00
            $sesiIds[] = DB::table('master_sesi_waktus')->insertGetId([
                'sesi_ke' => $i,
                'jam_mulai' => sprintf('%02d:00:00', $start),
                'jam_selesai' => sprintf('%02d:50:00', $start),
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // ==========================================
        // 2. INFRASTRUKTUR & ORGANISASI
        // ==========================================
        
        echo "Seeding Gedung, Ruangan, Fakultas, Prodi...\n";
        // 3 Gedung
        $gedungIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $gedungIds[] = DB::table('gedung')->insertGetId([
                'kode_gedung' => 'G' . $i,
                'nama_gedung' => 'Gedung ' . chr(64 + $i), // Gedung A, B, C
                'lokasi' => 'Kampus Pusat Area ' . $i,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // 15 Ruangan (5 per gedung)
        $ruanganIds = [];
        foreach ($gedungIds as $gId) {
            for ($j = 1; $j <= 5; $j++) {
                $ruanganIds[] = DB::table('master_ruangans')->insertGetId([
                    'gedung_id' => $gId,
                    'kode_ruangan' => 'R-' . $gId . '-0' . $j,
                    'nama_ruangan' => 'Ruang Teori ' . $gId . '.' . $j,
                    'kapasitas' => 40,
                    'jenis_ruangan' => $j == 5 ? 'Laboratorium' : 'Kelas',
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }

        // 3 Fakultas
        $fakultasIds = [];
        $fakNames = ['Teknik', 'Ekonomi', 'Ilmu Komputer'];
        foreach ($fakNames as $idx => $fName) {
            $fakultasIds[] = DB::table('fakultas')->insertGetId([
                'kode_fakultas' => 'F0' . ($idx + 1),
                'nama_fakultas' => 'Fakultas ' . $fName,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // 6 Prodi (2 per fakultas)
        $prodiIds = [];
        $prodiCounter = 1;
        foreach ($fakultasIds as $fId) {
            for ($p = 1; $p <= 2; $p++) {
                $prodiIds[] = DB::table('program_studis')->insertGetId([
                    'fakultas_id' => $fId,
                    'kode_prodi' => 'P0' . $prodiCounter++,
                    'nama_prodi' => 'Prodi Studi ' . $prodiCounter,
                    'jenjang' => 'S1',
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }

        // 6 Kurikulum (1 per prodi)
        $kurikulumIds = [];
        foreach ($prodiIds as $pId) {
            $kurikulumIds[$pId] = DB::table('kurikulum')->insertGetId([
                'prodi_id' => $pId,
                'nama_kurikulum' => 'Kurikulum Merdeka 2024',
                'tahun_mulai' => 2024,
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // ==========================================
        // 3. USERS (Dosen, Mahasiswa, Staf)
        // ==========================================

        echo "Seeding Users (Dosen, Mahasiswa)...\n";
        
        // 15 Dosen
        $dosenIds = [];
        for ($i = 1; $i <= 15; $i++) {
            $userId = DB::table('users')->insertGetId([
                'username' => 'dosen' . $i,
                'email' => 'dosen' . $i . '@univ.ac.id',
                'password' => $password,
                'role_id' => $roleIds['Dosen'],
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now()
            ]);

            $dosenIds[] = DB::table('dosens')->insertGetId([
                'user_id' => $userId,
                'nama_depan' => $faker->firstName,
                'nama_belakang' => $faker->lastName,
                'nip_dosen' => '1990010' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email_institusi' => 'dosen' . $i . '@univ.ac.id',
                'status_keaktifan' => 'Aktif',
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // 30 Mahasiswa
        $mahasiswaIds = [];
        for ($i = 1; $i <= 30; $i++) {
            $userId = DB::table('users')->insertGetId([
                'username' => 'mhs' . $i,
                'email' => 'mhs' . $i . '@student.univ.ac.id',
                'password' => $password,
                'role_id' => $roleIds['Mahasiswa'],
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now()
            ]);

            // Assign random prodi & dosen wali
            $mahasiswaIds[] = DB::table('mahasiswas')->insertGetId([
                'user_id' => $userId,
                'nama_lengkap' => $faker->name,
                'nim' => '202400' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'angkatan' => 2024,
                'prodi_id' => $prodiIds[array_rand($prodiIds)],
                'dosen_wali_id' => $dosenIds[array_rand($dosenIds)],
                'status_mahasiswa' => 'Aktif',
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // ==========================================
        // 4. AKADEMIK (MK, Kelas, Jadwal)
        // ==========================================

        echo "Seeding Mata Kuliah & Kelas...\n";
        
        // 20 Mata Kuliah
        $mkIds = [];
        foreach ($prodiIds as $pId) {
            for ($k = 1; $k <= 4; $k++) { // 3-4 MK per prodi
                $mkIds[] = DB::table('mata_kuliahs')->insertGetId([
                    'kurikulum_id' => $kurikulumIds[$pId],
                    'prodi_id' => $pId,
                    'kode_mk' => 'MK-' . $pId . '-' . $k,
                    'nama_mk' => 'Mata Kuliah ' . $faker->jobTitle, // Nama random aja
                    'sks' => rand(2, 4),
                    'semester_mk' => rand(1, 8),
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }

        // 10 Prasyarat MK
        for ($i = 0; $i < 10; $i++) {
            // Pastikan tidak duplicate dan tidak self-reference
            if (isset($mkIds[$i + 1])) {
                DB::table('prasyarat_mk')->insertOrIgnore([
                    'mk_id' => $mkIds[$i+1], // MK Lanjut
                    'mk_syarat_id' => $mkIds[$i], // MK Dasar
                    'jenis_syarat' => 'Wajib Lulus',
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }

        // 20 Kelas (Berdasarkan MK yang ada)
        $kelasIds = [];
        foreach ($mkIds as $idx => $mkId) {
            $kelasIds[] = DB::table('kelas')->insertGetId([
                'tahun_akademik_id' => $activeTaId,
                'mata_kuliah_id' => $mkId,
                'nama_kelas' => 'Kelas ' . chr(65 + ($idx % 3)), // Kelas A, B, C
                'kuota' => 40,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        echo "Seeding Jadwal & Kelas Dosen...\n";
        
        // Assign Dosen ke Kelas & Buat Jadwal
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        foreach ($kelasIds as $idx => $klsId) {
            $dosenPengajar = $dosenIds[$idx % count($dosenIds)];
            
            // 1. Insert Team Teaching / Kelas Dosen
            DB::table('kelas_dosen')->insert([
                'kelas_id' => $klsId,
                'dosen_id' => $dosenPengajar,
                'is_koordinator' => true,
                'sks_beban' => 3.00,
                'created_at' => now(), 'updated_at' => now()
            ]);

            // 2. Insert Jadwal (Hindari bentrok sederhana dengan modulo)
            DB::table('jadwal_perkuliahans')->insert([
                'kelas_id' => $klsId,
                'dosen_id' => $dosenPengajar,
                'ruangan_id' => $ruanganIds[$idx % count($ruanganIds)],
                'master_sesi_waktu_id' => $sesiIds[$idx % count($sesiIds)],
                'hari' => $hariList[$idx % 5],
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // ==========================================
        // 5. TRANSAKSI (KRS, LMS, Presensi)
        // ==========================================

        echo "Seeding KRS, Materi, Tugas, Realisasi...\n";

        // 60 Record KRS (Setiap mahasiswa ambil 2 kelas)
        foreach ($mahasiswaIds as $idx => $mhsId) {
            // Ambil 2 kelas random
            $ambilKelas = [$kelasIds[$idx % count($kelasIds)], $kelasIds[($idx + 1) % count($kelasIds)]];
            
            foreach ($ambilKelas as $klsId) {
                DB::table('krs_mahasiswas')->insertOrIgnore([
                    'mahasiswa_id' => $mhsId,
                    'kelas_id' => $klsId,
                    'tahun_akademik_id' => $activeTaId,
                    'status_approval' => 'Disetujui',
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }

        // 20 Materi Kuliah (1 per kelas)
        foreach ($kelasIds as $klsId) {
            DB::table('materi_kuliah')->insert([
                'kelas_id' => $klsId,
                'judul_materi' => 'Pengenalan ' . $faker->word,
                'deskripsi' => $faker->sentence,
                'file_path' => 'uploads/materi/sample.pdf',
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // 20 Tugas Kuliah
        $tugasIds = [];
        foreach ($kelasIds as $klsId) {
            $tugasIds[] = DB::table('tugas_kuliah')->insertGetId([
                'kelas_id' => $klsId,
                'judul_tugas' => 'Tugas 1: ' . $faker->word,
                'deskripsi' => 'Kerjakan soal halaman 1-10',
                'deadline' => now()->addDays(7),
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // 30 Pengumpulan Tugas (Random mahasiswa mengumpulkan)
        // Kita ambil data KRS untuk validasi mahasiswa ambil kelas tsb
        $krsData = DB::table('krs_mahasiswas')->get();
        $counterTugas = 0;
        foreach ($krsData as $krs) {
            if ($counterTugas >= 30) break; // Limit 30 data
            
            // Cari tugas di kelas ini
            $tugas = DB::table('tugas_kuliah')->where('kelas_id', $krs->kelas_id)->first();
            
            if ($tugas) {
                DB::table('pengumpulan_tugas')->insert([
                    'tugas_id' => $tugas->id,
                    'mahasiswa_id' => $krs->mahasiswa_id,
                    'file_jawaban_path' => 'uploads/tugas/jawab_mhs.pdf',
                    'waktu_pengumpulan' => now(),
                    'nilai' => rand(70, 100),
                    'catatan_dosen' => 'Bagus',
                    'created_at' => now(), 'updated_at' => now()
                ]);
                $counterTugas++;
            }
        }

        // 20 Realisasi Perkuliahan (Jurnal Dosen)
        $realisasiIds = [];
        foreach ($kelasIds as $idx => $klsId) {
            // Ambil data jadwal untuk referensi ruangan & dosen
            $jadwal = DB::table('jadwal_perkuliahans')->where('kelas_id', $klsId)->first();
            
            if($jadwal) {
                $realisasiIds[] = DB::table('realisasi_perkuliahan')->insertGetId([
                    'kelas_id' => $klsId,
                    'tanggal_aktual' => now()->subDays(rand(1, 5)),
                    'jam_mulai_aktual' => '08:00:00',
                    'jam_selesai_aktual' => '10:00:00',
                    'ruangan_id' => $jadwal->ruangan_id,
                    'dosen_pengajar_id' => $jadwal->dosen_id,
                    'topik_pembahasan' => 'Pertemuan ke-1: Kontrak Kuliah',
                    'status_pertemuan' => 'Terlaksana',
                    'kode_presensi' => strtoupper(Str::random(6)),
                    'batas_waktu_presensi' => now()->addHours(1),
                    'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }

        // 100 Presensi Mahasiswa
        // Loop realisasi, ambil mahasiswa yg ambil kelas itu (via KRS), lalu absenin
        $totalPresensi = 0;
        foreach ($realisasiIds as $realId) {
            $realisasi = DB::table('realisasi_perkuliahan')->where('id', $realId)->first();
            
            // Ambil mahasiswa di kelas ini
            $mhsDiKelas = DB::table('krs_mahasiswas')
                            ->where('kelas_id', $realisasi->kelas_id)
                            ->pluck('mahasiswa_id');

            foreach ($mhsDiKelas as $mhsId) {
                if ($totalPresensi >= 100) break 2; // Stop jika sudah 100 data
                
                DB::table('presensi_mahasiswa')->insert([
                    'realisasi_id' => $realId,
                    'mahasiswa_id' => $mhsId,
                    'waktu_presensi' => now(),
                    'status' => 'Hadir',
                    'device_info' => 'Android / Chrome',
                    'created_at' => now(), 'updated_at' => now()
                ]);
                $totalPresensi++;
            }
        }

        echo "Seeding Selesai! Database telah terisi data dummy yang kaya.\n";
        
    }
}