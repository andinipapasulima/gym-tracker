-- ============================================================
-- WZONE GYM TRACKER — Migration Script
-- Jalankan ini di phpMyAdmin pada database `gym_tracker`.
-- Aman dijalankan meskipun tabel lama sudah berisi data,
-- kolom lama TIDAK dihapus, hanya ditambah kolom baru.
-- ============================================================

-- 1. Perkuat tabel catatan_latihan yang sudah ada
ALTER TABLE catatan_latihan
    ADD COLUMN IF NOT EXISTS kategori VARCHAR(50) NOT NULL DEFAULT 'Lainnya' AFTER gerakan,
    ADD COLUMN IF NOT EXISTS sets INT NOT NULL DEFAULT 1 AFTER beban,
    ADD COLUMN IF NOT EXISTS reps INT NOT NULL DEFAULT 0 AFTER sets,
    ADD COLUMN IF NOT EXISTS catatan VARCHAR(255) NULL AFTER repetisi,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER catatan;

-- 2. Tabel baru untuk body weight / body log tracker
CREATE TABLE IF NOT EXISTS body_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    berat_badan DECIMAL(5,2) NOT NULL,
    catatan VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Index untuk mempercepat query dashboard & chart progress
CREATE INDEX IF NOT EXISTS idx_tanggal ON catatan_latihan (tanggal);
CREATE INDEX IF NOT EXISTS idx_gerakan ON catatan_latihan (gerakan);
CREATE INDEX IF NOT EXISTS idx_body_tanggal ON body_log (tanggal);
