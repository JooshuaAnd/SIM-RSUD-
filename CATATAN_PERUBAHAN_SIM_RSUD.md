# Catatan Perubahan SIM-RSUD

**Total perubahan yang sudah dilakukan dan masih tercatat: 74 poin.**

Hitungan berdasarkan isi catatan saat diperbarui: **73 poin** pada bagian 1–10 dan **1 perubahan label tabel** yang terbukti di Git pada bagian 11. Setiap poin dihitung sebagai satu perubahan, meskipun mencakup beberapa file atau tindakan. Lima catatan lainnya pada bagian 11 merupakan pekerjaan yang ditunda atau belum tuntas sehingga tidak masuk total.

## 1. Super Admin — 12 September 2026


- **Memperbaiki ganti password Super Admin:** menambahkan endpoint `POST /superadmin/update_password` dengan filter role Super Admin. Sebelumnya form mengarah ke endpoint Admin Diklat sehingga akses ditolak.
- **Menambahkan validasi password:** password lama harus benar, password baru minimal enam karakter, konfirmasi harus cocok, dan password baru disimpan dalam bentuk hash.
- **Menyesuaikan form password pada layout bersama:** action form mengikuti role pengguna, sehingga Super Admin dan Admin Diklat memakai endpoint masing-masing.
- **Memperketat form pembuatan admin:** NIK admin Pelatihan/Pengabdian harus tepat 16 digit angka; nama admin Riset/Pelatihan/Pengabdian hanya menerima huruf dan spasi. Pembatasan diterapkan pada input dan server.
- **Menyembunyikan menu Dashboard khusus Super Admin:** mencegah pengguna diarahkan ke dashboard Admin Diklat yang menolak role `superadmin`. Menu untuk Admin Diklat tetap tersedia.

## 2. Registrasi Institusi Pendidikan — 14–15 September 2026

- **Membatasi nomor telepon:** telepon kantor dan HP/WhatsApp penanggung jawab hanya menerima angka, dengan pemeriksaan ulang di server.
- **Memvalidasi nama penanggung jawab:** menerima huruf dan tanda yang diperlukan untuk nama/gelar, seperti spasi, titik, koma, apostrof, dan tanda hubung; menolak angka dan simbol tidak wajar.
- **Memvalidasi email institusi dan penanggung jawab:** menambahkan pemeriksaan format email di backend agar tidak hanya bergantung pada input browser.
- **Memvalidasi periode MoU:** menolak tanggal selesai yang lebih awal dari tanggal mulai. Pada tindak lanjut 15 September, tanggal mulai dan selesai juga dibuat wajib pada form dan server.
- **Mewajibkan dokumen registrasi:** MoU/PKS dan Surat Permohonan wajib diunggah; dokumen tambahan tetap opsional. Pemilih file dan backend membatasi dokumen tersebut ke PDF.
- **Memperbaiki konfirmasi password yang menghapus isian:** pada pemeriksaan di browser, ketidakcocokan password hanya mengosongkan kedua field password dan mempertahankan isian serta file lain. Fallback server menggunakan `withInput()` untuk field teks.
- **Menghapus autofill testing dari registrasi:** form tidak lagi mengisikan gambar testing sebagai pengganti dokumen asli.
- **Memperbaiki data penanggung jawab yang tidak tampil:** dashboard mengambil jenis institusi, jabatan, HP, dan email penanggung jawab dari data tersimpan, menggantikan nilai placeholder pada kartu Data Registrasi Institusi.

## 3. Dashboard, Profil, Dokumen, dan Revisi Institusi — 15 September 2026


- **Menghubungkan form perbaikan data ke penyimpanan nyata:** `update_profile()` yang sebelumnya hanya menampilkan pesan sukses kini memperbarui data institusi, data penanggung jawab, email akun, dan dokumen yang diunggah.
- **Memperbaiki form unggahan revisi:** menggunakan pengiriman multipart serta pemeriksaan nama, email, telepon, dan PDF sebelum data diproses.
- **Menyamakan lokasi dokumen institusi:** registrasi, revisi, dan profil menggunakan `writable/uploads/dokumen_institusi`; pembacaan tetap mendukung lokasi lama `public/uploads/institusi`.
- **Menambahkan akses dokumen untuk institusi yang login:** rute dokumen membaca MoU, Surat Permohonan, dan dokumen tambahan milik institusi dari sesi, sehingga file registrasi dapat dibuka tanpa harus diunggah ulang melalui Profil.
- **Menampilkan dokumen registrasi pada dashboard dan profil:** menambahkan tautan dokumen yang sebelumnya tidak muncul atau mengarah ke lokasi yang berbeda.
- **Menambahkan penanda waktu revisi:** membuat migration dan memasukkan `revisi_dikirim_at` ke model institusi. Log membuktikan migration dijalankan pada **15 September 2026**, meskipun nama file migration memakai tanggal `2026-10-02`.
- **Menyelesaikan alur revisi sesuai koreksi pengguna:** setelah institusi mengirim revisi, data tampil di **Revisi** dan **Inbox** dengan penanda **Revisi masuk**. Setelah admin memilih **Tandai Revisi Ditinjau**, data hilang dari Revisi dan tetap di Inbox untuk keputusan Setujui, Minta Revisi, atau Tolak.
- **Menambahkan revisi ke kartu Perlu Tindakan:** dashboard admin menampilkan institusi yang sudah mengirim revisi beserta labelnya, dengan urutan berdasarkan waktu pengiriman revisi.
- **Memperjelas pesan setelah revisi:** mengganti pesan sukses institusi menjadi pemberitahuan bahwa revisi telah dilakukan dan pengguna perlu menunggu persetujuan admin.

## 4. Validasi Server Modul Pendidikan — 15 September 2026


- **Menambahkan validasi pengajuan baru dan edit:** memeriksa program/program studi, profesi, periode, penanggung jawab, daftar mahasiswa, dan kelengkapan input sebelum penyimpanan.
- **Menambahkan aturan identitas mahasiswa:** nama dengan karakter terbatas, NIM 3–30 karakter dengan karakter yang diizinkan, nomor HP numerik, email valid, semester 1–20, jenis kelamin `L`/`P`, dan tanggal lahir valid sebelum hari ini. Tidak ditambahkan batas tahun lahir 2006.
- **Menambahkan pembatasan unggahan pengajuan dan mahasiswa:** foto JPG/PNG; ijazah, surat aktif, dokumen utama, logbook, dan tugas PDF; daftar mahasiswa PDF/XLS/XLSX; bukti pembayaran PDF/JPG/PNG. Unggahan pada alur tersebut dibatasi maksimal 2 MB dengan pemeriksaan MIME dan ukuran di server.
- **Menambahkan validasi edit mahasiswa oleh institusi:** memeriksa identitas serta file foto/ijazah/SK yang diganti, bukan hanya mengandalkan atribut pada form.
- **Menambahkan aturan data admin:** memperketat nama CI, format email CI, nama stase, NIP/telepon, nominal invoice nonnegatif, serta ukuran PDF invoice. Penambahan pemeriksaan identitas/status mahasiswa juga tercatat, tetapi audit berikutnya masih menemukan kekurangan pada endpoint pembaruan mahasiswa; lihat bagian keterbatasan.
- **Membatasi nilai akhir dan nilai tugas:** menambahkan rentang nilai 0–100 di server. Format koma pada nilai akhir kemudian ditangani lebih lanjut pada 5 Oktober.
- **Memvalidasi tugas CI:** membatasi panjang nama tugas dan memeriksa format deadline sebelum pembuatan tugas.
- **Membatasi status logbook:** tindakan validasi CI hanya menerima `Disetujui`, `Revisi`, atau `Ditolak`. Penempatan pemeriksaan ini sempat salah dan kemudian diperbaiki pada 5 Oktober; koreksinya dicatat di bagian CI.

## 5. Form Pengajuan dan Detail Institusi — 28 dan 30 September 2026


- **Merombak input mahasiswa menjadi kartu responsif:** form Ajukan Mahasiswa tidak lagi menumpuk field secara horizontal; informasi dan dokumen mahasiswa dikelompokkan lebih jelas.
- **Mengganti breadcrumb Ajukan Mahasiswa:** tulisan “Dashboard / Ajukan Mahasiswa” diganti dengan penjelasan singkat fungsi menu.
- **Menambahkan filter dan pemeriksaan file pada browser:** format yang dapat dipilih sesuai ketentuan masing-masing input; file tidak sesuai atau lebih dari 2 MB ditolak dengan pesan pada form.
- **Memperbaiki data/file yang hilang saat validasi gagal:** form dikirim melalui AJAX; backend mengembalikan field dan pesan kesalahan dengan HTTP 422. Form tetap terbuka, field bermasalah ditandai, lalu layar bergeser dan fokus ke input tersebut. Halaman Status Pengajuan dibuka setelah berhasil.
- **Menambahkan konfirmasi pengiriman SweetAlert2:** pengguna dapat memilih mengirim pengajuan atau memeriksa lagi; membatalkan konfirmasi mempertahankan isian. Penyebutan penerima dalam pesan dikoreksi menjadi **Admin**.
- **Membatasi input nama saat mengetik dan paste:** angka dicegah atau dibuang pada Nama Mahasiswa dan Nama Penanggung Jawab, termasuk baris mahasiswa yang ditambahkan secara dinamis.
- **Merombak Detail Pengajuan:** breadcrumb diganti tombol Kembali yang mengikuti gaya halaman Edit; informasi institusi/pengajuan dirapikan dan daftar mahasiswa ditampilkan sebagai kartu responsif dengan tautan dokumen.
- **Menyamakan Edit Pengajuan dengan form baru:** menggunakan susunan kartu, kelompok dokumen, aturan input/file, dan konfirmasi yang seragam. Data serta file pilihan tetap dipertahankan saat validasi AJAX gagal.
- **Menyesuaikan penamaan upload dinamis:** indeks field foto/ijazah/SK mahasiswa dirapikan setelah baris dihapus agar sesuai dengan pemrosesan backend.
- **Menangani respons edit dan sesi berakhir:** update menggunakan validasi pengajuan untuk mode edit, respons JSON sukses/gagal yang sesuai, dan arahan login ketika sesi tidak berlaku.

## 6. Tampilan Sidebar dan Popup — 14 September–5 Oktober 2026

- **Menyeragamkan notifikasi Admin Diklat dengan SweetAlert2:** mencakup institusi, pengajuan, CI, stase, mahasiswa, upload invoice, dan mapping ruangan. Helper bersama menangani konfirmasi, sukses, gagal, dan reload sesudah notifikasi selesai.
- **Memperbaiki bug hapus CI yang dilaporkan gagal:** respons API ditambah `success: true` dan frontend menerima kontrak hasil yang sesuai. Modal aksi ditutup, notifikasi ditampilkan, kemudian halaman di-refresh setelah notifikasi ditutup. Pola ini juga diterapkan pada aksi admin terkait.
- **Menyeragamkan konfirmasi penghapusan Pendidikan:** mengganti modal Bootstrap/native `confirm()` pada hapus CI/stase, hapus pengguna/mahasiswa, pelepasan CI/mahasiswa dari stase, serta penghapusan baris mahasiswa pada form pengajuan. View lama `diklatAdmin/stase_module.php` ikut disesuaikan.
- **Mengganti seluruh 10 native alert di sisi Institusi:** delapan alert pada daftar mahasiswa dan dua alert autofill testing di Ajukan Mahasiswa menjadi SweetAlert2. Isi pesan, alur clipboard/edit/invoice, dan urutan reload dipertahankan dalam perubahan tampilan ini.
- **Memisahkan hover dan menu aktif sidebar:** diterapkan pada Institusi, Admin Diklat, dan Mahasiswa. Hasil akhir hover menggunakan latar `#fff0f0`, teks/ikon `#343a40`, tanpa garis kiri; menu aktif menggunakan latar `#fde2e2`, teks/ikon merah, dan garis kiri merah. Ini menggabungkan pemisahan awal dan permintaan penambahan kontras berikutnya.
- **Memperbaiki penulisan Penilaian Stase pada dashboard mahasiswa:** tanda Markdown yang tampil sebagai teks pada pesan akses terkunci dan pembayaran terverifikasi diganti dengan `<strong>` agar benar-benar tebal.

Catatan cakupan: penyeragaman yang terbukti mencakup konfirmasi penghapusan, popup Admin Diklat, dan native alert Institusi. Arsip tidak membuktikan seluruh popup CI/Mahasiswa sudah diseragamkan; audit 15 September masih menemukan native alert di area tersebut.

## 7. Pembayaran dan Bukti Bayar — 5–6 Oktober 2026


- **Mengimplementasikan Unduh Bukti yang sebelumnya simulasi:** tombol kini mengunduh file bukti pembayaran asli milik mahasiswa, menggantikan popup “Frontend Only”.
- **Membatasi akses bukti pembayaran:** tombol tersedia untuk pembayaran `Lunas`; backend memeriksa sesi/kepemilikan institusi, mahasiswa, status pembayaran, nama file, dan keberadaan file sebelum mengirim unduhan.
- **Memperbaiki penolakan verifikasi pembayaran:** backend kini menerima `Ditolak`, memvalidasi dan menyimpan alasan penolakan, sehingga tombol Tolak tidak lagi berhenti dengan pesan “Status tidak valid”.
- **Memungkinkan revisi bukti yang ditolak:** alasan tersedia bagi institusi; unggahan pengganti melalui Bayar mengembalikan status ke `Menunggu Verifikasi`. Persetujuan berikutnya menghapus alasan penolakan lama.
- **Mencegah perubahan invoice membatalkan pembayaran lunas:** invoice dan nominal pembayaran `Lunas` dikunci di server dan disesuaikan di tampilan agar tidak kembali menjadi `Belum Bayar`.
- **Memvalidasi prasyarat pelunasan di endpoint:** permintaan `Lunas` ditolak bila invoice atau bukti pembayaran kosong, tidak dapat dibaca, atau tidak ditemukan pada direktori yang diizinkan. Request gagal tidak mengubah data pembayaran sebelumnya; ini berbeda dari admin menolak bukti dengan status `Ditolak`.

## 8. Nilai dan Profil Mahasiswa — 5 Oktober 2026


- **Mengimplementasikan Download Semua Nilai (PDF):** tombol yang sebelumnya tanpa handler dihubungkan ke route, `NilaiController`, dan template PDF baru.
- **Menyusun isi rekap nilai:** PDF memuat identitas mahasiswa, stase/ruangan, pembimbing, status/nilai tugas, serta nilai akhir institusi; nilai nol dan nilai yang belum tersedia ditampilkan dengan benar.
- **Membatasi rekap pada mahasiswa yang login:** pemeriksaan sesi dan pembayaran `Lunas` dilakukan di server; query nilai dibatasi pada mahasiswa, stase, dan ruangan yang bersangkutan.
- **Memperbaiki layout PDF:** template diatur agar tabel dan bagian stase tetap terbaca pada laporan beberapa halaman; sampel satu dan lima halaman dirender dan diperiksa dalam sesi implementasi.
- **Membatasi format input Nilai Akhir Institusi:** hanya angka dan satu koma desimal, misalnya `90` atau `90,5`; huruf, titik, minus, dan simbol lain disaring saat ketik/paste. Backend memvalidasi serta menormalkan format tersebut dengan rentang tetap 0–100.
- **Merapikan Profil & Dokumen mahasiswa:** menghapus tombol Kembali di bawah tabel dokumen dan memindahkan Simpan Perubahan ke kanan bawah di dalam kartu Dokumen Wajib Mahasiswa tanpa mengubah fungsi submit.

## 9. CI, Integritas Stase, dan Pengamanan Admin — 5 Oktober 2026


- **Memvalidasi tambah/edit CI:** NIP tepat 18 digit angka dan nomor telepon hanya angka. Aturan tambah dan edit disamakan; nomor telepon tetap opsional jika kosong.
- **Memvalidasi tanggal tambah/edit stase:** tanggal akhir tidak boleh sebelum tanggal mulai; tanggal yang sama tetap diterima. Form memasang batas tanggal dan menampilkan pesan khusus, dengan pemeriksaan backend.
- **Memperbaiki error `$status` pada validasi logbook CI:** pemeriksaan yang terletak di luar fungsi/class dipindahkan ke dalam `validateLogbook()` setelah status dibaca. Controller dan route dapat dimuat kembali tanpa error variabel tersebut.
- **Melindungi penghapusan CI:** CI yang masih terhubung ke stase, mapping, atau riwayat tugas akademik tidak dapat dihapus. CI yang aman dihapus beserta akun login terkait; akun yang masih dipakai profil lain dilindungi.
- **Melindungi penghapusan stase:** pemeriksaan mencakup penempatan lama, mapping ruangan–CI, tugas, dan riwayat logbook, sehingga penghapusan tidak menghilangkan data akademik yang masih terkait.
- **Memperketat penyimpanan penempatan:** server memeriksa profesi stase/CI/mahasiswa, status mahasiswa yang layak ditempatkan, ruangan anggota stase, unit kerja CI, periode lengkap dan valid, serta bentrok jadwal CI/mahasiswa dengan stase lain.
- **Menjaga mapping saat request gagal:** seluruh baris mapping diperiksa sebelum mengganti data lama; penulisan menggunakan transaksi, rollback, pemeriksaan hasil operasi, dan penguncian pada jalur database terkait. Pemeriksaan juga mencakup jalur penempatan lama.
- **Mengamankan output halaman Admin Diklat:** menambahkan escaping/encoding pada teks pengguna, data yang masuk ke JavaScript, dan pesan flash; alasan penolakan tidak lagi dimasukkan sebagai HTML mentah.
- **Menambahkan CSRF khusus Admin Pendidikan:** membuat filter terpisah, memasang token pada form dan AJAX, serta respons penolakan yang sesuai. Konfigurasi ini dibatasi pada area admin.
- **Mengubah hapus CI menjadi POST:** pada 14 September metode GET sempat dipertahankan sesuai instruksi saat itu; perubahan menjadi POST dilakukan dalam perbaikan keamanan yang disetujui pada 5 Oktober.

## 10. Dokumen Penilaian dan Dokumen Tambahan — 6 Oktober 2026


- **Memperbaiki nama dokumen penilaian yang tidak tersimpan:** menambahkan `file_dokumen_penilaian` ke `allowedFields` model pengajuan agar penyimpanan tidak dibuang oleh model.
- **Meneruskan dokumen penilaian ke tampilan institusi:** controller menyertakan file dalam data edit/detail; tautan file tersedia melalui mekanisme dokumen form edit dan bagian dokumen detail.
- **Menampilkan dokumen penilaian di admin:** menambahkannya pada detail pengajuan dan daftar dokumen terkait institusi.
- **Menampilkan dokumen tambahan institusi:** `file_lainnya` dimasukkan ke Admin Pendidikan → Manajemen Institusi → detail institusi → tab Dokumen, dengan tombol Lihat dan Unduh.
- **Memperbaiki pemetaan jenis dokumen admin:** jenis `mou`, `permohonan`, dan `lainnya` dipetakan secara eksplisit; jenis tidak dikenal ditolak. Tautan dokumen tidak dibuat jika file belum tersedia.

Catatan data lama: dokumen penilaian yang nama filenya sudah terlanjur tidak tersimpan tidak otomatis dipulihkan. Dalam sesi implementasi, pengguna diberi tahu untuk mengunggah ulang melalui Edit Pengajuan.


## 11. Perubahan yang Hanya Terbukti di Git atau Masih Ditunda

- **Label tabel Status Pengajuan:** commit `4779727` mengubah `Jml Mahasiswa` menjadi `Jumlah Mahasiswa` pada dua tabel di `app/Views/Pendidikan/Institusi/pengajuan/status.php`. Tidak ditemukan rekaman pengubahan Codex yang memastikan pelakunya. Perubahan ini dicatat sebagai bagian commit pekerjaan pengguna, bukan dimasukkan ke 60 file dengan bukti pengubahan Codex.
- **Sinkronisasi email mahasiswa dengan akun login:** ditemukan dalam audit 5 Oktober, tetapi tidak ditemukan tindak lanjut implementasi dalam arsip yang ditelusuri.
- **Konsistensi perubahan email CI dan akun login:** ditemukan masalah sukses semu/penyimpanan tidak lengkap; perbaikannya termasuk tahap tambahan yang ditunda.
- **Pemeriksaan ulang mapping ketika stase diedit:** validasi saat menyimpan penempatan sudah diperbaiki; pemeriksaan ulang terhadap penempatan lama saat mengubah stase belum tercatat diselesaikan.
- **Alasan penolakan hapus mahasiswa yang keliru:** termasuk temuan tambahan yang ditunda; tidak dicatat sebagai bug yang telah diperbaiki.
- **Validasi pembaruan mahasiswa admin:** patch aturan identitas/status pernah dilakukan pada 15 September, tetapi audit 5 Oktober masih menemukan endpoint tersebut belum memvalidasi secara memadai. Karena itu catatan ini tidak menyatakan validasi seluruh form admin telah tuntas.

Dua masalah tambahan yang akhirnya dikerjakan pada 6 Oktober—prasyarat pelunasan dan kelengkapan dokumen admin—sudah dicatat pada bagian pembayaran/dokumen, sehingga tidak diulang sebagai pekerjaan tertunda.


