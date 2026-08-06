Portal Gengz Mijen
Version 2.0

=========================================
1. Tentang Project
=========================================

=========================================
2. Prinsip Project
=========================================

=========================================
3. Struktur Database
=========================================

=========================================
4. Struktur Folder
=========================================

=========================================
5. Flow Homepage
=========================================

=========================================
6. Manage Mode
=========================================

=========================================
7. Roadmap Sprint
=========================================

=========================================
8. Coding Convention
=========================================

=========================================
9. Keputusan Arsitektur
=========================================

=========================================
10. Todo Future
=========================================

## Decision #001

Semua CardLink dibuka menggunakan target="_blank".

Reason:

- Konsisten
- Tidak perlu field open_in_new_tab
- Database lebih sederhana

Status:

Approved

## Decision #002

Category menggunakan nama warna, bukan HEX.

Reason:

- Mudah mengganti tema
- Database lebih bersih

Status:

Approved

## Decision #003

Icon menggunakan nama Lucide.

Reason:

- Frontend yang menentukan SVG
- Database tetap ringan

Status:

Approved

# Golden Rules

- Tidak ada Login
- Tidak ada Admin Panel
- Semua pengelolaan menggunakan Security Code
- Semua Link dibuka pada tab baru
- Minimal satu CardLink pada setiap Card
- Category boleh kosong
- Card tidak boleh kosong


# Project Structure
Portal

↓

Category

↓

Card

↓

CardLink

| Feature       | Status |
| ------------- | ------ |
| Homepage      | ✅      |
| Category      | 🚧     |
| Card          | 🚧     |
| Multi Link    | 🚧     |
| Expired       | 🚧     |
| Security Code | 🚧     |
| Drag & Drop   | ⏳      |
| Import JSON   | ⏳      |
| Export JSON   | ⏳      |
| Backup        | ⏳      |


Frontend

Homepage
    ↓
Repository
    ↓
Blade
    ↓
State
    ↓
Render Engine