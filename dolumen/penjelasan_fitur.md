# Penjelasan Fitur - Sistem Manajemen Tugas

## 1. Manajemen Proyek & Tugas

- Admin dapat membuat proyek baru.
- Setiap proyek dapat memiliki banyak tugas.
- Tugas bisa diberikan ke user tertentu berdasarkan proyek.
- Fitur pemberian tugas **personal (tanpa proyek)** juga tersedia, khusus untuk HR/Admin.
- Tugas memiliki:
  - Judul
  - Deskripsi
  - Status: To Do, In Progress, Done
  - Deadline
  - Prioritas
  - Pemberi tugas (`assigned_by`)
  - Lampiran opsional

## 2. Role-Based Access Control (RBAC)

- Menggunakan plugin Filament Shield
- Role:
  - `Admin`: akses penuh semua modul
  - `HR`: dapat assign tugas personal
  - `User`: hanya bisa melihat & mengerjakan tugas miliknya
- Permission dikontrol otomatis via Filament Shield

## 3. Ringkasan Status & Statistik

- Menampilkan grafik status tugas:
  - Berdasarkan user
  - Berdasarkan proyek
- Menggunakan ChartWidget bawaan Filament

## 4. Notifikasi

- Saat tugas dibuat, user yang ditugaskan akan mendapat notifikasi database melalui Filament Notification.
- Tampilan notifikasi tersedia di top bar UI.

## 5. Validasi & Keamanan

- Validasi pada form pembuatan tugas: field wajib diisi, deadline valid, dll.
- Foreign key `project_id` dan `assigned_by` sudah menggunakan UUID dan aman dihapus (`onDelete('cascade')`).

