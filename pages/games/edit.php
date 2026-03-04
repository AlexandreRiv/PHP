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

$pageTitle = 'Éditer ' . e($game['name']) . ' — Admin';
$errors = [];
$old = $game;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $old = [
            'type' => trim($_POST['type'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'image' => trim($_POST['image'] ?? ''),
    ];

    if (empty($old['name'])) $errors['name'] = 'Le nom est obligatoire.';
    if (empty($old['type'])) $errors['type'] = 'Le genre est obligatoire.';

    if (empty($errors)) {
        $stmt = $db->prepare('UPDATE games SET name=?, type=?, description=?, image=? WHERE id=?');
        $stmt->execute([$old['name'], $old['type'], $old['description'], $old['image'] ?: null, $id]);
        setFlash('Jeu mis à jour avec succès !', 'success');
        redirect('/pages/games/show.php?id=' . $id);
    }
}

include __DIR__ . '/../../includes/header.php';
?>
    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="flex items-center gap-3 mb-2">
            <h1 class="font-tft text-3xl font-bold text-gold">Éditer un jeu</h1>
            <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full uppercase">Admin</span>
        </div>
        <p class="text-gray-500 mb-8">Modification de <span class="text-gold"><?= e($game['name']) ?></span></p>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 mb-6 text-red-400 text-sm space-y-1">
                <?php foreach ($errors as $err): ?><p>⚠ <?= e($err) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <?= csrfField() ?>
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
            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold py-3 rounded-lg text-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    💾 Enregistrer
                </button>
                <a href="/pages/games/show.php?id=<?= $id ?>"
                   class="px-6 py-3 border border-tft-border text-gray-400 rounded-lg hover:border-gold hover:text-gold transition text-sm flex items-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>