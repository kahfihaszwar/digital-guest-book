# Publikasi Buku Tamu

## Yang aktif sekarang: Railway (tanpa kartu)

- URL publik: **https://buku-tamu-production-e0f2d7f8.up.railway.app**
- Project: `buku-tamu` (workspace `kahfihaszwar's Projects`), environment `production`
- Deploy dari folder lokal, bukan dari GitHub:
  ```bash
  railway up -y --service buku-tamu
  ```
  (CLI portable ada di `.tools/railway/railway.exe`)

### Environment variables di Railway

| Key | Nilai |
| --- | --- |
| `APP_NAME` | `Buku Tamu` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | base64 key (51 karakter, tanpa `\r`) |
| `APP_URL` | URL Railway di atas |
| `LOG_CHANNEL` | `stderr` |
| `DB_CONNECTION` | `sqlite` |
| `SESSION_DRIVER` | `file` |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |

Memakai `file`/`sync` supaya tidak bergantung pada tabel database. SQLite dibuat ulang setiap container start, jadi data tidak bertahan antar deploy — kalau nanti buku tamu menyimpan entri, tambahkan Postgres milik Railway.

### Hal yang sempat bikin gagal (jangan diubah tanpa alasan)

1. **Port**: container mendengarkan `$PORT` (Railway menyuntik 8080). `Dockerfile` memakai placeholder `@APP_PORT@` yang diisi `docker/entrypoint.sh`.
2. **MPM ganda**: image `php:8.4-apache` di runtime ternyata mengaktifkan `mpm_event` **dan** `mpm_prefork`, sehingga Apache mati dengan `AH00534: More than one MPM loaded`. Entrypoint sekarang menghapus `mpm_event`/`mpm_worker` sebelum start.
3. **APP_KEY**: key yang dibuat lewat PowerShell/Windows bisa membawa `\r` sehingga muncul `Unsupported cipher or incorrect key length`. Key harus tepat 51 karakter.

## Catatan paket gratis Railway

- Tanpa kartu: trial **$5 / 30 hari**, setelah itu Free plan memberi **$1 credit per bulan** — app ini butuh sekitar $2–3/bulan, jadi siap-siap pindah ke Hobby ($5/bln) atau host lain.
- URL `*.up.railway.app` tetap selama service aktif.

## Alternatif: Cloudflare Tunnel

Cloudflare Workers tidak bisa menjalankan PHP, jadi jalur gratis di Cloudflare adalah tunnel dari PC ini:

```bash
sh serve-public.sh
```

URL `https://<kata-acak>.trycloudflare.com` muncul di terminal dan **berganti** setiap dijalankan ulang. PC harus menyala. URL tetap butuh domain sendiri yang Nameserver-nya di Cloudflare + named tunnel.

## Alternatif: Render

`render.yaml` sudah disiapkan (web service free, region Singapore, health check `/up`), tetapi pembuatan service lewat API ditolak Render dengan pesan *"Payment information is required"* — workspace-nya belum punya metode pembayaran. Plan Free Render tetap mewajibkan kartu terdaftar.
