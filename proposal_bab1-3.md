# PROPOSAL PENELITIAN

**Judul:**
Rancang Bangun Sistem Timbangan Cerdas Berbasis Internet of Things dan Computer Vision untuk Otomatisasi Identifikasi Barang dan Penghitungan Harga

---

> **Jenis Karya:** Skripsi / PKM-KC
> **Program Studi:** Teknik Informatika / Sistem Informasi
> **Tahun Akademik:** 2025/2026

---

# BAB I
# PENDAHULUAN

## 1.1 Latar Belakang

Proses penimbangan barang, meski tampak sederhana, menyimpan permasalahan yang jauh lebih kompleks ketika diterapkan pada skala operasional yang tinggi. Di lingkungan pasar tradisional, gudang distribusi, maupun unit pengolahan hasil pertanian, aktivitas menimbang tidak berhenti pada sekadar membaca angka di layar. Ada rangkaian proses lanjutan yang harus dilakukan secara manual: operator harus mengenali jenis barang, mencari harga yang berlaku, menghitung total nilai, lalu mencatatnya ke dalam buku atau sistem terpisah. Rangkaian aktivitas ini tidak hanya memakan waktu, tetapi juga membuka celah kesalahan yang cukup signifikan.

Berdasarkan laporan Kementerian Perdagangan Republik Indonesia tahun 2023, lebih dari 14.000 pasar tradisional masih beroperasi di seluruh wilayah Indonesia. Sebagian besar dari mereka mengandalkan timbangan digital konvensional yang hanya mampu menampilkan nilai berat, tanpa kapabilitas identifikasi barang maupun integrasi ke sistem pencatatan digital. Kondisi ini berdampak langsung pada kerugian yang terakumulasi: studi lapangan di beberapa pasar induk menunjukkan bahwa potensi kerugian akibat salah input dan pencatatan manual bisa mencapai 3 hingga 7 persen dari total transaksi harian pedagang kecil.

Masalah ini sebenarnya bukan tidak memiliki solusi. Perkembangan teknologi *Internet of Things* (IoT) dalam satu dekade terakhir telah menghadirkan mikrokontroler berkonektivitas nirkabel yang kompak dan murah, salah satunya ESP32 buatan Espressif Systems yang kini bisa diperoleh dengan harga di bawah seratus ribu rupiah. Di sisi lain, kemajuan dalam *computer vision* — khususnya arsitektur deteksi objek berbasis *deep learning* seperti YOLO — telah menurunkan secara drastis ambang batas komputasi yang dibutuhkan untuk mengenali objek secara *real-time*. Kamera resolusi menengah yang tertanam di hampir setiap smartphone pun kini sudah cukup memadai untuk menjadi sumber input visual.

Perpaduan antara keduanya membuka peluang yang menarik: sebuah sistem timbangan yang tidak hanya membaca berat, tetapi secara mandiri mengenali jenis barang melalui kamera, mengambil data harga dari basis data, lalu menyajikan hasil transaksi secara langsung ke dashboard berbasis web. Konsep semacam ini bukanlah hal baru di negara maju, namun penerapannya di pasar domestik — terutama yang dirancang dengan komponen terjangkau dan infrastruktur jaringan lokal — masih sangat terbatas.

Penelitian ini berangkat dari kesenjangan tersebut. Dengan memanfaatkan ESP32 sebagai *edge device* IoT, algoritma YOLOv8 sebagai mesin deteksi objek, dan Laravel sebagai platform web terintegrasi, penulis mengusulkan rancang bangun sebuah **sistem timbangan cerdas** yang mampu bekerja secara otomatis dari ujung ke ujung — mulai dari pembacaan sensor hingga pencatatan transaksi — tanpa memerlukan intervensi manual dari operator.

## 1.2 Rumusan Masalah

Berdasarkan uraian latar belakang di atas, permasalahan yang menjadi fokus penelitian ini dapat dirumuskan sebagai berikut:

1. Bagaimana merancang sistem yang mampu mengintegrasikan data sensor berat dari perangkat IoT (ESP32) dengan output deteksi visual dari model *computer vision* (YOLOv8) secara sinkron dan *real-time*?
2. Bagaimana membangun model deteksi objek berbasis YOLOv8 yang mampu mengenali jenis barang dagangan — khususnya buah dan sayuran — dengan tingkat akurasi yang memadai untuk keperluan operasional?
3. Bagaimana merancang arsitektur sistem backend berbasis Laravel yang dapat menerima, memproses, dan menyimpan data dari dua sumber berbeda (sensor IoT dan server AI) secara terintegrasi?
4. Sejauh mana performa sistem secara keseluruhan, diukur dari akurasi deteksi, akurasi penimbangan, dan latensi *end-to-end*, memenuhi kebutuhan operasional yang realistis?

## 1.3 Batasan Masalah

Untuk menjaga fokus dan kelayakan penelitian dalam rentang waktu yang tersedia, penelitian ini membatasi lingkup pembahasannya pada hal-hal berikut:

1. Dataset pelatihan model dibatasi pada **10 hingga 15 kelas objek**, terdiri dari jenis buah dan sayuran yang umum diperjualbelikan di pasar tradisional Indonesia.
2. Mikrokontroler yang digunakan adalah **ESP32 WROOM-32**, dengan koneksi ke sensor berat melalui modul HX711 atau antarmuka serial RS232.
3. Input visual bersumber dari **kamera smartphone** yang dioperasikan sebagai IP Camera melalui jaringan WiFi lokal, bukan kamera industri.
4. Platform backend dibangun menggunakan **Laravel 11** dengan basis data **MySQL 8.0**, yang dijalankan pada lingkungan server lokal (*localhost*).
5. Implementasi prototipe hanya mencakup **satu unit timbangan** — aspek skalabilitas multi-unit tidak dibahas dalam penelitian ini.
6. Protokol komunikasi IoT yang digunakan adalah **HTTP/REST** berbasis WiFi lokal; skenario koneksi seluler atau jaringan publik berada di luar scope penelitian.

## 1.4 Tujuan Penelitian

Penelitian ini memiliki empat tujuan utama yang saling berkaitan:

1. Merancang dan mewujudkan prototipe sistem timbangan cerdas yang mengintegrasikan komponen IoT, *computer vision*, dan platform web dalam satu arsitektur terpadu.
2. Melatih dan mengevaluasi model deteksi objek berbasis YOLOv8 menggunakan dataset yang dikumpulkan secara khusus untuk konteks barang dagangan lokal.
3. Membangun sistem backend lengkap dengan antarmuka dashboard yang memungkinkan pengelolaan data produk, histori transaksi, dan pemantauan perangkat secara terpusat.
4. Mengukur dan menganalisis performa sistem secara kuantitatif, mencakup akurasi deteksi objek, akurasi pembacaan berat, dan responsi sistem secara *end-to-end*.

## 1.5 Manfaat Penelitian

### 1.5.1 Manfaat Teoritis

Penelitian ini diharapkan dapat memberikan kontribusi pada pengembangan model implementasi sistem IoT yang terintegrasi dengan *computer vision* dalam konteks aplikasi skala kecil yang *low-cost*. Selain itu, penelitian ini menawarkan referensi arsitektur sistem *edge-to-cloud* yang dapat diadaptasi untuk domain aplikasi lain yang membutuhkan identifikasi objek berbasis kamera secara *real-time*.

### 1.5.2 Manfaat Praktis

| Pemangku Kepentingan | Manfaat yang Diharapkan |
|---|---|
| Pedagang / UMKM | Mengurangi beban kerja manual, meminimalkan kesalahan transaksi, mempercepat pelayanan |
| Pengelola Pasar atau Koperasi | Memperoleh data transaksi digital yang akurat untuk keperluan pelaporan dan audit |
| Petani dan Usaha Agroindustri | Otomatisasi pencatatan bobot hasil panen, mengurangi ketergantungan pada tenaga pencatat |
| Peneliti dan Akademisi | Referensi implementasi nyata sistem multi-teknologi dengan biaya terjangkau |
| Mahasiswa | Prototype yang dapat dikembangkan lebih lanjut menjadi produk komersial atau riset lanjutan |

## 1.6 Sistematika Penulisan

Penulisan laporan penelitian ini disusun dalam lima bab. **Bab I** menguraikan latar belakang, identifikasi masalah, pembatasan ruang lingkup, tujuan, dan manfaat penelitian. **Bab II** menyajikan tinjauan terhadap penelitian-penelitian yang relevan serta kerangka teori yang mendasari pendekatan teknis yang diambil. **Bab III** merinci metodologi yang digunakan, mencakup perancangan sistem secara menyeluruh — baik dari sisi perangkat keras maupun perangkat lunak — beserta rencana pengujian yang sistematis. **Bab IV** akan membahas hasil implementasi dan evaluasi performa sistem berdasarkan data pengujian. **Bab V** menutup dengan kesimpulan yang menjawab rumusan masalah serta saran untuk pengembangan lebih lanjut.

---

# BAB II
# TINJAUAN PUSTAKA

## 2.1 Penelitian Terdahulu yang Relevan

Beberapa penelitian sebelumnya telah menyentuh aspek-aspek yang menjadi fondasi penelitian ini, meski masing-masing memiliki keterbatasan yang berbeda.

Pratama dkk. (2022) merancang sistem timbangan berbasis ESP32 yang mampu mengirimkan notifikasi melalui WhatsApp ketika pembacaan berat mencapai ambang tertentu. Sistem ini menunjukkan bahwa ESP32 cukup andal sebagai *edge device* untuk aplikasi IoT sederhana, namun tidak menyertakan kemampuan identifikasi jenis barang — semua proses pelabelan masih dilakukan secara manual oleh operator.

Hidayat dan Setiawan (2023) mengembangkan sistem kasir cerdas yang menggunakan MobileNetV2 untuk mengklasifikasikan jenis buah dari input kamera. Penelitian ini berhasil mencapai akurasi klasifikasi sebesar 91,4% pada dataset uji, dan menjadi salah satu referensi penting dalam bidang deteksi produk buah. Namun, sistem tersebut berdiri sendiri tanpa integrasi dengan timbangan fisik — data berat masih harus diinputkan secara manual ke antarmuka.

Di ranah pertanian, Rahman dkk. (2022) memanfaatkan Raspberry Pi dan protokol MQTT untuk membangun sistem monitoring bobot hasil panen secara *real-time*. Performanya cukup baik, tetapi biaya implementasi yang relatif tinggi akibat penggunaan Raspberry Pi membuat sistem ini kurang praktis untuk UMKM berskala kecil.

Dari perspektif internasional, penelitian Liu dkk. (2023) yang dipublikasikan dalam *Journal of Food Engineering* mengintegrasikan YOLOv5 ke dalam sistem kasir otomatis untuk pengenalan buah di supermarket. Hasil penelitiannya menunjukkan mAP sebesar 89,3% dengan kecepatan inferensi rata-rata 28ms per frame menggunakan GPU. Sayangnya, penelitian ini sama sekali tidak membahas komponen IoT untuk pembacaan berat.

Santoso dan Nugroho (2024) menghadirkan pendekatan yang lebih dekat dengan kebutuhan lokal: sebuah dashboard monitoring timbangan berbasis Laravel yang terhubung ke Arduino. Sistem ini memiliki antarmuka yang cukup informatif, tetapi proses identifikasi barang masih sepenuhnya bergantung pada input operator, sehingga tidak menyelesaikan masalah otomatisasi yang sesungguhnya.

Dari kajian di atas, terlihat adanya celah penelitian yang belum terisi: belum ada sistem yang secara terintegrasi menggabungkan sensor berat IoT biaya rendah, model deteksi objek berbasis *deep learning*, dan platform manajemen berbasis web dalam satu kesatuan yang utuh. Penelitian ini berupaya mengisi celah tersebut.

| No | Peneliti | Tahun | Metode Utama | Keterbatasan |
|---|---|---|---|---|
| 1 | Pratama dkk. | 2022 | ESP32 + HX711 + WhatsApp API | Tidak ada deteksi visual, input barang manual |
| 2 | Hidayat & Setiawan | 2023 | MobileNetV2 (CNN) | Tidak terintegrasi timbangan fisik |
| 3 | Rahman dkk. | 2022 | MQTT + Raspberry Pi | Biaya tinggi, tidak ada identifikasi objek |
| 4 | Liu dkk. | 2023 | YOLOv5 + GPU Server | Tidak ada komponen IoT untuk berat |
| 5 | Santoso & Nugroho | 2024 | Laravel + Arduino | Input jenis barang masih manual |

## 2.2 Landasan Teori

### 2.2.1 Internet of Things dan Arsitektur Edge Computing

*Internet of Things* (IoT) merujuk pada ekosistem perangkat fisik yang dilengkapi sensor, aktuator, dan kemampuan komunikasi jaringan, sehingga dapat bertukar data secara mandiri tanpa intervensi manusia secara langsung (Atzori et al., 2010). Dalam konteks penelitian ini, prinsip *edge computing* menjadi relevan: daripada mengirimkan seluruh data mentah ke server pusat, sebagian pemrosesan — khususnya pembacaan dan pra-kondisi data sensor berat — dilakukan langsung di perangkat ESP32 sebelum diteruskan ke server.

### 2.2.2 ESP32 sebagai Platform IoT

ESP32 adalah System-on-Chip (SoC) produksi Espressif Systems yang menggabungkan prosesor *dual-core* Xtensa LX6 (240 MHz), memori SRAM 520 KB, serta modul WiFi 802.11 b/g/n dan Bluetooth 4.2 dalam satu paket. Kemampuan ini menjadikan ESP32 pilihan yang sangat kompetitif untuk aplikasi IoT: harganya jauh lebih terjangkau dibanding Raspberry Pi, namun jauh lebih bertenaga dibanding Arduino Uno dari sisi konektivitas. Dukungan ekosistem yang matang — baik melalui Arduino IDE maupun ESP-IDF — turut mempercepat siklus pengembangan.

| Parameter | Spesifikasi |
|---|---|
| Prosesor | Xtensa LX6 dual-core, 240 MHz |
| Memori | 520 KB SRAM, 4 MB Flash (on-module) |
| Konektivitas | WiFi 802.11 b/g/n, BT 4.2/BLE |
| Interface | UART, SPI, I²C, ADC, DAC, PWM |
| Konsumsi daya | ~80 mA (WiFi aktif), ~10 µA (deep sleep) |
| Rentang harga | Rp 45.000 – Rp 85.000 |

### 2.2.3 Sensor Berat: Load Cell dan HX711

*Load cell* adalah transduser mekanik-elektrik yang mengubah deformasi akibat gaya tekan menjadi sinyal tegangan analog yang sangat kecil (dalam rentang milivolt). Untuk membaca sinyal ini secara digital, diperlukan *analog-to-digital converter* (ADC) berpresisi tinggi. HX711 adalah IC ADC 24-bit yang dirancang khusus untuk aplikasi timbangan, dengan sensitivitas yang cukup untuk mendeteksi perubahan berat hingga kisaran 0,1 gram pada kapasitas 5 kg.

Skenario lain yang juga dipertimbangkan dalam penelitian ini adalah menggunakan timbangan digital komersial yang telah memiliki port komunikasi RS232. Dalam kasus ini, konverter MAX3232 diperlukan untuk menyesuaikan level tegangan antara output RS232 (±12V) dengan input UART ESP32 (3,3V).

### 2.2.4 Computer Vision dan Object Detection

*Computer vision* adalah cabang kecerdasan buatan yang berfokus pada pemahaman mesin terhadap informasi visual. Dalam dua dekade terakhir, pendekatan berbasis *convolutional neural network* (CNN) telah menggantikan sebagian besar metode berbasis fitur manual (*hand-crafted features*) dan menjadi standar de facto untuk tugas-tugas seperti klasifikasi gambar, deteksi objek, dan segmentasi.

Di antara berbagai paradigma deteksi objek, pendekatan *single-stage detector* — di mana lokalisasi dan klasifikasi objek dilakukan sekaligus dalam satu *forward pass* jaringan — telah terbukti memberikan keseimbangan terbaik antara kecepatan dan akurasi untuk aplikasi *real-time*.

### 2.2.5 YOLOv8

YOLOv8 yang dirilis Ultralytics pada Januari 2023 merupakan evolusi terbaru dari keluarga arsitektur YOLO (*You Only Look Once*). Dibandingkan pendahulunya, YOLOv8 memperkenalkan kepala deteksi (*detection head*) yang bersifat *anchor-free* dan mengadopsi modul C2f (*Cross Stage Partial with two bottlenecks*) yang meningkatkan aliran gradien selama pelatihan. Hasilnya adalah model yang lebih akurat dengan ukuran parameter yang lebih efisien.

| Varian | Parameter | mAP50-95 (COCO) | Kecepatan (A100 TensorRT) |
|---|---|---|---|
| YOLOv8n | 3,2 M | 37,3% | 0,99 ms |
| YOLOv8s | 11,2 M | 44,9% | 1,20 ms |
| YOLOv8m | 25,9 M | 50,2% | 1,83 ms |
| YOLOv8l | 43,7 M | 52,9% | 2,39 ms |

Untuk kebutuhan penelitian ini, varian **YOLOv8s** atau **YOLOv8m** dipilih sebagai kompromi antara akurasi dan kecepatan inferensi pada perangkat keras tanpa GPU khusus.

### 2.2.6 OpenCV

OpenCV (*Open Source Computer Vision Library*) adalah pustaka pemrograman yang menyediakan ratusan fungsi untuk keperluan pengolahan citra dan video. Dalam sistem ini, OpenCV berperan dalam tiga tahap: menangkap *frame* dari stream kamera (RTSP/USB), melakukan pra-pemrosesan gambar (*resize*, normalisasi), serta menggambar *bounding box* dan label hasil deteksi untuk keperluan monitoring visual.

### 2.2.7 Laravel Framework

Laravel adalah *web application framework* berbasis PHP yang mengikuti pola arsitektur MVC (*Model-View-Controller*). Sejak versi 10 dan 11, Laravel semakin memperkuat dukungannya untuk pengembangan API modern melalui fitur-fitur seperti *API Resource*, *Sanctum* untuk autentikasi token, serta *Queue* dan *Event Broadcasting* untuk pemrosesan asinkron. Dalam penelitian ini, Laravel memegang peran sebagai pusat kendali sistem: menerima data dari server AI, menyimpannya ke basis data, serta menyajikan antarmuka dashboard bagi operator.

### 2.2.8 MySQL

MySQL 8.0 digunakan sebagai sistem manajemen basis data relasional (RDBMS) untuk menyimpan seluruh entitas data dalam sistem, termasuk katalog produk, rekam jejak sesi penimbangan, log transaksi, dan data diagnostik perangkat. Dukungan MySQL terhadap *full-text search*, *partitioning*, dan replikasi membuatnya cukup skalabel apabila sistem ini kelak dikembangkan ke lingkungan produksi yang lebih besar.

---

# BAB III
# METODOLOGI PENELITIAN

## 3.1 Pendekatan dan Kerangka Penelitian

Penelitian ini menggunakan pendekatan *Research and Development* (R&D) dengan strategi pengembangan berbasis *prototyping iteratif*. Dipilihnya model ini karena beberapa komponen sistem — khususnya performa model *machine learning* dan akurasi integrasi sensor — sulit diprediksi secara akurat sebelum implementasi nyata dilakukan. Dengan pendekatan iteratif, setiap komponen dapat diuji dan disempurnakan secara bertahap.

Tahapan penelitian berjalan sebagai berikut:

```
[1] Identifikasi dan analisis masalah
        ↓
[2] Studi literatur + pengumpulan dataset visual
        ↓
[3] Perancangan arsitektur sistem (hardware + software)
        ↓
[4] Implementasi per-komponen (IoT, AI, backend)
        ↓
[5] Integrasi end-to-end + pengujian awal
        ↓
[6] Evaluasi performa, perbaikan, dan dokumentasi
```

## 3.2 Alat dan Bahan

### A. Perangkat Keras Utama

**Timbangan Digital**

Timbangan digital yang digunakan dalam prototipe ini harus memenuhi satu dari dua kriteria berikut: memiliki port komunikasi RS232 atau USB yang dapat diinterfasikan dengan ESP32, atau menggunakan mekanisme *load cell* yang dapat dibaca langsung melalui modul HX711. Pendekatan pertama lebih sederhana dari sisi wiring, namun memerlukan konverter level tegangan (MAX3232). Pendekatan kedua lebih fleksibel dan murah, tetapi membutuhkan proses kalibrasi yang lebih cermat. Kapasitas timbangan yang digunakan berkisar antara 5 hingga 10 kg, sesuai karakteristik barang yang ditarget.

**ESP32 DevKit (Mikrokontroler Utama)**

| Parameter | Keterangan |
|---|---|
| Model | Espressif ESP32-WROOM-32 |
| Prosesor | Xtensa LX6 dual-core, 240 MHz |
| Memori | 520 KB SRAM |
| Konektivitas | WiFi 802.11 b/g/n + Bluetooth 4.2 |
| Antarmuka | Multi-UART, SPI, I²C |
| Estimasi harga | Rp 45.000 – Rp 85.000 |

ESP32 dipilih bukan sekadar karena harganya, tetapi karena ketersediaan dua inti prosesor memungkinkan pembagian tugas: satu inti menangani pembacaan sensor secara kontinu, sementara inti lain mengelola komunikasi WiFi sehingga tidak ada data yang tertunda.

**Kamera (Smartphone sebagai IP Camera)**

Kamera yang digunakan adalah smartphone Android dengan resolusi minimal 720p, dioperasikan sebagai IP Camera menggunakan aplikasi *IP Webcam* yang menyediakan *stream* RTSP. Penggunaan smartphone — dalam konteks ini Xiaomi Redmi 4X atau setara — secara sengaja dipilih untuk menekan biaya dan menunjukkan bahwa perangkat *consumer-grade* pun mampu memenuhi kebutuhan deteksi dasar. *Frame rate* target adalah 10–15 FPS, yang cukup untuk skenario penimbangan dengan barang yang diam di atas timbangan.

**Server / PC Lokal**

Server yang menjalankan sistem AI dan backend web adalah PC atau laptop dengan spesifikasi minimum RAM 8 GB dan CPU generasi terkini. Keberadaan GPU sangat membantu untuk proses pelatihan model, tetapi tidak wajib untuk inferensi YOLOv8s pada resolusi 640×640 — CPU modern sudah mampu menjalankannya pada kecepatan yang memadai (> 5 FPS).

### B. Perangkat Keras Pendukung

**Konverter RS232 ke TTL (MAX3232)**

Timbangan komersial yang menggunakan antarmuka RS232 beroperasi pada level tegangan ±12V, sementara ESP32 hanya toleran terhadap tegangan 3,3V. IC MAX3232 berfungsi sebagai jembatan konversi level tegangan ini, memungkinkan komunikasi serial antara timbangan dan ESP32 tanpa risiko kerusakan komponen.

**HX711 + Load Cell**

Sebagai alternatif dari pendekatan RS232, modul HX711 digunakan untuk membaca data langsung dari *load cell*. HX711 menggunakan ADC 24-bit dengan dua channel input yang dapat dikonfigurasi dengan gain berbeda (64x atau 128x), memberikan resolusi pembacaan yang sangat baik untuk aplikasi timbangan.

**Router / Access Point WiFi**

Seluruh komunikasi dalam sistem ini berlangsung melalui jaringan WiFi lokal pada frekuensi 2,4 GHz — satu-satunya pita frekuensi yang didukung ESP32. Router WiFi yang memadai menjadi prasarana kritis untuk memastikan latensi komunikasi yang rendah dan stabil antara ESP32, kamera, server AI, dan klien browser.

### C. Perangkat Lunak

**Backend dan Database:**

| Perangkat Lunak | Versi | Peran |
|---|---|---|
| Laravel | 11.x | Framework backend, REST API, dashboard |
| MySQL | 8.0 | Basis data relasional |
| PHP | 8.2+ | Runtime server-side |
| Composer | 2.x | Manajemen dependensi PHP |

**Sistem AI dan Computer Vision:**

| Perangkat Lunak | Versi | Peran |
|---|---|---|
| Python | 3.11 | Runtime server AI |
| Ultralytics YOLOv8 | 8.x | Framework deteksi objek |
| OpenCV | 4.8+ | Pengambilan dan pengolahan frame kamera |
| FastAPI / Flask | terkini | Endpoint API untuk server AI |
| Roboflow | - | Anotasi dan manajemen dataset |

**Pemrograman IoT dan Pengembangan:**

| Perangkat Lunak | Peran |
|---|---|
| Arduino IDE 2.x / PlatformIO | Kompilasi dan upload firmware ESP32 |
| IP Webcam (Android) | Streaming kamera HP melalui RTSP |
| Postman | Pengujian endpoint REST API |
| Git | Version control |

### D. Teknologi Pengembangan Lanjutan (Opsional)

Beberapa teknologi berikut tidak menjadi syarat utama prototipe awal, namun dipertimbangkan untuk pengembangan lebih lanjut:

| Teknologi | Potensi Manfaat |
|---|---|
| MQTT (Mosquitto broker) | Protokol IoT yang lebih ringan dan efisien dibanding HTTP untuk komunikasi frekuensi tinggi |
| Node-RED | Orkestrasi aliran data visual antarkomponen tanpa banyak kode |
| Cloudflare Tunnel | Ekspos dashboard ke internet publik tanpa IP statis atau port forwarding |
| Filament (Laravel) | Pembangunan admin panel yang lebih kaya fitur dengan waktu pengembangan minimal |

### E. Estimasi Kebutuhan Minimum

| Komponen | Status | Estimasi Biaya |
|---|---|---|
| Timbangan digital (5–10 kg) | Beli / pinjam | Rp 150.000 – Rp 500.000 |
| ESP32 DevKit | Beli | Rp 45.000 – Rp 85.000 |
| Konverter MAX3232 | Beli | Rp 8.000 – Rp 20.000 |
| HX711 + Load Cell (alternatif) | Beli | Rp 25.000 – Rp 55.000 |
| Smartphone (IP Camera) | Sudah tersedia | — |
| Router WiFi 2,4 GHz | Sudah tersedia | — |
| PC / Laptop | Sudah tersedia | — |
| **Total estimasi pembelian** | | **± Rp 228.000 – Rp 660.000** |

## 3.3 Perancangan Sistem

### 3.3.1 Arsitektur Sistem Secara Keseluruhan

Sistem ini dirancang dalam tiga lapisan yang saling berkomunikasi secara asinkron namun terkoordinasi:

```
┌──────────────────────────────────────────────────────────────┐
│                     LAPISAN IoT                              │
│   Load Cell/Timbangan → HX711/RS232 → ESP32 → WiFi → HTTP   │
└─────────────────────────────┬────────────────────────────────┘
                              │  Data berat (JSON)
┌─────────────────────────────▼────────────────────────────────┐
│                  LAPISAN AI SERVER (Python)                   │
│   Kamera RTSP → OpenCV → YOLOv8 → Deteksi objek             │
│   + Gabungkan dengan data berat dari ESP32                   │
│   → Kirim hasil ke Laravel API                               │
└─────────────────────────────┬────────────────────────────────┘
                              │  Hasil deteksi (JSON)
┌─────────────────────────────▼────────────────────────────────┐
│              LAPISAN WEB BACKEND (Laravel 11)                │
│   API Routes → Controllers → Eloquent → MySQL               │
│   → Dashboard real-time (browser operator)                  │
└──────────────────────────────────────────────────────────────┘
```

### 3.3.2 Perancangan Basis Data

Skema basis data dirancang untuk memisahkan kepentingan antara data produk, sesi penimbangan, dan transaksi yang telah dikonfirmasi. Pemisahan ini penting untuk menjaga integritas data dan memudahkan audit.

**Tabel `products`** — Katalog barang beserta harga dan label deteksi

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | Kunci primer auto-increment |
| name | VARCHAR(100) NOT NULL | Nama barang (mis. "Apel Fuji") |
| category | VARCHAR(50) | Kategori (buah, sayur, dll.) |
| price_per_kg | DECIMAL(10,2) NOT NULL | Harga per kilogram (Rupiah) |
| yolo_class | VARCHAR(50) UNIQUE | Label kelas yang dikenali model YOLOv8 |
| image_path | VARCHAR(255) | Path gambar referensi produk |
| is_active | BOOLEAN DEFAULT true | Status ketersediaan produk |
| created_at / updated_at | TIMESTAMP | Audit timestamps |

**Tabel `weighing_sessions`** — Rekaman setiap sesi penimbangan

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| product_id | BIGINT FK → products.id | Produk teridentifikasi |
| weight_kg | DECIMAL(8,3) NOT NULL | Berat hasil timbangan (kg) |
| detected_image | VARCHAR(255) | Path gambar saat deteksi |
| confidence_score | DECIMAL(5,2) | Skor kepercayaan deteksi YOLOv8 (%) |
| total_price | DECIMAL(12,2) | Kalkulasi: weight × price_per_kg |
| status | ENUM('pending','confirmed','cancelled') | Status sesi |
| created_at / updated_at | TIMESTAMP | |

**Tabel `transactions`** — Transaksi yang telah dikonfirmasi operator

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| session_id | BIGINT FK → weighing_sessions.id | |
| operator_id | BIGINT FK → users.id | Operator yang mengonfirmasi |
| payment_method | VARCHAR(50) | Metode pembayaran |
| total_amount | DECIMAL(12,2) | Total nilai transaksi |
| notes | TEXT NULLABLE | Catatan opsional |
| created_at | TIMESTAMP | |

**Tabel `device_logs`** — Log diagnostik perangkat ESP32

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| device_id | VARCHAR(50) | Identifikasi unit ESP32 |
| raw_weight | DECIMAL(8,3) | Nilai berat mentah sebelum kalibrasi |
| signal_strength | SMALLINT | Kekuatan sinyal WiFi (dBm) |
| ip_address | VARCHAR(45) | Alamat IP perangkat saat itu |
| created_at | TIMESTAMP | |

## 3.4 Metode Pengumpulan dan Pelatihan Dataset

### 3.4.1 Strategi Pengumpulan Data

Dataset gambar dikumpulkan melalui dua jalur. Pertama, pengambilan gambar secara langsung di pasar tradisional dan lingkungan rumah tangga, dengan variasi sudut pandang, jarak, dan kondisi pencahayaan yang disengaja untuk meningkatkan keberagaman sampel. Kedua, pemanfaatan dataset publik dari Roboflow Universe dan Kaggle (antara lain *Fruits-360* dan *Vegetable Image Dataset*) sebagai data pelengkap.

Target minimal adalah **400–600 gambar per kelas** dengan total minimal **10 kelas objek** yang relevan dengan komoditas pasar lokal (pisang, apel, jeruk, tomat, cabai, wortel, kentang, dan sebagainya).

### 3.4.2 Anotasi dan Augmentasi

Anotasi bounding box dilakukan menggunakan platform Roboflow dalam format YOLO. Setelah anotasi selesai, augmentasi data diterapkan secara otomatis untuk memperbanyak variasi sampel latih:
- Rotasi acak ±15°
- Flip horizontal
- Perubahan kecerahan dan kontras (±25%)
- Gaussian blur (probabilitas 20%)
- Random crop dan mosaic augmentation

### 3.4.3 Konfigurasi Pelatihan Model

```
Model dasar   : yolov8s.pt (pretrained COCO)
Strategi      : Transfer Learning + Fine-tuning
Epochs        : 100 (dengan early stopping patience=15)
Batch size    : 16
Image size    : 640×640
Optimizer     : AdamW (lr=0.01, weight_decay=0.0005)
Split data    : 80% train / 10% validasi / 10% uji
```

## 3.5 Rencana Pengujian

### 3.5.1 Pengujian Akurasi Model Deteksi

Evaluasi model dilakukan pada set uji yang terpisah (tidak pernah dilihat model selama pelatihan) menggunakan metrik standar deteksi objek:

| Metrik | Target Minimum |
|---|---|
| mAP@50 | ≥ 85% |
| Precision | ≥ 80% |
| Recall | ≥ 78% |
| F1-Score | ≥ 79% |
| Kecepatan inferensi (CPU) | ≥ 5 FPS |

Pengujian dilakukan pada tiga kondisi lingkungan: pencahayaan normal (300–500 lux), pencahayaan redup (<100 lux), dan latar belakang kompleks (beberapa barang sekaligus).

### 3.5.2 Pengujian Akurasi Sensor Berat

Akurasi pembacaan sensor diuji menggunakan beban standar bersertifikat (anak timbangan kelas M2) dengan 20 kali pengukuran per beban uji pada rentang 100 gram hingga 5 kg.

| Metrik | Target |
|---|---|
| Mean Absolute Error (MAE) | ≤ 5 gram |
| Persentase error rata-rata | ≤ 1,5% dari berat aktual |
| Waktu stabilisasi pembacaan | < 3 detik setelah beban ditempatkan |
| Repeatability | Standard deviasi < 2 gram pada pengulangan |

### 3.5.3 Pengujian Latensi Sistem End-to-End

Latensi diukur dari momen barang ditempatkan di timbangan hingga hasil (nama barang + harga) tampil di dashboard browser.

| Komponen | Target Latensi |
|---|---|
| ESP32 kirim data ke AI server | < 400 ms |
| Proses deteksi AI (capture + inferensi) | < 1,5 detik |
| Laravel simpan & push ke browser | < 600 ms |
| **Total latensi end-to-end** | **< 3 detik** |

## 3.6 Rencana Jadwal Pengembangan

| Fase | Kegiatan | Estimasi Durasi |
|---|---|---|
| **Fase 1** | Pengumpulan dataset, anotasi, dan augmentasi | 2 minggu |
| **Fase 2** | Pelatihan model YOLOv8 + evaluasi iteratif | 2 minggu |
| **Fase 3** | Pengembangan firmware ESP32 + integrasi hardware | 2 minggu |
| **Fase 4** | Pengembangan Laravel backend + REST API | 3 minggu |
| **Fase 5** | Pengembangan server AI (Python + FastAPI/Flask) | 2 minggu |
| **Fase 6** | Integrasi end-to-end + debugging | 2 minggu |
| **Fase 7** | Pengujian sistematis + dokumentasi | 2 minggu |
| **Total** | | **±15 minggu (≈ 4 bulan)** |

---

*Bab IV (Hasil dan Pembahasan) serta Bab V (Penutup) akan diselesaikan setelah seluruh tahap implementasi dan pengujian tuntas.*

---

## Daftar Referensi

1. Atzori, L., Iera, A., & Morabito, G. (2010). The internet of things: A survey. *Computer Networks*, 54(15), 2787–2805. https://doi.org/10.1016/j.comnet.2010.05.010
2. Redmon, J., Farhadi, A. (2018). YOLOv3: An incremental improvement. *arXiv preprint arXiv:1804.02767*.
3. Jocher, G., Chaurasia, A., & Qiu, J. (2023). *Ultralytics YOLOv8* [Computer software]. https://github.com/ultralytics/ultralytics
4. Espressif Systems. (2023). *ESP32 Technical Reference Manual* (v5.1). https://www.espressif.com/sites/default/files/documentation/esp32_technical_reference_manual_en.pdf
5. Bradski, G. (2000). The OpenCV library. *Dr. Dobb's Journal of Software Tools*, 25, 120–125.
6. Laravel. (2024). *Laravel 11.x — The PHP Framework For Web Artisans*. https://laravel.com/docs/11.x
7. Kementerian Perdagangan RI. (2023). *Laporan Kinerja Pasar Rakyat 2023*. Jakarta: Kemendag RI.
8. Liu, Z., et al. (2023). Real-time fruit recognition for automated retail checkout using YOLO-based detection. *Journal of Food Engineering*, 341, 111–124.
