<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) : 'OAPDN' ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&display=swap"
          rel="stylesheet">
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', sans-serif;
            --font-tft: 'Cinzel', serif;
            --color-gold: #FE895E;
            --color-gold-light: #F99F72;
            --color-gold-dark: #CD6889;
            --color-tft-dark: #2a1a24;
            --color-tft-card: #5B374D;
            --color-tft-card-deep: #462a3c;
            --color-tft-nav: #3d2435;
            --color-tft-nav-mid: #5B374D;
            --color-tft-border: #96527A;
        }
        body {
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 5 L52 17.5 L52 42.5 L30 55 L8 42.5 L8 17.5 Z' fill='none' stroke='%23ffffff08' stroke-width='1'/%3E%3C/svg%3E") repeat,
            linear-gradient(135deg, #2a1a24 0%, #5B374D 30%, #3d2435 70%, #2a1a24 100%) fixed;
        }
    </style>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="min-h-screen font-sans text-gray-200">

<?php
if (!function_exists('isLoggedIn')) require_once __DIR__ . '/../includes/auth.php';
if (!function_exists('e')) require_once __DIR__ . '/../includes/functions.php';

include __DIR__ . '/navbar.php';

$flash = getFlash();
if ($flash):
$flashColors = match ($flash['type']) {
    'success' => 'bg-green-500/10 border-green-500/30 text-green-400',
    'error' => 'bg-red-500/10 border-red-500/30 text-red-400',
    default => 'bg-blue-500/10 border-blue-500/30 text-blue-400',
};
?>
<div class="max-w-7xl mx-auto px-4 pt-4">
    <div class="<?= $flashColors ?> border rounded-lg px-4 py-3 text-sm flex items-center justify-between"
         id="flash-msg">
        <span><?= e($flash['message']) ?></span>
        <button onclick="document.getElementById('flash-msg').remove()"
                class="ml-4 opacity-60 hover:opacity-100 transition text-lg">&times;
        </button>
    </div>
</div>
<?php endif; ?>