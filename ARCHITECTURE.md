# Architecture Lock — V1

## Keputusan utama

V1 menggunakan arsitektur **hybrid AI-optional**. Semua fungsi inti harus tetap berjalan tanpa AI. AI hanya boleh masuk setelah retrieval dan evidence extraction selesai.

## Pipeline

1. `QueryExpansionService`
2. `CrossrefService` / `OpenAlexService`
3. `ArticleDeduplicationService`
4. `SintaRegistryService`
5. `RelevanceRankingService`
6. `EvidenceExtractionService`
7. `LiteratureSynthesisService`
8. `CitationFormatterService`

## Grounding rule

- `metadata-only`: boleh ditampilkan, tidak boleh dipakai membuat claim detail.
- `abstract`: boleh dipakai untuk claim yang memang didukung kalimat abstrak.
- `full-text`: disiapkan untuk V2.
- AI menerima hanya evidence + metadata sumber yang telah lolos retrieval.

## Data/cache

V1 tidak memerlukan database besar. Cache Laravel menyimpan respons API berdasarkan query/filter. Registry SINTA disimpan lokal sebagai JSON agar mudah dipelihara.

Database dapat ditambahkan di V2 untuk:

- `searches`
- `articles`
- `search_results`
- `evidence`
- `sinta_journals`

## Ranking V1

Skor konservatif berbasis:

- keyword match pada judul
- keyword match pada abstrak
- exact phrase
- ketersediaan abstrak
- DOI
- open-access flag

Ranking sengaja transparan dan mudah diuji. BM25/embedding reranker dapat ditambahkan di V2 tanpa mengubah kontrak service utama.

## AI

AI bukan dependency wajib. Endpoint dibuat OpenAI-compatible agar provider dapat diganti. Temperature rendah dan prompt melarang pembuatan authors/DOI/method/result yang tidak ada pada evidence.

## Research gap

Belum diaktifkan sebagai output utama V1. Gap yang bertanggung jawab membutuhkan agregasi evidence lintas artikel, coverage yang cukup, dan rule untuk membedakan absence-of-evidence dari true gap.

## API economics (checked September 2026)

- Crossref REST API remains publicly accessible without signup; using `mailto` and caching is recommended.
- OpenAlex basic use remains free to start, but search calls have usage pricing and an API key is recommended for a larger daily budget. V1 therefore caches searches aggressively and supports `OPENALEX_API_KEY`.
- AI is optional. With a low-cost model, the cost per synthesis can remain very small because only the selected evidence is sent, not a full corpus.
