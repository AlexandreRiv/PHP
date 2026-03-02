<?php
session_start();
$pageTitle = 'Accueil — PHP ';

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<div class="max-w-5xl mx-auto px-4 pt-16 pb-10 text-center">
    <h1 class="font-tft text-5xl font-bold text-gold mb-4">⚔️ Bienvenue, Invocateur</h1>
    <p class="text-gray-400 text-lg mb-2">Suivez vos champions, gérez vos compositions et grimpez dans le classement.</p>
    <p class="text-violet-400 text-sm mb-8">Préparez votre plateau. La bataille commence.</p>
    <a href="/pages/games/index.php"
       class="bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold tracking-wide px-8 py-3 rounded-lg text-lg inline-block hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.5)] transition-all">
        Explorer les Champions
    </a>
</div>

<!-- STATS RAPIDES -->
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.15)] transition-all">
            <p class="font-tft text-3xl font-bold text-gold">4</p>
            <p class="text-gray-400 text-sm mt-1">Champions suivis</p>
        </div>
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.15)] transition-all">
            <p class="font-tft text-3xl font-bold text-violet-400">12</p>
            <p class="text-gray-400 text-sm mt-1">Parties jouées</p>
        </div>
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.15)] transition-all">
            <p class="font-tft text-3xl font-bold text-amber-400">🏆 Top 4</p>
            <p class="text-gray-400 text-sm mt-1">Meilleur résultat</p>
        </div>
    </div>
</div>

<!-- LISTE DES CHAMPIONS -->
<div class="max-w-5xl mx-auto px-4 pb-10">
    <h2 class="font-tft text-2xl font-bold text-gold-light mb-6">🛡️ Vos Champions</h2>

    <?php
    $games = [
        ['name' => 'Yasuo',    'type' => 'Duelliste',   'cost' => '⭐'],
        ['name' => 'Jinx',     'type' => 'Canonnière',  'cost' => '⭐⭐⭐'],
        ['name' => 'Thresh',   'type' => 'Gardien',     'cost' => '⭐⭐'],
        ['name' => 'Kayn',     'type' => 'Assassin',    'cost' => '⭐⭐⭐⭐⭐'],
    ];

    $rarityBorders = [
        'border-l-3 border-l-gray-400',
        'border-l-3 border-l-gold',
        'border-l-3 border-l-blue-500',
        'border-l-3 border-l-purple-500',
    ];
    ?>

    <div class="space-y-3">
        <?php foreach ($games as $i => $game): ?>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border <?= $rarityBorders[$i] ?> rounded-lg p-4 flex items-center justify-between hover:border-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.15)] transition-all">
                <div class="flex items-center gap-4">
                    <span class="text-2xl">🎭</span>
                    <div>
                        <h3 class="font-tft font-bold text-gold"><?= htmlspecialchars($game['name']) ?></h3>
                        <p class="text-gray-400 text-sm"><?= htmlspecialchars($game['type']) ?></p>
                    </div>
                </div>
                <span class="text-lg"><?= $game['cost'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
