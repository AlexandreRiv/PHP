<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('/pages/games/index.php');

$db = getDB();
$stmt = $db->prepare('SELECT * FROM games WHERE id = ?');
$stmt->execute([$id]);
$game = $stmt->fetch();
if (!$game) abort(404, 'Ce jeu n\'existe pas.');

// Charger les niveaux existants
$stmtLvl = $db->prepare('SELECT * FROM levels WHERE game_id = ? ORDER BY CASE difficulty WHEN "easy" THEN 1 WHEN "medium" THEN 2 WHEN "hard" THEN 3 WHEN "extreme" THEN 4 END');
$stmtLvl->execute([$id]);
$existingLevels = $stmtLvl->fetchAll();

// Charger les succès existants
$stmtAch = $db->prepare('SELECT * FROM achievements WHERE game_id = ? ORDER BY rarity DESC');
$stmtAch->execute([$id]);
$existingAchievements = $stmtAch->fetchAll();

$pageTitle = 'Éditer ' . e($game['name']) . ' — Admin';
$errors = [];
$old = $game;
$oldLevels = array_map(fn($l) => ['id' => $l['id'], 'name' => $l['name'], 'difficulty' => $l['difficulty'], 'description' => $l['description'] ?? ''], $existingLevels);
$oldAchievements = array_map(fn($a) => ['id' => $a['id'], 'name' => $a['name'], 'rarity' => $a['rarity'], 'description' => $a['description'] ?? ''], $existingAchievements);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $old = [
            'name' => trim($_POST['name'] ?? ''),
            'type' => trim($_POST['type'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'image' => trim($_POST['image'] ?? ''),
            'price' => trim($_POST['price'] ?? '0'),
    ];

    // Récupérer les niveaux du POST
    $oldLevels = [];
    if (!empty($_POST['levels'])) {
        foreach ($_POST['levels'] as $lvl) {
            $lvlName = trim($lvl['name'] ?? '');
            $lvlDiff = $lvl['difficulty'] ?? 'medium';
            $lvlDesc = trim($lvl['description'] ?? '');
            if ($lvlName !== '') {
                $oldLevels[] = ['name' => $lvlName, 'difficulty' => $lvlDiff, 'description' => $lvlDesc];
            }
        }
    }

    // Récupérer les succès du POST
    $oldAchievements = [];
    if (!empty($_POST['achievements'])) {
        foreach ($_POST['achievements'] as $ach) {
            $achName = trim($ach['name'] ?? '');
            $achRarity = $ach['rarity'] ?? 'common';
            $achDesc = trim($ach['description'] ?? '');
            if ($achName !== '') {
                $oldAchievements[] = ['name' => $achName, 'rarity' => $achRarity, 'description' => $achDesc];
            }
        }
    }

    if (empty($old['name'])) $errors['name'] = 'Le nom est obligatoire.';
    if (empty($old['type'])) $errors['type'] = 'Le genre est obligatoire.';

    if (empty($errors)) {
        $db->beginTransaction();

        // Mettre à jour le jeu
        $stmt = $db->prepare('UPDATE games SET name=?, type=?, description=?, image=?, price=? WHERE id=?');
        $stmt->execute([$old['name'], $old['type'], $old['description'], $old['image'] ?: null, (float)$old['price'], $id]);

        // Remplacer les niveaux : supprimer puis réinsérer
        $db->prepare('DELETE FROM levels WHERE game_id = ?')->execute([$id]);
        if (!empty($oldLevels)) {
            $stmtLvlIns = $db->prepare('INSERT INTO levels (game_id, name, difficulty, description) VALUES (?, ?, ?, ?)');
            foreach ($oldLevels as $lvl) {
                $diff = in_array($lvl['difficulty'], ['easy', 'medium', 'hard', 'extreme']) ? $lvl['difficulty'] : 'medium';
                $stmtLvlIns->execute([$id, $lvl['name'], $diff, $lvl['description']]);
            }
        }

        // Remplacer les succès : supprimer puis réinsérer
        // Note : on supprime aussi les user_achievements liés (CASCADE dans le schema)
        $db->prepare('DELETE FROM achievements WHERE game_id = ?')->execute([$id]);
        if (!empty($oldAchievements)) {
            $stmtAchIns = $db->prepare('INSERT INTO achievements (game_id, name, description, rarity) VALUES (?, ?, ?, ?)');
            foreach ($oldAchievements as $ach) {
                $rarity = in_array($ach['rarity'], ['common', 'uncommon', 'rare', 'epic', 'legendary']) ? $ach['rarity'] : 'common';
                $stmtAchIns->execute([$id, $ach['name'], $ach['description'], $rarity]);
            }
        }

        $db->commit();
        setFlash('Jeu mis à jour avec succès !', 'success');
        redirect('/pages/games/show.php?id=' . $id);
    }
}

include __DIR__ . '/../../includes/header.php';
?>
    <div class="max-w-3xl mx-auto px-4 py-10">
        <div class="flex items-center gap-3 mb-2">
            <h1 class="font-tft text-3xl font-bold text-gold">Éditer un jeu</h1>
            <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full uppercase">Admin</span>
        </div>
        <p class="text-gray-500 mb-8">Modification de <span class="text-gold"><?= e($game['name']) ?></span></p>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 mb-6 text-red-400 text-sm space-y-1">
                <?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-8">
            <?= csrfField() ?>

            <!-- Infos du jeu -->
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 space-y-5">
                <h2 class="font-tft text-xl font-bold text-gold">Informations</h2>
                <div>
                    <label for="name" class="block text-sm font-medium text-gold-light mb-1">Nom du jeu *</label>
                    <input type="text" id="name" name="name" required value="<?= e($old['name']) ?>"
                           class="w-full bg-tft-card-deep border <?= isset($errors['name']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gold-light mb-1">Genre *</label>
                    <input type="text" id="type" name="type" required value="<?= e($old['type']) ?>"
                           class="w-full bg-tft-card-deep border <?= isset($errors['type']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gold-light mb-1">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all"><?= e($old['description'] ?? '') ?></textarea>
                </div>
                <div>
                    <label for="image" class="block text-sm font-medium text-gold-light mb-1">URL de l'image</label>
                    <input type="url" id="image" name="image" value="<?= e($old['image'] ?? '') ?>"
                           placeholder="https://..."
                           class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gold-light mb-1">Prix (en euros)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?= e($old['price'] ?? '0') ?>"
                           placeholder="29.99"
                           class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                </div>
            </div>

            <!-- Niveaux -->
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-tft text-xl font-bold text-gold">Niveaux</h2>
                    <button type="button" onclick="addLevel()"
                            class="text-xs text-gold border border-gold/30 px-3 py-1.5 rounded-lg hover:bg-gold/10 transition cursor-pointer flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter un niveau
                    </button>
                </div>
                <div id="levels-container" class="space-y-3">
                    <?php foreach ($oldLevels as $i => $lvl): ?>
                        <div class="level-row flex flex-col sm:flex-row gap-2 p-3 border border-tft-border rounded-lg bg-tft-dark/30">
                            <input type="text" name="levels[<?= $i ?>][name]" value="<?= e($lvl['name']) ?>" placeholder="Nom du niveau"
                                   class="flex-1 bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
                            <select name="levels[<?= $i ?>][difficulty]"
                                    class="bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
                                <option value="easy" <?= $lvl['difficulty'] === 'easy' ? 'selected' : '' ?>>Facile</option>
                                <option value="medium" <?= $lvl['difficulty'] === 'medium' ? 'selected' : '' ?>>Moyen</option>
                                <option value="hard" <?= $lvl['difficulty'] === 'hard' ? 'selected' : '' ?>>Difficile</option>
                                <option value="extreme" <?= $lvl['difficulty'] === 'extreme' ? 'selected' : '' ?>>Extrême</option>
                            </select>
                            <input type="text" name="levels[<?= $i ?>][description]" value="<?= e($lvl['description']) ?>" placeholder="Description"
                                   class="flex-[2] bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
                            <button type="button" onclick="this.closest('.level-row').remove()"
                                    class="text-red-400 hover:text-red-300 px-2 cursor-pointer shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-gray-600 text-xs mt-3">Les niveaux vides seront ignorés.</p>
            </div>

            <!-- Succès -->
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-tft text-xl font-bold text-gold">Succès</h2>
                    <button type="button" onclick="addAchievement()"
                            class="text-xs text-gold border border-gold/30 px-3 py-1.5 rounded-lg hover:bg-gold/10 transition cursor-pointer flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter un succès
                    </button>
                </div>
                <div id="achievements-container" class="space-y-3">
                    <?php foreach ($oldAchievements as $i => $ach): ?>
                        <div class="ach-row flex flex-col sm:flex-row gap-2 p-3 border border-tft-border rounded-lg bg-tft-dark/30">
                            <input type="text" name="achievements[<?= $i ?>][name]" value="<?= e($ach['name']) ?>" placeholder="Nom du succès"
                                   class="flex-1 bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
                            <select name="achievements[<?= $i ?>][rarity]"
                                    class="bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
                                <option value="common" <?= $ach['rarity'] === 'common' ? 'selected' : '' ?>>Commun</option>
                                <option value="uncommon" <?= $ach['rarity'] === 'uncommon' ? 'selected' : '' ?>>Peu commun</option>
                                <option value="rare" <?= $ach['rarity'] === 'rare' ? 'selected' : '' ?>>Rare</option>
                                <option value="epic" <?= $ach['rarity'] === 'epic' ? 'selected' : '' ?>>Épique</option>
                                <option value="legendary" <?= $ach['rarity'] === 'legendary' ? 'selected' : '' ?>>Légendaire</option>
                            </select>
                            <input type="text" name="achievements[<?= $i ?>][description]" value="<?= e($ach['description']) ?>" placeholder="Description"
                                   class="flex-[2] bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
                            <button type="button" onclick="this.closest('.ach-row').remove()"
                                    class="text-red-400 hover:text-red-300 px-2 cursor-pointer shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-gray-600 text-xs mt-3">Les succès vides seront ignorés. ⚠️ Modifier les succès réinitialisera la progression des joueurs sur ce jeu.</p>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold py-3 rounded-lg text-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    Enregistrer
                </button>
                <a href="/pages/games/show.php?id=<?= $id ?>"
                   class="px-6 py-3 border border-tft-border text-gray-400 rounded-lg hover:border-gold hover:text-gold transition text-sm flex items-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    <script>
    let levelIdx = <?= count($oldLevels) ?>;
    let achIdx = <?= count($oldAchievements) ?>;

    function addLevel() {
        const container = document.getElementById('levels-container');
        const html = `
        <div class="level-row flex flex-col sm:flex-row gap-2 p-3 border border-tft-border rounded-lg bg-tft-dark/30">
            <input type="text" name="levels[${levelIdx}][name]" placeholder="Nom du niveau"
                   class="flex-1 bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
            <select name="levels[${levelIdx}][difficulty]"
                    class="bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
                <option value="easy">Facile</option>
                <option value="medium" selected>Moyen</option>
                <option value="hard">Difficile</option>
                <option value="extreme">Extrême</option>
            </select>
            <input type="text" name="levels[${levelIdx}][description]" placeholder="Description"
                   class="flex-[2] bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
            <button type="button" onclick="this.closest('.level-row').remove()"
                    class="text-red-400 hover:text-red-300 px-2 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
        levelIdx++;
    }

    function addAchievement() {
        const container = document.getElementById('achievements-container');
        const html = `
        <div class="ach-row flex flex-col sm:flex-row gap-2 p-3 border border-tft-border rounded-lg bg-tft-dark/30">
            <input type="text" name="achievements[${achIdx}][name]" placeholder="Nom du succès"
                   class="flex-1 bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
            <select name="achievements[${achIdx}][rarity]"
                    class="bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
                <option value="common" selected>Commun</option>
                <option value="uncommon">Peu commun</option>
                <option value="rare">Rare</option>
                <option value="epic">Épique</option>
                <option value="legendary">Légendaire</option>
            </select>
            <input type="text" name="achievements[${achIdx}][description]" placeholder="Description"
                   class="flex-[2] bg-tft-card-deep border border-tft-border rounded-lg px-3 py-2 text-gray-200 text-sm focus:border-gold outline-none">
            <button type="button" onclick="this.closest('.ach-row').remove()"
                    class="text-red-400 hover:text-red-300 px-2 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
        achIdx++;
    }
    </script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>