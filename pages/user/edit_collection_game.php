<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireLogin();

$gameId = (int)($_GET['game_id'] ?? 0);
if (!$gameId) redirect('/pages/user/collection.php');

$db = getDB();
$userId = $_SESSION['user']['id'];

// Vérifier que le jeu est dans la collection de l'utilisateur
$stmt = $db->prepare('
    SELECT g.*, ug.id as ug_id, ug.playtime_hours, ug.added_at
    FROM user_games ug
    JOIN games g ON g.id = ug.game_id
    WHERE ug.user_id = ? AND ug.game_id = ?
');
$stmt->execute([$userId, $gameId]);
$userGame = $stmt->fetch();
if (!$userGame) {
    setFlash('Ce jeu n\'est pas dans votre collection.', 'error');
    redirect('/pages/user/collection.php');
}

// Récupérer les succès du jeu + statut déverrouillage
$stmtA = $db->prepare('
    SELECT a.*, ua.unlocked_at
    FROM achievements a
    LEFT JOIN user_achievements ua ON ua.achievement_id = a.id AND ua.user_id = ?
    WHERE a.game_id = ?
    ORDER BY a.rarity DESC
');
$stmtA->execute([$userId, $gameId]);
$achievements = $stmtA->fetchAll();

$errors = [];
$old = ['playtime_hours' => $userGame['playtime_hours']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $playtime = trim($_POST['playtime_hours'] ?? '');
    $old['playtime_hours'] = $playtime;

    // Validation
    if ($playtime === '' || !is_numeric($playtime) || (int)$playtime < 0) {
        $errors['playtime_hours'] = 'Le temps de jeu doit être un nombre positif.';
    } elseif ((int)$playtime > 99999) {
        $errors['playtime_hours'] = 'Le temps de jeu ne peut pas dépasser 99 999 heures.';
    }

    if (empty($errors)) {
        $playtime = (int)$playtime;

        // Mettre à jour le temps de jeu
        $stmtUp = $db->prepare('UPDATE user_games SET playtime_hours = ? WHERE user_id = ? AND game_id = ?');
        $stmtUp->execute([$playtime, $userId, $gameId]);

        // Mettre à jour les succès
        $unlockedIds = $_POST['achievements'] ?? [];

        // Supprimer tous les succès de l'utilisateur pour ce jeu
        $stmtDel = $db->prepare('
            DELETE FROM user_achievements
            WHERE user_id = ? AND achievement_id IN (
                SELECT id FROM achievements WHERE game_id = ?
            )
        ');
        $stmtDel->execute([$userId, $gameId]);

        // Réinsérer les succès cochés
        if (!empty($unlockedIds)) {
            $stmtIns = $db->prepare('INSERT INTO user_achievements (user_id, achievement_id, unlocked_at) VALUES (?, ?, ?)');
            foreach ($unlockedIds as $achId) {
                $achId = (int)$achId;
                // Vérifier que le succès appartient bien au jeu
                $check = $db->prepare('SELECT id FROM achievements WHERE id = ? AND game_id = ?');
                $check->execute([$achId, $gameId]);
                if ($check->fetch()) {
                    // Utiliser l'ancienne date de déverrouillage si existante, sinon maintenant
                    $oldDate = null;
                    foreach ($achievements as $a) {
                        if ((int)$a['id'] === $achId && $a['unlocked_at'] !== null) {
                            $oldDate = $a['unlocked_at'];
                            break;
                        }
                    }
                    $stmtIns->execute([$userId, $achId, $oldDate ?? date('Y-m-d H:i:s')]);
                }
            }
        }

        setFlash('Statistiques de "' . e($userGame['name']) . '" mises à jour !', 'success');
        redirect('/pages/user/collection.php');
    }
}

$pageTitle = 'Modifier mes stats — ' . e($userGame['name']);
include __DIR__ . '/../../includes/header.php';
?>

<div class="max-w-3xl mx-auto px-4 py-12">

    <!-- En-tête -->
    <div class="flex items-center gap-4 mb-8">
        <?php if ($userGame['image']): ?>
            <img src="<?= e($userGame['image']) ?>" alt="<?= e($userGame['name']) ?>"
                 class="w-16 h-16 rounded-xl object-cover border border-tft-border">
        <?php else: ?>
            <div class="w-16 h-16 rounded-xl bg-tft-dark flex items-center justify-center border border-tft-border">
                <svg class="w-8 h-8 text-gold/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        <?php endif; ?>
        <div>
            <h1 class="font-tft text-3xl font-bold text-gold"><?= e($userGame['name']) ?></h1>
            <p class="text-gray-400 text-sm">Modifier mes statistiques</p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 mb-6 text-red-400 text-sm space-y-1">
            <?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-8">
        <?= csrfField() ?>

        <!-- Temps de jeu -->
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6">
            <h2 class="font-tft text-xl font-bold text-gold mb-4">Temps de jeu</h2>
            <div>
                <label for="playtime_hours" class="block text-sm font-medium text-gold-light mb-2">Heures de jeu</label>
                <div class="flex items-center gap-3">
                    <input type="number" id="playtime_hours" name="playtime_hours" min="0" max="99999"
                           value="<?= e((string)$old['playtime_hours']) ?>"
                           class="w-40 bg-tft-card-deep border <?= isset($errors['playtime_hours']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all text-center text-lg font-tft">
                    <span class="text-gray-400 text-sm">heures</span>
                </div>
            </div>
        </div>

        <!-- Succès -->
        <?php if (!empty($achievements)): ?>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-tft text-xl font-bold text-gold">Succes</h2>
                    <div class="flex gap-2">
                        <button type="button" onclick="document.querySelectorAll('.ach-checkbox').forEach(c => c.checked = true)"
                                class="text-xs text-gold border border-gold/30 px-3 py-1 rounded-lg hover:bg-gold/10 transition cursor-pointer">
                            Tout cocher
                        </button>
                        <button type="button" onclick="document.querySelectorAll('.ach-checkbox').forEach(c => c.checked = false)"
                                class="text-xs text-gray-400 border border-tft-border px-3 py-1 rounded-lg hover:border-gray-400 transition cursor-pointer">
                            Tout décocher
                        </button>
                    </div>
                </div>
                <p class="text-gray-500 text-sm mb-4">Cochez les succès que vous avez débloqués.</p>
                <div class="space-y-2">
                    <?php foreach ($achievements as $a):
                        $r = rarityConfig($a['rarity']);
                        $isUnlocked = $a['unlocked_at'] !== null;
                        // En cas de resoumission POST, prendre les valeurs du POST
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $isUnlocked = in_array((string)$a['id'], $_POST['achievements'] ?? []);
                        }
                        ?>
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-tft-border hover:border-gold/30 transition cursor-pointer group">
                            <input type="checkbox" name="achievements[]" value="<?= $a['id'] ?>"
                                   class="ach-checkbox w-5 h-5 rounded accent-[#FE895E] cursor-pointer"
                                <?= $isUnlocked ? 'checked' : '' ?>>
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 <?= $r['bg'] ?> <?= $r['color'] ?>">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-200 group-hover:text-gold transition"><?= e($a['name']) ?></span>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full <?= $r['color'] ?> <?= $r['bg'] ?>">
                                        <?= $r['label'] ?>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5"><?= e($a['description'] ?? '') ?></p>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="flex gap-4">
            <button type="submit"
                    class="flex-1 bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold py-3 rounded-lg text-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all cursor-pointer">
                Enregistrer
            </button>
            <a href="/pages/user/collection.php"
               class="px-6 py-3 border border-tft-border text-gray-400 rounded-lg hover:border-gold hover:text-gold transition text-sm flex items-center">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
