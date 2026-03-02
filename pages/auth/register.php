<?php
session_start();
$pageTitle = 'Inscription — PHP';

include __DIR__ . '/../../includes/header.php';
?>

<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-8 shadow-[0_0_15px_rgba(240,178,50,0.3)]">
        <h1 class="font-tft text-3xl font-bold text-gold mb-2 text-center">📜 Inscription</h1>
        <p class="text-gray-500 text-center text-sm mb-8">Rejoignez l'arène de Teamfight Tactics</p>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gold-light mb-1">Nom d'invocateur</label>
                <input type="text" id="username" name="username" required placeholder="MonPseudo"
                       class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gold-light mb-1">Email</label>
                <input type="email" id="email" name="email" required placeholder="invocateur@tft.gg"
                       class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gold-light mb-1">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="••••••••"
                       class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
            </div>
            <div>
                <label for="gender" class="block text-sm font-medium text-gold-light mb-1">Genre</label>
                <select id="gender" name="gender" required
                        class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
                    <option value="">-- Choisir --</option>
                    <option value="male">Homme</option>
                    <option value="female">Femme</option>
                    <option value="other">Autre</option>
                </select>
            </div>
            <button type="submit"
                    class="w-full bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold tracking-wide py-3 rounded-lg text-lg hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.5)] transition-all">
                ⚔️ Rejoindre l'arène
            </button>
            <p class="text-center text-gray-500 text-sm">
                Déjà un compte ?
                <a href="/pages/auth/login.php" class="text-gold hover:text-gold-light hover:underline transition">Se connecter</a>
            </p>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
