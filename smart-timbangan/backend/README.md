# Smart Timbangan - Backend (Laravel + React)

Aplikasi ini adalah sistem *backend* dan *dashboard operator* untuk Proyek Smart Timbangan IoT. 
Sistem ini menggunakan Laravel 11 sebagai API dan Filament Panel, serta React.js untuk *real-time dashboard* operator timbangan.

## Arsitektur Sistem

1. **Database:** MySQL
2. **Backend API & Admin Panel:** Laravel 11 + Filament v3
3. **Frontend Operator:** React.js (tertanam dalam Laravel Blade)
4. **Real-time Engine:** Laravel Reverb (WebSocket)

### Alur Kerja (Real-Time)
- **ESP32 IoT** mengirim data berat sensor secara konstan setiap 1 detik melalui HTTP POST ke `/api/sensor/weight`.
- **Laravel** memproses data tersebut, menyimpannya ke tabel *Production Costing* jika ada BOM (Bill of Materials) yang *Pending*, atau menyimpannya di Cache jika tidak.
- **Laravel Reverb** mem-*broadcast* (push) data tersebut ke channel WebSocket secara instan.
- **React Frontend (Operator Dashboard)** yang sudah *subscribe* ke channel WebSocket tersebut akan langsung memperbarui UI (angka berat dan tabel BOM) tanpa perlu me-*refresh* halaman.

## Persyaratan Sistem

- PHP 8.2 atau lebih baru
- Composer
- Node.js & npm (untuk *build* React)
- MySQL Server

## Instalasi & Konfigurasi

1. **Install dependensi PHP & Node.js:**
   ```bash
   composer install
   npm install
   ```

2. **Konfigurasi Environment:**
   Pastikan file `.env` sudah diatur, khususnya bagian database dan Reverb:
   ```env
   APP_ENV=local
   APP_DEBUG=true
   DB_CONNECTION=mysql
   DB_DATABASE=smart_timbangan
   
   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=553504
   REVERB_APP_KEY=f1ehwd6zjxppjtoegquz
   REVERB_APP_SECRET=ve3vgisudcqpw4b6xrbi
   REVERB_HOST="localhost"
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

3. **Migrasi Database:**
   ```bash
   php artisan migrate
   ```

4. **Build Frontend (React):**
   ```bash
   npm run build
   ```
   *(Atau `npm run dev` jika sedang dalam tahap pengembangan UI Frontend)*

## Cara Menjalankan Aplikasi

Sistem ini membutuhkan **2 proses yang berjalan bersamaan** agar fitur *real-time* berfungsi:

**Terminal 1 (Backend Web Server):**
Menjalankan REST API dan antarmuka web.
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
*(Gunakan opsi `--host=0.0.0.0` agar ESP32 dan perangkat lain di jaringan lokal bisa mengakses API).*

**Terminal 2 (WebSocket Server):**
Menjalankan Laravel Reverb untuk mendengarkan dan mendistribusikan *event real-time*.
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

## Akses Aplikasi

- **Halaman Utama & Login Operator:** `http://localhost:8000/`
- **Panel Admin (Filament):** `http://localhost:8000/admin`
  - *Catatan: Hanya akun dengan role `admin` yang dapat mengakses `/admin`.*

## Struktur Folder Penting

- `app/Http/Controllers/DeviceController.php` - Endpoint API untuk menerima data dari ESP32.
- `app/Events/` - Berisi class Event (`WeightReceived`, `CostingUpdated`) untuk *broadcast* ke WebSocket.
- `resources/js/components/` - Seluruh komponen UI React untuk Dashboard Operator.
- `resources/views/welcome.blade.php` - Halaman *landing* utama (Pilih login).
- `routes/api.php` - Daftar semua rute API yang digunakan IoT dan React.
