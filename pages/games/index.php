<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

$pageTitle = 'Catalogue — OAPDN';
$db = getDB();
$games = $db->query('SELECT * FROM games ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="font-tft text-4xl font-bold text-gold mb-1">Catalogue</h1>
                <p class="text-gray-400"><?= count($games) ?> jeu<?= count($games) > 1 ? 'x' : '' ?>
                    disponible<?= count($games) > 1 ? 's' : '' ?></p>
            </div>
            <?php if (isAdmin()): ?>
                <a href="/pages/games/add.php"
                   class="bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold px-5 py-2.5 rounded-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all text-sm">
                    ➕ Ajouter un jeu
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($games)): ?>
            <div class="text-center py-24">
                <p class="font-tft text-2xl text-gold mb-2">Aucun jeu dans le catalogue</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($games as $game): ?>
                    <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border border-t-2 border-t-gold rounded-xl overflow-hidden hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] hover:-translate-y-1 transition-all flex flex-col">
                        <a href="/pages/games/show.php?id=<?= $game['id'] ?>">
                            <?php if ($game['image']): ?>
                                <img src="<?= e($game['image']) ?>" alt="<?= e($game['name']) ?>"
                                     class="w-full h-44 object-cover opacity-80 hover:opacity-100 transition">
                            <?php else: ?>
                                <div class="w-full h-44 bg-tft-dark flex items-center justify-center text-5xl">🎮</div>
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
<?php include __DIR__ . '/../../includes/footer.php'; ?>