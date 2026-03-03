<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

if (isLoggedIn()) redirect('/index.php');

$pageTitle = 'Inscription — OAPDN';
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'gender' => $_POST['gender'] ?? '',
    ];
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (empty($old['username']) || mb_strlen($old['username']) < 3) {
        $errors['username'] = 'Le pseudo doit faire au moins 3 caractères.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse e-mail invalide.';
    }
    if (mb_strlen($password) < 6) {
        $errors['password'] = 'Le mot de passe doit faire au moins 6 caractères.';
    }
    if ($password !== $password2) {
        $errors['password2'] = 'Les mots de passe ne correspondent pas.';
    }
    if (!in_array($old['gender'], ['male', 'female', 'other'])) {
        $errors['gender'] = 'Veuillez choisir un genre.';
    }

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare('SELECT id FROM user WHERE email = ?');
        $stmt->execute([$old['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Cette adresse e-mail est déjà utilisée.';
        }
    }

    if (empty($errors)) {
        $db = getDB();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare('
            INSERT INTO user (username, email, password_hash, role, gender)
            VALUES (?, ?, ?, \'user\', ?)
        ');
        $stmt->execute([$old['username'], $old['email'], $hash, $old['gender']]);

        $newUser = $db->prepare('SELECT * FROM user WHERE id = ?');
        $newUser->execute([$db->lastInsertId()]);
        loginUser($newUser->fetch());

        setFlash('Bienvenue ' . e($old['username']) . ' ! Votre compte a été créé.', 'success');
        redirect('/pages/user/profile.php');
    }
}

include __DIR__ . '/../../includes/header.php';
?>

    <div class="max-w-md mx-auto px-4 py-16">
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-8 shadow-[0_0_15px_rgba(240,178,50,0.3)]">
            <h1 class="font-tft text-3xl font-bold text-gold mb-2 text-center">Inscription</h1>
            <p class="text-gray-500 text-center text-sm mb-8">Rejoignez l'arène de Teamfight Tactics</p>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-3 mb-6 text-red-400 text-sm space-y-1">
                    <?php foreach ($errors as $err): ?>
                        <p>⚠ <?= e($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-gold-light mb-1">Nom
                        d'invocateur</label>
                    <input type="text" id="username" name="username" required
                           value="<?= e($old['username'] ?? '') ?>"
                           placeholder="MonPseudo"
                           class="w-full bg-tft-card-deep border <?= isset($errors['username']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gold-light mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                           value="<?= e($old['email'] ?? '') ?>"
                           placeholder="example@gmail.com"
                           class="w-full bg-tft-card-deep border <?= isset($errors['email']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gold-light mb-1">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           class="w-full bg-tft-card-deep border <?= isset($errors['password']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
                </div>
                <div>
                    <label for="password2" class="block text-sm font-medium text-gold-light mb-1">Confirmer le mot de
                        passe</label>
                    <input type="password" id="password2" name="password2" required
                           placeholder="••••••••"
                           class="w-full bg-tft-card-deep border <?= isset($errors['password2']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
                </div>
                <div>
                    <label for="gender" class="block text-sm font-medium text-gold-light mb-1">Genre</label>
                    <select id="gender" name="gender" required
                            class="w-full bg-tft-card-deep border <?= isset($errors['gender']) ? 'border-red-500' : 'border-tft-border' ?> rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
                        <option value="">-- Choisir --</option>
                        <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Homme</option>
                        <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Femme
                        </option>
                        <option value="other" <?= ($old['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Autre</option>
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold tracking-wide py-3 rounded-lg text-lg hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.5)] transition-all">
                    Rejoindre !
                </button>
                <p class="text-center text-gray-500 text-sm">
                    Déjà un compte ?
                    <a href="/pages/auth/login.php" class="text-gold hover:text-gold-light hover:underline transition">Se
                        connecter</a>
                </p>
            </form>
        </div>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>