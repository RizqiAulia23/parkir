@php
    $fontsManifestPath = public_path('build/fonts-manifest.json');
    $fontsManifest = is_file($fontsManifestPath)
        ? json_decode((string) file_get_contents($fontsManifestPath), true)
        : null;
@endphp

@if (! empty($fontsManifest['style']['file']))
    <link rel="stylesheet" href="{{ asset('build/'.$fontsManifest['style']['file']) }}">
@endif
