<?php
return [
    'http_timeout' => (int) env('LITREVIEW_HTTP_TIMEOUT', 12),
    'cache_minutes' => (int) env('LITREVIEW_CACHE_MINUTES', 1440),
    'crossref_mailto' => env('CROSSREF_MAILTO'),
    'openalex_mailto' => env('OPENALEX_MAILTO'),
    'openalex_api_key' => env('OPENALEX_API_KEY'),
    'ai' => [
        'enabled' => filter_var(env('LITREVIEW_AI_ENABLED', false), FILTER_VALIDATE_BOOL),
        'provider' => env('LITREVIEW_AI_PROVIDER', 'openai_compatible'),
        'base_url' => env('LITREVIEW_AI_BASE_URL', 'https://api.openai.com/v1'),
        'api_key' => env('LITREVIEW_AI_API_KEY'),
        'model' => env('LITREVIEW_AI_MODEL', 'gpt-5-mini'),
    ],
];
