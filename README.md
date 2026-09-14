# Literature Review Generator

Aplikasi web Laravel untuk membantu menyusun **literature review / penelitian terdahulu** dengan prinsip **retrieval terlebih dahulu, baru synthesis**. Referensi diambil dari sumber artikel nyata, kemudian dideduplikasi, diberi skor relevansi, diekstrak evidence dari abstrak, dan baru disintesis.

## Fitur V1

- Input topik atau judul penelitian
- Live retrieval dari **OpenAlex** dan **Crossref**
- Query expansion sederhana Indonesia–Inggris
- Deduplikasi berdasarkan DOI/judul
- Relevance ranking berbasis lexical evidence
- Evidence extraction dari abstrak
- Status evidence: `abstract` atau `metadata-only`
- Synthesis tanpa AI (template-grounded)
- AI synthesis opsional melalui endpoint OpenAI-compatible
- Sitasi dalam teks dan daftar pustaka gaya APA 7 (draft berbasis metadata)
- Filter tahun, jumlah artikel, sumber, open access, abstrak, dan SINTA level
- Registry SINTA lokal berbasis ISSN
- Export TXT dan Markdown + copy text
- UI Bahasa Indonesia, Blade + Bootstrap
- Tanpa login

## Tech Stack

- PHP 8.2+
- Laravel 12
- Blade
- Bootstrap 5
- Laravel HTTP Client & Cache

## Instalasi

```bash
git clone <URL_REPOSITORY>
cd literature-review-generator
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Buka `http://127.0.0.1:8000`.

> V1 tidak memerlukan database untuk proses utama. Retrieval menggunakan API langsung dan Laravel Cache.

## Konfigurasi API

Crossref dapat digunakan tanpa API key. OpenAlex dapat dicoba tanpa key, tetapi pada kebijakan API saat ini API key direkomendasikan untuk budget penggunaan yang lebih besar. Sebaiknya isi email dan, bila tersedia, API key OpenAlex:

```env
CROSSREF_MAILTO=email@anda.com
OPENALEX_MAILTO=email@anda.com
OPENALEX_API_KEY=opsional_api_key_openalex
```

### AI opsional

Aplikasi tetap dapat digunakan tanpa AI. Jika ingin synthesis berbasis AI, aktifkan konfigurasi berikut:

```env
LITREVIEW_AI_ENABLED=true
LITREVIEW_AI_BASE_URL=https://api.openai.com/v1
LITREVIEW_AI_API_KEY=...
LITREVIEW_AI_MODEL=gpt-5-mini
```

AI hanya menerima paket evidence dari artikel yang sudah ditemukan. AI **bukan sumber referensi**.

## Registry SINTA

Isi file:

`storage/app/data/sinta_journals.json`

Format contoh tersedia pada:

`storage/app/data/sinta_journals.example.json`

Contoh:

```json
[
  {
    "name": "Nama Jurnal",
    "level": 2,
    "issn": ["1234-5678", "8765-4321"],
    "url": "https://journal.example.org"
  }
]
```

Artikel hasil retrieval dicocokkan ke registry berdasarkan ISSN. Aplikasi tidak melakukan scraping SINTA secara otomatis.

## Workflow

```text
Topik/Judul
→ Query Expansion
→ OpenAlex/Crossref
→ Deduplication
→ SINTA Enrichment
→ Relevance Ranking
→ Abstract Evidence Extraction
→ Grounded Synthesis
→ APA Reference List
```

## Testing

```bash
php artisan test
```

Tersedia unit test untuk query expansion, deduplikasi, ranking, citation formatter, serta feature test halaman utama.

## Catatan V1

- Full-text extraction belum menjadi default; evidence berasal dari abstrak.
- Artikel metadata-only tidak dipakai untuk membuat claim detail.
- Formatter APA menggunakan metadata yang tersedia dan tetap perlu diverifikasi.
- Research-gap otomatis sengaja belum diaktifkan pada V1 karena membutuhkan corpus/evidence yang cukup agar tidak menghasilkan gap generik.
- Synthesis tanpa AI cenderung konservatif dan lebih mirip evidence summary daripada prose akademik final.

## Disclaimer Akademik

Aplikasi ini adalah alat bantu penyusunan literature review, bukan pengganti membaca artikel asli. Referensi, DOI, metadata, evidence, interpretasi, dan sitasi harus diverifikasi sebelum digunakan dalam karya akademik. Pengguna bertanggung jawab terhadap penggunaan hasil aplikasi.

## License

MIT

## Troubleshooting HTTP 500

Jika halaman awal menampilkan HTTP 500 setelah instalasi:

```bash
php artisan optimize:clear
php artisan key:generate
php artisan serve
```

Pastikan `.env` sudah ada dan `APP_KEY` terisi. Untuk melihat error sebenarnya:

```bash
php artisan about
php artisan route:list
```

Log aplikasi tersedia di `storage/logs/laravel.log`.
