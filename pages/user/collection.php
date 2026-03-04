<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireLogin();

$pageTitle = 'Ma Collection — OAPDN';
$db = getDB();
$userId = $_SESSION['user']['id'];

$stmtGames = $db->prepare('
    SELECT g.*, ug.id as ug_id, ug.playtime_hours, ug.added_at
    FROM user_games ug
    JOIN games g ON g.id = ug.game_id
    WHERE ug.user_id = ?
    ORDER BY ug.added_at DESC
');
$stmtGames->execute([$userId]);
$userGames = $stmtGames->fetchAll();

$totalPlaytime = array_sum(array_column($userGames, 'playtime_hours'));

$stmtAch = $db->prepare('
    SELECT COUNT(*) as total,
           SUM(CASE WHEN ua.id IS NOT NULL THEN 1 ELSE 0 END) as unlocked
    FROM achievements a
    JOIN user_games ug ON ug.game_id = a.game_id AND ug.user_id = ?
    LEFT JOIN user_achievements ua ON ua.achievement_id = a.id AND ua.user_id = ?
');
$stmtAch->execute([$userId, $userId]);
$achStats = $stmtAch->fetch();
$totalAch = $achStats['total'] ?? 0;
$unlockedAch = $achStats['unlocked'] ?? 0;
$completionPct = $totalAch > 0 ? round(($unlockedAch / $totalAch) * 100) : 0;

include __DIR__ . '/../../includes/header.php';
?>

    <div class="max-w-6xl mx-auto px-4 py-12">

        <div class="text-center mb-10">
            <h1 class="font-tft text-4xl font-bold text-gold mb-2">Ma Collection</h1>
            <p class="text-gray-400">Vos jeux et vos succès</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-2xl font-bold text-gold"><?= count($userGames) ?></p>
                <p class="text-gray-400 text-xs mt-1">Jeux possédés</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-2xl font-bold text-gold"><?= $totalPlaytime ?>h</p>
                <p class="text-gray-400 text-xs mt-1">Temps de jeu</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-2xl font-bold text-gold"><?= $unlockedAch ?>/<?= $totalAch ?></p>
                <p class="text-gray-400 text-xs mt-1">Succès débloqués</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-2xl font-bold text-gold"><?= $completionPct ?>%</p>
                <p class="text-gray-400 text-xs mt-1">Complétion</p>
            </div>
        </div>

        <!-- Barre progression globale -->
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 mb-10">
            <div class="flex items-center justify-between mb-3">
                <span class="font-tft text-sm text-gold">Progression globale</span>
                <span class="text-gray-400 text-sm"><?= $unlockedAch ?> / <?= $totalAch ?> succès</span>
            </div>
            <div class="w-full h-3 bg-tft-dark rounded-full overflow-hidden">
                <div class="h-full bg-linear-to-r from-gold to-gold-dark rounded-full transition-all duration-700"
                     style="width: <?= $completionPct ?>%"></div>
            </div>
        </div>

        <!-- Liste des jeux -->
        <?php if (empty($userGames)): ?>
            <div class="text-center py-20">
                <p class="font-tft text-2xl text-gold mb-2">Aucun jeu dans votre collection</p>
                <p class="text-gray-500 mb-6">Parcourez le catalogue et ajoutez des jeux !</p>
                <a href="/pages/games/index.php"
                   class="inline-block bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold px-6 py-3 rounded-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    🎮 Voir le catalogue
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($userGames as $ug):
                    $stmtA = $db->prepare('
                    SELECT a.*,
                           ua.unlocked_at
                    FROM achievements a
                    LEFT JOIN user_achievements ua ON ua.achievement_id = a.id AND ua.user_id = ?
                    WHERE a.game_id = ?
                    ORDER BY a.rarity DESC
                ');
                    $stmtA->execute([$userId, $ug['id']]);
                    $achievements = $stmtA->fetchAll();

                    $stmtL = $db->prepare('SELECT * FROM levels WHERE game_id = ? ORDER BY CASE difficulty WHEN "easy" THEN 1 WHEN "medium" THEN 2 WHEN "hard" THEN 3 WHEN "extreme" THEN 4 END');
                    $stmtL->execute([$ug['id']]);
                    $levels = $stmtL->fetchAll();

                    $gameUnlocked = count(array_filter($achievements, fn($a) => $a['unlocked_at'] !== null));
                    $gameTotal = count($achievements);
                    $gamePct = $gameTotal > 0 ? round(($gameUnlocked / $gameTotal) * 100) : 0;
                    ?>
                    <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-2xl overflow-hidden hover:border-gold/50 transition-all">

                        <!-- Header jeu -->
                        <div class="flex flex-col sm:flex-row">
                            <div class="sm:w-48 h-40 sm:h-auto shrink-0">
                                <?php if ($ug['image']): ?>
                                    <img src="<?= e($ug['image']) ?>" alt="<?= e($ug['name']) ?>"
                                         class="w-full h-full object-cover opacity-80">
                                <?php else: ?>
                                    <div class="w-full h-full bg-tft-dark flex items-center justify-center text-5xl">🎮
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 p-6">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-3">
                                    <div>
                                        <h2 class="font-tft text-2xl font-bold text-gold"><?= e($ug['name']) ?></h2>
                                        <span class="inline-block bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full mt-1">
                                        <?= e($ug['type']) ?>
                                    </span>
                                    </div>
                                    <div class="flex items-center gap-4 shrink-0">
                                        <div class="text-right">
                                            <p class="font-tft text-xl font-bold text-gold"><?= $ug['playtime_hours'] ?>
                                                h</p>
                                            <p class="text-gray-500 text-xs">de jeu</p>
                                        </div>
                                        <form action="/pages/games/remove_from_collection.php" method="POST" class="inline"
                                              onsubmit="return confirm('Retirer ce jeu de votre collection ?')">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $ug['id'] ?>">
                                            <button type="submit"
                                                    class="border border-red-500/50 text-red-400 px-3 py-1.5 rounded-lg hover:bg-red-500/20 transition text-xs cursor-pointer">
                                                🗑️ Retirer
                                            </button>
                                        </form>
                                    </div>
                                </div>


                                <!-- Progression -->
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-tft-dark rounded-full overflow-hidden">
                                        <div class="h-full bg-linear-to-r from-gold to-gold-dark rounded-full"
                                             style="width: <?= $gamePct ?>%"></div>
                                    </div>
                                    <span class="text-sm <?= $gamePct === 100 ? 'text-green-400' : 'text-gray-400' ?>">
                                    <?= $gameUnlocked ?>/<?= $gameTotal ?>
                                        <?php if ($gamePct === 100): ?> ✓<?php endif; ?>
                                </span>
                                </div>
                                <p class="text-xs text-gray-600 mt-2">Ajouté
                                    le <?= date('d/m/Y', strtotime($ug['added_at'])) ?></p>
                            </div>
                        </div>

                        <!-- Niveaux -->
                        <?php if (!empty($levels)): ?>
                            <div class="border-t border-tft-border px-6 py-3">
                                <button onclick="this.closest('.border-t').querySelector('.levels-list').classList.toggle('hidden'); this.querySelector('.chev').classList.toggle('rotate-180')"
                                        class="w-full flex items-center justify-between text-sm text-gray-400 hover:text-gold transition cursor-pointer">
                                    <span class="font-tft text-gold text-sm">Niveaux (<?= count($levels) ?>)</span>
                                    <svg class="w-4 h-4 chev transition-transform" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="levels-list hidden mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
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
                                                <div class="flex items-center gap-2">
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

                        <!-- Succès -->
                        <?php if (!empty($achievements)): ?>
                            <div class="border-t border-tft-border px-6 py-3">
                                <button onclick="this.closest('.border-t').querySelector('.ach-list').classList.toggle('hidden'); this.querySelector('.chev2').classList.toggle('rotate-180')"
                                        class="w-full flex items-center justify-between text-sm text-gray-400 hover:text-gold transition cursor-pointer">
                                    <span class="font-tft text-gold text-sm">Succès (<?= $gameUnlocked ?>/<?= $gameTotal ?>)</span>
                                    <svg class="w-4 h-4 chev2 transition-transform" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="ach-list hidden mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <?php foreach ($achievements as $a):
                                        $r = rarityConfig($a['rarity']);
                                        $unlocked = $a['unlocked_at'] !== null;
                                        ?>
                                        <div class="flex items-start gap-3 p-3 rounded-lg border
                                    <?= $unlocked ? $r['border'] . ' ' . $r['bg'] : 'border-tft-border bg-tft-dark/50 opacity-60' ?>">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 <?= $unlocked ? $r['bg'] . ' ' . $r['color'] : 'bg-gray-700/50 text-gray-600' ?>">
                                                <?= $unlocked ? '🏆' : '🔒' ?>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="text-sm font-medium <?= $unlocked ? 'text-gray-200' : 'text-gray-500' ?> truncate">
                                                        <?= e($a['name']) ?>
                                                    </p>
                                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full <?= $r['color'] ?> <?= $r['bg'] ?>">
                                                <?= $r['label'] ?>
                                            </span>
                                                </div>
                                                <p class="text-xs <?= $unlocked ? 'text-gray-400' : 'text-gray-600' ?> mt-0.5">
                                                    <?= e($a['description'] ?? '') ?>
                                                </p>
                                                <?php if ($unlocked): ?>
                                                    <p class="text-[10px] text-gray-600 mt-1">Débloqué
                                                        le <?= date('d/m/Y', strtotime($a['unlocked_at'])) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($unlocked): ?><span
                                                    class="text-green-400 shrink-0">✓</span><?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>