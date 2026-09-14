<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class LiteratureSearchRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'query' => ['required','string','min:3','max:500'],
            'year_from' => ['nullable','integer','min:1900','max:2100'],
            'year_to' => ['nullable','integer','min:1900','max:2100','gte:year_from'],
            'limit' => ['required','integer','in:5,10,15,20'],
            'sources' => ['required','array','min:1'],
            'sources.*' => ['in:crossref,openalex'],
            'only_abstract' => ['nullable','boolean'],
            'open_access' => ['nullable','boolean'],
            'sinta_level' => ['nullable','integer','between:1,6'],
            'output_language' => ['required','in:id,en'],
            'mode' => ['required','in:previous,narrative,thematic,summary'],
            'use_ai' => ['nullable','boolean'],
        ];
    }
}
