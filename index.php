<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
secureSessionStart();

$pageTitle = 'Accueil — TFT Collection';

$db = getDB();
$games = $db->query('SELECT * FROM games ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

    <div class="max-w-7xl mx-auto px-4 py-10">

        <!-- Hero TFT -->
        <div class="text-center mb-6">
            <p class="text-gold-light text-sm font-medium tracking-[0.3em] uppercase mb-2">Bienvenue dans l'arene</p>
            <h1 class="font-tft text-4xl md:text-5xl font-bold text-gold mb-3" style="text-shadow: 0 0 30px rgba(254,137,94,0.3);">Le Carrousel</h1>
            <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-gold to-transparent mx-auto mb-2"></div>
            <p class="text-gray-500 text-sm">Choisissez votre champion — cliquez pour decouvrir</p>
        </div>

        <?php if (!empty($games)): ?>
            <div class="carousel-scene" id="carousel-scene">
                <div class="carousel-arena">
                    <div class="carousel-ring" id="carousel-ring">
                        <?php foreach ($games as $i => $game): ?>
                            <div class="carousel-item"
                                 data-index="<?= $i ?>"
                                 data-id="<?= $game['id'] ?>"
                                 data-desc="<?= e($game['description'] ?? '') ?>"
                                 data-date="<?= date('d/m/Y', strtotime($game['created_at'])) ?>"
                                 data-price="<?= number_format($game['price'], 2, ',', '') ?>">
                                <div class="carousel-price"><?= number_format($game['price'], 2, ',', '') ?> &euro;</div>
                                <a href="/pages/games/show.php?id=<?= $game['id'] ?>" class="carousel-card-link">
                                    <div class="carousel-card-3d">
                                        <?php if ($game['image']): ?>
                                            <img src="<?= e($game['image']) ?>"
                                                 alt="<?= e($game['name']) ?>"
                                                 class="carousel-card-img">
                                        <?php else: ?>
                                            <div class="carousel-card-img flex items-center justify-center bg-tft-dark">
                                                <svg class="w-10 h-10 text-gold/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                        <?php endif; ?>
                                        <div class="carousel-card-info">
                                            <h3 class="font-tft text-gold text-sm font-bold truncate"><?= e($game['name']) ?></h3>
                                            <span class="text-[#F99F72] text-xs"><?= e($game['type']) ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="carousel-controls">
                    <button id="carousel-prev" class="carousel-btn" aria-label="Precedent">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button id="carousel-pause" class="carousel-btn font-tft text-xs">
                        <svg id="pause-icon" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                        <svg id="play-icon" class="w-4 h-4 hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                    <button id="carousel-next" class="carousel-btn" aria-label="Suivant">
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
            <div class="text-center mb-10">
                <p class="text-gold-light text-xs font-medium tracking-[0.3em] uppercase mb-2">Catalogue</p>
                <h2 class="font-tft text-3xl font-bold text-gold mb-2">Nos jeux</h2>
                <div class="w-16 h-0.5 bg-gradient-to-r from-transparent via-gold to-transparent mx-auto mb-3"></div>
                <p class="text-gray-500">Retrouvez l'ensemble de notre collection</p>
            </div>

            <?php if (empty($games)): ?>
                <div class="text-center py-24">
                    <p class="font-tft text-2xl text-gold mb-2">Aucun jeu disponible pour le moment</p>
                    <?php if (isAdmin()): ?>
                        <a href="/pages/games/add.php"
                           class="inline-block mt-4 bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold px-6 py-3 rounded-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                            Ajouter un jeu
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
                                    <div class="w-full h-44 bg-tft-dark flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gold/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="p-5 flex flex-col flex-1">
                                <a href="/pages/games/show.php?id=<?= $game['id'] ?>">
                                    <h3 class="font-tft text-lg font-bold text-gold hover:text-gold-light transition mb-2">
                                        <?= e($game['name']) ?>
                                    </h3>
                                </a>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="inline-block bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full">
                                        <?= e($game['type']) ?>
                                    </span>
                                    <span class="text-gold font-tft font-bold text-sm"><?= number_format($game['price'], 2, ',', '') ?> &euro;</span>
                                </div>
                                <p class="text-sm text-gray-400 mb-4 flex-1"><?= e($game['description'] ?? '') ?></p>
                                <div class="flex items-center justify-between mt-auto pt-3 border-t border-tft-border">
                                    <p class="text-xs text-gray-600">Ajoute
                                        le <?= date('d/m/Y', strtotime($game['created_at'])) ?></p>
                                    <?php if (isAdmin()): ?>
                                        <div class="flex gap-3 items-center">
                                            <a href="/pages/games/edit.php?id=<?= $game['id'] ?>"
                                               class="text-xs text-gold hover:text-gold-light transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form action="/pages/games/delete.php" method="POST" class="inline"
                                                  onsubmit="return confirm('Supprimer ce jeu ?')">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="id" value="<?= $game['id'] ?>">
                                                <button type="submit"
                                                        class="text-xs text-red-400 hover:text-red-300 transition cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
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