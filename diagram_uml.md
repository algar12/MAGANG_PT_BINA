# Kumpulan Diagram UML & Flowchart
**Sistem Timbangan Industrial IoT untuk Bill of Materials (BOM) Produksi**

Dokumen ini berisi arsitektur sistem terkini berdasarkan implementasi aktual. ESP32 berkomunikasi langsung dengan Laravel 11 (Filament v3) untuk memproses penimbangan *Batching* sesuai resep/formula BOM. Stack: **Laravel 11 + Filament v3 + MySQL + Laravel Sanctum + ESP32 (HX711)**.

---

## 1. 🏗️ Arsitektur Sistem (Deployment Diagram)

```mermaid
graph TD
    subgraph "Lantai Produksi (Industrial Edge)"
        A[Load Cell / Sensor Berat] -->|Sinyal Analog| B(Modul ADC HX711)
        B -->|Sinyal Digital| C{Mikrokontroler ESP32}
        C -->|HTTP POST /api/sensor/weight| E((Jaringan WiFi Lokal))
        D[Tombol TARE / Operator] -->|Digital Input| C
    end

    subgraph "Server (Laravel 11 + Filament v3)"
        E -->|JSON Payload| F[REST API\nDeviceController]
        F <--> G[(MySQL 8.0 Database)]
        F -->|Update Costing| H[ProductionCosting\nModel]
        F -->|Cache Berat Live| I[("Laravel Cache\nscale_{device_id}")]

        subgraph "Filament Admin Panel"
            J[Master Data\nMaterial & Device]
            K[Formula / BOM\nFormulaResource]
            L[Production Order\nProductionOrderResource]
            M[Production Costing\nProductionCostingResource]
            N[Dashboard Widgets\nStatsOverview &\nLatestProductionActivity]
        end

        G <--> J
        G <--> K
        G <--> L
        G <--> M
    end

    subgraph "Workstation Operator / Supervisor"
        O[Browser PC/Tablet] <-->|HTTPS + Filament UI| F
        O -..->|"AJAX Polling /api/costing-live/{id}"| F
        O -..->|Live Update Tabel Costing| M
    end

```

---

## 2. 🚶‍♂️ Flowchart Sistem (Alur Penimbangan BOM)

```mermaid
graph TD
    Start(["Mulai Sesi Produksi"])

    A["Supervisor/Operator Login\nke Filament Admin Panel"]
    B["Supervisor membuat Production Order\n(Pilih Formula, Qty Batch, Tanggal)"]
    C["System auto-generate Production Costings\nper BOM Item dari Formula"]
    D["Status Order: 'In Progress'\nOperator buka halaman Costing"]

    E["Operator melihat daftar bahan (BOM Items)\ndengan Netto Target & Status 'Pending'"]
    F["Operator meletakkan wadah di timbangan\n& tekan tombol TARE di ESP32"]
    G["ESP32 me-reset pembacaan menjadi 0"]

    H["Operator menuang bahan ke timbangan"]
    I["ESP32 baca HX711 secara kontinu"]
    J{"Berat\nStabil?"}

    I --> J
    J -- "Belum" --> I

    J -- "Ya" --> K["ESP32 kirim HTTP POST\nPOST /api/sensor/weight\n{device_id, weight}"]

    L["DeviceController menerima data\nCek ProductionCosting status 'Pending'\nuntuk device tersebut"]

    M{"Ada BOM\nPending?"}
    L --> M

    M -- "Ada" --> N["Update ProductionCosting:\nnetto_produksi = weight\nsub_cost_price = weight × price_bom\nstatus = 'Weighed'\nweighed_at = NOW()"]

    M -- "Tidak Ada" --> O["Simpan ke Laravel Cache\ncache('scale_{device_id}', weight, 5 menit)"]

    P["UI Operator di-refresh via\nAJAX Polling /api/costing-live/{order_id}\nsetiap 2 detik"]

    Q["Tabel Costing update:\nkolom Netto Produksi & Sub Cost Price tampil"]

    R{"Semua BOM Item\nstatus 'Weighed'?"}

    S["Supervisor ubah status\nProduction Order → 'Completed'"]

    N --> P --> Q --> R
    R -- "Belum" --> E
    R -- "Selesai" --> S
    S --> End(["Selesai Sesi Produksi"])

    Start --> A --> B --> C --> D --> E --> F --> G --> H
```

---

## 3. 🧑‍🤝‍🧑 Use Case Diagram

```mermaid
graph LR
    subgraph Actors
        SUP["👤 Supervisor / PPIC"]
        OPR["👷 Operator Produksi"]
        IOT["🔌 Perangkat IoT (ESP32)"]
    end

    subgraph "Sistem Laravel + Filament"
        UC1["Kelola Master Data Material"]
        UC2["Kelola Master Data Device (Timbangan)"]
        UC3["Kelola Formula / BOM"]
        UC4["Buat & Kelola Production Order"]
        UC5["Lihat Dashboard & Statistik"]
        UC6["Lihat & Monitor Production Costing"]
        UC7["Export Laporan Costing (Excel)"]
        UC8["Kirim Data Berat ke API\nPOST /api/sensor/weight"]
        UC9["Ambil Live Costing\nGET /api/costing-live/{id}"]
    end

    SUP --> UC1
    SUP --> UC2
    SUP --> UC3
    SUP --> UC4
    SUP --> UC5
    SUP --> UC6
    SUP --> UC7

    OPR --> UC5
    OPR --> UC6
    OPR --> UC9

    IOT --> UC8
    UC8 -.->|update otomatis| UC6
```

---

## 4. ⏱️ Sequence Diagram (Alur Penimbangan Aktual)

```mermaid
sequenceDiagram
    actor SUP as Supervisor
    actor OPR as Operator
    participant UI as Browser (Filament UI)
    participant API as Laravel API
    participant DB as MySQL Database
    participant CACHE as Laravel Cache
    participant ESP as ESP32 + HX711

    SUP->>UI: Buat Production Order (pilih Formula, qty_order)
    UI->>API: POST /filament/production-orders (form submit)
    API->>DB: INSERT production_orders
    API->>DB: INSERT production_costings (generate per BOM item)
    Note over API,DB: netto_target = bom.netto_target × qty_order<br/>price_bom = material.standart_cost<br/>sub_price = netto_target × price_bom<br/>status = 'Pending'
    DB-->>API: Success
    API-->>UI: Redirect ke halaman Costing

    OPR->>UI: Buka halaman Production Costing
    UI->>API: GET /api/costing-live/{order_id}
    API->>DB: SELECT production_costings WHERE order_id = ?
    DB-->>API: Data costings (status: Pending)
    API-->>UI: JSON costings
    UI->>UI: Render tabel BOM Items + Target Netto

    loop Penimbangan Real-time
        OPR->>ESP: Tuang bahan ke timbangan
        ESP->>ESP: Baca HX711, filter sinyal stabil
        ESP->>API: POST /api/sensor/weight {device_id, weight}
        API->>DB: SELECT devices WHERE device_id = ?
        DB-->>API: Device record

        alt Ada ProductionCosting status 'Pending' untuk device ini
            API->>DB: UPDATE production_costings SET<br/>netto_produksi=weight, status='Weighed',<br/>sub_cost_price=weight×price_bom, weighed_at=NOW()
            DB-->>API: Updated
            API-->>ESP: 200 OK {success: true, message: "Saved to BOM"}
        else Tidak ada BOM Pending
            API->>CACHE: cache.put('scale_{device_id}', weight, 5min)
            API-->>ESP: 200 OK {success: true, message: "Weight cached"}
        end

        OPR->>UI: (AJAX Polling tiap 2 detik)
        UI->>API: GET /api/costing-live/{order_id}
        API->>DB: SELECT production_costings
        DB-->>API: Updated costings
        API-->>UI: JSON dengan netto_produksi & sub_cost_price terbaru
        UI->>UI: Update tabel (tampilkan berat aktual & biaya)
    end

    SUP->>UI: Verifikasi semua BOM Item selesai
    SUP->>UI: Ubah status Production Order → 'Completed'
    UI->>API: PATCH production_orders/{id}
    API->>DB: UPDATE status = 'Completed', end_date = TODAY()
    DB-->>API: Success
    API-->>UI: Order selesai
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
        enum role "default: operator"
        timestamps created_at
    }

    MATERIALS {
        bigint id PK
        varchar kode_produk UK
        varchar nama_produk
        varchar uom_dasar "default: GRAM"
        decimal standart_cost
        boolean is_active "default: true"
        timestamps created_at
    }

    FORMULAS {
        bigint id PK
        varchar formula_code UK
        varchar formula_name
        varchar mix_kategory "nullable"
        boolean status "default: true"
        bigint created_by FK
        timestamps created_at
    }

    BOM_ITEMS {
        bigint id PK
        bigint formula_id FK
        bigint material_id FK
        decimal bom_konversi_qty "default: 1.00"
        varchar bom_konversi_uom
        decimal netto_target
        varchar mix_id "nullable"
        boolean is_optional "default: false"
        bigint created_by FK
        timestamps created_at
    }

    DEVICES {
        bigint id PK
        varchar device_id UK
        varchar name
        varchar location "nullable"
        boolean is_active "default: true"
        timestamps created_at
    }

    PRODUCTION_ORDERS {
        bigint id PK
        varchar order_number UK
        bigint formula_id FK
        int qty_order "default: 1"
        date start_date
        date end_date "nullable"
        enum status "Draft|In Progress|Completed|Cancelled"
        bigint operator_id FK
        timestamps created_at
    }

    PRODUCTION_COSTINGS {
        bigint id PK
        bigint production_order_id FK
        bigint bom_item_id FK
        bigint device_id FK "nullable"
        decimal netto_target "dari BOM × qty_order"
        decimal netto_produksi "nullable - berat aktual IoT"
        decimal price_bom "harga standar saat order dibuat"
        decimal sub_price "nullable - netto_target × price_bom"
        decimal sub_cost_price "nullable - netto_produksi × price_bom"
        enum status "Pending|Weighed|Approved"
        timestamp weighed_at "nullable"
        timestamps created_at
    }

    USERS ||--o{ FORMULAS : "membuat (created_by)"
    USERS ||--o{ PRODUCTION_ORDERS : "mengerjakan (operator_id)"
    USERS ||--o{ BOM_ITEMS : "membuat (created_by)"

    FORMULAS ||--o{ BOM_ITEMS : "terdiri dari"
    MATERIALS ||--o{ BOM_ITEMS : "dipakai di"

    FORMULAS ||--o{ PRODUCTION_ORDERS : "diproduksi melalui"
    PRODUCTION_ORDERS ||--o{ PRODUCTION_COSTINGS : "memiliki rincian biaya"
    BOM_ITEMS ||--o{ PRODUCTION_COSTINGS : "menjadi target timbang"
    DEVICES ||--o{ PRODUCTION_COSTINGS : "menimbang aktual (nullable)"
```

---

## 6. 📦 Struktur Komponen (Component Diagram)

```mermaid
graph TB
    subgraph "smart-timbangan/backend (Laravel 11)"
        subgraph "Filament Admin Panel (Port 80)"
            FA["FormulaResource\n(Master Formula/BOM)"]
            FB["BomItemResource\n(Detail BOM per Formula)"]
            FC["MaterialResource\n(Master Bahan Baku)"]
            FD["DeviceResource\n(Master Timbangan IoT)"]
            FE["ProductionOrderResource\n(Kelola Order Produksi)"]
            FF["ProductionCostingResource\n(Monitor & Set Timbang)"]
            FG["UserResource\n(Kelola User & Role)"]
            FH["StatsOverview Widget\n(Statistik Total)"]
            FI["LatestProductionActivity Widget\n(Order Terbaru)"]
            FJ["Exports\n(Excel Export Costing)"]
        end

        subgraph "REST API (Laravel Sanctum)"
            AC["DeviceController\nPOST /api/sensor/weight"]
            PC["ProductionController\nGET /api/costing-live/{id}\nGET /production/costing/{id}"]
        end

        subgraph "Models (Eloquent ORM)"
            M1[User]
            M2[Material]
            M3[Formula]
            M4[BomItem]
            M5[Device]
            M6[ProductionOrder]
            M7[ProductionCosting]
        end

        subgraph "Database"
            DB[(MySQL 8.0)]
        end

        AC --> M5
        AC --> M7
        PC --> M6
        PC --> M7
        M1 & M2 & M3 & M4 & M5 & M6 & M7 --> DB
    end

    subgraph "Hardware Layer"
        ESP32["ESP32\nFirmware C++"]
        HX711["HX711 ADC\nLoad Cell"]
        HX711 --> ESP32
    end

    ESP32 -->|"POST /api/sensor/weight"| AC
    Browser["Browser (PC/Tablet)"] -->|HTTPS| FA & FB & FC & FD & FE & FF & FG
    Browser -->|AJAX Polling| PC
```

---

## 7. 🔄 State Diagram (Siklus Hidup Production Order & Costing)

```mermaid
stateDiagram-v2
    [*] --> Draft : Supervisor buat\nProduction Order

    Draft --> InProgress : Supervisor mulai order\n(Costings di-generate otomatis)
    Draft --> Cancelled : Dibatalkan

    InProgress --> Completed : Semua BOM Item\nstatus 'Weighed'
    InProgress --> Cancelled : Dibatalkan

    Completed --> [*]
    Cancelled --> [*]

    state InProgress {
        [*] --> Pending : Costing di-generate
        Pending --> Weighed : ESP32 kirim berat\nDeviceController update
        Weighed --> Approved : Supervisor verifikasi
        Approved --> [*]
    }

    note right of InProgress
        Status Costing per BOM Item:
        Pending → Weighed → Approved
    end note
```
