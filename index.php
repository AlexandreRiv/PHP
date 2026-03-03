<?php
session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Accueil — OAPDN';

$db = getDB();
$games = $db->query('SELECT * FROM games ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

    <div class="max-w-7xl mx-auto px-4 py-10">

        <?php if (!empty($games)): ?>
            <div class="carousel-scene" id="carousel-scene">
                <div class="carousel-arena">
                    <div class="carousel-ring" id="carousel-ring">
                        <?php foreach ($games as $i => $game): ?>
                            <div class="carousel-item"
                                 data-index="<?= $i ?>"
                                 data-desc="<?= e($game['description'] ?? '') ?>"
                                 data-date="<?= date('d/m/Y', strtotime($game['created_at'])) ?>">
                                <div class="carousel-card-3d">
                                    <?php if ($game['image']): ?>
                                        <img src="<?= e($game['image']) ?>"
                                             alt="<?= e($game['name']) ?>"
                                             class="carousel-card-img">
                                    <?php else: ?>
                                        <div class="carousel-card-img flex items-center justify-center text-5xl bg-tft-dark">
                                            🎮
                                        </div>
                                    <?php endif; ?>
                                    <div class="carousel-card-info">
                                        <h3 class="font-tft text-gold text-sm font-bold truncate"><?= e($game['name']) ?></h3>
                                        <span class="text-[#F99F72] text-xs"><?= e($game['type']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="carousel-controls">
                    <button id="carousel-prev" class="carousel-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button id="carousel-pause" class="carousel-btn font-tft text-xs">⏸ Pause</button>
                    <button id="carousel-next" class="carousel-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <div id="carousel-detail" class="carousel-detail">
                    <h2 class="font-tft text-xl text-gold font-bold mb-1" id="detail-name"></h2>
                    <p class="text-[#F99F72] text-sm mb-2" id="detail-type"></p>
                    <p class="text-gray-400 text-sm" id="detail-desc"></p>
                    <p class="text-gray-600 text-xs mt-2" id="detail-date"></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Catalogue -->
        <div class="mt-20">
            <h2 class="font-tft text-3xl font-bold text-center text-gold mb-2">Nos jeux</h2>
            <p class="text-center text-gray-500 mb-10">Retrouvez l'ensemble de notre catalogue</p>

            <?php if (empty($games)): ?>
                <div class="text-center py-24">
                    <p class="font-tft text-2xl text-gold mb-2">Aucun jeu disponible pour le moment</p>
                    <?php if (isAdmin()): ?>
                        <a href="/pages/games/add.php"
                           class="inline-block mt-4 bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold px-6 py-3 rounded-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                            ➕ Ajouter un jeu
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($games as $game): ?>
                        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border border-t-2 border-t-gold rounded-xl overflow-hidden hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] hover:-translate-y-1 transition-all flex flex-col">
                            <a href="/pages/games/show.php?id=<?= $game['id'] ?>">
                                <?php if ($game['image']): ?>
                                    <img src="<?= e($game['image']) ?>"
                                         alt="<?= e($game['name']) ?>"
                                         class="w-full h-44 object-cover opacity-80 hover:opacity-100 transition">
                                <?php else: ?>
                                    <div class="w-full h-44 bg-tft-dark flex items-center justify-center text-5xl">🎮
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="p-5 flex flex-col flex-1">
                                <a href="/pages/games/show.php?id=<?= $game['id'] ?>">
                                    <h3 class="font-tft text-lg font-bold text-gold hover:text-gold-light transition mb-2">
                                        <?= e($game['name']) ?>
                                    </h3>
                                </a>
                                <span class="inline-block bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full mb-3 self-start">
                                <?= e($game['type']) ?>
                            </span>
                                <p class="text-sm text-gray-400 mb-4 flex-1"><?= e($game['description'] ?? '') ?></p>
                                <div class="flex items-center justify-between mt-auto pt-3 border-t border-tft-border">
                                    <p class="text-xs text-gray-600">Ajouté
                                        le <?= date('d/m/Y', strtotime($game['created_at'])) ?></p>
                                    <?php if (isAdmin()): ?>
                                        <div class="flex gap-3">
                                            <a href="/pages/games/edit.php?id=<?= $game['id'] ?>"
                                               class="text-xs text-gold hover:text-gold-light transition">✏️</a>
                                            <a href="/pages/games/delete.php?id=<?= $game['id'] ?>"
                                               onclick="return confirm('Supprimer ce jeu ?')"
                                               class="text-xs text-red-400 hover:text-red-300 transition">🗑️</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>