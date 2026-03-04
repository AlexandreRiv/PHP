<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pageTitle = 'Modifier le profil — OAPDN';
$db = getDB();
$userId = $_SESSION['user']['id'];

$stmt = $db->prepare('SELECT * FROM user WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

$errors = [];
$old = $user;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
    ];
    $newPassword = $_POST['new_password'] ?? '';
    $newPassword2 = $_POST['new_password2'] ?? '';

    if (strlen($old['username']) < 3)
        $errors['username'] = 'Le pseudo doit faire au moins 3 caractères.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Adresse e-mail invalide.';

    if (empty($errors['email'])) {
        $check = $db->prepare('SELECT id FROM user WHERE email = ? AND id != ?');
        $check->execute([$old['email'], $userId]);
        if ($check->fetch()) $errors['email'] = 'Cet email est déjà utilisé.';
    }

    if (!empty($newPassword)) {
        if (strlen($newPassword) < 6)
            $errors['new_password'] = 'Le mot de passe doit faire au moins 6 caractères.';
        elseif ($newPassword !== $newPassword2)
            $errors['new_password2'] = 'Les mots de passe ne correspondent pas.';
    }

    if (empty($errors)) {
        if (!empty($newPassword)) {
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $db->prepare('UPDATE user SET username=?, email=?,  password_hash=? WHERE id=?');
            $stmt->execute([$old['username'], $old['email'], $hash, $userId]);
        } else {
            $stmt = $db->prepare('UPDATE user SET username=?, email=? WHERE id=?');
            $stmt->execute([$old['username'], $old['email'], $userId]);
        }
        $_SESSION['user']['username'] = $old['username'];
        $_SESSION['user']['email'] = $old['email'];
        setFlash('Profil mis à jour !', 'success');
        redirect('/pages/user/profile.php');
    }
}

include __DIR__ . '/../../includes/header.php';
?>

    <div class="max-w-lg mx-auto px-4 py-12">
        <h1 class="font-tft text-3xl font-bold text-gold mb-2">Modifier le profil</h1>
        <p class="text-gray-500 mb-8">Mettez à jour vos informations personnelles</p>

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
                <label for="email" class="block text-sm font-medium text-gold-light mb-1">Adresse e-mail</label>
                <input type="email" id="email" name="email" required value="<?= e($old['email']) ?>"
                       class="w-full bg-tft-card-deep border <?= isset($errors['email']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
            </div>

            <div class="border-t border-tft-border pt-5">
                <p class="text-xs text-gray-500 mb-4">Laissez vide pour conserver votre mot de passe actuel.</p>
                <div class="space-y-4">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gold-light mb-1">Nouveau mot de
                            passe</label>
                        <input type="password" id="new_password" name="new_password" placeholder="••••••••"
                               class="w-full bg-tft-card-deep border <?= isset($errors['new_password']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                    </div>
                    <div>
                        <label for="new_password2" class="block text-sm font-medium text-gold-light mb-1">Confirmer le
                            mot de passe</label>
                        <input type="password" id="new_password2" name="new_password2" placeholder="••••••••"
                               class="w-full bg-tft-card-deep border <?= isset($errors['new_password2']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-2">
                <button type="submit"
                        class="flex-1 bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold py-3 rounded-lg hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    💾 Enregistrer
                </button>
                <a href="/pages/user/profile.php"
                   class="px-6 py-3 border border-tft-border text-gray-400 rounded-lg hover:border-gold hover:text-gold transition text-sm flex items-center">
                    Annuler
                </a>
            </div>
        </form>

        <!-- Suppression du compte -->
        <div class="mt-10 border-t border-red-500/30 pt-8">
            <h2 class="font-tft text-xl font-bold text-red-400 mb-2">Zone dangereuse</h2>
            <p class="text-gray-500 text-sm mb-4">
                La suppression de votre compte est <strong class="text-red-400">irréversible</strong>.
                Toutes vos données seront définitivement perdues.
            </p>
            <button onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                    class="border border-red-500/50 text-red-400 px-5 py-2.5 rounded-lg hover:bg-red-500/20 hover:text-red-300 transition font-medium text-sm cursor-pointer">
                🗑️ Supprimer mon compte
            </button>
        </div>
    </div>

    <!-- Modal de confirmation -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
        <div class="bg-tft-card border border-red-500/30 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="font-tft text-2xl font-bold text-red-400 mb-2">Supprimer votre compte ?</h3>
            <p class="text-gray-400 text-sm mb-6">
                Cette action est irréversible. Entrez votre mot de passe pour confirmer la suppression.
            </p>
            <form action="/pages/user/delete_account.php" method="POST" class="space-y-4">
                <div>
                    <label for="delete-password" class="block text-sm font-medium text-gold-light mb-1">Mot de passe</label>
                    <input type="password" id="delete-password" name="password" required placeholder="••••••••"
                           class="w-full bg-tft-card-deep border border-red-500/30 rounded-lg px-4 py-3 text-gray-200 focus:border-red-500 outline-none transition-all">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-tft font-bold py-3 rounded-lg transition-all cursor-pointer">
                        🗑️ Confirmer la suppression
                    </button>
                    <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')"
                            class="px-6 py-3 border border-tft-border text-gray-400 rounded-lg hover:border-gold hover:text-gold transition text-sm cursor-pointer">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>