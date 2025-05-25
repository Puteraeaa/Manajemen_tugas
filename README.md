
## 🛠️ Cara Instalasi TaskFlow Secara Lokal

> Berikut langkah-langkah untuk menjalankan project Laravel + Filament + Shield ini secara lokal.

---

### 🔧 1. Clone Project

```bash
git clone https://github.com/Puteraeaa/Manajemen_tugas
cd Manajemen_tugas
```

---

### 📦 2. Install Dependencies

```bash
composer install
npm install && npm run dev
```

> ⏳ Tunggu sampai proses selesai, biasanya makan waktu 1-2 menit.

---

### 🔐 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Lalu buka file `.env` dan atur koneksi database:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_system
DB_USERNAME=root
DB_PASSWORD=
```

---

### 🧱 4. Migrasi Database

```bash
php artisan migrate
```

---

### 🛡️ 5. Generate Permission (Filament Shield)

```bash
php artisan shield:generate --all
```

> Ini akan generate permission, policy, dan assign secara otomatis.

---

### 👤 6. Buat Admin Pertama

```bash
php artisan make:filament-user
```

Isi sesuai prompt:
- Nama
- Email
- Password

---

### 👑 7. Jadikan User Tersebut Super Admin

```bash
php artisan shield:super-admin user@gmail.com
```

> Ganti `user@gmail.com` dengan email yang kamu isi saat create user.

---

### 🚀 8. Jalankan Aplikasi

```bash
php artisan serve
```

Akses di browser: [http://localhost:8000/admin](http://localhost:8000/admin)

---

### ✅ Done!

Sekarang kamu bisa login sebagai Super Admin dan mulai atur proyek, buat tugas, kirim notifikasi, dan mantau progres tim langsung dari dashboard. 🎯
