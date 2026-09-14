@extends('layouts.app')

@section('content')
<div class="hero mx-auto">

    <div class="mb-4">
        <span class="badge text-bg-secondary mb-2">Retrieval-first</span>

        <h1 class="display-6 fw-bold">
            Susun literature review dari artikel yang benar-benar ditemukan
        </h1>

        <p class="lead text-secondary">
            Masukkan topik atau judul penelitian. Sistem mencari sumber,
            menghapus duplikasi, memberi skor relevansi, mengekstrak evidence
            dari abstrak, lalu menyusun sintesis yang dapat ditelusuri.
        </p>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger">
            <strong>Periksa input:</strong>

            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post"
          action="{{ route('literature.search') }}"
          class="card shadow-sm border-0">

        <div class="card-body p-4">
            @csrf

            <label class="form-label fw-semibold">
                Topik atau judul penelitian
            </label>

            <textarea
                class="form-control form-control-lg"
                name="query"
                rows="3"
                required
                placeholder="Contoh: Pengaruh kualitas sistem dan kualitas informasi terhadap kepuasan pengguna sistem informasi akademik"
            >{{ old('query') }}</textarea>

            <div class="row g-3 mt-1">

                <div class="col-md-3">
                    <label class="form-label">Tahun awal</label>
                    <input
                        type="number"
                        name="year_from"
                        class="form-control"
                        value="{{ old('year_from', 2020) }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tahun akhir</label>
                    <input
                        type="number"
                        name="year_to"
                        class="form-control"
                        value="{{ old('year_to', date('Y')) }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jumlah artikel</label>

                    <select name="limit" class="form-select">
                        @foreach([5, 10, 15, 20] as $n)
                            <option
                                value="{{ $n }}"
                                @selected(old('limit', 10) == $n)
                            >
                                {{ $n }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Bahasa output</label>

                    <select name="output_language" class="form-select">
                        <option value="id">Bahasa Indonesia</option>
                        <option value="en">English</option>
                    </select>
                </div>

            </div>

            <div class="row g-3 mt-1">

                <div class="col-md-4">
                    <label class="form-label">Mode output</label>

                    <select name="mode" class="form-select">
                        <option value="previous">Penelitian Terdahulu</option>
                        <option value="narrative">Literature Review Naratif</option>
                        <option value="thematic">Literature Review Tematik</option>
                        <option value="summary">Ringkasan per Artikel</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">SINTA</label>

                    <select name="sinta_level" class="form-select">
                        <option value="">Semua jurnal</option>

                        @foreach(range(1, 6) as $n)
                            <option value="{{ $n }}">
                                SINTA {{ $n }}
                            </option>
                        @endforeach
                    </select>

                    <div class="form-text">
                        Memerlukan registry ISSN lokal yang diisi pengguna.
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label d-block">Sumber</label>

                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            checked
                            type="checkbox"
                            name="sources[]"
                            value="openalex"
                            id="oa"
                        >

                        <label class="form-check-label" for="oa">
                            OpenAlex
                        </label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            checked
                            type="checkbox"
                            name="sources[]"
                            value="crossref"
                            id="cr"
                        >

                        <label class="form-check-label" for="cr">
                            Crossref
                        </label>
                    </div>
                </div>

            </div>

            <div class="mt-3 d-flex flex-wrap gap-4">

                <div class="form-check">
                    <input type="hidden" name="only_abstract" value="0">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="only_abstract"
                        value="1"
                        id="abs"
                        checked
                    >

                    <label class="form-check-label" for="abs">
                        Hanya artikel dengan abstrak
                    </label>
                </div>

                <div class="form-check">
                    <input type="hidden" name="open_access" value="0">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="open_access"
                        value="1"
                        id="oaonly"
                    >

                    <label class="form-check-label" for="oaonly">
                        Prioritaskan/filter open access di OpenAlex
                    </label>
                </div>

                <div class="form-check">
                    <input type="hidden" name="use_ai" value="0">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="use_ai"
                        value="1"
                        id="ai"
                    >

                    <label class="form-check-label" for="ai">
                        Gunakan AI jika API dikonfigurasi
                    </label>
                </div>

            </div>

            <button class="btn btn-dark btn-lg mt-4 w-100">
                Cari & Susun Literature Review
            </button>

        </div>
    </form>

    <div class="alert alert-light border mt-4 small">
        <strong>Disclaimer akademik:</strong>
        aplikasi membantu retrieval dan penyusunan awal.
        Hasil bukan pengganti membaca artikel asli.
        Metadata, DOI, evidence, dan interpretasi tetap perlu diverifikasi oleh pengguna.
    </div>

</div>
@endsection