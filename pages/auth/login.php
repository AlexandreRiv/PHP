<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();

if (isLoggedIn()) redirect('/index.php');

$pageTitle = 'Connexion — OAPDN';
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $old = ['email' => trim($_POST['email'] ?? '')];
    $password = $_POST['password'] ?? '';

    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Adresse e-mail invalide.';
    if (empty($password))
        $errors['password'] = 'Veuillez entrer votre mot de passe.';

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->execute([$old['email']]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash']))
            $errors['global'] = 'Email ou mot de passe incorrect.';
    }

    if (empty($errors)) {
        // Re-hash le mot de passe si l'algorithme a évolué
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $db->prepare('UPDATE user SET password_hash = ? WHERE id = ?')
               ->execute([$newHash, $user['id']]);
        }

        session_regenerate_id(true);
        loginUser($user);
        setFlash('Bienvenue ' . e($user['username']) . ' !', 'success');
        redirect('/pages/user/profile.php');
    }
}

include __DIR__ . '/../../includes/header.php';
?>

    <div class="max-w-md mx-auto px-4 py-16">
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-8">
            <h1 class="font-tft text-3xl font-bold text-gold mb-2 text-center">Connexion</h1>
            <p class="text-gray-500 text-center text-sm mb-8">Connectez-vous pour accéder à votre plateau</p>

            <?php if (isset($errors['global'])): ?>
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 mb-6 text-red-400 text-sm">
                    ⚠ <?= e($errors['global']) ?>
                </div>
            <?php endif; ?>

            <?php $flash = getFlash();
            if ($flash): ?>
                <div class="bg-green-500/10 border border-green-500/30 rounded-lg px-4 py-3 mb-6 text-green-400 text-sm">
                    ✓ <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <?= csrfField() ?>
                <div>
                    <label for="email" class="block text-sm font-medium text-gold-light mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                           value="<?= e($old['email'] ?? '') ?>" placeholder="example@gmail.com"
                           class="w-full bg-tft-card-deep border <?= isset($errors['email']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gold-light mb-1">Mot de passe</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                           class="w-full bg-tft-card-deep border <?= isset($errors['password']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold outline-none transition-all">
                </div>
                <button type="submit"
                        class="w-full bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold py-3 rounded-lg text-lg hover:shadow-[0_0_20px_rgba(240,178,50,0.5)] transition-all">
                    Se connecter
                </button>
                <p class="text-center text-gray-500 text-sm">
                    Pas encore de compte ?
                    <a href="/pages/auth/register.php" class="text-gold hover:underline">S'inscrire</a>
                </p>
            </form>
        </div>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>