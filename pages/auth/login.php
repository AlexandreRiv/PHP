<?php
session_start();
$pageTitle = 'Connexion — PHP';

include __DIR__ . '/../../includes/header.php';
?>

<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-8 shadow-[0_0_15px_rgba(139,92,246,0.3)]">
        <h1 class="font-tft text-3xl font-bold text-gold mb-2 text-center">⚔️ Connexion</h1>
        <p class="text-gray-500 text-center text-sm mb-8">Connectez-vous pour accéder à votre plateau</p>

        <form action="" method="POST" class="space-y-6">
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
            <button type="submit"
                    class="w-full bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold tracking-wide py-3 rounded-lg text-lg hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.5)] transition-all">
                🔐 Se connecter
            </button>
            <p class="text-center text-gray-500 text-sm">
                Pas encore de compte ?
                <a href="/pages/auth/register.php" class="text-gold hover:text-gold-light hover:underline transition">S'inscrire</a>
            </p>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
