<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('/pages/games/index.php');

$db = getDB();
$stmt = $db->prepare('SELECT * FROM games WHERE id = ?');
$stmt->execute([$id]);
$game = $stmt->fetch();
if (!$game) abort(404, 'Ce jeu n\'existe pas.');

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
                <div class="w-full h-64 bg-tft-dark flex items-center justify-center">
                    <svg class="w-16 h-16 text-gold/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-linear-to-t from-tft-dark to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-8">
            <div class="flex items-center gap-3 mb-3">
                <span class="inline-block bg-[#96527A]/80 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full">
                    <?= e($game['type']) ?>
                </span>
                <span class="inline-block bg-gradient-to-r from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full">
                    <?= number_format($game['price'], 2, ',', '') ?> &euro;
                </span>
            </div>
                <h1 class="font-tft text-4xl font-bold text-gold"><?= e($game['name']) ?></h1>
            </div>
            <?php if (isAdmin()): ?>
                <div class="absolute top-4 right-4 flex gap-2">
                    <a href="/pages/games/edit.php?id=<?= $game['id'] ?>"
                       class="bg-tft-card/90 border border-tft-border text-gold px-4 py-2 rounded-lg text-sm hover:bg-gold hover:text-tft-dark transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editer</a>
                    <form action="/pages/games/delete.php" method="POST" class="inline"
                          onsubmit="return confirm('Supprimer ce jeu ?')">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $game['id'] ?>">
                        <button type="submit"
                                class="bg-tft-card/90 border border-red-500/30 text-red-400 px-4 py-2 rounded-lg text-sm hover:bg-red-500/20 transition cursor-pointer">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
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

        <?php
        $stmtLvl = $db->prepare('SELECT * FROM levels WHERE game_id = ? ORDER BY CASE difficulty WHEN "easy" THEN 1 WHEN "medium" THEN 2 WHEN "hard" THEN 3 WHEN "extreme" THEN 4 END');
        $stmtLvl->execute([$id]);
        $levels = $stmtLvl->fetchAll();
        ?>
        <?php if (!empty($levels)): ?>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-tft-border">
                    <h2 class="font-tft text-xl font-bold text-gold">Niveaux</h2>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($levels as $lvl):
                        $diffConfig = match ($lvl['difficulty']) {
                            'easy' => ['label' => 'Facile', 'color' => 'text-green-400', 'bg' => 'bg-green-500/10', 'border' => 'border-green-500/30'],
                            'medium' => ['label' => 'Moyen', 'color' => 'text-blue-400', 'bg' => 'bg-blue-500/10', 'border' => 'border-blue-500/30'],
                            'hard' => ['label' => 'Difficile', 'color' => 'text-orange-400', 'bg' => 'bg-orange-500/10', 'border' => 'border-orange-500/30'],
                            'extreme' => ['label' => 'Extrême', 'color' => 'text-red-400', 'bg' => 'bg-red-500/10', 'border' => 'border-red-500/30'],
                            default => ['label' => 'Moyen', 'color' => 'text-gray-400', 'bg' => 'bg-gray-500/10', 'border' => 'border-gray-500/30'],
                        };
                        ?>
                        <div class="flex items-start gap-3 p-3 rounded-lg border <?= $diffConfig['border'] ?> <?= $diffConfig['bg'] ?>">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-medium text-gray-200"><?= e($lvl['name']) ?></p>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full <?= $diffConfig['color'] ?> <?= $diffConfig['bg'] ?>">
                            <?= $diffConfig['label'] ?>
                        </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5"><?= e($lvl['description'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                                <?= $unlocked ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>' ?>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-medium text-sm <?= $unlocked ? 'text-gray-200' : 'text-gray-500' ?>"><?= e($a['name']) ?></p>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full <?= $r['color'] ?> <?= $r['bg'] ?>"><?= $r['label'] ?></span>
                                </div>
                                <p class="text-xs <?= $unlocked ? 'text-gray-400' : 'text-gray-600' ?> mt-0.5"><?= e($a['description'] ?? '') ?></p>
                            </div>
                            <?php if ($unlocked): ?><svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><?php endif; ?>
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
            <div class="mt-6 flex flex-wrap gap-3">
                <?php if ($inCollection): ?>
                    <a href="/pages/user/edit_collection_game.php?game_id=<?= $game['id'] ?>"
                       class="inline-block border border-gold/50 text-gold px-6 py-2.5 rounded-lg hover:bg-gold/10 transition font-medium text-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Modifier mes stats
                    </a>
                    <form action="/pages/games/remove_from_collection.php" method="POST" class="inline"
                          onsubmit="return confirm('Retirer ce jeu de votre collection ?')">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $game['id'] ?>">
                        <button type="submit"
                                class="inline-block border border-red-500/50 text-red-400 px-6 py-2.5 rounded-lg hover:bg-red-500/20 transition font-medium text-sm cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Retirer de ma collection
                        </button>
                    </form>
                <?php else: ?>
                    <form action="/pages/games/add_to_collection.php" method="POST" class="inline">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $game['id'] ?>">
                        <button type="submit"
                                class="inline-block bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold px-6 py-2.5 rounded-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all text-sm cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ajouter a ma collection
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mt-6">
            <a href="/pages/games/index.php" class="text-gray-400 hover:text-gold transition text-sm">← Retour au
                catalogue</a>
        </div>
    </div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>