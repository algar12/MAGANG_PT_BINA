# Tutorial Deployment Sistem Timbangan IoT untuk BOM Produksi

Tutorial ini memandu Anda untuk menjalankan sistem *Smart Timbangan* dari nol. Tidak ada lagi kebutuhan untuk n8n, AI Camera, maupun server Python. Sistem kini jauh lebih ringan, cepat, dan efisien!

---

## TAHAP 1: Menyiapkan Server Web (Laravel & Database)

Jika Anda sudah menjalankan `install.sh` sebelumnya, maka sebagian besar persiapan sudah selesai. Berikut cara menjalankan Laravel:

1. **Jalankan Database MySQL**
   Pastikan MySQL Service berjalan di latar belakang:
   ```bash
   sudo systemctl start mysql
   ```

2. **Jalankan Laravel**
   Buka terminal, masuk ke folder backend dan jalankan *development server*.
   ```bash
   cd ~/Desktop/ProjectMagang/smart-timbangan/backend
   php artisan serve --host=0.0.0.0 --port=8000
   ```

   *Catatan:* Opsi `--host=0.0.0.0` sangat penting agar Laravel bisa diakses oleh ESP32 melalui jaringan WiFi.

3. **Cek Alamat IP Lokal Anda**
   Buka tab terminal baru, ketik:
   ```bash
   ip a
   ```
   Cari alamat IP komputer Anda yang berawalan `192.168...` (Misal: `192.168.1.15`). Ini adalah IP Server Anda. ESP32 nantinya harus mengirim data ke `http://192.168.1.15:8000`.

---

## TAHAP 2: Menyiapkan Perangkat Keras (ESP32)

1. **Rakit Perangkat:**
   - Hubungkan Load Cell ke Modul HX711.
   - Hubungkan HX711 ke ESP32:
     - HX711 `VCC` -> ESP32 `3.3V` atau `VIN`
     - HX711 `GND` -> ESP32 `GND`
     - HX711 `DT` (Data) -> ESP32 `D2`
     - HX711 `SCK` (Clock) -> ESP32 `D4`

2. **Program ESP32:**
   - Buka **Arduino IDE** di laptop Anda.
   - Install *library*: **HX711 by Bogdan Necula** dan **ArduinoJson**.
   - Masukkan *source code* berikut:

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include "HX711.h"

// 1. Konfigurasi WiFi
const char* ssid = "NAMA_WIFI_ANDA";
const char* password = "PASSWORD_WIFI_ANDA";

// 2. Konfigurasi Laravel Server
const char* serverUrl = "http://192.168.1.15:8000/api/sensor/weight"; // GANTI DENGAN IP LAPTOP
const String deviceId = "TIMBANGAN-01"; // HARUS SAMA DENGAN DATA DI DATABASE

// 3. Konfigurasi Pin Load Cell
const int LOADCELL_DOUT_PIN = 2;
const int LOADCELL_SCK_PIN = 4;
HX711 scale;

// Faktor Kalibrasi (Cari secara manual!)
float calibration_factor = 2280.f; 

void setup() {
  Serial.begin(115200);

  // Mulai Load Cell
  scale.begin(LOADCELL_DOUT_PIN, LOADCELL_SCK_PIN);
  scale.set_scale(calibration_factor);
  scale.tare(); // Asumsikan timbangan kosong saat dinyalakan
  
  // Sambung WiFi
  Serial.println("Menyambungkan ke WiFi...");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500); Serial.print(".");
  }
  Serial.println("\nWiFi terhubung!");
}

void loop() {
  if (scale.is_ready()) {
    float weight = scale.get_units(5); // Ambil rata-rata 5 bacaan agar stabil
    if(weight < 0) weight = 0.0; // Hindari minus

    Serial.print("Berat Live: ");
    Serial.print(weight);
    Serial.println(" KG"); // Ubah sesuai satuan (misal: gram ke KG)

    // Kirim ke Laravel
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(serverUrl);
      http.addHeader("Content-Type", "application/json");

      String jsonPayload = "{\"device_id\":\"" + deviceId + "\",\"weight\":" + String(weight, 3) + "}";
      int httpResponseCode = http.POST(jsonPayload);

      if (httpResponseCode > 0) {
        Serial.println("Data terkirim!");
      } else {
        Serial.println("Gagal mengirim: " + http.errorToString(httpResponseCode));
      }
      http.end();
    } else {
      Serial.println("WiFi terputus!");
    }
  }
  
  // Kirim data setiap 1 detik
  delay(1000); 
}
```

3. **Upload Kode ke ESP32:**
   Pilih *Board* `ESP32 Dev Module` dan port yang sesuai, lalu klik *Upload*. Buka *Serial Monitor* (baudrate 115200) untuk mengecek apakan data sudah berhasil dibaca dan dikirim!

---

## TAHAP 3: Arsitektur Real-Time dengan WebSockets (Laravel Reverb)

Sistem Web Frontend untuk Operator Timbangan (diakses di `/dashboard` atau `/`) kini menggunakan arsitektur **WebSocket** berbasis Laravel Reverb, yang memberikan pengalaman 100% Real-Time.

**Alur Kerja Data:**
1. **Hardware (ESP32):** Membaca berat dari sensor HX711 setiap 1 detik.
2. **REST API:** ESP32 mengirim HTTP POST ke `/api/sensor/weight` dengan payload `{"device_id": "TIMBANGAN-01", "weight": 2.5}`.
3. **Laravel Controller:** `DeviceController` menerima data tersebut, menyimpannya untuk BOM yang sedang *Pending*, dan secara otomatis men- *trigger* Event Broadcast (`WeightReceived` & `CostingUpdated`).
4. **WebSocket Server:** Laravel Reverb (berjalan di port 8080) menerima Event Broadcast dari backend.
5. **React Frontend:** Aplikasi Operator Timbangan yang terhubung ke channel WebSocket Reverb akan langsung menerima *push notification* tersebut dan mengupdate angka berat serta tabel BOM di layar secara instan **tanpa perlu me-refresh halaman (polling)**.

**Cara Menjalankan Server Lengkap (Butuh 2 Terminal):**

```bash
# Terminal 1: Menjalankan Backend API Laravel
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2: Menjalankan WebSocket Server (Reverb)
php artisan reverb:start --host=0.0.0.0 --port=8080
```

---
Selesai! Sekarang sistem Anda difokuskan murni pada integrasi Hardware IoT C++ & Backend Laravel yang super cepat dengan WebSockets.