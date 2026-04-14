# PROPOSAL PENELITIAN

## "Smart Timbangan Otomatis Berbasis IoT dan Computer Vision untuk Sistem Penimbangan Cerdas"

---

> **Program:** Skripsi / PKM-KC (Karsa Cipta)
> **Bidang:** Teknik Informatika / Sistem Informasi
> **Tahun:** 2025/2026

---

# BAB 1 — PENDAHULUAN

---

### 1.1 Latar Belakang

Perkembangan teknologi digital yang pesat telah membuka peluang besar untuk mentransformasi berbagai proses konvensional menjadi sistem yang lebih cerdas, efisien, dan terintegrasi. Salah satu proses yang masih banyak dilakukan secara manual dan rentan terhadap kesalahan manusia adalah proses penimbangan barang, terutama di sektor perdagangan, pertanian, dan industri kecil menengah.

Timbangan digital yang beredar saat ini umumnya hanya mampu menampilkan data berat tanpa kemampuan untuk mengenali jenis barang yang ditimbang. Operator masih harus melakukan input data secara manual ke dalam sistem, mulai dari jenis barang, satuan harga, hingga pencatatan transaksi. Proses ini membuka celah terjadinya kesalahan input, kecurangan, dan ketidakefisienan waktu yang signifikan, terutama pada lingkungan dengan volume transaksi tinggi seperti pasar tradisional, supermarket, dan gudang pertanian.

Berdasarkan data Kementerian Perdagangan Republik Indonesia (2023), terdapat lebih dari 14.000 pasar tradisional aktif di seluruh Indonesia yang masih mengoperasikan timbangan manual dengan tingkat akurasi dan efisiensi yang rendah. Selain itu, kesalahan penimbangan dan pencatatan manual berkontribusi terhadap kerugian rata-rata 3–7% dari total pendapatan pedagang kecil setiap harinya.

Di sisi lain, teknologi *Internet of Things* (IoT) dan *Computer Vision* telah mengalami kemajuan yang luar biasa dalam beberapa tahun terakhir. Mikrokontroler seperti ESP32 memungkinkan pengiriman data sensor secara nirkabel dengan biaya rendah, sementara algoritma deteksi objek berbasis *deep learning* seperti YOLO (*You Only Look Once*) mampu mengenali ratusan jenis objek secara akurat dan *real-time* menggunakan perangkat keras yang terjangkau.

Melihat gap antara kondisi riil di lapangan dan potensi teknologi yang tersedia, maka penelitian ini mengusulkan rancangan sebuah sistem bernama **Smart Timbangan Otomatis** — sebuah sistem timbangan cerdas yang mengintegrasikan sensor berat berbasis ESP32, kamera untuk *computer vision*, model AI untuk deteksi objek, dan platform web berbasis Laravel sebagai dashboard manajemen. Sistem ini dirancang agar mampu **mengenali jenis barang secara otomatis, menghitung harga, dan menyimpan data transaksi secara digital** tanpa memerlukan input manual dari operator.

---

### 1.2 Rumusan Masalah

Berdasarkan latar belakang yang telah dipaparkan, maka rumusan masalah dalam penelitian ini adalah sebagai berikut:

1. Bagaimana merancang sistem timbangan yang mampu mengenali jenis barang secara otomatis menggunakan teknologi *computer vision* berbasis AI?
2. Bagaimana mengintegrasikan data sensor berat dari ESP32 dengan sistem deteksi visual secara *real-time* melalui koneksi IoT?
3. Bagaimana membangun platform manajemen berbasis web (Laravel) yang mampu menampilkan, menyimpan, dan mengelola data hasil penimbangan secara terintegrasi?
4. Bagaimana tingkat akurasi model deteksi objek yang dibangun dalam mengenali jenis barang pada kondisi pencahayaan dan sudut pandang yang bervariasi?

---

### 1.3 Batasan Masalah

Agar penelitian ini tetap fokus dan dapat diselesaikan secara optimal, maka ditetapkan batasan masalah sebagai berikut:

1. Objek deteksi yang digunakan terbatas pada **jenis buah dan sayuran umum** yang tersedia di pasar (minimal 10 kelas objek).
2. Sistem menggunakan **ESP32** sebagai mikrokontroler dan **sensor Load Cell + HX711** sebagai sensor berat.
3. Kamera yang digunakan adalah **kamera smartphone** atau **webcam USB** dengan resolusi minimal 720p.
4. Model deteksi objek yang digunakan berbasis **YOLOv8** yang dilatih menggunakan dataset kustom.
5. Backend sistem dibangun menggunakan **Laravel 11** dengan database **MySQL**.
6. Sistem hanya mencakup **satu unit timbangan** (tidak multi-unit) dalam implementasi prototipe.
7. Koneksi antara ESP32 dan server menggunakan **WiFi lokal** (tidak membahas skenario tanpa koneksi internet).

---

### 1.4 Tujuan Penelitian

Berdasarkan rumusan masalah di atas, penelitian ini bertujuan untuk:

1. **Merancang dan membangun** prototipe sistem timbangan otomatis yang mengintegrasikan IoT dan *computer vision*.
2. **Mengimplementasikan** model deteksi objek YOLOv8 untuk mengenali jenis barang secara *real-time* tanpa input manual.
3. **Membangun** platform web berbasis Laravel sebagai pusat manajemen data penimbangan dan dashboard transaksi.
4. **Mengevaluasi** tingkat akurasi, kecepatan deteksi, dan kehandalan sistem secara keseluruhan.

---

### 1.5 Manfaat Penelitian

#### 1.5.1 Manfaat Teoritis

- Memberikan kontribusi pada pengembangan ilmu di bidang integrasi **IoT dan *Computer Vision*** dalam aplikasi dunia nyata.
- Menghasilkan model referensi arsitektur sistem *embedded* yang terkoneksi dengan platform web secara *real-time*.

#### 1.5.2 Manfaat Praktis

| Pemangku Kepentingan | Manfaat |
|---|---|
| **Pedagang / UMKM** | Mengurangi kesalahan manual, mempercepat proses transaksi |
| **Pengelola Pasar** | Monitoring transaksi digital, laporan otomatis |
| **Petani / Agroindustri** | Pencatatan hasil panen otomatis dan akurat |
| **Peneliti** | Referensi implementasi IoT + AI skala kecil yang low-cost |
| **Mahasiswa** | Prototype yang dapat dikembangkan untuk tugas akhir / PKM |

---

### 1.6 Sistematika Penulisan

- **BAB 1 – Pendahuluan**: Memuat latar belakang, rumusan masalah, batasan masalah, tujuan, manfaat, dan sistematika penulisan.
- **BAB 2 – Tinjauan Pustaka**: Memuat kajian penelitian terkait dan landasan teori yang mendukung penelitian.
- **BAB 3 – Metode Penelitian**: Memuat kerangka penelitian, perancangan sistem (hardware & software), diagram arsitektur, dan rencana pengujian.
- **BAB 4 – Hasil dan Pembahasan**: Memuat hasil implementasi, pengujian akurasi model, dan evaluasi sistem.
- **BAB 5 – Penutup**: Memuat kesimpulan dan saran pengembangan.

---

# BAB 2 — TINJAUAN PUSTAKA

---

### 2.1 Penelitian Terkait

| No | Peneliti | Tahun | Judul | Metode | Kelebihan | Kekurangan |
|---|---|---|---|---|---|---|
| 1 | Pratama et al. | 2022 | *Sistem Timbangan Digital Berbasis IoT dengan Notifikasi WhatsApp* | ESP32 + HX711 | Notifikasi real-time | Tidak ada deteksi visual |
| 2 | Hidayat & Setiawan | 2023 | *Deteksi Jenis Buah Menggunakan CNN pada Sistem Kasir* | MobileNetV2 | Akurasi 91% | Tidak terintegrasi timbangan |
| 3 | Rahman et al. | 2022 | *Smart Agriculture: Monitoring Hasil Panen IoT* | MQTT + Raspberry Pi | Real-time monitoring | Biaya tinggi |
| 4 | Liu et al. | 2023 | *YOLO-based Fruit Recognition for Automated Checkout* | YOLOv5 | Akurasi tinggi | Tidak ada komponen IoT |
| 5 | Santoso & Nugroho | 2024 | *Dashboard Monitoring Timbangan Pasar berbasis Web* | Laravel + Arduino | Integrasi web baik | Manual input jenis barang |

> **Posisi penelitian ini:** Menjadi penelitian yang mengintegrasikan **ESP32 (IoT) + YOLOv8 (Computer Vision) + Laravel (Web)** dalam satu sistem timbangan yang sepenuhnya otomatis dan low-cost.

---

### 2.2 Landasan Teori

#### 2.2.1 Internet of Things (IoT)

*Internet of Things* (IoT) adalah paradigma teknologi yang memungkinkan perangkat fisik untuk terhubung ke internet dan bertukar data secara otomatis. IoT terdiri dari tiga lapisan utama: *perception layer* (sensor/aktuator), *network layer* (komunikasi data), dan *application layer* (pengolahan dan visualisasi data).

#### 2.2.2 ESP32

ESP32 adalah mikrokontroler *dual-core* berbasis Tensilica LX6 yang dikembangkan oleh Espressif Systems.

| Spesifikasi | Nilai |
|---|---|
| Prosesor | Xtensa dual-core LX6, 240 MHz |
| RAM | 520 KB SRAM |
| Konektivitas | WiFi 802.11 b/g/n, Bluetooth 4.2 |
| GPIO | 34 pin programmable |
| Harga (estimasi) | Rp 45.000 – Rp 85.000 |

#### 2.2.3 Load Cell dan HX711

*Load Cell* adalah sensor transduser yang mengubah gaya mekanis (berat) menjadi sinyal elektrik. HX711 adalah modul *analog-to-digital converter* (ADC) 24-bit yang dirancang khusus untuk membaca data Load Cell dengan presisi tinggi.

#### 2.2.4 YOLOv8 (You Only Look Once)

YOLOv8 adalah algoritma deteksi objek *single-stage* terbaru dari Ultralytics (2023) yang terkenal karena kecepatan dan akurasinya.

| Fitur | YOLOv8 |
|---|---|
| Arsitektur | CSPDarknet + C2f modules |
| Format model | PyTorch, ONNX, TFLite |
| mAP (COCO) | ~53.9% (YOLOv8m) |
| Kecepatan inferensi | ~35ms per frame (GPU) |

#### 2.2.5 OpenCV

OpenCV adalah library *computer vision* open-source yang digunakan untuk menangkap frame kamera, preprocessing gambar, dan menampilkan hasil deteksi dengan bounding box.

#### 2.2.6 Laravel Framework

Laravel 11 adalah *web framework* PHP berbasis MVC yang digunakan sebagai backend dan dashboard, dengan fitur Eloquent ORM, RESTful API Routes, dan Queue system.

#### 2.2.7 MySQL

MySQL adalah RDBMS open-source yang digunakan untuk menyimpan data produk, transaksi, histori penimbangan, dan konfigurasi sistem.

---

# BAB 3 — METODE PENELITIAN

---

### 3.1 Kerangka Penelitian

Penelitian ini menggunakan pendekatan **Research and Development (R&D)** dengan model **prototyping**:

```
[1] Identifikasi Masalah
        ↓
[2] Studi Literatur & Pengumpulan Dataset
        ↓
[3] Perancangan Sistem (Hardware + Software)
        ↓
[4] Implementasi & Integrasi
        ↓
[5] Pengujian & Evaluasi
        ↓
[6] Analisis Hasil & Kesimpulan
```

---

### 3.2 Alat dan Bahan

#### 🧰 A. Perangkat Utama (Wajib)

**1. Timbangan Digital**

- **Fungsi:** Mengukur berat barang secara akurat
- **Kriteria pemilihan:**
  - Memiliki port **RS232 atau USB** untuk komunikasi serial (lebih mudah diintegrasikan)
  - Jika tidak ada port RS232 → gunakan **Load Cell + HX711** sebagai alternatif
- **Kapasitas:** 5 kg atau 10 kg (disesuaikan kebutuhan)

**2. ESP32 DevKit (Mikrokontroler Utama)**

| Spesifikasi | Nilai |
|---|---|
| Model | Espressif ESP32-WROOM-32 |
| Prosesor | Dual-core Xtensa LX6, 240 MHz |
| RAM | 520 KB SRAM |
| Konektivitas | WiFi 802.11 b/g/n + Bluetooth 4.2 |
| UART | Banyak port UART (cocok multi-device) |
| Harga estimasi | Rp 45.000 – Rp 85.000 |

- **Fungsi:**
  - Membaca data berat dari timbangan via serial (RS232/UART)
  - Mengirimkan data ke server via WiFi (HTTP/JSON)
- **Alasan dipilih:** WiFi built-in kuat, banyak UART, komunitas besar, dan harga terjangkau — ideal untuk IoT

**3. Kamera (HP Lama sebagai IP Camera)**

- **Contoh:** Xiaomi Redmi 4X atau smartphone Android setara
- **Fungsi:**
  - Mengambil gambar/video objek yang ada di atas timbangan
  - Menjadi input utama untuk sistem *Computer Vision*
- **Aplikasi:** **IP Webcam** (Android) → streaming RTSP ke server AI
- **Resolusi minimum:** 720p

**4. Server / PC / Laptop**

- **Fungsi:**
  - Menjalankan AI processing (OpenCV + YOLOv8)
  - Menjalankan Laravel backend + database MySQL
  - Menyimpan semua data transaksi
- **Spesifikasi minimum:** RAM 8 GB, CPU modern (GPU opsional untuk training)

---

#### 🔌 B. Perangkat Tambahan (Penting)

**1. Converter RS232 ke TTL (MAX3232 / MAX232)**

- **Fungsi:** Menjembatani komunikasi timbangan (RS232 level ±12V) ke ESP32 (TTL level 3.3V)
- **Model yang direkomendasikan:** MAX3232 (lebih kompatibel dengan ESP32 3.3V)
- **Diperlukan jika:** timbangan memiliki port RS232 dan akan dihubungkan langsung ke ESP32

**2. HX711 + Load Cell (Alternatif tanpa port RS232)**

- **Fungsi:** Membaca data berat langsung dari sensor Load Cell (tanpa timbangan digital berport)
- **Resolusi ADC:** 24-bit precision
- **Kapasitas Load Cell:** 5 kg / 10 kg
- **Kegunaan:** Solusi jika timbangan tidak memiliki output digital; lebih fleksibel untuk prototipe DIY

**3. Router / Access Point WiFi**

- **Fungsi:** Menghubungkan semua perangkat (ESP32, kamera, server, client browser) dalam satu jaringan lokal
- **Persyaratan:** WiFi 2.4 GHz (ESP32 hanya mendukung 2.4 GHz)

---

#### 🧠 C. Software yang Digunakan

**Backend & Database:**

| Software | Versi | Fungsi |
|---|---|---|
| Laravel | 11.x | Backend, REST API, & dashboard web |
| MySQL | 8.0 | Database relasional |
| PHP | 8.2+ | Bahasa server-side |

**Computer Vision & AI:**

| Software | Versi | Fungsi |
|---|---|---|
| Python | 3.11 | Runtime AI processing server |
| YOLOv8 (Ultralytics) | 8.x | Model deteksi objek real-time |
| OpenCV | 4.8+ | Capture & preprocessing gambar |
| Roboflow | - | Anotasi dan augmentasi dataset |

**Pemrograman IoT:**

| Software | Fungsi |
|---|---|
| Arduino IDE 2.x / PlatformIO | Pemrograman dan upload firmware ESP32 |
| IP Webcam (Android App) | Streaming kamera HP ke server via RTSP |
| Postman | Testing REST API endpoint |

---

#### 📊 D. Teknologi Opsional (Pengembangan Lanjutan)

| Teknologi | Manfaat |
|---|---|
| **MQTT** (Mosquitto) | Komunikasi IoT real-time yang lebih ringan dan efisien |
| **Node-RED** | Visual flow programming untuk orkestrasi data antar device |
| **Cloudflare Tunnel** | Akses dashboard web dari internet tanpa IP publik |
| **Filament (Laravel)** | Admin panel yang lebih kaya fitur dan modern |

---

#### 🎯 E. Rangkuman Kebutuhan Minimum

| Komponen | Ketersediaan | Estimasi Harga |
|---|---|---|
| Timbangan digital | Beli / pinjam | Rp 150.000 – Rp 500.000 |
| ESP32 DevKit | Beli | Rp 45.000 – Rp 85.000 |
| Converter RS232 (MAX3232) | Beli | Rp 8.000 – Rp 20.000 |
| HX711 + Load Cell | Beli (alternatif) | Rp 25.000 – Rp 50.000 |
| HP kamera (Redmi 4X dll) | Sudah dimiliki | - |
| Router WiFi | Sudah dimiliki | - |
| Laptop / PC | Sudah dimiliki | - |
| **Total estimasi** | | **~Rp 230.000 – Rp 655.000** |

---

### 3.3 Perancangan Database

**Tabel `products`**

| Field | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | Primary Key |
| name | VARCHAR(100) | Nama produk |
| category | VARCHAR(50) | Kategori (buah, sayur) |
| price_per_kg | DECIMAL(10,2) | Harga per kilogram |
| yolo_class | VARCHAR(50) | Label kelas YOLO |

**Tabel `weighing_sessions`**

| Field | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | Primary Key |
| product_id | BIGINT FK | Relasi ke products |
| weight_kg | DECIMAL(8,3) | Hasil timbangan |
| confidence_score | DECIMAL(5,2) | Keyakinan AI (%) |
| total_price | DECIMAL(12,2) | Harga total |
| status | ENUM | pending/confirmed/cancelled |

**Tabel `transactions`**

| Field | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | Primary Key |
| session_id | BIGINT FK | Relasi ke weighing_sessions |
| operator_id | BIGINT FK | Relasi ke users |
| total_amount | DECIMAL(12,2) | Total bayar |

**Tabel `device_logs`**

| Field | Tipe | Keterangan |
|---|---|---|
| id | BIGINT PK | Primary Key |
| device_id | VARCHAR(50) | ID unik ESP32 |
| raw_weight | DECIMAL(8,3) | Data berat mentah |
| signal_strength | INT | RSSI WiFi (dBm) |
| created_at | TIMESTAMP | |

---

### 3.4 Rencana Pengujian

#### Pengujian Akurasi Deteksi Objek

| Metrik | Target |
|---|---|
| mAP@50 | ≥ 85% |
| Precision | ≥ 80% |
| Recall | ≥ 80% |
| FPS (inferensi) | ≥ 5 FPS |

#### Pengujian Akurasi Sensor Berat

| Metrik | Target |
|---|---|
| Error rata-rata | ≤ 2% dari berat aktual |
| Resolusi | 0.5 gram |
| Stabilitas pembacaan | < 3 detik |

#### Pengujian Latency End-to-End

| Tahap | Target |
|---|---|
| ESP32 kirim data berat | < 500ms |
| AI deteksi objek | < 2 detik |
| Laravel simpan & tampilkan | < 1 detik |
| **Total end-to-end** | **< 4 detik** |

---

### 3.5 Timeline Pengembangan

| Fase | Kegiatan | Durasi |
|---|---|---|
| **Fase 1** | Perancangan + Pengumpulan Dataset | 2 minggu |
| **Fase 2** | Training model YOLOv8 + Evaluasi | 2 minggu |
| **Fase 3** | Pemrograman ESP32 + Integrasi Hardware | 2 minggu |
| **Fase 4** | Pengembangan Backend Laravel + API | 3 minggu |
| **Fase 5** | Integrasi end-to-end + Dashboard | 2 minggu |
| **Fase 6** | Pengujian + Evaluasi + Dokumentasi | 2 minggu |
| **Total** | | **~13 minggu (±3 bulan)** |

---

*Dokumen ini akan dilengkapi dengan BAB 4 (Hasil & Pembahasan) dan BAB 5 (Penutup) setelah tahap implementasi selesai.*

---

**Referensi:**
1. Atzori, L., Iera, A., & Morabito, G. (2010). The internet of things: A survey. *Computer Networks*, 54(15), 2787–2805.
2. Redmon, J., et al. (2016). You only look once: Unified, real-time object detection. *CVPR 2016*.
3. Jocher, G. (2023). *Ultralytics YOLOv8*. https://github.com/ultralytics/ultralytics
4. Espressif Systems. (2023). *ESP32 Technical Reference Manual*.
5. Laravel. (2024). *Laravel 11.x Documentation*. https://laravel.com/docs
