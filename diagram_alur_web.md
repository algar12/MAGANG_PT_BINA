# Diagram UML Alur Web Smart Timbangan

Dokumen ini menggambarkan alur web terbaru pada aplikasi Smart Timbangan berbasis Laravel 11 dan Filament. Alur saat ini memakai menu `Bahan Baku` sebagai master data utama, lalu menu `Mulai Menimbang` membuat nomor penimbangan otomatis dan menghubungkan bahan baku ke data penimbangan.

---

## 1. Use Case Diagram

```mermaid
usecaseDiagram
    actor Admin as "Admin / Operator"
    actor Device as "ESP32 / Timbangan"

    rectangle "Web Smart Timbangan" {
        usecase UC1 as "Login Panel Admin"
        usecase UC2 as "Kelola Bahan Baku"
        usecase UC3 as "Kelola Alat Timbangan"
        usecase UC4 as "Mulai Menimbang"
        usecase UC5 as "Generate Nomor Otomatis"
        usecase UC6 as "Pilih Bahan Baku"
        usecase UC7 as "Simpan Hasil Timbang"
        usecase UC8 as "Filter Data per Hari/Bulan/Tahun"
        usecase UC9 as "Export Excel per Hari/Bulan/Tahun"
        usecase UC10 as "Kirim Berat Aktual"
    }

    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC8
    Admin --> UC9

    UC4 --> UC5 : "<<include>>"
    UC4 --> UC6 : "<<include>>"
    UC4 --> UC7 : "<<include>>"

    Device --> UC10
    UC10 --> UC7 : "<<update>>"
```

---

## 2. Activity Diagram Alur Web

```mermaid
flowchart TD
    Start([Mulai])
    Login[Admin membuka /admin dan login]
    Dashboard[Dashboard Filament tampil]

    Menu{Pilih menu}
    BahanBaku[Kelola Bahan Baku]
    Device[Kelola Alat Timbangan]
    Mulai[Masuk menu Mulai Menimbang]

    InputBahan[Input / edit data bahan baku]
    InputDevice[Input / edit data alat]

    PilihBahan[Pilih Bahan Baku]
    PilihDevice[Pilih Device Timbangan]
    InputStatus[Isi status dan data penimbangan]
    GenerateNomor[Sistem generate nomor otomatis TMB-YYYYMMDD-0001]
    AutoBom[Sistem membuat/mengambil BOM internal otomatis]
    Simpan[Simpan data Production Costing]

    DeviceSend[ESP32 mengirim berat ke API /api/sensor/weight]
    CariPending[Sistem mencari penimbangan Pending berdasarkan device]
    UpdateBerat[Update Netto Aktual, status Weighed, dan waktu timbang]

    Filter[Admin filter data Hari Ini / Bulan Ini / Tahun Ini]
    Export[Admin export Excel sesuai periode]
    End([Selesai])

    Start --> Login --> Dashboard --> Menu
    Menu --> BahanBaku --> InputBahan --> Dashboard
    Menu --> Device --> InputDevice --> Dashboard
    Menu --> Mulai --> PilihBahan --> PilihDevice --> InputStatus --> GenerateNomor --> AutoBom --> Simpan --> Dashboard

    DeviceSend --> CariPending --> UpdateBerat --> Dashboard
    Dashboard --> Filter --> Export --> End
```

---

## 3. Sequence Diagram Membuat Data Mulai Menimbang

```mermaid
sequenceDiagram
    actor Admin as Admin / Operator
    participant UI as Filament Panel
    participant PC as ProductionCostingResource
    participant Create as CreateProductionCosting
    participant DB as Database

    Admin->>UI: Buka menu Mulai Menimbang
    UI->>PC: Render form create
    PC->>DB: Ambil daftar Material aktif
    DB-->>PC: Data Bahan Baku
    PC-->>UI: Tampilkan pilihan Bahan Baku dan Device

    Admin->>UI: Pilih Bahan Baku, Device, Status
    Admin->>UI: Klik Create
    UI->>Create: Kirim data form

    Create->>DB: Ambil Material berdasarkan material_id
    Create->>DB: Cari / buat Formula Default internal
    Create->>DB: Cari / buat BOM Item internal
    Create->>Create: Generate nomor TMB-YYYYMMDD-0001
    Create->>DB: Buat Production Order otomatis
    Create->>DB: Simpan Production Costing
    DB-->>Create: Data tersimpan
    Create-->>UI: Redirect ke daftar Mulai Menimbang
```

---

## 4. Sequence Diagram Update Berat dari Timbangan

```mermaid
sequenceDiagram
    participant ESP as ESP32 / Timbangan
    participant API as Laravel API
    participant Device as DeviceController
    participant DB as Database
    participant UI as Filament Mulai Menimbang

    ESP->>API: POST /api/sensor/weight {device_id, weight}
    API->>Device: Validasi payload
    Device->>DB: Cari device berdasarkan device_id
    DB-->>Device: Data device
    Device->>DB: Cari ProductionCosting Pending untuk device

    alt Ada data Pending
        Device->>DB: Update netto_produksi, sub_cost_price, status Weighed, weighed_at
        DB-->>Device: Update berhasil
        Device-->>ESP: 200 OK Saved to BOM
        UI->>DB: Poll tabel setiap 2 detik
        DB-->>UI: Data terbaru tampil
    else Tidak ada data Pending
        Device->>Device: Simpan berat ke cache scale_device_id
        Device-->>ESP: 200 OK Weight cached
    end
```

---

## 5. Component Diagram

```mermaid
flowchart LR
    subgraph Client
        Browser[Browser Admin / Operator]
        ESP[ESP32 Timbangan]
    end

    subgraph Laravel["Laravel Backend"]
        Filament[Filament Admin Panel]
        Resources[Filament Resources]
        Exporter[Excel Exporter]
        API[REST API Controller]
        Models[Eloquent Models]
    end

    subgraph Data
        DB[(Database MySQL)]
        Cache[(Laravel Cache)]
    end

    Browser --> Filament
    Filament --> Resources
    Resources --> Models
    Resources --> Exporter
    Exporter --> Models
    ESP --> API
    API --> Models
    API --> Cache
    Models --> DB
```

---

## 6. ERD Sederhana

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email
    }

    MATERIALS {
        bigint id PK
        string kode_produk
        string nama_produk
        string uom_dasar
        decimal standart_cost
        boolean is_active
    }

    DEVICES {
        bigint id PK
        string device_id
        string name
        string location
        boolean is_active
    }

    FORMULAS {
        bigint id PK
        string formula_code
        string formula_name
        boolean status
    }

    BOM_ITEMS {
        bigint id PK
        bigint formula_id FK
        bigint material_id FK
        decimal bom_konversi_qty
        string bom_konversi_uom
        decimal netto_target
    }

    PRODUCTION_ORDERS {
        bigint id PK
        string order_number
        bigint formula_id FK
        int qty_order
        date start_date
        string status
        bigint operator_id FK
    }

    PRODUCTION_COSTINGS {
        bigint id PK
        bigint production_order_id FK
        bigint bom_item_id FK
        bigint device_id FK
        decimal netto_target
        decimal netto_produksi
        decimal price_bom
        decimal sub_price
        decimal sub_cost_price
        string status
        timestamp weighed_at
    }

    USERS ||--o{ PRODUCTION_ORDERS : "operator"
    MATERIALS ||--o{ BOM_ITEMS : "dipakai"
    FORMULAS ||--o{ BOM_ITEMS : "memiliki"
    FORMULAS ||--o{ PRODUCTION_ORDERS : "default internal"
    PRODUCTION_ORDERS ||--o{ PRODUCTION_COSTINGS : "nomor timbang"
    BOM_ITEMS ||--o{ PRODUCTION_COSTINGS : "bahan internal"
    DEVICES ||--o{ PRODUCTION_COSTINGS : "mengirim berat"
```

---

## 7. State Diagram Status Penimbangan

```mermaid
stateDiagram-v2
    [*] --> Pending: Data Mulai Menimbang dibuat
    Pending --> Weighed: ESP32 mengirim berat aktual
    Weighed --> Approved: Admin menyetujui hasil
    Pending --> Approved: Admin set manual bila diperlukan
    Approved --> [*]
```

