<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Gedung;
use App\Models\MasterRuangan;
use App\Models\TahunAkademik;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\PrasyaratMk;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\MasterSesiWaktu;
use App\Models\JadwalPerkuliahan;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Reset Database & Matikan Foreign Key Check Sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate(); Role::truncate(); Fakultas::truncate(); ProgramStudi::truncate();
        Gedung::truncate(); MasterRuangan::truncate(); TahunAkademik::truncate();
        Kurikulum::truncate(); MataKuliah::truncate(); PrasyaratMk::truncate();
        Dosen::truncate(); Mahasiswa::truncate(); Kelas::truncate(); 
        MasterSesiWaktu::truncate(); JadwalPerkuliahan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Setup Role
        $roleAdmin = Role::create(['nama_role' => 'Admin']);
        $roleDosen = Role::create(['nama_role' => 'Dosen']);
        $roleMhs   = Role::create(['nama_role' => 'Mahasiswa']);

        // 3. Setup Kampus (Fakultas & Prodi)
        $ft = Fakultas::create(['kode_fakultas' => 'FT', 'nama_fakultas' => 'Fakultas Teknik']);
        $prodiTI = ProgramStudi::create(['fakultas_id' => $ft->id, 'kode_prodi' => 'TI', 'nama_prodi' => 'Teknik Informatika', 'jenjang' => 'S1']);

        // 4. Setup Fasilitas (Gedung & Ruang)
        $gedungA = Gedung::create(['kode_gedung' => 'GA', 'nama_gedung' => 'Gedung A', 'lokasi' => 'Kampus Pusat']);
        $ruang101 = MasterRuangan::create(['gedung_id' => $gedungA->id, 'kode_ruangan' => '101', 'nama_ruangan' => 'Lab Komputer 1', 'kapasitas' => 40, 'jenis_ruangan' => 'Laboratorium']);

        // 5. Setup Akademik (Tahun & Sesi)
        $ta = TahunAkademik::create(['kode_tahun' => '20241', 'nama_tahun' => 'Ganjil 2024/2025', 'tanggal_mulai' => '2024-09-01', 'tanggal_selesai' => '2025-01-31', 'status' => 'Aktif']);
        $sesi1 = MasterSesiWaktu::create(['sesi_ke' => 1, 'jam_mulai' => '08:00', 'jam_selesai' => '09:40']);

        // 6. Setup Kurikulum & Mata Kuliah
        $kurikulum = Kurikulum::create(['prodi_id' => $prodiTI->id, 'nama_kurikulum' => 'Kurikulum 2024', 'tahun_mulai' => 2024, 'is_active' => true]);
        
        // MK 1: Dasar (ID 1)
        $mkDasar = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id, 'prodi_id' => $prodiTI->id,
            'kode_mk' => 'TI-101', 'nama_mk' => 'Algoritma Dasar', 'sks' => 3, 'semester_mk' => 1
        ]);
        
        // MK 2: Lanjut (ID 2) -> Punya Syarat MK 1
        $mkLanjut = MataKuliah::create([
            'kurikulum_id' => $kurikulum->id, 'prodi_id' => $prodiTI->id,
            'kode_mk' => 'TI-201', 'nama_mk' => 'Struktur Data', 'sks' => 4, 'semester_mk' => 2
        ]);

        // Set Prasyarat: Mau ambil Struktur Data, harus lulus Algoritma Dasar
        PrasyaratMk::create(['mk_id' => $mkLanjut->id, 'mk_syarat_id' => $mkDasar->id, 'jenis_syarat' => 'Wajib Lulus']);

        // 7. Buat User & Profile (Dosen & Mahasiswa)
        
        // --- DOSEN (Pak Budi) ---
        $userDosen = User::create([
            'username' => 'dosen1', 'email' => 'dosen@univ.ac.id', 'password' => Hash::make('password'), 
            'role_id' => $roleDosen->id, 'is_active' => true
        ]);
        $dosen = Dosen::create([
            'user_id' => $userDosen->id, 'nama_depan' => 'Budi', 'nip_dosen' => '123456', 'status_keaktifan' => 'Aktif'
        ]);

        // --- MAHASISWA (Siti - Semester 1) ---
        $userMhs = User::create([
            'username' => 'mhs1', 'email' => 'siti@univ.ac.id', 'password' => Hash::make('password'), 
            'role_id' => $roleMhs->id, 'is_active' => true
        ]);
        $mhs = Mahasiswa::create([
            'user_id' => $userMhs->id, 'nama_lengkap' => 'Siti Aminah', 'nim' => '2024001', 
            'angkatan' => 2024, 'prodi_id' => $prodiTI->id, 'status_mahasiswa' => 'Aktif'
        ]);

        // 8. Buka Kelas & Jadwal
        // Kelas untuk MK Lanjut (Struktur Data)
        $kelasA = Kelas::create([
            'tahun_akademik_id' => $ta->id, 'mata_kuliah_id' => $mkLanjut->id, 
            'nama_kelas' => 'TI-2A', 'kuota' => 30
        ]);

        JadwalPerkuliahan::create([
            'kelas_id' => $kelasA->id, 'dosen_id' => $dosen->id, 'ruangan_id' => $ruang101->id, 
            'master_sesi_waktu_id' => $sesi1->id, 'hari' => 'Senin'
        ]);
    }
}