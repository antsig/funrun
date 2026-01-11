# Dokumentasi & Riwayat Proyek

Dokumen ini berfungsi sebagai catatan komprehensif tentang kemajuan pengembangan, rencana implementasi, dan panduan (walkthrough) untuk Panel Admin FunRun, dimulai dari pengaturan awal.

## Daftar Isi

1. [Fase 1: Inisialisasi & Pengaturan Inti](#fase-1-inisialisasi--pengaturan-inti)
2. [Fase 2: Fungsionalitas Inti & Perbaikan](#fase-2-fungsionalitas-inti--perbaikan)
3. [Fase 3: Perluasan Fitur](#fase-3-perluasan-fitur)
4. [Fase 4: Manajemen Media Sosial](#fase-4-manajemen-media-sosial)
5. [Fase 5: Peningkatan UI/UX](#fase-5-peningkatan-uiux)
6. [Fase 6: Konfigurasi Lanjutan](#fase-6-konfigurasi-lanjutan)

---

## Fase 1: Inisialisasi & Pengaturan Inti

**Tujuan**: Membangun proyek starter CodeIgniter 4 yang fungsional dengan Panel Admin yang lengkap.

### Implementasi Utama

- **Arsitektur Database**: Menyiapkan tabel inti termasuk `users`, `roles`, `permissions`, `user_roles`, `role_permissions`, `menus`, dan `pages`.
- **Otentikasi & Keamanan**:
  - Mengimplementasikan alur login/logout yang aman.
  - Memperbaiki kesalahan konfigurasi di `Security.php`.
  - Mendefinisikan rute dengan filter `adminAuth` dan berbasis peran (`role:administrator`).
- **Dashboard Admin**:
  - Membuat logika backend (Controller/Model) untuk dashboard.
  - Menyelesaikan kesalahan terkait tampilan untuk memastikan antarmuka AdminLTE yang konsisten.
- **Refactoring**:
  - Menggabungkan CSS yang tersebar ke dalam `public/assets/css/custom.css`.
  - Memastikan kompatibilitas PHP 8.4.

---

## Fase 2: Fungsionalitas Inti & Perbaikan

**Tujuan**: Menstabilkan fitur sistem yang kritis dan memastikan keandalan.

### Sistem Email

- **Masalah**: Email (OTP, Reset Password) tidak terkirim di lingkungan hosting.
- **Perbaikan**: Mendiagnosa dan menyelesaikan masalah konfigurasi SMTP di `.env` dan pustaka `Email`.

### Manajemen Acara & Pesanan (Awal)

- **Fitur**: CRUD Dasar untuk Acara dan Peserta.
- **Refactoring**: Membersihkan kode Controller (`Status.php`, `Callback.php`) untuk penanganan pembayaran.

---

## Fase 3: Perluasan Fitur

**Tujuan**: Menambahkan logika bisnis spesifik dan fitur manajemen acara.

### Pembuatan BIB & Filter Admin

- **Pembuatan BIB**: Mengimplementasikan logika penetapan nomor BIB otomatis berdasarkan prefiks kategori.
- **Filter Admin**: Menambahkan kemampuan pemfilteran (berdasarkan Kategori dan Status) ke tampilan Manajemen Pesanan.
- **Konfigurasi Manual**: Memungkinkan konfigurasi manual format BIB.

### Integrasi Pembayaran Manual

- **Tujuan**: Penanganan bukti transfer manual yang lebih baik.
- **Perubahan**:
  - Merefaktor tabel `orders` untuk menyertakan kolom terkait pembayaran secara langsung.
  - Membuat controller `ManualPayment` untuk memverifikasi dan mengelola unggahan bukti.

### Add-on Acara

- **Penghitung Mundur (Countdown)**: Menambahkan penghitung waktu mundur berbasis JavaScript untuk tenggat waktu pendaftaran acara di frontend.
- **Fitur Publik**: Menambahkan antarmuka "Unggah Bukti Pembayaran Manual" untuk pengguna.

---

## Fase 4: Manajemen Media Sosial

**Tujuan**: Membuat sistem dinamis untuk mengelola tautan media sosial dari Panel Admin.

### Iterasi 1: Modul Awal

- Membuat tabel `social_media_links` dan modul CRUD.
- Memungkinkan admin mengunggah ikon kustom.

### Iterasi 2: Refactor Deteksi Otomatis

- **Tujuan**: Menyederhanakan input pengguna dan menstandarisasi ikon.
- **Perubahan**:
  - Menghapus unggahan file untuk ikon.
  - Mengimplementasikan **Deteksi Otomatis**: Sistem menganalisis URL input untuk menentukan platform (Facebook, Instagram, dll).
  - **Ikon Otomatis**: Secara otomatis menetapkan kelas FontAwesome yang benar berdasarkan platform yang terdeteksi.
  - Menambahkan kolom `account_name` untuk kontrol tampilan yang lebih baik.

---

## Fase 5: Peningkatan UI/UX

**Tujuan**: Memoles Antarmuka Admin untuk pengalaman pengguna yang lebih baik.

### Perbaikan Logika Sidebar

- **Masalah**: Menu "Website & Email" tetap aktif saat submenu "Social Media" dipilih.
- **Perbaikan**: Memperbarui logika `admin_sidebar.php` untuk secara ketat mengecualikan URL `social-media` dari kondisi status aktif menu pengaturan induk.

### Desain Ulang Halaman Profil

- **Tujuan**: Memodernisasi halaman "Profil Saya".
- **Perubahan**:
  - Mengganti formulir sederhana dengan **Tata Letak 2 Kolom**.
  - **Kiri**: Kartu Profil (Avatar, Nama, Ringkasan Peran).
  - **Kanan**: Formulir Pengaturan Tab untuk pembaruan profil.

---

## Fase 6: Konfigurasi Lanjutan

**Tujuan**: Kontrol granular atas konfigurasi Acara.

### Konfigurasi BIB

- **Database**: Menambahkan kunci `settings`: `bib_config_length` dan `bib_config_custom_allowed`.
- **Pengaturan Admin**: Menambahkan tab "Event & BIB" di `Admin\Settings` untuk mengonfigurasi panjang karakter dan mengaktifkan input kustom.
- **Proses Checkout**:
  - Memperbarui controller `Checkout` untuk mengambil pengaturan.
  - Mengimplementasikan logika kondisional: Jika diaktifkan, pengguna melihat input "Request Custom BIB".
  - **Validasi**: Menegakkan panjang karakter yang ketat dan pemeriksaan keunikan terhadap database.

---

## Fase 7: Laporan Event

**Tujuan**: Modul pelaporan komprehensif untuk Pesanan dan Peserta dengan kemampuan ekspor.

### Fitur

- **Dashboard Laporan**: Akses cepat ke laporan pesanan dan peserta.
- **Laporan Pesanan**:
  - Filter: Tanggal, Status Pembayaran.
  - Ekspor: Excel (.csv), Cetak PDF.
- **Laporan Peserta**:
  - Filter: Kategori, Gender.
  - Ekspor: Excel (.csv), Cetak PDF.

### Implementasi

- **Controller**: `Admin/Reports.php` menangani logika filter dan pembuatan file CSV.
- **Views**:
  - `admin/reports/orders.php`: Tabel data pesanan.
  - `admin/reports/participants.php`: Tabel data peserta.

---

## Fase 8: Pemeliharaan & Pengaturan Email

**Tujuan**: Perbaikan bug dan peningkatan konfigurasi sistem.

### Perbaikan

- **404 Laporan**: Menambahkan rute yang hilang untuk modul Laporan (`admin/reports`).

### Pengaturan Email

- **Fitur Baru**:
  - Input **SMTP Port** (misal: 465, 587).
  - Input **SMTP Crypto** (ssl/tls).
  - **Deteksi Otomatis**: Mengubah port secara otomatis menyarankan protokol yang sesuai via JavaScript.
- **Implementasi**:
  - Migrasi `AddSmtpPortAndCrypto` untuk kolom database baru.
  - Update `Admin/Settings` view dan controller logika test email.

---

## Fase 9: System Tools (Backup & Restore)

**Tujuan**: Alat bantu sistem untuk pencadangan data dan kode bagi Administrator.

### Fitur

- **Backup Database**: Mengunduh file SQL lengkap (struktur + data).
- **Restore Database**: Mengembalikan database dari file SQL yang diunggah (Overwrite).
- **Backup Kode**: Mengunduh source code dalam format ZIP (mengecualikan `vendor`, `node_modules`, `.git`).

### Implementasi

- **Akses**: Terbatas hanya untuk role `administrator`.
- **Controller**: `Admin/Backup.php` menggunakan logika custom dumping untuk SQL dan `ZipArchive` untuk file.
- **Routes**: Group route terproteksi `role:administrator`.

---

## Fase 10: Peningkatan Dashboard

**Tujuan**: Memberikan wawasan bisnis yang lebih baik melalui visualisasi data.

### Fitur

- **Kartu Total Pendapatan**: Menampilkan total uang masuk dari pesanan berstatus `paid`.
- **Grafik Statistik Tiket**: Visualisasi diagram batang bertumpuk (Stacked Bar Chart) untuk memantau penjualan tiket per kategori (Terjual vs Pending vs Sisa).

### Implementasi

- **Library**: `Chart.js` via CDN.
- **Controller**: `Dashboard.php` menghitung `total_revenue`.
- **Controller**: `Dashboard.php` menghitung `total_revenue`.
- **View**: `dashboard.php` dimodifikasi untuk menampung Canvas chart dan kartu baru.

### Update Laporan

- **Export Excel**: Diubah dari CSV biasa ke **Format Excel XML (.xls)** yang memiliki styling (Header tebal berwarna biru, border tabel) tanpa memerlukan library tambahan yang berat.
