# PROPOSAL PENELITIAN

**Judul:**
Rancang Bangun Sistem Timbangan Cerdas Berbasis Industrial Internet of Things (IIoT) Terintegrasi Web untuk Digitalisasi Bill of Materials (BOM) pada Industri Manufaktur Makanan

---

> **Jenis Karya:** Skripsi / Proyek Akhir
> **Program Studi:** Teknik Informatika / Sistem Informasi / Teknik Industri
> **Tahun Akademik:** 2025/2026

---

# BAB I
# PENDAHULUAN

## 1.1 Latar Belakang

Dalam industri manufaktur makanan—seperti pada produksi bakery berskala menengah hingga besar—proses batching atau penimbangan bahan baku sesuai dengan resep (*Bill of Materials* / BOM) merupakan tahapan paling krusial. Konsistensi rasa, kualitas produk akhir, serta akurasi perhitungan Harga Pokok Produksi (HPP) sangat bergantung pada seberapa presisi operator di lantai produksi (shop floor) menimbang bahan baku seperti tepung, margarin, pengembang (SP), hingga minyak goreng. 

Praktik yang banyak terjadi di lapangan saat ini masih mengandalkan sistem manual: operator melihat resep yang dicetak di atas kertas, menimbang bahan menggunakan timbangan digital biasa, lalu mencatat angka yang tertera di layar timbangan ke dalam buku log atau form *Costing Produksi*. Proses yang berulang ini sangat rentan terhadap *human error* (salah baca angka, salah tulis, atau lupa mencatat), serta membuka peluang terjadinya *shrinkage* (penyusutan bahan baku yang tidak wajar). Lebih lanjut, data yang dicatat secara manual di atas kertas menyebabkan keterlambatan informasi bagi manajemen (supervisor/PPIC) untuk mengetahui realisasi *Costing Produksi* aktual secara *real-time*.

Di era Industri 4.0, konsep *Industrial Internet of Things* (IIoT) menawarkan solusi untuk mengeliminasi kesenjangan antara perangkat keras di lantai pabrik dengan sistem informasi manajemen (ERP/MRP). Mikrokontroler berbiaya rendah seperti ESP32 yang memiliki kapabilitas Wi-Fi dapat dihubungkan ke sensor berat (Load Cell dan modul HX711) untuk mengambil data secara digital langsung dari sumbernya. Dengan menghubungkan perangkat ini secara nirkabel ke sebuah platform web manajemen produksi, setiap mili-gram bahan yang ditimbang dapat secara instan tercatat di database tanpa perlu campur tangan manual.

Penelitian ini bertujuan untuk merancang bangun sebuah prototipe sistem timbangan pintar yang terintegrasi secara langsung dengan aplikasi web manajemen produksi berbasis framework Laravel. Sistem web ini dirancang agar dapat menampilkan daftar kebutuhan bahan (BOM) berdasarkan *Formula* produk (misalnya: *PREMIX BROWNIES KUKUS*), menetapkan target *Netto*, dan kemudian secara otomatis membaca berat aktual dari timbangan IoT untuk mengisi kolom *Netto Produksi* dan menghitung *Sub Cost Price*. Dengan demikian, diharapkan tercipta sebuah sistem otomasi data yang mampu meningkatkan akurasi *costing*, mengurangi potensi kesalahan penimbangan, dan menyajikan data *real-time* bagi pihak manajemen pabrik.

## 1.2 Rumusan Masalah

Berdasarkan latar belakang tersebut, rumusan masalah dalam penelitian ini adalah:
1. Bagaimana merancang dan mengimplementasikan perangkat keras timbangan berbasis ESP32 dan modul HX711 yang mampu mengirimkan data berat secara presisi ke server lokal?
2. Bagaimana membangun sistem informasi manajemen berbasis web (menggunakan Laravel) yang mampu mengelola *Master Data Material*, *Bill of Materials* (BOM), dan *Production Orders*?
3. Bagaimana mengintegrasikan perangkat IoT (timbangan) dengan platform web sehingga hasil penimbangan aktual secara otomatis mengisi form *Production Costing* untuk setiap *item* pada BOM?
4. Sejauh mana sistem ini mampu meningkatkan kecepatan dan akurasi proses penimbangan dibandingkan dengan pencatatan manual?

## 1.3 Batasan Masalah

Untuk menjaga fokus penelitian, batasan masalah yang ditetapkan adalah:
1. Perangkat keras mikrokontroler yang digunakan dibatasi pada keluarga ESP32 (ESP32 WROOM).
2. Sensor berat yang digunakan adalah sensor *Load Cell* dengan kapasitas maksimum 10-20 kg (skala pilot/premix) beserta modul konverter analog-to-digital HX711.
3. Aplikasi web dikembangkan menggunakan framework Laravel 11 dan database MySQL 8.0, dijalankan pada lingkungan server lokal (Intranet pabrik) tanpa eksposur publik.
4. Modul perangkat lunak yang dikembangkan dibatasi pada manajemen bahan baku (*Materials*), *Formulas* (BOM), *Production Orders*, dan *Production Costings*.
5. Pengiriman data dari ESP32 ke server web menggunakan protokol HTTP/REST API melalui jaringan Wi-Fi lokal standar (2.4 GHz).

## 1.4 Tujuan Penelitian

1. Menghasilkan prototipe timbangan digital berbasis IoT yang fungsional dan mampu terhubung ke jaringan lokal.
2. Membangun aplikasi web *manufacturing* yang dapat menampilkan dan menyimpan rekam jejak *Bill of Materials* dan *Costing Produksi*.
3. Mewujudkan integrasi antara perangkat keras dan perangkat lunak sehingga data *Netto* pada form produksi dapat terisi secara otomatis (*auto-fill*) berdasarkan angka aktual pada timbangan, sehingga meminimalisir intervensi manual dari operator.

## 1.5 Manfaat Penelitian

### 1.5.1 Manfaat Teoritis
Memberikan kontribusi wawasan mengenai penerapan arsitektur *Industrial Internet of Things* (IIoT) berskala mikro yang memanfaatkan komponen murah (ESP32) untuk mendigitalisasi proses *manufacturing execution system* (MES), khususnya di sub-sektor industri pangan.

### 1.5.2 Manfaat Praktis
Bagi industri manufaktur makanan atau UMKM Bakery:
- **Akurasi Data:** Mengurangi kesalahan pencatatan (*human error*) yang berdampak pada salah hitung Harga Pokok Produksi.
- **Efisiensi Waktu:** Operator dapat lebih fokus pada proses fisik penuangan bahan tanpa harus bolak-balik mencatat data.
- **Traceability:** Manajemen dapat memantau proses produksi, variansi bahan (target vs aktual), dan status penyelesaian pesanan secara *real-time*.

---

# BAB II
# TINJAUAN PUSTAKA

## 2.1 Penelitian Terdahulu

Penelitian oleh Setiawan, dkk. (2022) mengusulkan sistem penimbangan cerdas berbasis Arduino untuk mencatat hasil panen pertanian ke dalam database secara otomatis. Meskipun berhasil mengotomatisasi pencatatan, sistem tersebut belum memiliki kapabilitas pengolahan struktur data kompleks seperti *Bill of Materials* yang memerlukan sinkronisasi antara target (resep) dan realisasi aktual.

Sari & Wibowo (2023) merancang sistem informasi *Manufacturing Execution System* (MES) sederhana untuk UMKM berbasis web yang mengelola HPP dan *Costing*. Namun, penginputan data realisasi produksi di sistem mereka masih bergantung pada input keyboard dari operator, sehingga data *waste* atau *shrinkage* akibat kelebihan tuang bahan baku seringkali tidak tercatat secara jujur atau akurat.

Penelitian ini menggabungkan kedua pendekatan di atas: mengadopsi struktur data MES/BOM berbasis web, dan mengeliminasi proses input keyboard melalui injeksi data langsung dari timbangan IoT (ESP32).

## 2.2 Landasan Teori

### 2.2.1 Industrial Internet of Things (IIoT)
IIoT merupakan pemanfaatan perangkat pintar berbasis sensor dan aktuator yang terhubung dalam sebuah jaringan komputer industrial. Berbeda dengan IoT konsumen, IIoT sangat menekankan pada presisi, keandalan, dan integrasi data dengan sistem perusahaan (seperti ERP atau MES) untuk mengoptimalkan proses operasional bisnis (Boyes et al., 2018).

### 2.2.2 Bill of Materials (BOM)
*Bill of Materials* adalah daftar komprehensif dari seluruh bahan baku, perakitan, dan sub-perakitan yang dibutuhkan untuk memproduksi sebuah produk akhir. Dalam industri makanan, BOM sering disebut sebagai resep atau formula. Akurasi penimbangan komponen dalam BOM adalah syarat mutlak untuk menjaga konsistensi produk akhir dan kelayakan perhitungan finansial (HPP).

### 2.2.3 ESP32 dan Load Cell HX711
- **ESP32:** Sebuah SoC (*System on a Chip*) buatan Espressif yang memiliki modul Wi-Fi terintegrasi. Performanya yang cepat (prosesor 240MHz) membuatnya sanggup menangani pembacaan sensor berkecepatan tinggi sekaligus menjaga komunikasi jaringan nirkabel.
- **Load Cell:** Sensor tranduser yang mendeteksi gaya tekan mekanik dan mengubahnya menjadi sinyal elektronik analog.
- **HX711:** *Analog-to-Digital Converter* (ADC) 24-bit yang dirancang khusus untuk timbangan digital guna memperkuat (amplify) dan mendigitalisasi sinyal lemah dari *Load Cell* sebelum diproses oleh mikrokontroler.

### 2.2.4 Framework Laravel
Laravel adalah framework web berbasis bahasa pemrograman PHP yang sangat populer karena arsitektur Model-View-Controller (MVC) yang elegan, keamanan yang solid (seperti perlindungan CSRF dan SQL Injection), serta ekosistem manajemen database (Eloquent ORM) yang mempermudah perancangan relasi antar tabel (seperti relasi antara *Formula*, *Bom Items*, dan *Production Costing*).

---

# BAB III
# METODOLOGI PENELITIAN

## 3.1 Pendekatan Pengembangan Sistem

Metode yang digunakan dalam pengembangan sistem ini adalah *Prototyping*. Metode ini dipilih karena sangat cocok untuk mengintegrasikan *hardware* dan *software* di mana *feedback* iteratif sangat diperlukan pada saat mengkalibrasi pembacaan timbangan dan menguji antarmuka aplikasi dengan interaksi nyata pengguna.

Tahapan pengembangannya meliputi:
1. **Analisis Kebutuhan:** Mengumpulkan format tabel BOM dan form Costing Produksi yang biasa dipakai di industri.
2. **Perancangan Sistem:** Merancang ERD (*Entity Relationship Diagram*) untuk database dan mendesain rangkaian elektronik ESP32.
3. **Pembangunan Web App:** Membangun antarmuka Laravel untuk modul Master Data dan modul Penimbangan BOM.
4. **Pemrograman IoT:** Menulis *firmware* Arduino (C++) untuk ESP32 agar dapat membaca HX711 secara *moving average* dan mengirim *request* HTTP POST ke Laravel.
5. **Integrasi & Pengujian:** Menjalankan sistem secara utuh (*end-to-end*).

## 3.2 Alat dan Bahan

### 3.2.1 Perangkat Keras (Hardware)
1. Mikrokontroler ESP32 DevKit V1
2. Modul HX711 (ADC 24-bit)
3. Sensor Load Cell Bar (Kapasitas 10kg - 20kg)
4. Modul Push Button (Sebagai tombol 'Tare' perangkat keras)
5. Modul Display LCD I2C 16x2 atau 20x4 (Untuk menampilkan berat saat ini secara lokal di alat)
6. Base timbangan akrilik / besi
7. PC / Laptop sebagai Server lokal

### 3.2.2 Perangkat Lunak (Software)
1. **Sistem Operasi:** Ubuntu 24.04 / Windows 11
2. **IDE Hardware:** Arduino IDE 2.x
3. **Web Server & Backend:** PHP 8.2, Laravel 11.x, Nginx/Apache
4. **Database:** MySQL 8.0
5. **REST Client:** Postman (untuk pengujian API)

## 3.3 Perancangan Database (ERD)

Untuk mengakomodasi struktur *Bill of Materials* dan *Costing Produksi*, basis data dirancang dengan relasi saling terkait:

1. **Table `materials`:** Menyimpan referensi bahan baku (Kode, Nama, UOM Dasar, Standar Harga).
2. **Table `formulas`:** Menyimpan informasi produk jadi (Misal: Premix Brownies).
3. **Table `bom_items`:** Menyimpan relasi formula dengan bahan beserta target ukurannya (Misal: Butuh 315 gram Minyak Sovia untuk Premix Brownies).
4. **Table `production_orders`:** Daftar antrean produksi (Order Produksi) yang dijalankan hari itu.
5. **Table `production_costings`:** *Tabel Transaksional Utama*. Menyimpan target *netto* versus **realisasi netto aktual dari Timbangan IoT**, beserta kalkulasi total biayanya (*Sub Cost Price*).

## 3.4 Skenario Penggunaan Sistem (Use Case)

Alur atau siklus operasional sistem dirancang sebagai berikut:

1. **Pemilihan Formula (Web):** Operator di area timbang (*mixing*) membuka halaman web melalui tablet/PC. Mereka memilih "Formula" yang akan diproduksi.
2. **Tampilan BOM (Web):** Web menampilkan daftar *bom items* lengkap dengan kolom Target Netto.
3. **Fokus pada Bahan (Web/Timbangan):** Operator mengklik baris pertama (misal: "Minyak Goreng").
4. **Proses Penimbangan (Fisik):** Operator meletakkan wadah di timbangan, menekan *Tare* (Nol). Lalu menuangkan Minyak Goreng ke dalam wadah.
5. **Pembacaan IoT (Hardware):** ESP32 membaca berat dengan stabil. Apabila nilai timbangan telah stabil selama 2 detik, ESP32 mengirim perintah ke server web via HTTP REST API berisikan payload data `{ "device_id": "Timbangan-1", "weight": 315.5 }`.
6. **Sinkronisasi Otomatis (Web):** Angka di baris tabel web pada kolom "NETTO PRODUKSI" langsung terisi dengan angka "315.5" (bersifat *live update*). 
7. **Persetujuan (Web):** Jika sudah sesuai target, operator menekan tombol "SET PRODUKSI". Baris ini terkunci, dan sistem secara otomatis menghitung *Sub Cost Price* dan menyimpannya. Operator lalu berlanjut menimbang bahan berikutnya di baris ke-2.

## 3.5 Rencana Pengujian

Pengujian dilakukan dalam beberapa skenario utama:
1. **Pengujian Akurasi Sensor:** Menguji batas error (MPE) dari rakitan *Load Cell* dengan menggunakan anak timbangan standar kelas M1. 
2. **Pengujian Latensi Komunikasi:** Menghitung jeda waktu sejak barang diletakkan di timbangan hingga layar web ter-update angkanya (Target latensi: di bawah 500ms di jaringan LAN lokal).
3. **Pengujian Fungsionalitas BOM:** Menguji apakah perhitungan *Sub Price* (Standar) dan *Sub Cost Price* (Aktual) berjalan sesuai formula matematika dan tidak terjadi kesalahan penulisan (*overwrite*) saat pindah dari satu komponen ke komponen lain.

---
*(Bab IV dan Bab V akan diselesaikan setelah tahap implementasi selesai dilakukan.)*
