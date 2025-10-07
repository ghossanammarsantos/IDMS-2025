# 🏗️ IDMS — Integrated Depot Management System

**IDMS (Integrated Depot Management System)** adalah aplikasi berbasis web yang digunakan untuk mengelola seluruh aktivitas operasional depot kontainer secara terintegrasi — mulai dari proses **Gate In**, **Survey In**, **Survey Out**, hingga **Gate Out**, termasuk fitur **laporan otomatis harian**.

---

## 🚀 About Project

Aplikasi ini dikembangkan untuk mendigitalisasi proses manual di lapangan dan meningkatkan efisiensi pengelolaan kontainer di depot.  
Dengan IDMS, seluruh kegiatan operasional bisa terpantau secara real-time dan terdokumentasi rapi.

### 🎯 Tujuan Utama:
- Meningkatkan kecepatan dan akurasi pencatatan kontainer.
- Meminimalkan kesalahan input manual.
- Mempermudah proses audit dan pelaporan harian.
- Menyediakan data operasional yang terintegrasi antar divisi.

---

## ⚙️ Fitur Utama

- 📦 **Gate In Management**  
  Pencatatan kontainer yang masuk ke depot beserta data kendaraan dan sopir.

- 🧾 **Survey In / Survey Out**  
  Pemeriksaan kondisi kontainer masuk dan keluar, termasuk foto bukti dan status kerusakan.

- 🚛 **Gate Out Management**  
  Monitoring kontainer yang keluar dari area depot dengan detail waktu dan petugas (PIC).

- 📊 **Automatic Reporting**  
  Sistem menghasilkan laporan survey harian dan mengirimkan secara otomatis melalui email.

- 💰 **Tarif & Customer Data Management**  
  Mengatur data pelanggan, vendor, serta tarif depo yang berlaku.

- 🔐 **Role-Based Access Control (RBAC)**  
  Pengaturan hak akses sistem berdasarkan level pengguna (Administrator, Petugas Survey, Admin Staff).

---

## 🔁 Flow Singkat IDMS

flowchart LR
    [Gate In] --> [Survey In] --> [Stack/Storage] --> [Survey Out] --> [Gate Out] --> [Reporting & Export]
Penjelasan Alur:
    Gate In → Kontainer tiba di depot, dicatat oleh petugas gate.
    Survey In → Tim survey memeriksa kondisi fisik kontainer saat masuk.
    Stack/Storage → Penumpukan di dalam depot container
    Survey Out → Setelah penyimpanan atau perbaikan, kontainer diperiksa kembali sebelum keluar.
    Gate Out → Petugas mencatat waktu dan data kontainer saat keluar.
    Reporting → Sistem menghasilkan laporan otomatis harian/mingguan.


| Komponen                 | Teknologi                           |
| ------------------------ | ----------------------------------- |
| **Backend**              | Laravel 7 (PHP 7.2.5)               |
| **Database**             | Oracle Database                     |
| **Frontend**             | Blade Template, Bootstrap 5, jQuery |
| **Scheduler / Cron Job** | Laravel Task Scheduler              |
| **Version Control**      | Git & GitHub                        |
| **IDE / Tools**          | Visual Studio Code, GitHub Desktop  |


📧 Author
👨‍💻 Developer: Ghossan Ammar Santos
📍 Integrated Depot Management System (IDMS)
📬 Email: [ghossan@perserobatam.com]
🌐 Repository: [https://github.com/ghossanammarsantos/idms]
