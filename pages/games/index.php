<?php
session_start();
$pageTitle = 'Mes Jeux — PHP';


$user = $_SESSION['user'] ?? [
    'id'       => 1,
    'username' => 'Pochi',
];

$userGames = [
    [
        'id'             => 1,
        'name'           => 'Hollow Knight Silksong',
        'type'           => 'Action / Aventure',
        'description'    => 'Incarnez Hornet dans une toute nouvelle aventure à travers un royaume hanté par la soie et les chants.',
        'image'          => '/assets/images/silksong.png',
        'playtime_hours' => 64,
        'added_at'       => '2025-07-01 10:00:00',
        'death_date'     => null,
        'achievements'   => [
            ['name' => 'Premier pas',        'description' => 'Terminer le tutoriel',                     'rarity' => 'common',    'unlocked' => true,  'unlocked_at' => '2025-07-01'],
            ['name' => 'Tisseur de soie',    'description' => 'Vaincre 100 ennemis avec le fil de soie',  'rarity' => 'uncommon',  'unlocked' => true,  'unlocked_at' => '2025-07-10'],
            ['name' => 'Danse macabre',      'description' => 'Battre un boss sans prendre de dégâts',    'rarity' => 'rare',      'unlocked' => false, 'unlocked_at' => null],
            ['name' => 'Reine des aiguilles','description' => 'Terminer le jeu en difficulté maximale',   'rarity' => 'legendary', 'unlocked' => false, 'unlocked_at' => null],
        ],
    ],
    [
        'id'             => 2,
        'name'           => 'Elden Ring',
        'type'           => 'RPG / Action',
        'description'    => 'Explorez un vaste monde créé par Hidetaka Miyazaki et George R.R. Martin.',
        'image'          => '/assets/images/eldenRing.png',
        'playtime_hours' => 187,
        'added_at'       => '2022-03-01 00:00:00',
        'death_date'     => null,
        'achievements'   => [
            ['name' => 'Sans-éclat',         'description' => 'Arriver aux Terres Intermédiaires',        'rarity' => 'common',    'unlocked' => true,  'unlocked_at' => '2022-03-01'],
            ['name' => 'Seigneur ancestral', 'description' => 'Vaincre un demi-dieu',                     'rarity' => 'uncommon',  'unlocked' => true,  'unlocked_at' => '2022-03-15'],
            ['name' => 'Étoile ancienne',    'description' => 'Obtenir la fin secrète',                   'rarity' => 'epic',      'unlocked' => true,  'unlocked_at' => '2022-04-20'],
            ['name' => 'Légende vivante',    'description' => 'Terminer le jeu au niveau 1',              'rarity' => 'legendary', 'unlocked' => false, 'unlocked_at' => null],
            ['name' => 'Explorateur',        'description' => 'Découvrir toutes les zones de la carte',   'rarity' => 'rare',      'unlocked' => false, 'unlocked_at' => null],
        ],
    ],
    [
        'id'             => 3,
        'name'           => 'Minecraft',
        'type'           => 'Sandbox / Survie',
        'description'    => 'Construisez, explorez et survivez dans un monde infini fait de blocs.',
        'image'          => '/assets/images/minecraft.png',
        'playtime_hours' => 96,
        'added_at'       => '2024-01-10 00:00:00',
        'death_date'     => null,
        'achievements'   => [
            ['name' => 'Bûcheron',           'description' => 'Couper votre premier arbre',               'rarity' => 'common',    'unlocked' => true,  'unlocked_at' => '2024-01-10'],
            ['name' => 'Diamant !',          'description' => 'Miner votre premier diamant',              'rarity' => 'uncommon',  'unlocked' => true,  'unlocked_at' => '2024-01-12'],
            ['name' => 'The End',            'description' => 'Vaincre l\'Ender Dragon',                  'rarity' => 'rare',      'unlocked' => true,  'unlocked_at' => '2024-02-01'],
            ['name' => 'Cartographe ultime', 'description' => 'Explorer 500 chunks différents',           'rarity' => 'epic',      'unlocked' => false, 'unlocked_at' => null],
        ],
    ],
];


$totalGames        = count($userGames);
$totalPlaytime     = array_sum(array_column($userGames, 'playtime_hours'));
$totalAchievements = 0;
$unlockedTotal     = 0;
foreach ($userGames as $g) {
    $totalAchievements += count($g['achievements']);
    $unlockedTotal     += count(array_filter($g['achievements'], fn($a) => $a['unlocked']));
}
$completionPercent = $totalAchievements > 0 ? round(($unlockedTotal / $totalAchievements) * 100) : 0;


$rarityConfig = [
    'common'    => ['color' => 'text-gray-400',   'bg' => 'bg-gray-500/20',    'border' => 'border-gray-500/30',  'label' => 'Commun'],
    'uncommon'  => ['color' => 'text-green-400',   'bg' => 'bg-green-500/20',   'border' => 'border-green-500/30', 'label' => 'Peu commun'],
    'rare'      => ['color' => 'text-blue-400',    'bg' => 'bg-blue-500/20',    'border' => 'border-blue-500/30',  'label' => 'Rare'],
    'epic'      => ['color' => 'text-purple-400',  'bg' => 'bg-purple-500/20',  'border' => 'border-purple-500/30','label' => 'Épique'],
    'legendary' => ['color' => 'text-gold',        'bg' => 'bg-gold/20',        'border' => 'border-gold/30',      'label' => 'Légendaire'],
];

include __DIR__ . '/../../includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 py-12">


    <div class="text-center mb-10">
        <h1 class="font-tft text-4xl font-bold text-gold mb-2">Ma Collection</h1>
        <p class="text-gray-400">Retrouvez vos jeux et récupérez vos succès</p>
    </div>


    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
            <p class="font-tft text-2xl font-bold text-gold"><?= $totalGames ?></p>
            <p class="text-gray-400 text-xs mt-1">Jeux possédés</p>
        </div>
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
            <p class="font-tft text-2xl font-bold text-gold"><?= $totalPlaytime ?>h</p>
            <p class="text-gray-400 text-xs mt-1">Temps de jeu</p>
        </div>
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
            <p class="font-tft text-2xl font-bold text-gold"><?= $unlockedTotal ?>/<?= $totalAchievements ?></p>
            <p class="text-gray-400 text-xs mt-1">Succès débloqués</p>
        </div>
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
            <p class="font-tft text-2xl font-bold text-gold"><?= $completionPercent ?>%</p>
            <p class="text-gray-400 text-xs mt-1">Complétion</p>
        </div>
    </div>

    <div class="mb-12 bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6">
        <div class="flex items-center justify-between mb-3">
            <span class="font-tft text-sm text-gold">Progression globale</span>
            <span class="text-gray-400 text-sm"><?= $unlockedTotal ?> / <?= $totalAchievements ?> succès</span>
        </div>
        <div class="w-full h-3 bg-tft-dark rounded-full overflow-hidden">
            <div class="h-full bg-linear-to-r from-gold to-gold-dark rounded-full transition-all duration-700"
                 style="width: <?= $completionPercent ?>%"></div>
        </div>
    </div>


    <div class="space-y-8">
        <?php foreach ($userGames as $game):
            $gameUnlocked = count(array_filter($game['achievements'], fn($a) => $a['unlocked']));
            $gameTotal    = count($game['achievements']);
            $gamePercent  = $gameTotal > 0 ? round(($gameUnlocked / $gameTotal) * 100) : 0;
        ?>
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-2xl overflow-hidden hover:border-gold/50 transition-all">


            <div class="flex flex-col sm:flex-row">

                <div class="sm:w-56 h-48 sm:h-auto shrink-0">
                    <img src="<?= htmlspecialchars($game['image']) ?>"
                         alt="<?= htmlspecialchars($game['name']) ?>"
                         class="w-full h-full object-cover opacity-80">
                </div>

                <div class="flex-1 p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-3">
                        <div>
                            <h2 class="font-tft text-2xl font-bold text-gold"><?= htmlspecialchars($game['name']) ?></h2>
                            <span class="inline-block bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full mt-1">
                                <?= htmlspecialchars($game['type']) ?>
                            </span>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-tft text-xl font-bold text-gold"><?= $game['playtime_hours'] ?>h</p>
                            <p class="text-gray-500 text-xs">de jeu</p>
                        </div>
                    </div>

                    <p class="text-gray-400 text-sm mb-4"><?= htmlspecialchars($game['description']) ?></p>

                    <!-- Barre de progression du jeu -->
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-2 bg-tft-dark rounded-full overflow-hidden">
                            <div class="h-full bg-linear-to-r from-gold to-gold-dark rounded-full transition-all duration-700"
                                 style="width: <?= $gamePercent ?>%"></div>
                        </div>
                        <span class="text-sm font-medium <?= $gamePercent === 100 ? 'text-gold' : 'text-gray-400' ?>">
                            <?= $gameUnlocked ?>/<?= $gameTotal ?>
                            <?php if ($gamePercent === 100): ?>
                                <span class="ml-1 text-green-400">Complété</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">Ajouté le <?= date('d/m/Y', strtotime($game['added_at'])) ?></p>
                </div>
            </div>

            <div class="border-t border-tft-border">
                <div class="px-6 py-3 flex items-center justify-between">
                    <h3 class="font-tft text-sm font-bold text-gold">Succès</h3>
                    <button onclick="this.closest('.border-t').querySelector('.achievements-list').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')"
                            class="text-gray-400 hover:text-gold transition text-sm flex items-center gap-1 cursor-pointer">
                        <span>Voir les succès</span>
                        <svg class="w-4 h-4 chevron transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                <div class="achievements-list px-6 pb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php foreach ($game['achievements'] as $achievement):
                            $r = $rarityConfig[$achievement['rarity']] ?? $rarityConfig['common'];
                        ?>
                        <div class="relative flex items-start gap-3 p-3 rounded-lg border <?= $achievement['unlocked'] ? $r['border'] . ' ' . $r['bg'] : 'border-tft-border bg-tft-dark/50 opacity-60' ?> transition-all hover:opacity-100">

                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 text-lg
                                        <?= $achievement['unlocked'] ? $r['bg'] . ' ' . $r['color'] : 'bg-gray-700/50 text-gray-600' ?>">
                                <?= $achievement['unlocked'] ? '🏆' : '🔒' ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-medium text-sm <?= $achievement['unlocked'] ? 'text-gray-200' : 'text-gray-500' ?> truncate">
                                        <?= htmlspecialchars($achievement['name']) ?>
                                    </p>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full <?= $r['color'] ?> <?= $r['bg'] ?>">
                                        <?= $r['label'] ?>
                                    </span>
                                </div>
                                <p class="text-xs <?= $achievement['unlocked'] ? 'text-gray-400' : 'text-gray-600' ?> mt-0.5">
                                    <?= htmlspecialchars($achievement['description']) ?>
                                </p>
                                <?php if ($achievement['unlocked'] && $achievement['unlocked_at']): ?>
                                    <p class="text-[10px] text-gray-600 mt-1">Débloqué le <?= date('d/m/Y', strtotime($achievement['unlocked_at'])) ?></p>
                                <?php elseif (!$achievement['unlocked']): ?>
                                    <p class="text-[10px] text-gold-dark mt-1 italic">À récupérer</p>
                                <?php endif; ?>
                            </div>

                            <?php if ($achievement['unlocked']): ?>
                                <span class="text-green-400 text-lg shrink-0">✓</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


    <?php if (empty($userGames)): ?>
    <div class="text-center py-20">
        <h2 class="font-tft text-2xl text-gold mb-2">Aucun jeu dans votre collection</h2>
        <p class="text-gray-500 mb-6">Commencez par ajouter des jeux à votre bibliothèque</p>
        <a href="/pages/games/add.php"
           class="inline-block bg-linear-to-br from-gold to-gold-dark text-[#2a1a24] font-tft font-bold tracking-wide px-6 py-3 rounded-lg text-sm hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
            ➕ Ajouter un jeu
        </a>
    </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

