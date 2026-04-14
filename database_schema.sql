-- ============================================================
-- DATABASE SCHEMA
-- Smart Timbangan Otomatis — IoT + Computer Vision
-- Laravel 11 + MySQL 8.0
-- ============================================================

CREATE DATABASE IF NOT EXISTS `smart_timbangan`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `smart_timbangan`;

-- ------------------------------------------------------------
-- 1. TABEL: users
--    Menyimpan data akun operator dan admin sistem
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(100)    NOT NULL,
  `email`             VARCHAR(150)    NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP       NULL DEFAULT NULL,
  `password`          VARCHAR(255)    NOT NULL,
  `role`              ENUM('admin', 'operator') NOT NULL DEFAULT 'operator',
  `is_active`         BOOLEAN         NOT NULL DEFAULT TRUE,
  `remember_token`    VARCHAR(100)    NULL,
  `created_at`        TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_users_email`  (`email`),
  INDEX `idx_users_role`   (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Akun operator dan admin sistem';

-- ------------------------------------------------------------
-- 2. TABEL: categories
--    Kategori jenis barang (buah, sayur, dll)
-- ------------------------------------------------------------
CREATE TABLE `categories` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(80)     NOT NULL UNIQUE,
  `slug`       VARCHAR(80)     NOT NULL UNIQUE,
  `created_at` TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Kategori produk (buah, sayur, rempah, dll)';

-- Isi data awal
INSERT INTO `categories` (`name`, `slug`) VALUES
  ('Buah',    'buah'),
  ('Sayuran', 'sayuran'),
  ('Rempah',  'rempah'),
  ('Lainnya', 'lainnya');

-- ------------------------------------------------------------
-- 3. TABEL: products
--    Katalog barang + harga + label kelas YOLO
-- ------------------------------------------------------------
CREATE TABLE `products` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`  BIGINT UNSIGNED NOT NULL,
  `name`         VARCHAR(100)    NOT NULL,
  `yolo_class`   VARCHAR(80)     NOT NULL UNIQUE
                   COMMENT 'Label kelas yang dikenali model YOLOv8 (harus unik)',
  `price_per_kg` DECIMAL(10, 2)  NOT NULL
                   COMMENT 'Harga jual per kilogram dalam Rupiah',
  `unit`         VARCHAR(20)     NOT NULL DEFAULT 'kg'
                   COMMENT 'Satuan timbangan (kg, ons, dll)',
  `image_path`   VARCHAR(255)    NULL
                   COMMENT 'Path gambar referensi produk',
  `description`  TEXT            NULL,
  `is_active`    BOOLEAN         NOT NULL DEFAULT TRUE,
  `created_at`   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_products_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_products_yolo_class`  (`yolo_class`),
  INDEX `idx_products_category`    (`category_id`),
  INDEX `idx_products_active`      (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Katalog produk beserta harga dan label deteksi AI';

-- Contoh data produk
INSERT INTO `products` (`category_id`, `name`, `yolo_class`, `price_per_kg`) VALUES
  (1, 'Apel Merah',    'apple',     18000.00),
  (1, 'Pisang Ambon',  'banana',    12000.00),
  (1, 'Jeruk Siam',    'orange',    15000.00),
  (1, 'Mangga Harum',  'mango',     22000.00),
  (2, 'Tomat',         'tomato',     8000.00),
  (2, 'Cabai Merah',   'chili',     35000.00),
  (2, 'Wortel',        'carrot',     9000.00),
  (2, 'Kentang',       'potato',     8500.00),
  (2, 'Bawang Merah',  'shallot',   30000.00),
  (3, 'Jahe',          'ginger',    20000.00);

-- ------------------------------------------------------------
-- 4. TABEL: devices
--    Registrasi perangkat ESP32 yang terhubung ke sistem
-- ------------------------------------------------------------
CREATE TABLE `devices` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id`       VARCHAR(50)     NOT NULL UNIQUE
                      COMMENT 'ID unik ESP32 (MAC address atau kode custom)',
  `name`            VARCHAR(100)    NOT NULL
                      COMMENT 'Nama lokasi/unit timbangan',
  `location`        VARCHAR(150)    NULL,
  `firmware_version`VARCHAR(20)     NULL,
  `last_online_at`  TIMESTAMP       NULL,
  `is_active`       BOOLEAN         NOT NULL DEFAULT TRUE,
  `created_at`      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_devices_device_id` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registrasi perangkat ESP32 IoT';

-- ------------------------------------------------------------
-- 5. TABEL: weighing_sessions
--    Rekaman setiap sesi penimbangan (dari deteksi s/d konfirmasi)
-- ------------------------------------------------------------
CREATE TABLE `weighing_sessions` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id`        BIGINT UNSIGNED NOT NULL,
  `product_id`       BIGINT UNSIGNED NULL
                       COMMENT 'NULL jika barang tidak dikenali',
  `weight_kg`        DECIMAL(8, 3)   NOT NULL
                       COMMENT 'Berat hasil timbangan dalam kilogram',
  `raw_weight_data`  DECIMAL(8, 3)   NOT NULL
                       COMMENT 'Nilai berat mentah sebelum kalibrasi',
  `detected_class`   VARCHAR(80)     NULL
                       COMMENT 'Label kelas yang terdeteksi YOLOv8',
  `confidence_score` DECIMAL(5, 2)   NULL
                       COMMENT 'Skor kepercayaan deteksi 0.00 - 100.00 (%)',
  `detected_image`   VARCHAR(255)    NULL
                       COMMENT 'Path file gambar saat deteksi',
  `price_per_kg`     DECIMAL(10, 2)  NULL
                       COMMENT 'Snapshot harga saat sesi berlangsung',
  `total_price`      DECIMAL(12, 2)  NULL
                       COMMENT 'Kalkulasi: weight_kg × price_per_kg',
  `status`           ENUM('pending', 'confirmed', 'cancelled', 'timeout')
                       NOT NULL DEFAULT 'pending',
  `notes`            TEXT            NULL,
  `created_at`       TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_sessions_device`
    FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_sessions_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX `idx_sessions_status`     (`status`),
  INDEX `idx_sessions_device`     (`device_id`),
  INDEX `idx_sessions_product`    (`product_id`),
  INDEX `idx_sessions_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Rekaman setiap sesi penimbangan beserta hasil deteksi AI';

-- ------------------------------------------------------------
-- 6. TABEL: transactions
--    Sesi yang telah dikonfirmasi operator → menjadi transaksi sah
-- ------------------------------------------------------------
CREATE TABLE `transactions` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id`     BIGINT UNSIGNED NOT NULL UNIQUE
                     COMMENT 'Satu sesi hanya menghasilkan satu transaksi',
  `operator_id`    BIGINT UNSIGNED NOT NULL,
  `invoice_number` VARCHAR(30)     NOT NULL UNIQUE
                     COMMENT 'Nomor invoice format: TRX-YYYYMMDD-XXXX',
  `payment_method` ENUM('cash', 'transfer', 'qris', 'lainnya')
                     NOT NULL DEFAULT 'cash',
  `total_amount`   DECIMAL(12, 2)  NOT NULL,
  `amount_paid`    DECIMAL(12, 2)  NULL
                     COMMENT 'Uang yang dibayarkan (untuk menghitung kembalian)',
  `change_amount`  DECIMAL(12, 2)  NULL
                     COMMENT 'Kembalian',
  `notes`          TEXT            NULL,
  `created_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_transactions_session`
    FOREIGN KEY (`session_id`) REFERENCES `weighing_sessions` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_transactions_operator`
    FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_transactions_operator`      (`operator_id`),
  INDEX `idx_transactions_invoice`       (`invoice_number`),
  INDEX `idx_transactions_payment`       (`payment_method`),
  INDEX `idx_transactions_created_at`    (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Transaksi yang telah dikonfirmasi operator';

-- ------------------------------------------------------------
-- 7. TABEL: device_logs
--    Log diagnostik pengiriman data dari setiap ESP32
-- ------------------------------------------------------------
CREATE TABLE `device_logs` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id`       BIGINT UNSIGNED NOT NULL,
  `raw_weight`      DECIMAL(8, 3)   NULL,
  `signal_strength` SMALLINT        NULL
                      COMMENT 'Kekuatan sinyal WiFi dalam dBm (negatif)',
  `ip_address`      VARCHAR(45)     NULL
                      COMMENT 'IPv4 atau IPv6 perangkat',
  `firmware_version`VARCHAR(20)     NULL,
  `log_type`        ENUM('weight', 'heartbeat', 'error', 'boot')
                      NOT NULL DEFAULT 'weight',
  `message`         TEXT            NULL
                      COMMENT 'Pesan error atau info tambahan',
  `created_at`      TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_logs_device`
    FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX `idx_logs_device`      (`device_id`),
  INDEX `idx_logs_type`        (`log_type`),
  INDEX `idx_logs_created_at`  (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Log diagnostik dan heartbeat dari perangkat ESP32';

-- ------------------------------------------------------------
-- 8. TABEL: price_histories
--    Riwayat perubahan harga produk (untuk audit dan laporan)
-- ------------------------------------------------------------
CREATE TABLE `price_histories` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`    BIGINT UNSIGNED NOT NULL,
  `changed_by`    BIGINT UNSIGNED NOT NULL
                    COMMENT 'User yang mengubah harga',
  `old_price`     DECIMAL(10, 2)  NOT NULL,
  `new_price`     DECIMAL(10, 2)  NOT NULL,
  `reason`        VARCHAR(255)    NULL,
  `created_at`    TIMESTAMP       NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_price_history_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_price_history_user`
    FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_price_hist_product`    (`product_id`),
  INDEX `idx_price_hist_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Riwayat perubahan harga produk untuk keperluan audit';

-- ------------------------------------------------------------
-- VIEW: v_transaction_summary
--    Ringkasan transaksi untuk laporan dashboard
-- ------------------------------------------------------------
CREATE VIEW `v_transaction_summary` AS
SELECT
  t.id                                    AS transaction_id,
  t.invoice_number,
  p.name                                  AS product_name,
  c.name                                  AS category_name,
  ws.weight_kg,
  ws.confidence_score,
  ws.total_price,
  t.payment_method,
  t.amount_paid,
  t.change_amount,
  u.name                                  AS operator_name,
  d.name                                  AS device_name,
  d.location                              AS device_location,
  t.created_at                            AS transaction_time
FROM `transactions` t
  JOIN `weighing_sessions` ws ON t.session_id = ws.id
  LEFT JOIN `products`    p  ON ws.product_id = p.id
  LEFT JOIN `categories`  c  ON p.category_id = c.id
  JOIN `users`            u  ON t.operator_id = u.id
  JOIN `devices`          d  ON ws.device_id = d.id;

-- ============================================================
-- END OF SCHEMA
-- ============================================================
