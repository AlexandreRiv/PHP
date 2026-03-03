<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('/pages/admin/users.php');

$db = getDB();
$stmt = $db->prepare('SELECT * FROM user WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) redirect('/pages/admin/users.php');

$pageTitle = 'Éditer ' . e($user['username']) . ' — Admin';
$errors = [];
$old = $user;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'username' => trim($_POST['username'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'role' => $_POST['role'] ?? 'user',
        'gender' => $_POST['gender'] ?? 'other',
    ];

    if (mb_strlen($old['username']) < 3)
        $errors['username'] = 'Le pseudo doit faire au moins 3 caractères.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Email invalide.';
    if (!in_array($old['role'], ['admin', 'user']))
        $errors['role'] = 'Rôle invalide.';

    if (empty($errors['email'])) {
        $check = $db->prepare('SELECT id FROM user WHERE email = ? AND id != ?');
        $check->execute([$old['email'], $id]);
        if ($check->fetch()) $errors['email'] = 'Email déjà utilisé.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare('UPDATE user SET username=?, email=?, role=?, gender=? WHERE id=?');
        $stmt->execute([$old['username'], $old['email'], $old['role'], $old['gender'], $id]);
        setFlash('Utilisateur mis à jour !', 'success');
        redirect('/pages/admin/users.php');
    }
}

include __DIR__ . '/../../includes/header.php';
?>

    <div class="max-w-lg mx-auto px-4 py-12">
        <div class="flex items-center gap-3 mb-2">
            <h1 class="font-tft text-3xl font-bold text-gold">Éditer un utilisateur</h1>
            <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full uppercase">Admin</span>
        </div>
        <p class="text-gray-500 mb-8">Modification de <span class="text-gold"><?= e($user['username']) ?></span></p>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 mb-6 text-red-400 text-sm space-y-1">
                <?php foreach ($errors as $err): ?><p>⚠ <?= e($err) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-medium text-gold-light mb-1">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required value="<?= e($old['username']) ?>"
                       class="w-full bg-tft-card-deep border <?= isset($errors['username']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gold-light mb-1">Email</label>
                <input type="email" id="email" name="email" required value="<?= e($old['email']) ?>"
                       class="w-full bg-tft-card-deep border <?= isset($errors['email']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
            </div>
            <div>
                <label for="gender" class="block text-sm font-medium text-gold-light mb-1">Genre</label>
                <select id="gender" name="gender"
                        class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                    <option value="male" <?= $old['gender'] === 'male' ? 'selected' : '' ?>>Homme</option>
                    <option value="female" <?= $old['gender'] === 'female' ? 'selected' : '' ?>>Femme</option>
                    <option value="other" <?= $old['gender'] === 'other' ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gold-light mb-1">Rôle</label>
                <select id="role" name="role"
                        class="w-full bg-tft-card-deep border <?= isset($errors['role']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                    <option value="user" <?= $old['role'] === 'user' ? 'selected' : '' ?>>Joueur</option>
                    <option value="admin" <?= $old['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="flex gap-4 pt-2">
                <button type="submit"
                        class="flex-1 bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold py-3 rounded-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    💾 Enregistrer
                </button>
                <a href="/pages/admin/users.php"
                   class="px-6 py-3 border border-tft-border text-gray-400 rounded-lg hover:border-gold hover:text-gold transition text-sm flex items-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>