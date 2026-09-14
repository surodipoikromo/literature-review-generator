@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
        <div class="smallcaps text-secondary">Topik</div>

        <h1 class="h3 mb-1">
            {{ $input['query'] }}
        </h1>

        <div class="text-secondary">
            {{ count($result['selected']) }} artikel terpilih dari {{ count($result['all']) }} hasil unik
        </div>
    </div>

    <a
        class="btn btn-outline-dark"
        href="{{ route('literature.index') }}"
    >
        Pencarian Baru
    </a>
</div>

@if($result['errors'])
    <div class="alert alert-warning">
        <strong>Sebagian sumber gagal diakses.</strong>

        <ul class="mb-0">
            @foreach($result['errors'] as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="smallcaps text-secondary mb-1">
            Query expansion
        </div>

        <div class="d-flex gap-2 flex-wrap">
            @foreach($result['queries'] as $q)
                <span class="badge rounded-pill text-bg-light border">
                    {{ $q }}
                </span>
            @endforeach
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h2 class="h4 mb-1">
                    Literature Review
                </h2>

                <div class="text-secondary small">
                    Engine: {{ $result['syn']['engine'] }}
                </div>
            </div>

            <div class="btn-group">
                <button
                    class="btn btn-sm btn-outline-secondary"
                    onclick="copyReview()"
                >
                    Copy
                </button>

                <a
                    class="btn btn-sm btn-outline-secondary"
                    href="{{ route('literature.export', 'txt') }}"
                >
                    TXT
                </a>

                <a
                    class="btn btn-sm btn-outline-secondary"
                    href="{{ route('literature.export', 'md') }}"
                >
                    Markdown
                </a>
            </div>
        </div>

        <div
            id="reviewText"
            class="mt-3"
            style="white-space: pre-line"
        >
            {{ $result['syn']['text'] }}
        </div>

    </div>
</div>

<h2 class="h4 mb-3">
    Artikel Terpilih
</h2>

@forelse($result['selected'] as $i => $a)

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">

            <div class="d-flex justify-content-between gap-3">

                <div class="flex-grow-1">

                    <div class="small text-secondary mb-1">
                        {{ $a['source'] }}
                        ·
                        {{ $a['year'] ?? 'n.d.' }}

                        @if($a['sinta'])
                            · SINTA {{ $a['sinta']['level'] }}
                        @endif
                    </div>

                    <h3 class="h5 article-title">
                        {{ $a['title'] }}
                    </h3>

                    <div class="small mb-2">
                        {{ implode(', ', $a['authors'] ?? []) ?: 'Penulis tidak tersedia' }}
                    </div>

                    <div class="small text-secondary">
                        {{ $a['journal'] ?: 'Nama jurnal tidak tersedia' }}
                    </div>

                </div>

                <div class="text-center">
                    <span class="badge text-bg-dark score">
                        {{ $a['relevance_score'] }}
                    </span>

                    <div class="small text-secondary mt-1">
                        relevansi
                    </div>
                </div>

            </div>

            <div class="mt-3 small">
                <strong>Alasan:</strong>
                {{ $a['relevance_reason'] }}
            </div>

            <div class="mt-2 d-flex gap-2 flex-wrap">

                @if($a['doi'])
                    <a
                        class="btn btn-sm btn-outline-primary"
                        target="_blank"
                        rel="noopener noreferrer"
                        href="https://doi.org/{{ $a['doi'] }}"
                    >
                        DOI
                    </a>
                @endif

                @if($a['url'])
                    <a
                        class="btn btn-sm btn-outline-secondary"
                        target="_blank"
                        rel="noopener noreferrer"
                        href="{{ $a['url'] }}"
                    >
                        Sumber
                    </a>
                @endif

                <span
                    class="badge {{ $a['evidence_level'] === 'abstract' ? 'text-bg-success' : 'text-bg-secondary' }} align-self-center"
                >
                    {{ $a['evidence_level'] === 'abstract' ? 'Abstract evidence' : 'Metadata-only' }}
                </span>

            </div>

            @if($a['evidence'])

                <div class="mt-3">
                    <div class="smallcaps text-secondary mb-2">
                        Evidence
                    </div>

                    @foreach($a['evidence'] as $ev)
                        <div class="evidence p-3 mb-2 small">
                            {{ $ev['text'] }}
                        </div>
                    @endforeach
                </div>

            @elseif($a['abstract'])

                <details class="mt-3">
                    <summary class="small fw-semibold">
                        Lihat abstrak
                    </summary>

                    <div class="small mt-2 text-secondary">
                        {{ $a['abstract'] }}
                    </div>
                </details>

            @endif

        </div>
    </div>

@empty

    <div class="alert alert-info">
        Tidak ada artikel yang memenuhi filter.
        Coba perluas tahun, matikan filter abstrak atau SINTA,
        atau gunakan istilah yang lebih umum.
    </div>

@endforelse

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">

        <h2 class="h4">
            Daftar Pustaka — APA 7
        </h2>

        <ol class="mb-0">
            @foreach($result['refs'] as $ref)
                <li class="mb-2">
                    {{ $ref }}
                </li>
            @endforeach
        </ol>

        <div class="alert alert-light border small mt-3 mb-0">
            Formatter menggunakan metadata yang tersedia dari API.
            Volume, issue, halaman, kapitalisasi judul, dan nama penulis
            tetap perlu diperiksa terhadap artikel atau publisher asli.
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyReview() {
        navigator.clipboard
            .writeText(document.getElementById('reviewText').innerText)
            .then(() => alert('Literature review disalin.'));
    }
</script>
@endpush