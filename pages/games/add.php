<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireAdmin();

$pageTitle = 'Ajouter un jeu — Admin';
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $old = [
            'name' => trim($_POST['name'] ?? ''),
            'type' => trim($_POST['type'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'image' => trim($_POST['image'] ?? ''),
            'price' => trim($_POST['price'] ?? '0'),
    ];

    if (empty($old['name'])) $errors['name'] = 'Le nom est obligatoire.';
    if (empty($old['type'])) $errors['type'] = 'Le genre est obligatoire.';

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO games (name, type, description, image, price) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$old['name'], $old['type'], $old['description'], $old['image'] ?: null, (float)$old['price']]);
        setFlash('Jeu "' . e($old['name']) . '" ajouté avec succès !', 'success');
        redirect('/pages/games/index.php');
    }
}

include __DIR__ . '/../../includes/header.php';
?>
    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="flex items-center gap-3 mb-2">
            <h1 class="font-tft text-3xl font-bold text-gold">Ajouter un jeu</h1>
            <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full uppercase">Admin</span>
        </div>
        <p class="text-gray-500 mb-8">Ajoutez un nouveau jeu au catalogue</p>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 mb-6 text-red-400 text-sm space-y-1">
                <?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <?= csrfField() ?>
            <div>
                <label for="name" class="block text-sm font-medium text-gold-light mb-1">Nom du jeu *</label>
                <input type="text" id="name" name="name" required value="<?= e($old['name'] ?? '') ?>"
                       placeholder="Ex: Elden Ring, Minecraft..."
                       class="w-full bg-tft-card-deep border <?= isset($errors['name']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gold-light mb-1">Genre *</label>
                <input type="text" id="type" name="type" required value="<?= e($old['type'] ?? '') ?>"
                       placeholder="Ex: RPG / Action"
                       class="w-full bg-tft-card-deep border <?= isset($errors['type']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gold-light mb-1">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Décrivez ce jeu..."
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
            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold py-3 rounded-lg text-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    Ajouter ce jeu
                </button>
                <a href="/pages/games/index.php"
                   class="px-6 py-3 border border-tft-border text-gray-400 rounded-lg hover:border-gold hover:text-gold transition text-sm flex items-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>