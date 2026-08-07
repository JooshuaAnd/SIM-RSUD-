# BUKU PANDUAN PENGGUNA (USER MANUAL) TERLENGKAP - ADMIN PELATIHAN SIM-RSUD

Selamat datang di Panduan Pengguna Sistem Informasi Manajemen Rumah Sakit Umum Daerah (SIM-RSUD) khusus **Modul Pelatihan**. Buku panduan ini disusun secara khusus untuk Anda selaku Administrator (Admin) dengan bahasa yang sangat mudah dipahami oleh orang awam (non-IT). 

Semua istilah teknis, nama tombol, batas pengisian, format file, dan langkah-langkah detail telah dijabarkan di bawah ini agar Anda dapat mengoperasikan sistem tanpa kendala.

---

## DAFTAR ISI
1. [CARA LOGIN DAN KELUAR (LOGOUT)](#1-cara-login-dan-keluar-logout)
2. [PANDUAN NAVIGASI DASHBOARD UTAMA](#2-panduan-navigasi-dashboard-utama)
3. [MENGELOLA DATA MASTER (DATA DASAR)](#3-mengelola-data-master-data-dasar)
4. [MEMBUAT PROGRAM PELATIHAN BARU (FORMULIR UTAMA)](#4-membuat-program-pelatihan-baru-formulir-utama)
5. [MANAJEMEN KONTEN PELATIHAN (KELOLA DETAIL)](#5-manajemen-konten-pelatihan-kelola-detail)
   - [A. Mengatur Sesi Pelatihan (Jadwal Kelas)](#a-mengatur-sesi-pelatihan-jadwal-kelas)
   - [B. Mengunggah Materi Pembelajaran (Multi-Format)](#b-mengunggah-materi-pembelajaran-multi-format)
   - [C. Mengelola Kuis Mandiri (Pre-Test & Post-Test + Lampiran Media)](#c-mengelola-kuis-mandiri-pre-test--post-test--lampiran-media)
   - [D. Mengelola Kuesioner Kepuasan (Evaluasi Auto-Generated & Custom)](#d-mengelola-kuesioner-kepuasan-evaluasi-auto-generated--custom)
6. [MANAJEMEN PESERTA DAN ABSENSI](#6-manajemen-peserta-dan-absensi)
7. [PANDUAN SERTIFIKAT DAN PEJABAT PENANDATANGAN](#7-panduan-sertifikat-dan-pejabat-penandatangan)

---

## 1. CARA LOGIN DAN KELUAR (LOGOUT)

### A. Cara Masuk (Login)
1. BUKU PANDUAN PENGGUNA (USER MANUAL) TERLENGKAP - ADMIN PELATIHAN SIM-RSUD
2. Buka aplikasi browser di komputer Anda (Gunakan **Google Chrome** atau **Microsoft Edge** versi terbaru demi kelancaran sistem).
3. Ketikkan alamat situs web SIM-RSUD yang diberikan oleh tim IT.
4. Pada halaman beranda, klik tombol **Login** di pojok kanan atas.
5. Anda akan melihat form login dengan input berikut:
   - **Email / NIK**: Ketikkan alamat email terdaftar Anda (Contoh: `admin.diklat@rsud.go.id`) atau NIK Anda (16 digit angka tanpa spasi).
   - **Password (Kata Sandi)**: Ketikkan kata sandi Anda. **Penting:** Perhatikan huruf besar/kecil (*case-sensitive*), simbol, dan angka. Pastikan tombol *Caps Lock* pada keyboard Anda tidak aktif secara tidak sengaja.
6. Klik tombol merah **Masuk / Login**. Jika sukses, Anda akan langsung dialihkan ke Dashboard Admin.

### B. Cara Keluar (Logout)
1. Pada halaman kerja mana saja, arahkan kursor mouse Anda ke pojok kanan atas layar.
2. Klik nama profil Anda atau foto Anda.
3. Pada menu drop-down yang muncul, klik tulisan **Logout** atau **Keluar**.
4. Akun Anda berhasil diamankan dan sistem kembali ke layar masuk.

---

## 2. PANDUAN NAVIGASI DASHBOARD UTAMA

Setelah masuk, Anda akan melihat beranda admin (Dashboard). Di sini terdapat widget ringkasan:
- **Total Pelatihan**: Menampilkan angka jumlah program diklat yang terdaftar di database.
- **Total Peserta**: Menampilkan jumlah pegawai/peserta yang telah memiliki akun.
- **Sidebar (Menu Kiri)**: Panel navigasi berwarna gelap di sebelah kiri layar. Jika Anda menggunakan layar tablet atau layar kecil, klik ikon **tiga garis horizontal (hamburger menu)** di pojok kiri atas untuk memunculkan panel ini. 
  - Menu di sidebar meliputi: *Dashboard*, *Data Master*, *Kelola Pelatihan*, *Manajemen Peserta*, *Sertifikat*, dan *Pengaturan Logo*.

---

## 3. MENGELOLA DATA MASTER (DATA DASAR)

Data Master wajib diisi sebelum membuat pelatihan. Anda tidak bisa memilih narasumber atau ruangan di form pelatihan jika datanya belum diinput di sini.

### A. Penyelenggara (Unit/Instansi Pembuat Pelatihan)
1. Di Sidebar kiri, klik **Data Master** -> **Penyelenggara**.
2. Klik tombol **Tambah Penyelenggara** (tombol dengan ikon plus `+` warna biru).
3. **Isian Formulir:**
   - **Nama Penyelenggara**: Ketikkan nama unit. (Contoh: `Bagian Diklat RSUD`, `Kementerian Kesehatan RI`). 
     - *Batasan:* Maksimal 100 karakter. Hanya mendukung format teks alfabet biasa.
4. Klik **Simpan**.

### B. Narasumber (Pemateri)
1. Klik **Data Master** -> **Narasumber**.
2. Klik tombol **Tambah Narasumber**.
3. **Isian Formulir:**
   - **Gelar Depan**: Ketik gelar di awal nama (Contoh: `dr.`, `Prof. Dr.`). Kosongkan jika tidak ada.
   - **Nama Pejabat/Narasumber**: Nama lengkap pemateri tanpa gelar (Contoh: `Budi Santoso`, `Siti Aminah`). *Batasan: Maksimal 150 karakter.*
   - **Gelar Belakang**: Ketik gelar di akhir nama (Contoh: `Sp.A`, `M.Kes`, `Ph.D`). Kosongkan jika tidak ada.
   - **Instansi Asal**: Ketik nama instansi tempat narasumber bekerja (Contoh: `RSUD Provinsi`, `Universitas Airlangga`). *Batasan: Maksimal 100 karakter.*
   - **Profesi**: Pekerjaan narasumber (Contoh: `Dokter Spesialis Anak`, `Dosen Keperawatan`).
4. Klik **Simpan**.

### C. Ruangan (Tempat Pelaksanaan Luring)
1. Klik **Data Master** -> **Ruangan**.
2. Klik tombol **Tambah Ruangan**.
3. **Isian Formulir:**
   - **Nama Ruangan**: (Contoh: `Aula Serbaguna Lt.2`, `Klinik Simulasi A`).
   - **Kapasitas**: Ketik jumlah maksimal orang yang bisa ditampung. **Wajib Angka** (Contoh: `40`). Sistem akan menolak jika Anda mengetik huruf atau simbol.
4. Klik **Simpan**.

### D. Kategori SKP (Satuan Kredit Profesi)
Menentukan ranah penilaian yang akan diakui bagi profesi peserta.
1. Klik **Data Master** -> **Kategori SKP**.
2. Klik tombol **Tambah Kategori**.
3. **Isian Formulir:**
   - **Ranah**: Klik kotak pilihan (dropdown) dan pilih `Pembelajaran` atau `Pengabdian`.
   - **Nama Kategori**: (Contoh: `Pelatihan Klinis`, `Seminar Pengabdian Masyarakat`).
4. Klik **Simpan**.

---

## 4. MEMBUAT PROGRAM PELATIHAN BARU (FORMULIR UTAMA)

Untuk membuat pelatihan, klik menu **Pelatihan** -> **Kelola Pelatihan** di Sidebar. Kemudian klik tombol merah **Tambah Pelatihan Baru**. 

Formulir ini sangat panjang, berikut adalah detail kecil yang wajib diisi:

### A. Informasi Dasar Program
1. **NAMA PELATIHAN (JUDUL)**: Tuliskan nama lengkap acara (Contoh: `Pelatihan Advanced Cardiac Life Support (ACLS)`). *Panjang: Maksimal 255 karakter. Teks bebas.*
2. **TEMA PELATIHAN**: Tulis tema umum (Contoh: `Kardiovaskular dan Kegawatdaruratan`). *Panjang: Maksimal 150 karakter.*
3. **PROGRAM**: Pilih jenis acara dari dropdown: `Webinar`, `Seminar`, `Workshop`, `Bimtek`, atau `Pelatihan`.
4. **KATEGORI ILMU**: Ketik bidang keilmuan (Contoh: `Kesehatan / Keperawatan`, `Teknologi Informasi`).
5. **RANAH PEMENUHAN SKP**: Pilih ranah (Contoh: `Pembelajaran`).
6. **KATEGORI KEGIATAN SKP**: Pilihan kategori yang sesuai dengan ranah SKP yang dipilih sebelumnya.
7. **BOBOT SKP**: Jumlah nilai kredit profesi yang didapatkan (Contoh: `2` atau `1.5`). **Gunakan tanda titik `.` untuk nilai desimal/koma**. Jangan menggunakan koma `,`.
8. **BIAYA**: Pilih `Gratis` atau `Berbayar`.
   - Jika Anda memilih **Berbayar**, maka kolom informasi rekening berikut akan otomatis muncul dan wajib diisi:
     - **BANK**: Ketik nama bank tujuan transfer (Contoh: `Bank Mandiri`, `BCA`).
     - **NO. REKENING**: Nomor rekening bank. Ketik angka tanpa tanda strip (Contoh: `1234567890123`).
     - **A.N REKENING**: Nama pemilik rekening sesuai buku tabungan (Contoh: `Bendahara Diklat RSUD`).
     - **NOMINAL (RP)**: Ketik angka saja tanpa titik/koma (Contoh: `150000` untuk Rp 150.000).
9. **LEVEL**: Pilih tingkat kesulitan materi: `Pemula`, `Menengah`, atau `Lanjut`.
10. **JPL (Jam Pelajaran)**: Masukkan total durasi jam pelajaran dalam angka bulat (Contoh: `30`).
11. **CAKUPAN**: Pilih skala wilayah diklat: `Lokal`, `Nasional`, atau `Internasional`.

### B. Metode & Penyelenggaraan
12. **MEKANISME**:
    - **Terbuka**: Siapa saja peserta yang memiliki akun dapat mendaftar.
    - **Tertutup**: Hanya ditujukan untuk pegawai tertentu. Jika memilih *Tertutup*, kolom target berikut wajib ditentukan:
      - **TARGET KHUSUS (PROFESI)**: Anda bisa memilih lebih dari satu profesi (Contoh: *Dokter*, *Perawat*). Gunakan klik untuk memilih.
      - **TARGET KHUSUS (UNIT KERJA)**: Pilih unit kerja yang ditargetkan (Contoh: *Instalasi Gawat Darurat*, *ICU*).
13. **METODE**: Pilih media penyampaian: `Online` (Daring), `Offline / Classical` (Luring), atau `Blended / Hybrid` (Campuran).
14. **PENYELENGGARA**: Pilih instansi penyelenggara yang telah didaftarkan di Data Master.
15. **KUOTA**: Masukkan jumlah peserta maksimal dalam angka bulat (Contoh: `50`).
16. **Brosur / Banner (Gambar Poster)**: Klik tombol pilih file untuk mengunggah gambar.
    - *Syarat File:* **Wajib berformat .JPG, .JPEG, atau .PNG**. Ukuran maksimal file adalah **2 Megabyte (2 MB)**. Gambar dengan ukuran lebih besar dari 2MB akan ditolak oleh server secara otomatis.

Setelah semua kolom terisi, klik **Simpan & Lanjutkan**. Pelatihan Anda sukses dibuat dengan status **Draft** (tidak terlihat oleh peserta umum).

---

## 5. MANAJEMEN KONTEN PELATIHAN (KELOLA DETAIL)

Setelah membuat Pelatihan Utama, Anda akan masuk ke halaman **Manajemen Konten**. Di sini terdapat 4 tab utama di sebelah kiri layar: **Sesi Pelatihan**, **Materi**, **Kuis Mandiri**, dan **Kuesioner**.

Untuk mengelola pelatihan yang sudah ada, masuk ke menu **Daftar Pelatihan**, lalu cari pelatihan Anda dan klik tombol berikon **Roda Gigi (Gear) / Tindakan Kelola**.

---

### A. Mengatur Sesi Pelatihan (Jadwal Kelas)
Sesi adalah jadwal hari per hari atau topik per topik dari pelatihan Anda.

#### 1. Menambah Sesi Baru
1. Pada tab **Sesi Pelatihan**, klik tombol **Tambah Sesi** di pojok kanan atas.
2. Lengkapi form berikut:
   - **Tipe Sesi**: Pilih `Online Meeting` atau `Tatap Muka (Offline)`.
   - **Nama/Topik Sesi**: Tuliskan nama topik (Contoh: `Hari ke-1: Resusitasi Jantung Paru`).
   - **Narasumber**: Klik kotak pilihan. Anda dapat memilih lebih dari satu narasumber untuk sesi ini dengan mencentang namanya.
   - **Penyelenggara**: Pilih divisi penyelenggara terkait sesi tersebut.
   - **Tanggal Sesi**: Klik ikon kalender dan pilih tanggalnya.
   - **Jam Mulai & Jam Tutup**: Ketik jam dengan format 24 jam (Contoh: Mulai `08:00`, Tutup `12:00`).
   - **Detail Pelaksanaan**:
     - Jika Tipe Sesi adalah **Online**: Masukkan **Link Zoom / Google Meet** (Wajib diawali `https://` contoh: `https://zoom.us/j/987654321`) dan **Meeting Passcode** (Jika ada).
     - Jika Tipe Sesi adalah **Offline**: Pilih **Ruangan** dari Data Master, ketik nama **Tempat/Gedung** (Contoh: `Gedung Diklat Lt. 3`), ketik **Alamat Lengkap**, dan masukkan **Link Google Maps (Maps URL)** (Contoh: `https://maps.app.goo.gl/...` agar peserta bisa menavigasi lewat HP).
3. Klik tombol **Simpan Sesi**.

#### 2. Tombol Aksi Sesi
Pada setiap baris sesi yang telah dibuat, terdapat tombol tindakan:
- **Tombol Presensi (Warna Hijau)**: Hanya muncul pada sesi *Offline*. Klik tombol ini untuk membuka halaman pencatatan kehadiran absensi peserta (Scan barcode atau centang manual).
- **Tombol Edit (Ikon Pensil Biru)**: Untuk mengubah nama sesi, jam, atau ruangan jika terjadi perubahan jadwal.
- **Tombol Hapus (Ikon Tong Sampah Merah)**: Untuk menghapus sesi. *Peringatan: Menghapus sesi juga akan menghapus data kehadiran (absensi) di sesi tersebut.*

---

### B. Mengunggah Materi Pembelajaran (Multi-Format)
Materi adalah modul belajar mandiri yang dibaca/ditonton oleh peserta.

#### 1. Menambah Materi
1. Buka tab **Materi**, klik tombol **Tambah Materi Pembelajaran** (ikon plus).
2. Lengkapi formulir:
   - **Judul Materi**: Judul modul (Contoh: `Modul 1: Teknik Kompresi Dada`).
   - **Segmen ke-**: Tuliskan nomor urut materi dalam angka (Contoh: `1`). Peserta harus membuka materi sesuai urutan segmen.
   - **Terkait Sesi (Opsional)**: Hubungkan materi ini dengan Sesi tertentu yang sudah Anda buat di tab sebelumnya.
   - **Tipe Materi & Ketentuan File**: 
     Pilih salah satu tipe dari pilihan dropdown berikut. Setiap tipe memiliki syarat format file yang berbeda:
     - **Video Pembelajaran**: File video penjelasan materi. 
       *Format:* `.mp4, .webm, .ogg, .mov, .avi, .mkv, .wmv`.
     - **Foto / Gambar**: Gambar anatomi atau infografis. 
       *Format:* `.jpg, .jpeg, .png, .webp`.
     - **Dokumen PDF**: Buku modul atau slide presentasi. 
       *Format:* `.pdf` (sangat direkomendasikan).
     - **Dokumen (Word)**: Dokumen teks. 
       *Format:* `.doc, .docx`.
     - **Dokumen (Excel)**: Tabel data atau *spreadsheet*. 
       *Format:* `.xls, .xlsx`.
     - **Rekaman Suara (Audio)**: Podcast atau penjelasan suara. 
       *Format:* `.mp3, .wav, .m4a`.
     - **Link Eksternal**: Tautan ke situs luar (Contoh: Jurnal Kemenkes).
       *Syarat:* Masukkan URL utuh diawali `https://`.
     - **Lainnya**: Untuk file dengan format lain di luar daftar di atas.
   - **Deskripsi Singkat**: Tulis penjelasan ringkas apa isi dari materi ini (Maksimal 250 karakter).
   - **File / Link Materi**: 
     - Jika berupa file, klik tombol **Browse** untuk mencari file di komputer Anda. 
     - Jika berupa *Link Eksternal*, ketikkan link situs web di kotak input link.
3. Klik tombol **Simpan Materi**.

#### 2. Tombol Aksi Materi
- **Tombol Edit (Ikon Pensil)**: Untuk mengganti file materi dengan versi yang lebih baru atau memperbaiki typo judul.
- **Tombol Hapus (Ikon Tong Sampah)**: Untuk menghapus materi dari sistem.

---

### C. Mengelola Kuis Mandiri (Pre-Test & Post-Test + Lampiran Media)
Kuis terdiri dari **Pre-Test** (Ujian Awal, sebelum membaca materi) dan **Post-Test** (Ujian Akhir untuk kelulusan).

#### 1. Cara Mengakses Pengaturan Kuis
1. Buka tab **Kuis Mandiri**.
2. Anda akan melihat dua kotak informasi: Pre-Test dan Post-Test.
3. Klik tombol **Kelola Soal & KKM** pada salah satu tes yang ingin diatur. Akan muncul jendela pop-up (Modal) baru.

#### 2. Mengatur Batas Kelulusan (KKM)
1. Di bagian atas jendela kelola kuis, cari kolom **Nilai Ambang Kelulusan (KKM)**.
2. Ketik nilai batas lulus dalam angka (Contoh: `80` dari skala 100).
3. Klik tombol **Simpan KKM**.

#### 3. Membuat Soal Ujian & Mengunggah Lampiran Soal
1. Klik tombol biru **Tambah Soal** di pojok kanan atas jendela modal. Form pertanyaan baru akan muncul di bawah.
2. **Isian Form Soal:**
   - **Terkait Materi (Opsional)**: Klik dropdown dan hubungkan soal ini dengan materi pembelajaran tertentu. Hal ini berguna bagi peserta untuk meninjau kembali materi jika jawaban mereka salah.
   - **Upload Lampiran Soal (Opsional)**: 
     Anda dapat menyertakan media pendukung untuk soal tersebut (Misalnya: Gambar grafik jantung, foto rontgen pasien, dokumen kasus PDF, atau video demonstrasi tindakan).
     - *Syarat File:* Mendukung format gambar (`.jpg, .jpeg, .png, .webp`), dokumen (`.pdf`), dan video (`.mp4, .webm, .ogg`). Ukuran file maksimal **5MB**.
   - **Pertanyaan**: Ketikkan soal pertanyaan secara lengkap pada kotak teks besar (Contoh: *Manakah gambar EKG yang menunjukkan Ventricular Fibrillation di bawah ini?*).
   - **Opsi A, B, C, D**: Masukkan teks pilihan jawaban pada masing-masing kotak input secara detail.
   - **Kunci Jawaban**: Klik tombol bulatan (Radio Button) pada pilihan huruf A, B, C, atau D yang menjadi jawaban yang benar.
3. **Penyimpanan Soal:**
   - Anda bisa langsung menyimpan soal tersebut secara instan dengan mengklik tulisan hijau **Simpan** di pojok kanan atas form soal tersebut.
   - Atau klik **Simpan Semua Soal** di bagian pojok kanan bawah modal jika Anda membuat banyak soal sekaligus.
- **Tombol Tambah Setelah**: Klik tombol plus `+ Tambah` untuk menyelipkan soal baru persis di bawah nomor soal tersebut.
- **Tombol Hapus (Trash Ikon)**: Klik untuk menghapus soal.

---

### D. Mengelola Kuesioner Kepuasan (Evaluasi Auto-Generated & Custom)
Evaluasi ini diisi peserta untuk memberikan umpan balik pelayanan diklat.

#### 1. Cara Mengaktifkan Template Otomatis (Auto-Generated)
Sistem memiliki fitur cerdas pembuat template otomatis.
1. Klik tab **Kuesioner** -> klik tombol **Kelola Kuesioner**.
2. Jika kuesioner masih kosong, sistem akan memunculkan notifikasi peringatan berwarna kuning bahwa Kategori Evaluasi Wajib (Materi, Narasumber, Penyelenggara) harus ada.
3. Klik tombol **Muat Template Standar (Generate)**.
4. Sistem secara otomatis membuat pertanyaan rating 1-5 bintang untuk mengevaluasi *Materi*, *Narasumber*, dan *Penyelenggara*. Anda tidak perlu mengetik apa pun secara manual jika ingin menggunakan standar rumah sakit.

#### 2. Membuat Pertanyaan Custom (Tambahan Sendiri)
Jika ingin menambahkan kriteria penilaian khusus:
1. Pada form tambah kuesioner:
   - **Kategori**: Pilih kategori yang ada, atau pilih `+ Buat Kategori Baru` (Ketik nama kategori baru di kolom bawahnya, contoh: `Konsumsi & Fasilitas`).
   - **Pertanyaan**: Tulis pertanyaan evaluasi (Contoh: *Bagaimana kualitas konsumsi makan siang yang disediakan panitia?*).
2. Klik **Simpan Pertanyaan**. Peserta akan memberikan rating skala 1 (Sangat Kurang) hingga 5 (Sangat Baik) pada aplikasi mereka.
- Anda dapat mengklik teks pertanyaan pada daftar untuk mengeditnya secara langsung di tempat, atau mengklik ikon tong sampah merah untuk menghapus pertanyaan tersebut.
- Klik **Pratinjau Kuesioner** untuk melihat contoh tampilan evaluasi di layar HP peserta.

---

## 6. MANAJEMEN PESERTA DAN ABSENSI

Setelah pelatihan berstatus **Aktif (Published)**, kelola jalannya kelas di menu berikut:

### A. Verifikasi Pendaftaran & Bukti Transfer
1. Buka menu **Manajemen Peserta** -> **Verifikasi Pendaftaran**.
2. Klik tombol **Lihat Bukti Bayar** pada peserta yang mendaftar di pelatihan berbayar.
3. Periksa gambar transfer bank. Jika valid, klik tombol hijau **Terima (Approve)**. Jika bukti palsu, klik tombol merah **Tolak (Reject)** dan masukkan alasan penolakannya secara jelas.

### B. Absensi Kelas Luring (Offline)
1. Buka menu **Manajemen Peserta** -> **Presensi** (atau dari tab Sesi Pelatihan klik tombol Presensi pada sesi luring).
2. Anda akan melihat daftar nama peserta kelas tersebut.
3. Untuk mencatat kehadiran:
   - Centang kotak kehadiran secara manual jika peserta hadir secara fisik.
   - Atau gunakan scanner barcode/QR Code (bila tersedia alatnya) untuk melakukan absensi instan saat peserta masuk ruangan.
4. Klik **Simpan Kehadiran**.

---

## 7. PANDUAN SERTIFIKAT DAN PEJABAT PENANDATANGAN

Bagian ini harus diisi dengan sangat teliti agar sertifikat digital yang diterbitkan resmi, sah, dan rapi secara estetika.

### A. Mendaftarkan Pejabat Penandatangan
1. Di Sidebar kiri, buka menu **Sertifikat** -> **Kelola Pejabat TTD**.
2. Klik tombol **Tambah Pejabat**.
3. **Isian Formulir:**
   - **Nama Pejabat**: Nama lengkap beserta gelar depan dan belakang (Contoh: `dr. Agus Setiawan, MARS`). *Batasan: Maksimal 150 karakter.*
   - **Jabatan**: Jabatan resmi (Contoh: `Direktur RSUD Kelas A`).
   - **NIP**: Nomor Induk Pegawai. **Wajib Angka Tanpa Spasi** (Contoh: `198505122010011002`). Jangan masukkan tanda baca.
   - **Tanda Tangan Digital (TTD)**: File scan tanda tangan basah pejabat.
     - **PENTING / SYARAT MUTLAK:** File gambar **WAJIB berformat .PNG Transparan** (tidak memiliki latar belakang putih). Jika Anda mengunggah file JPG dengan latar putih, gambar tanda tangan akan menimpa bingkai sertifikat dan terlihat tidak profesional. Anda bisa meminta bantuan tim IT untuk menghapus background foto tanda tangan menjadi format PNG transparan.
4. Klik **Simpan**.

### B. Mengatur Desain & Posisi Tulisan Sertifikat
Setiap program pelatihan dapat memiliki desain bingkai sertifikat sendiri.
1. Masuk ke halaman **Kelola Pelatihan** -> pilih pelatihan Anda -> klik tab **Pengaturan Sertifikat** (atau dari menu Sertifikat pilih nama pelatihan).
2. **Pengaturan Layout:**
   - **Desain Gambar Background**: Klik pilih file. Unggah gambar desain kosong sertifikat (Format: **JPG/PNG**, resolusi tinggi, ukuran kertas **A4 posisi Landscape / Tidur**).
   - **Nomor Sertifikat**: Ketik pola penomoran surat keluar (Contoh: `/DIKLAT-RSUD/IV/2026`). Sistem akan otomatis menyisipkan nomor urut unik peserta di depan kode ini secara dinamis.
   - **Pejabat Penandatangan 1**: Pilih nama pejabat utama (Kiri) dari dropdown.
   - **Pejabat Penandatangan 2**: Pilih nama pejabat pendamping (Kanan) dari dropdown. Kosongkan jika tanda tangan hanya satu orang.
3. **Mengatur Posisi Tulisan (Koordinat X dan Y):**
   Pada form terdapat pengaturan angka X (Horisontal/Kiri-Kanan) and Y (Vertikal/Atas-Bawah) untuk Nama Peserta, Nomor Sertifikat, dan Gambar TTD.
   - Nilai X menentukan posisi dari kiri layar (Semakin besar angka, semakin ke kanan).
   - Nilai Y menentukan posisi dari atas layar (Semakin besar angka, semakin ke bawah).
4. Klik tombol **Preview / Pratinjau Sertifikat**. Gambar sertifikat simulasi akan muncul di layar.
   - Periksa posisi nama peserta: Apakah terlalu tinggi? Naikkan angka Y untuk menurunkannya. Apakah terlalu ke kiri? Naikkan angka X untuk menggesernya ke kanan.
   - Lakukan penyesuaian angka sedikit demi sedikit, klik Preview lagi, sampai posisi tulisan nama, nomor, dan tanda tangan berada pas di tengah garis kosong sertifikat Anda.
5. Klik **Simpan Pengaturan Sertifikat**.

### C. Menerbitkan Sertifikat Resmi ke Peserta
Sertifikat tidak akan otomatis terbit demi menghindari kesalahan data. Admin harus merilisnya secara manual setelah kelas selesai.
1. Buka menu **Sertifikat** -> **Penerbitan Sertifikat**.
2. Pilih program pelatihan Anda yang sudah berakhir.
3. Layar akan memuat daftar peserta yang berstatus **Lulus** (Telah menyelesaikan semua sesi, materi, evaluasi, dan nilai Post-Test di atas KKM).
4. Centang kotak kecil di samping nama-nama peserta yang ingin diterbitkan sertifikatnya (Atau klik kotak centang paling atas di tabel untuk memilih semua nama sekaligus).
5. Klik tombol biru **Terbitkan Sertifikat (Generate)**.
6. **PERINGATAN KERAS:** Jendela browser/layar **JANGAN DITUTUP atau DI-REFRESH** selama proses loading berlangsung. Sistem sedang bekerja membuat dokumen PDF terenkripsi dan menempelkan nama serta tanda tangan pejabat satu per satu ke setiap peserta. Menutup halaman di tengah proses akan menyebabkan kerusakan file sertifikat.
7. Tunggu hingga pop-up hijau bertuliskan **Sukses** muncul. Sertifikat digital kini resmi tayang di menu "Sertifikat Saya" pada akun masing-masing peserta dan dapat mereka unduh sendiri dalam format PDF berkualitas tinggi.

### D. Memvalidasi Sertifikat Eksternal (Upload Mandiri Peserta)
Peserta kadang mengikuti pelatihan di luar RSUD dan mengunggahnya ke sistem agar JPL tahunan mereka tercatat. Tugas Anda adalah memvalidasinya:
1. Buka menu **Sertifikat** -> **Validasi Sertifikat Eksternal**.
2. Anda akan melihat tabel pengajuan dokumen berstatus *Pending*.
3. Klik tombol **Lihat Dokumen** untuk memeriksa file PDF/Gambar sertifikat luar tersebut.
4. Periksa kecocokan data fisik dengan form input: Apakah nama peserta sama? Apakah nama acaranya cocok? Apakah jumlah angka JPL yang diinput peserta sesuai dengan yang tertulis di sertifikat asli?
5. Jika data benar dan valid, klik tombol hijau **Approve (Setujui)**. JPL peserta akan otomatis bertambah secara akumulatif.
6. Jika data salah isi atau file tidak terbaca/palsu, klik tombol merah **Reject (Tolak)**, lalu ketik alasan penolakannya pada kolom pesan (Contoh: *"Foto sertifikat buram tidak terbaca, mohon unggah ulang file PDF yang jelas"*). Peserta akan menerima notifikasi penolakan dan dapat mengunggah ulang dokumen yang benar.

---

### RINGKASAN PENGGUNAAN & KENDALA
- **Mengapa Tombol Tidak Bisa Diklik (Warna Pudar)?** Sistem melarang pengubahan data tertentu demi integritas database. Misalnya, KKM kuis atau daftar sesi tidak boleh diubah jika pelatihan tersebut sudah berjalan dan memiliki peserta aktif yang sedang belajar di dalamnya.
- **Layar Putih atau Loading Lama Sekali**: Terjadi karena jaringan internet Anda terputus sejenak atau server sedang memproses dokumen besar. Tekan tombol **F5** pada keyboard komputer untuk melakukan penyegaran halaman (*Refresh*).

*Selamat bertugas! Panduan lengkap ini dirancang untuk memudahkan pekerjaan administrasi diklat Anda. Jika muncul kotak eror berwarna merah dengan tulisan bahasa inggris teknis yang tidak Anda pahami, segera ambil foto layar (screenshot) dan laporkan kepada Tim IT untuk penanganan cepat.*
