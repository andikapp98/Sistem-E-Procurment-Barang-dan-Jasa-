# 📚 Index Dokumentasi - Sistem e-Procurement

Panduan lengkap untuk semua dokumentasi aplikasi e-Procurement RSUD Ibnu Sina.

---

## 🗂️ Daftar Dokumentasi

### 📘 Dokumentasi Utama

| No | Dokumentasi | Deskripsi | Untuk | Link |
|----|------------|-----------|-------|------|
| 1 | **README.md** | Overview aplikasi, tech stack, fitur | Semua | [Buka](README.md) |
| 2 | **INSTALASI.md** | Panduan instalasi lengkap dev & production | Developer, SysAdmin | [Buka](INSTALASI.md) |
| 3 | **PENGGUNAAN.md** | Panduan penggunaan untuk end-user | User, Staff RS | [Buka](PENGGUNAAN.md) |
| 4 | **QUICK_REFERENCE.md** | Referensi cepat command, URL, dll | Developer, Admin | [Buka](QUICK_REFERENCE.md) |
| 5 | **DEPLOYMENT_CHECKLIST.md** | Checklist deployment ke production | SysAdmin, DevOps | [Buka](DEPLOYMENT_CHECKLIST.md) |
| 6 | **PANDUAN_GIT.md** | Panduan upload ke Git/GitHub lengkap | Developer, Team | [Buka](PANDUAN_GIT.md) |

### 📗 Dokumentasi Data

| No | Dokumentasi | Deskripsi | Untuk | Link |
|----|------------|-----------|-------|------|
| 7 | **CONTOH_DATA_IGD.md** | 7 contoh permintaan IGD dengan template | User, Tester | [Buka](CONTOH_DATA_IGD.md) |
| 8 | **README_CONTOH_DATA.md** | Panduan menggunakan sample data | Developer, Tester | [Buka](README_CONTOH_DATA.md) |

### 📙 File Teknis

| No | File | Deskripsi | Lokasi |
|----|------|-----------|--------|
| 9 | **IGDPermintaanSeeder.php** | Laravel seeder untuk data IGD | `database/seeders/` |
| 10 | **sample_data_igd.sql** | SQL insert untuk data IGD | `database/` |
| 11 | **.env.example** | Template environment variables | Root |
| 12 | **composer.json** | PHP dependencies | Root |
| 13 | **package.json** | NPM dependencies | Root |
| 14 | **.gitignore** | File yang tidak di-track Git | Root |

---

## 🎯 Quick Navigation

### 🚀 Untuk Developer Baru

**Mulai dari sini:**
1. [README.md](README.md) - Pahami aplikasi
2. [INSTALASI.md](INSTALASI.md) - Setup development
3. [PANDUAN_GIT.md](PANDUAN_GIT.md) - Upload ke Git/GitHub
4. [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Command & referensi
5. [CONTOH_DATA_IGD.md](CONTOH_DATA_IGD.md) - Load sample data

### 👤 Untuk End User

**Panduan penggunaan:**
1. [PENGGUNAAN.md](PENGGUNAAN.md) - Panduan lengkap
2. [CONTOH_DATA_IGD.md](CONTOH_DATA_IGD.md) - Contoh pengisian form

### 🖥️ Untuk System Administrator

**Setup production:**
1. [INSTALASI.md](INSTALASI.md) - Section Production
2. [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Checklist deployment
3. [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Command reference

### 🧪 Untuk QA/Tester

**Testing & data:**
1. [PENGGUNAAN.md](PENGGUNAAN.md) - Alur penggunaan
2. [CONTOH_DATA_IGD.md](CONTOH_DATA_IGD.md) - Data test
3. [README_CONTOH_DATA.md](README_CONTOH_DATA.md) - Seed data

---

## 📖 Cara Membaca Dokumentasi

### Simbol & Notasi

| Simbol | Arti |
|--------|------|
| ✅ | Checklist / To-do item |
| ⚠️ | Peringatan penting |
| 💡 | Tips atau info berguna |
| 🔒 | Terkait keamanan |
| 📝 | Contoh atau template |
| 🚀 | Quick start / Getting started |
| ⚡ | Performance tip |
| 🐛 | Bug fix atau troubleshooting |

### Code Blocks

**Bash/Shell Commands:**
```bash
php artisan migrate
```

**PHP Code:**
```php
$user = User::find(1);
```

**Environment Variables:**
```env
APP_ENV=production
```

**SQL:**
```sql
SELECT * FROM users;
```

---

## 🗺️ Roadmap Dokumentasi

### Version 1.0 (Current) ✅
- [x] README.md
- [x] INSTALASI.md
- [x] PENGGUNAAN.md
- [x] QUICK_REFERENCE.md
- [x] DEPLOYMENT_CHECKLIST.md
- [x] PANDUAN_GIT.md
- [x] CONTOH_DATA_IGD.md
- [x] README_CONTOH_DATA.md
- [x] DOKUMENTASI_INDEX.md

### Version 1.1 (Planned)
- [ ] API_DOCUMENTATION.md
- [ ] TROUBLESHOOTING_GUIDE.md
- [ ] BACKUP_RESTORE.md
- [ ] PERFORMANCE_TUNING.md
- [ ] SECURITY_GUIDELINES.md

### Version 1.2 (Future)
- [ ] VIDEO_TUTORIALS.md (links)
- [ ] FAQ_ADVANCED.md
- [ ] INTEGRATION_GUIDE.md
- [ ] MOBILE_APP_GUIDE.md
- [ ] CHANGELOG.md

---

## 📋 Dokumentasi Template

### Untuk Developer yang Ingin Berkontribusi

Jika Anda ingin menambah dokumentasi baru, gunakan template ini:

```markdown
# [Judul Dokumentasi]

[Deskripsi singkat tentang apa yang dicakup dokumentasi ini]

---

## 📋 Daftar Isi

- [Section 1](#section-1)
- [Section 2](#section-2)
...

---

## [Section 1]

[Konten...]

### Sub-section

[Konten...]

---

## 📞 Support & Bantuan

[Kontak information...]

---

**Versi Dokumentasi:** X.X  
**Terakhir Diperbarui:** [Tanggal]  
**Untuk:** Sistem e-Procurement RSUD Ibnu Sina
```

---

## 🔍 Pencarian Cepat

### Topik Umum

| Topik | Lihat Dokumentasi | Section |
|-------|------------------|---------|
| Instalasi pertama kali | [INSTALASI.md](INSTALASI.md) | Instalasi Development |
| Upload ke GitHub | [PANDUAN_GIT.md](PANDUAN_GIT.md) | Upload Project Baru |
| Login ke sistem | [PENGGUNAAN.md](PENGGUNAAN.md) | Login dan Autentikasi |
| Buat permintaan baru | [PENGGUNAAN.md](PENGGUNAAN.md) | Membuat Permintaan Baru |
| Command Laravel | [QUICK_REFERENCE.md](QUICK_REFERENCE.md) | Command Cheat Sheet |
| Command Git | [PANDUAN_GIT.md](PANDUAN_GIT.md) | Common Git Commands |
| Deploy ke production | [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) | Deployment Steps |
| Error & troubleshooting | [INSTALASI.md](INSTALASI.md) | Troubleshooting |
| Contoh data test | [CONTOH_DATA_IGD.md](CONTOH_DATA_IGD.md) | Semua section |
| Database setup | [INSTALASI.md](INSTALASI.md) | Konfigurasi Database |
| Environment config | [QUICK_REFERENCE.md](QUICK_REFERENCE.md) | Environment Variables |

### Troubleshooting

| Error | Solusi di | Section |
|-------|-----------|---------|
| Permission denied | [INSTALASI.md](INSTALASI.md) | Troubleshooting |
| Vite manifest not found | [INSTALASI.md](INSTALASI.md) | Troubleshooting |
| Database connection failed | [INSTALASI.md](INSTALASI.md) | Troubleshooting |
| No encryption key | [INSTALASI.md](INSTALASI.md) | Troubleshooting |
| 500 Internal Server Error | [INSTALASI.md](INSTALASI.md) | Troubleshooting |

---

## 📊 Statistik Dokumentasi

| Metric | Value |
|--------|-------|
| Total Dokumentasi | 9 files |
| Total Halaman | ~120 pages |
| Total Words | ~18,000 words |
| Code Examples | 200+ |
| Screenshots | 0 (text-based) |
| Last Updated | 16 Oktober 2025 |

---

## 🤝 Kontribusi Dokumentasi

### Cara Berkontribusi

1. **Fork repository**
2. **Buat branch baru** untuk dokumentasi
   ```bash
   git checkout -b docs/nama-dokumentasi
   ```
3. **Tulis dokumentasi** sesuai template
4. **Commit perubahan**
   ```bash
   git commit -m "docs: add [nama dokumentasi]"
   ```
5. **Push ke branch**
   ```bash
   git push origin docs/nama-dokumentasi
   ```
6. **Create Pull Request**

### Guidelines Penulisan

- Gunakan bahasa yang jelas dan sederhana
- Sertakan contoh code yang lengkap
- Tambahkan screenshot jika perlu
- Update index dokumentasi
- Ikuti format Markdown yang konsisten
- Test semua command/code yang didokumentasikan

---

## 📞 Kontak & Support

**Tim Dokumentasi:**
- Technical Writer: -
- Lead Developer: -
- Documentation Review: Tim IT RSUD

**Report Issues:**
- Documentation bugs: Create issue di GitHub
- Suggestion: Email ke it@rsudibsnugresik.id
- Urgent: Kontak Tim IT RSUD

---

## 📄 License

Dokumentasi ini adalah bagian dari Sistem e-Procurement RSUD Ibnu Sina Kabupaten Gresik.

---

**Index Version:** 1.0  
**Last Updated:** 16 Oktober 2025  
**Maintained by:** Tim IT RSUD Ibnu Sina Kabupaten Gresik

---

<p align="center">
  <strong>📚 Happy Documentation Reading! 📚</strong>
</p>
