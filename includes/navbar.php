<?php
require_once __DIR__ . '/../includes/auth.php';
?>
<nav class="bg-linear-to-r from-tft-nav via-tft-nav-mid to-tft-nav border-b border-tft-border">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">

        <a href="/index.php" class="flex items-center gap-2">
            <span class="text-3xl">🎮</span>
            <span class="font-tft text-2xl font-bold text-gold">OAPDN</span>
        </a>

        <div class="flex gap-6 items-center">
            <a href="/index.php"
               class="text-gray-400 hover:text-gold transition font-medium">Accueil</a>
            <a href="/pages/games/index.php"
               class="text-gray-400 hover:text-gold transition font-medium">Jeux</a>

            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <a href="/pages/admin/dashboard.php"
                       class="text-gray-400 hover:text-gold transition font-medium">⚙ Admin</a>
                <?php endif; ?>
                <a href="/pages/games/add.php"
                   class="text-gray-400 hover:text-gold transition font-medium">➕ Ajouter</a>
                <a href="/pages/user/profile.php"
                   class="border border-gold text-gold px-4 py-1.5 rounded-lg hover:bg-gold hover:text-[#2a1a24] transition font-medium text-sm">
                    👤 <?= e($_SESSION['user']['username']) ?>
                </a>
                <a href="/pages/auth/logout.php"
                   class="border border-red-500/50 text-red-400 px-4 py-1.5 rounded-lg hover:bg-red-500/20 transition font-medium text-sm">
                    Déconnexion
                </a>
            <?php else: ?>
                <a href="/pages/auth/login.php"
                   class="border border-gold text-gold px-4 py-1.5 rounded-lg hover:bg-gold hover:text-[#2a1a24] transition font-medium text-sm">
                    Connexion
                </a>
                <a href="/pages/auth/register.php"
                   class="bg-linear-to-br from-gold to-gold-dark text-[#2a1a24] font-tft font-bold tracking-wide px-4 py-1.5 rounded-lg text-sm hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    Inscription
                </a>
            <?php endif; ?>
        </div>

    </div>
</nav>