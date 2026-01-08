# Panduan Konfigurasi Midtrans Dashboard

Agar status pembayaran otomatis terupdate (Callback) dan redirect berfungsi dengan baik, Anda perlu mengatur beberapa hal di Dashboard Midtrans (Sandbox/Production).

## 1. Access Keys (Sudah dilakukan di .env)

Pastikan `Server Key` dan `Client Key` dari menu **Settings > Access Keys** sudah dicopy ke file `.env` project Anda.

## 2. Notification URL (Webhooks)

Menu: **Settings > Configuration**

Bagian **Payment Notification URL** sangat penting agar Midtrans bisa memberi tahu website Anda saat pembayaran sukses.

- **URL**: `http://localhost:8080/callback/midtrans`
  - _Catatan:_ Jika Anda menggunakan `localhost`, Midtrans **TIDAK BISA** mengirim notifikasi ke laptop Anda secara langsung.
  - **Solusi Testing**: Gunakan **Ngrok** atau hosting online.
  - Jika pakai Ngrok: `https://abcd-1234.ngrok.io/callback/midtrans`

## 3. Snap Preferences (Redirect URL)

Menu: **Settings > Snap Preferences > System Settings**

Isi bagian **Notification URL** (opsional, backup) dan **Finish/Unfinish/Error Redirect URL**.

- **Finish Redirect URL**: `http://localhost:8080/` (atau halaman riwayat order jika ada)
- **Unfinish Redirect URL**: `http://localhost:8080/`
- **Error Redirect URL**: `http://localhost:8080/`

Karena kita menggunakan frontend popup (Snap.js), redirect ini hanya terjadi jika user menutup popup atau selesai bayar di halaman redirect khusus.

## 4. Konfigurasi Khusus

Menu: **Settings > Snap Preferences > Theme & Logo** (Opsional)

- Anda bisa upload logo "FunRun" agar muncul di dalam popup pembayaran.

---

> [!TIP] > **Testing di Localhost Tanpa Ngrok:**
> Karena Midtrans tidak bisa menembus localhost, status order di database **tidak akan berubah otomatis** menjadi 'paid' saat Anda bayar di simulator.
>
> Anda perlu mengupdate status secara manual lewat **Admin Panel > Orders** atau menggunakan software seperti Postman untuk "menembak" URL callback Anda dengan payload dummy.
