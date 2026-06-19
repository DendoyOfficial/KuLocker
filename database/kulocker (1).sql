-- ============================================================
--  KuLocker Database Schema
--  Sistem Manajemen Loker
-- ============================================================

CREATE DATABASE IF NOT EXISTS kulocker
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE kulocker;

-- ------------------------------------------------------------
-- 1. USERS
-- ------------------------------------------------------------
CREATE TABLE users (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nama        VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL,
    password    VARCHAR(255)    NOT NULL COMMENT 'bcrypt hash',
    role        ENUM('mahasiswa','admin') NOT NULL DEFAULT 'mahasiswa',
    nim         VARCHAR(20)     NULL COMMENT 'Khusus mahasiswa',
    no_hp       VARCHAR(15)     NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. LOCKERS
-- ------------------------------------------------------------
CREATE TABLE lockers (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    kode_loker  VARCHAR(10)     NOT NULL COMMENT 'Contoh: A-01',
    lokasi      VARCHAR(100)    NOT NULL COMMENT 'Contoh: Gedung A Lt.2',
    ukuran      ENUM('S','M','L') NOT NULL,
    status      ENUM('tersedia','terpakai','rusak') NOT NULL DEFAULT 'tersedia',
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_lockers_kode (kode_loker)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. PEMESANAN
-- ------------------------------------------------------------
CREATE TABLE pemesanan (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED    NOT NULL,
    locker_id       INT UNSIGNED    NOT NULL,
    tanggal_mulai   DATE            NOT NULL,
    tanggal_selesai DATE            NOT NULL,
    status          ENUM('pending','aktif','selesai','dibatalkan') NOT NULL DEFAULT 'pending',
    kode_akses      VARCHAR(20)     NOT NULL COMMENT 'PIN / token akses',
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT fk_pemesanan_user
        FOREIGN KEY (user_id)   REFERENCES users   (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_pemesanan_locker
        FOREIGN KEY (locker_id) REFERENCES lockers (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. AKSES LOG
-- ------------------------------------------------------------
CREATE TABLE akses_log (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    pemesanan_id    INT UNSIGNED    NOT NULL,
    user_id         INT UNSIGNED    NOT NULL,
    waktu_akses     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    jenis           ENUM('buka','tutup') NOT NULL,
    status          ENUM('berhasil','gagal') NOT NULL,
    keterangan      TEXT            NULL,

    PRIMARY KEY (id),
    CONSTRAINT fk_akseslog_pemesanan
        FOREIGN KEY (pemesanan_id) REFERENCES pemesanan (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_akseslog_user
        FOREIGN KEY (user_id)      REFERENCES users     (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. NOTIFIKASI
-- ------------------------------------------------------------
CREATE TABLE notifikasi (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED    NOT NULL,
    judul       VARCHAR(150)    NOT NULL,
    pesan       TEXT            NOT NULL,
    jenis       ENUM('info','peringatan','pengingat') NOT NULL DEFAULT 'info',
    is_read     TINYINT(1)      NOT NULL DEFAULT 0,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT fk_notifikasi_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. KELUHAN
-- ------------------------------------------------------------
CREATE TABLE keluhan (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED    NOT NULL,
    locker_id       INT UNSIGNED    NULL,
    pemesanan_id    INT UNSIGNED    NULL,
    judul           VARCHAR(150)    NOT NULL,
    deskripsi       TEXT            NOT NULL,
    status          ENUM('open','proses','selesai') NOT NULL DEFAULT 'open',
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT fk_keluhan_user
        FOREIGN KEY (user_id)      REFERENCES users     (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_keluhan_locker
        FOREIGN KEY (locker_id)    REFERENCES lockers   (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_keluhan_pemesanan
        FOREIGN KEY (pemesanan_id) REFERENCES pemesanan (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. KELUHAN RESPONSE
-- ------------------------------------------------------------
CREATE TABLE keluhan_response (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    keluhan_id  INT UNSIGNED    NOT NULL,
    admin_id    INT UNSIGNED    NOT NULL,
    pesan       TEXT            NOT NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT fk_keluhanresp_keluhan
        FOREIGN KEY (keluhan_id) REFERENCES keluhan (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_keluhanresp_admin
        FOREIGN KEY (admin_id)   REFERENCES users   (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. PEMBAYARAN (opsional)
-- ------------------------------------------------------------
CREATE TABLE pembayaran (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    pemesanan_id    INT UNSIGNED    NOT NULL,
    jumlah          DECIMAL(10,2)   NOT NULL,
    metode          ENUM('transfer','qris','tunai') NOT NULL,
    status          ENUM('pending','lunas','gagal') NOT NULL DEFAULT 'pending',
    bukti           VARCHAR(255)    NULL COMMENT 'Path file bukti transfer',
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT fk_pembayaran_pemesanan
        FOREIGN KEY (pemesanan_id) REFERENCES pemesanan (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  SAMPLE DATA (opsional untuk testing)
-- ============================================================

-- Admin default (password: admin123 - ganti setelah deploy!)
INSERT INTO users (nama, email, password, role, nim, no_hp) VALUES
('Admin KuLocker', 'admin@kulocker.ac.id', '$2y$10$exampleHashedPasswordHere', 'admin', NULL, '081234567890');

-- Contoh loker
INSERT INTO lockers (kode_loker, lokasi, ukuran, status) VALUES
('A-01', 'Gedung A Lt.1', 'S', 'tersedia'),
('A-02', 'Gedung A Lt.1', 'S', 'tersedia'),
('A-03', 'Gedung A Lt.1', 'M', 'tersedia'),
('B-01', 'Gedung B Lt.2', 'M', 'tersedia'),
('B-02', 'Gedung B Lt.2', 'L', 'tersedia'),
('B-03', 'Gedung B Lt.2', 'L', 'tersedia');
