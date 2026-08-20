<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <!-- REVIEW-SURFACE-CONTRACT
        THESIS: A single-screen thank-you ritual — one rating, one optional story, answered in a warm moment — refusing the multi-field survey form the category defaults to.
        OWN-WORLD: Sunset-gradient wash with three drifting saturated blobs behind one frosted-glass card; a coral-to-pink gradient marks the chosen rating and the action; Instrument Sans; offset shadows tinted warm.
        STORY: The visitor rates how likely they are to recommend us on a 1-5 tactile scale and optionally explains in their own words; promoters (4-5) are handed off to the Google review page, detractors (1-3) are emailed to the admin and thanked in place.
        FIRST VIEWPORT: Full-viewport gradient with blurred blobs drifting; a centered max-w-xl glass card holds the question, the five tiles with edge captions, the optional feedback field, and the full-width gradient submit.
        FORM: Vibrant gradient — the user's selected option from the structured design question; seed key USER-PINNED.
        FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, and DESIGN.md.
        -->
        <x-inertia::app />
    </body>
</html>
