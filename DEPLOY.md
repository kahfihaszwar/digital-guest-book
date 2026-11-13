# Deploy Buku Tamu ke Render (subdomain gratis)

Tujuan: website bisa diakses publik di **https://buku-tamu.onrender.com** tanpa biaya.

## File yang sudah disiapkan

| File | Fungsi |
| --- | --- |
| `Dockerfile` | Build 3 tahap: aset Vite/Tailwind → dependency Composer → runtime PHP 8.4 + Apache (listen port `10000`, sesuai default Render) |
| `docker/apache-vhost.conf` | DocumentRoot diarahkan ke `public/` dan `mod_rewrite` aktif |
| `docker/entrypoint.sh` | Jalankan `config:cache` + `migrate --force` setiap container start, lalu Apache |
| `render.yaml` | Blueprint Render: web service **free**, region Singapore, Docker, health check `/up`, env produksi (`APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` digenerate otomatis) |
| `.dockerignore` | Menyingkirkan `vendor`, `node_modules`, `.env`, build lokal dari konteks Docker |

SQLite dipakai sebagai database; pada paket free disk bersifat sementara, jadi skema dibuat ulang setiap deploy.
Session & cache memakai driver `file` supaya tidak bergantung pada tabel database.

## Langkah aktivasi (butuh akun Render kamu, ±5 menit)

1. **Push kode ke GitHub** (Render deploy dari repo Git):
   ```bash
   git remote add origin https://github.com/<username>/digital-guest-book.git
   git push -u origin main
   ```
2. Buka [dashboard.render.com](https://dashboard.render.com) → **New** → **Blueprint** → pilih repo di atas.
   Render membaca `render.yaml` dan membuat service `buku-tamu` (plan Free).
3. Klik **Apply** / **Create**, tunggu build pertama selesai (±5–10 menit karena build Docker).
4. URL publik otomatis: **https://buku-tamu.onrender.com** (subdomain mengikuti nama service; kalau sudah dipakai orang lain, ganti `name:` di `render.yaml`).
5. Setelah service hidup, pastikan `APP_URL` di Environment sudah `https://buku-tamu.onrender.com` — kalau nama service diganti, ubah juga nilai ini.

## Catatan paket gratis Render

- Instance tidur setelah ±15 menit tanpa traffic; request pertama setelah tidur butuh ±1 menit untuk bangun.
- Disk sementara: data yang ditulis ke SQLite hilang saat redeploy/restart. Kalau nanti buku tamu sudah menyimpan entri, pakai Render Postgres (atau Neon) supaya data permanen.
- Domain kustom (mis. `bukutamu.my.id`) bisa ditambahkan nanti di **Settings → Custom Domains** setelah domain dibeli.
