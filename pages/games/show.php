<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('/pages/games/index.php');

$db = getDB();
$stmt = $db->prepare('SELECT * FROM games WHERE id = ?');
$stmt->execute([$id]);
$game = $stmt->fetch();
if (!$game) redirect('/pages/games/index.php');

$stmtA = $db->prepare('SELECT * FROM achievements WHERE game_id = ? ORDER BY rarity DESC');
$stmtA->execute([$id]);
$achievements = $stmtA->fetchAll();

$unlockedIds = [];
if (isLoggedIn()) {
    $stmtU = $db->prepare('
        SELECT a.id FROM user_achievements ua
        JOIN achievements a ON a.id = ua.achievement_id
        WHERE ua.user_id = ? AND a.game_id = ?
    ');
    $stmtU->execute([$_SESSION['user']['id'], $id]);
    $unlockedIds = array_column($stmtU->fetchAll(), 'id');
}

$stmtP = $db->prepare('SELECT COUNT(*) as cnt FROM user_games WHERE game_id = ?');
$stmtP->execute([$id]);
$playersCount = $stmtP->fetch()['cnt'];

$pageTitle = e($game['name']) . ' — OAPDN';
include __DIR__ . '/../../includes/header.php';
?>
    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="relative rounded-2xl overflow-hidden border border-tft-border mb-8">
            <?php if ($game['image']): ?>
                <img src="<?= e($game['image']) ?>" alt="<?= e($game['name']) ?>"
                     class="w-full h-64 object-cover opacity-60">
            <?php else: ?>
                <div class="w-full h-64 bg-tft-dark flex items-center justify-center text-8xl">🎮</div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-linear-to-t from-tft-dark to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-8">
            <span class="inline-block bg-[#96527A]/80 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full mb-3">
                <?= e($game['type']) ?>
            </span>
                <h1 class="font-tft text-4xl font-bold text-gold"><?= e($game['name']) ?></h1>
            </div>
            <?php if (isAdmin()): ?>
                <div class="absolute top-4 right-4 flex gap-2">
                    <a href="/pages/games/edit.php?id=<?= $game['id'] ?>"
                       class="bg-tft-card/90 border border-tft-border text-gold px-4 py-2 rounded-lg text-sm hover:bg-gold hover:text-tft-dark transition">✏️
                        Éditer</a>
                    <a href="/pages/games/delete.php?id=<?= $game['id'] ?>"
                       onclick="return confirm('Supprimer ce jeu ?')"
                       class="bg-tft-card/90 border border-red-500/30 text-red-400 px-4 py-2 rounded-lg text-sm hover:bg-red-500/20 transition">🗑️</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-2xl font-bold text-gold"><?= $playersCount ?></p>
                <p class="text-gray-400 text-xs mt-1">Joueurs</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-2xl font-bold text-gold"><?= count($achievements) ?></p>
                <p class="text-gray-400 text-xs mt-1">Succès</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-2xl font-bold text-gold"><?= date('d/m/Y', strtotime($game['created_at'])) ?></p>
                <p class="text-gray-400 text-xs mt-1">Ajouté le</p>
            </div>
        </div>

        <?php if ($game['description']): ?>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 mb-8">
                <h2 class="font-tft text-xl font-bold text-gold mb-3">Description</h2>
                <p class="text-gray-300 leading-relaxed"><?= nl2br(e($game['description'])) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($achievements)): ?>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-tft-border">
                    <h2 class="font-tft text-xl font-bold text-gold">Succès</h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($achievements as $a):
                        $r = rarityConfig($a['rarity']);
                        $unlocked = in_array($a['id'], $unlockedIds);
                        ?>
                        <div class="flex items-start gap-3 p-3 rounded-lg border
                    <?= $unlocked ? $r['border'] . ' ' . $r['bg'] : 'border-tft-border bg-tft-dark/50 opacity-60' ?>">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 <?= $unlocked ? $r['bg'] . ' ' . $r['color'] : 'bg-gray-700/50 text-gray-600' ?>">
                                <?= $unlocked ? '🏆' : '🔒' ?>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-medium text-sm <?= $unlocked ? 'text-gray-200' : 'text-gray-500' ?>"><?= e($a['name']) ?></p>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full <?= $r['color'] ?> <?= $r['bg'] ?>"><?= $r['label'] ?></span>
                                </div>
                                <p class="text-xs <?= $unlocked ? 'text-gray-400' : 'text-gray-600' ?> mt-0.5"><?= e($a['description'] ?? '') ?></p>
                            </div>
                            <?php if ($unlocked): ?><span
                                    class="text-green-400 text-lg shrink-0">✓</span><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Ajouter / Retirer de la collection -->
        <?php if (isLoggedIn()): ?>
            <?php
            $inCollection = false;
            $stmtCol = $db->prepare('SELECT id FROM user_games WHERE user_id = ? AND game_id = ?');
            $stmtCol->execute([$_SESSION['user']['id'], $game['id']]);
            $inCollection = (bool)$stmtCol->fetch();
            ?>
            <div class="mt-6">
                <?php if ($inCollection): ?>
                    <a href="/pages/games/remove_from_collection.php?id=<?= $game['id'] ?>"
                       onclick="return confirm('Retirer ce jeu de votre collection ?')"
                       class="inline-block border border-red-500/50 text-red-400 px-6 py-2.5 rounded-lg hover:bg-red-500/20 transition font-medium text-sm">
                        🗑️ Retirer de ma collection
                    </a>
                <?php else: ?>
                    <a href="/pages/games/add_to_collection.php?id=<?= $game['id'] ?>"
                       class="inline-block bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold px-6 py-2.5 rounded-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all text-sm">
                        ➕ Ajouter à ma collection
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mt-6">
            <a href="/pages/games/index.php" class="text-gray-400 hover:text-gold transition text-sm">← Retour au
                catalogue</a>
        </div>
    </div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>