<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'PHP' ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', sans-serif;
            --font-tft: 'Cinzel', serif;

            --color-gold: #f0b232;
            --color-gold-light: #ffd866;
            --color-gold-dark: #d4991f;

            --color-tft-dark: #0f0f23;
            --color-tft-card: #1a1a3e;
            --color-tft-card-deep: #12122a;
            --color-tft-nav: #0d0d24;
            --color-tft-nav-mid: #1a0a2e;
            --color-tft-border: #2a2a5e;
        }

        /* ── Seul le CSS impossible à faire en Tailwind pur ── */
        body {
            background:
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 5 L52 17.5 L52 42.5 L30 55 L8 42.5 L8 17.5 Z' fill='none' stroke='%23ffffff08' stroke-width='1'/%3E%3C/svg%3E") repeat,
                linear-gradient(135deg, #0a0a1a 0%, #1a0a2e 30%, #0d1b2a 70%, #0a0a1a 100%) fixed;
        }
    </style>
</head>
<body class="min-h-screen font-sans text-gray-200">

<?php include __DIR__ . '/navbar.php'; ?>
