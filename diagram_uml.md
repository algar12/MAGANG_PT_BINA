# 📊 Diagram UML & Flowchart
## Smart Timbangan Otomatis — Berbasis IoT + Computer Vision

---

## 1. 🎭 Use Case Diagram

```mermaid
graph TD
    subgraph Actors
        O(["👤 Operator"])
        A(["👑 Admin"])
        E(["🔌 ESP32 Device"])
        AI(["🤖 AI Server"])
    end

    subgraph "Smart Timbangan System"
        UC1["Letakkan Barang di Timbangan"]
        UC2["Baca & Kirim Data Berat"]
        UC3["Ambil Gambar via Kamera"]
        UC4["Deteksi Jenis Barang (YOLO)"]
        UC5["Hitung Harga Otomatis"]
        UC6["Tampilkan Hasil di Dashboard"]
        UC7["Konfirmasi / Batalkan Transaksi"]
        UC8["Kelola Data Produk & Harga"]
        UC9["Lihat Laporan & Histori"]
        UC10["Monitoring Device & Kalibrasi"]
        UC11["Export Data Transaksi"]
    end

    O --> UC1
    O --> UC7
    O --> UC6
    O --> UC9

    A --> UC8
    A --> UC9
    A --> UC10
    A --> UC11
    A --> UC7

    E --> UC2
    E --> UC3

    AI --> UC4
    AI --> UC5

    UC2 --> UC4
    UC3 --> UC4
    UC4 --> UC5
    UC5 --> UC6
```

---

## 2. 🔄 Activity Diagram — Alur Utama Sistem

```mermaid
flowchart TD
    Start(["🟢 Mulai"])
    A["Operator meletakkan barang\ndi atas timbangan"]
    B["ESP32 membaca data berat\ndari sensor Load Cell + HX711"]
    C{Berat stabil\n≥ threshold?}
    D["ESP32 mengirim data berat\nke server via HTTP/JSON"]
    E["Kamera mengambil\ngambar barang"]
    F["OpenCV memproses\ngambar (resize, normalize)"]
    G["YOLOv8 mendeteksi\njenis barang"]
    H{Confidence\n≥ 70%?}
    I["Tandai sebagai 'Tidak Dikenal'\n(Unknown)"]
    J["Ambil harga dari\ndatabase Laravel"]
    K["Hitung total harga:\nBerat × Harga/kg"]
    L["Simpan ke database\n(weighing_sessions)"]
    M["Tampilkan hasil di\ndashboard real-time"]
    N{Operator\nkonfirmasi?}
    O["Update status → 'confirmed'\nSimpan ke transactions"]
    P["Update status → 'cancelled'\nHapus sesi"]
    End1(["🔴 Selesai — Transaksi Berhasil"])
    End2(["🔴 Selesai — Dibatalkan"])
    Wait["Tunggu berat stabil\n(retry setiap 500ms)"]

    Start --> A
    A --> B
    B --> C
    C -- "Tidak" --> Wait
    Wait --> B
    C -- "Ya" --> D
    D --> E
    E --> F
    F --> G
    G --> H
    H -- "Ya" --> J
    H -- "Tidak" --> I
    I --> M
    J --> K
    K --> L
    L --> M
    M --> N
    N -- "Konfirmasi" --> O
    N -- "Batalkan" --> P
    O --> End1
    P --> End2
```

---

## 3. 📡 Sequence Diagram — Interaksi Antar Komponen

```mermaid
sequenceDiagram
    actor Op as 👤 Operator
    participant ESP as 🔌 ESP32
    participant AI as 🤖 AI Server (Python)
    participant Cam as 📷 Kamera
    participant API as 🌐 Laravel API
    participant DB as 🗄️ MySQL
    participant UI as 💻 Dashboard Web

    Op->>ESP: Letakkan barang di timbangan
    
    loop Baca berat setiap 500ms
        ESP->>ESP: Baca data HX711
    end

    ESP->>AI: POST /api/weight {device_id, weight_kg}
    
    AI->>Cam: Capture frame
    Cam-->>AI: Return image frame
    
    AI->>AI: Preprocess gambar (OpenCV)
    AI->>AI: Inferensi YOLOv8
    AI->>AI: Gabungkan: jenis_barang + weight_kg
    
    AI->>API: POST /api/weighing-session {product_class, weight, confidence}
    API->>DB: Query harga produk berdasarkan kelas YOLO
    DB-->>API: Return data produk & harga
    API->>API: Hitung total_price = weight × price_per_kg
    API->>DB: INSERT weighing_sessions
    DB-->>API: Return session_id
    API-->>AI: Return {session_id, status: "pending"}
    AI-->>ESP: ACK (data diterima)
    
    API->>UI: Push realtime via WebSocket/Polling
    UI-->>Op: Tampilkan: nama_barang, berat, harga_total
    
    Op->>UI: Klik "Konfirmasi Transaksi"
    UI->>API: PATCH /api/transactions/{session_id}/confirm
    API->>DB: UPDATE status = 'confirmed'\nINSERT transactions
    DB-->>API: OK
    API-->>UI: Return {status: "success"}
    UI-->>Op: Tampilkan notifikasi "Transaksi Berhasil ✅"
```

---

## 4. 🏗️ Component Diagram — Arsitektur Sistem

```mermaid
graph TB
    subgraph "⚙️ Hardware Layer"
        LC["Load Cell\n(Sensor Berat)"]
        HX["HX711\n(ADC Module)"]
        ESP["ESP32\n(WiFi MCU)"]
        CAM["Kamera\n(Webcam / IP Cam)"]
        LC --> HX --> ESP
    end

    subgraph "🤖 AI Processing Server (Python)"
        CV["OpenCV\n(Frame Capture)"]
        YOLO["YOLOv8\n(Object Detection)"]
        PROC["Data Processor\n(Merge weight + object)"]
        CAM --> CV --> YOLO --> PROC
        ESP --"HTTP POST /weight"--> PROC
    end

    subgraph "🌐 Backend (Laravel 11)"
        ROUTES["API Routes"]
        CTRL["Controllers"]
        MODEL["Eloquent Models"]
        QUEUE["Queue Jobs\n(Async)"]
        ROUTES --> CTRL --> MODEL
        CTRL --> QUEUE
    end

    subgraph "🗄️ Database (MySQL)"
        TB1["products"]
        TB2["weighing_sessions"]
        TB3["transactions"]
        TB4["users"]
        TB5["device_logs"]
    end

    subgraph "💻 Frontend (Dashboard Web)"
        DASH["Dashboard\n(Real-time)"]
        MGMT["Manajemen Produk"]
        HIST["Histori Transaksi"]
        RPT["Laporan & Grafik"]
        MON["Device Monitor"]
    end

    PROC --"HTTP POST /api/weighing-session"--> ROUTES
    MODEL <--> TB1
    MODEL <--> TB2
    MODEL <--> TB3
    MODEL <--> TB4
    MODEL <--> TB5
    CTRL --"JSON Response"--> DASH
    CTRL --> MGMT
    CTRL --> HIST
    CTRL --> RPT
    CTRL --> MON
```

---

## 5. 🗄️ Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS {
        bigint id PK
        varchar name
        varchar email
        varchar password
        enum role
        timestamp created_at
        timestamp updated_at
    }

    PRODUCTS {
        bigint id PK
        varchar name
        varchar category
        decimal price_per_kg
        varchar yolo_class
        varchar image_path
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    WEIGHING_SESSIONS {
        bigint id PK
        bigint product_id FK
        decimal weight_kg
        varchar detected_image
        decimal confidence_score
        decimal total_price
        enum status
        timestamp created_at
        timestamp updated_at
    }

    TRANSACTIONS {
        bigint id PK
        bigint session_id FK
        bigint operator_id FK
        varchar payment_method
        decimal total_amount
        text notes
        timestamp created_at
    }

    DEVICE_LOGS {
        bigint id PK
        varchar device_id
        decimal raw_weight
        int signal_strength
        varchar ip_address
        timestamp created_at
    }

    USERS ||--o{ TRANSACTIONS : "mencatat"
    PRODUCTS ||--o{ WEIGHING_SESSIONS : "terdeteksi sebagai"
    WEIGHING_SESSIONS ||--|| TRANSACTIONS : "dikonfirmasi menjadi"
```

---

## 6. 🔁 State Diagram — Status Sesi Penimbangan

```mermaid
stateDiagram-v2
    [*] --> Idle : Sistem aktif

    Idle --> Detecting : Barang diletakkan\n(berat terdeteksi)

    Detecting --> Processing : ESP32 kirim data berat\n+ kamera ambil gambar

    Processing --> Pending : AI selesai deteksi\n& hitung harga

    Pending --> Confirmed : Operator klik "Konfirmasi"
    Pending --> Cancelled : Operator klik "Batalkan"
    Pending --> Timeout : Tidak ada aksi > 60 detik

    Confirmed --> [*] : Transaksi tersimpan ✅
    Cancelled --> Idle : Kembali ke standby
    Timeout --> Idle : Auto-reset ke standby

    Processing --> Error : Deteksi gagal /\nkoneksi terputus
    Error --> Idle : Retry / Reset manual
```

---

## 7. 📶 Deployment Diagram — Topologi Jaringan

```mermaid
graph LR
    subgraph "🏪 Lokasi Fisik (Timbangan)"
        HW["ESP32 + Load Cell\n+ HX711"]
        CAM2["Kamera USB/WiFi"]
    end

    subgraph "💻 Local Server / PC"
        PY["Python AI Server\n(YOLOv8 + OpenCV)\n:5000"]
        LAR["Laravel Web Server\n(PHP + MySQL)\n:8000"]
        DB2["MySQL Database\n:3306"]
        LAR <--> DB2
    end

    subgraph "🌐 Client Browser"
        BR["Dashboard Web\n(Browser Operator / Admin)"]
    end

    HW --"WiFi / HTTP POST"--> PY
    CAM2 --"USB / RTSP stream"--> PY
    PY --"HTTP POST /api"--> LAR
    BR --"HTTP / WebSocket"--> LAR
    LAR --"JSON Response"--> BR
```

---

## 8. 🔧 Flowchart Training Model YOLOv8

```mermaid
flowchart TD
    S(["🟢 Mulai Training"])
    A["Kumpulkan dataset gambar\n(buah & sayuran)"]
    B["Anotasi gambar dengan Roboflow\n(format YOLO bounding box)"]
    C["Augmentasi data\n(rotasi, flip, brightness)"]
    D["Split dataset\n80% train / 10% val / 10% test"]
    E["Load pretrained YOLOv8m.pt\n(Transfer Learning)"]
    F["Training model\nepochs=100, batch=16, imgsz=640"]
    G["Evaluasi val set\n(mAP, Precision, Recall)"]
    H{"mAP@50 ≥ 85%?"}
    I["Hyperparameter tuning\n(learning rate, augmentation)"]
    J["Evaluasi test set final"]
    K["Export model\n(.pt → ONNX / TFLite)"]
    L["Deploy ke AI Server\n(Python + FastAPI)"]
    End(["🔴 Model Siap Digunakan"])

    S --> A --> B --> C --> D --> E --> F --> G --> H
    H -- "Belum" --> I --> F
    H -- "Ya" --> J --> K --> L --> End
```

---

## 📌 Ringkasan Komponen Sistem

| Layer | Komponen | Teknologi | Peran |
|---|---|---|---|
| **Hardware** | Sensor berat | Load Cell + HX711 | Membaca berat barang |
| **Hardware** | Mikrokontroler | ESP32 | Kirim data via WiFi |
| **Hardware** | Visual input | Kamera USB/IP | Ambil gambar barang |
| **AI Server** | Object Detection | YOLOv8 + OpenCV | Identifikasi jenis barang |
| **AI Server** | Data Processor | Python + FastAPI | Gabungkan data & kirim ke API |
| **Backend** | REST API | Laravel 11 | Manajemen data & bisnis logic |
| **Database** | Storage | MySQL 8.0 | Simpan semua data transaksi |
| **Frontend** | Dashboard | Laravel Blade/Vue | Visualisasi real-time |
