<?php
session_start();
$pageTitle = 'Page de jeux — PHP';

include __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex justify-center mb-8">
    </div>

    <?php
    $games = [
            [
                    'name' => 'Hollow Knight Silksong',
                    'type' => 'Action / Aventure',
                    'description' => 'Incarnez Hornet dans une toute nouvelle aventure à travers un royaume hanté par la soie et les chants.',
                    'image' => '/assets/images/silksong.png',
                    'cost' => 19.25,
                    'created_at' => '2025-06-12 10:00:00'
            ],
            [
                    'name' => 'GTA 6',
                    'type' => 'Action / Open World',
                    'description' => 'Plongez dans un monde ouvert immense inspiré de Vice City avec une histoire explosive.',
                    'image' => '/assets/images/gta6.png',
                    'cost' => 89.99,
                    'created_at' => '2025-09-17 18:00:00'
            ],
            [
                    'name' => 'Elden Ring',
                    'type' => 'RPG / Action',
                    'description' => 'Explorez un vaste monde créé par Hidetaka Miyazaki et George R.R. Martin, rempli de défis redoutables.',
                    'image' => '/assets/images/eldenRing.png',
                    'cost' => 59.99,
                    'created_at' => '2022-02-25 00:00:00'
            ],
            [
                    'name' => 'Minecraft',
                    'type' => 'Sandbox / Survie',
                    'description' => 'Construisez, explorez et survivez dans un monde infini fait de blocs.',
                    'image' => '/assets/images/minecraft.png',
                    'cost' => 26.95,
                    'created_at' => '2011-11-18 00:00:00'
            ],
    ];
    ?>

    <?php if (!empty($games)): ?>
        <div class="carousel-scene" id="carousel-scene">
            <div class="carousel-arena">
                <div class="carousel-ring" id="carousel-ring">
                    <?php foreach ($games as $i => $game): ?>
                        <div class="carousel-item" data-index="<?= $i ?>" data-cost="<?= $game['cost'] ?>" data-desc="<?= htmlspecialchars($game['description']) ?>" data-date="<?= date('d/m/Y', strtotime($game['created_at'])) ?>">
                            <div class="carousel-price">
                                <?= $game['cost'] ?> €
                            </div>
                            <div class="carousel-card-3d">
                                <img src="<?= htmlspecialchars($game['image']) ?>"
                                     alt="<?= htmlspecialchars($game['name']) ?>"
                                     class="carousel-card-img">
                                <div class="carousel-card-info">
                                    <h3 class="font-tft text-gold text-sm font-bold truncate"><?= htmlspecialchars($game['name']) ?></h3>
                                    <span class="text-[#F99F72] text-xs"><?= htmlspecialchars($game['type']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div class="carousel-controls">
                <button id="carousel-prev" class="carousel-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button id="carousel-pause" class="carousel-btn font-tft text-xs">⏸ Pause</button>
                <button id="carousel-next" class="carousel-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <div id="carousel-detail" class="carousel-detail">
                <h2 class="font-tft text-xl text-gold font-bold mb-1" id="detail-name"></h2>
                <p class="text-[#F99F72] text-sm mb-2" id="detail-type"></p>
                <p class="text-gray-400 text-sm" id="detail-desc"></p>
                <p class="text-gray-600 text-xs mt-2" id="detail-date"></p>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-500 text-lg mt-20">Aucun jeu trouvé.</p>
    <?php endif; ?>

    <div class="mt-20">
        <h2 class="font-tft text-3xl font-bold text-center text-gold mb-2">Nos jeux</h2>
        <p class="text-center text-gray-500 mb-10">Retrouvez l'ensemble de notre catalogue</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($games as $game): ?>
                <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border border-t-3 border-t-gold rounded-xl overflow-hidden hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] hover:-translate-y-1 transition-all">
                    <img src="<?= htmlspecialchars($game['image']) ?>"
                         alt="<?= htmlspecialchars($game['name']) ?>"
                         class="w-full h-44 object-cover opacity-80">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-tft text-lg font-bold text-gold"><?= htmlspecialchars($game['name']) ?></h3>
                            <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-2 py-1 rounded-full"><?= $game['cost'] ?> €</span>
                        </div>
                        <span class="inline-block bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full mb-3">
                            <?= htmlspecialchars($game['type']) ?>
                        </span>
                        <p class="text-sm text-gray-400 mb-3"><?= htmlspecialchars($game['description']) ?></p>
                        <p class="text-xs text-gray-600">Ajouté le <?= date('d/m/Y', strtotime($game['created_at'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
