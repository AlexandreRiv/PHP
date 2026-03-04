<?php
require_once __DIR__ . '/../includes/auth.php';
?>
<nav class="bg-linear-to-r from-tft-nav via-tft-nav-mid to-tft-nav border-b border-tft-border">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">

        <a href="/index.php" class="flex items-center gap-2">
            <svg class="w-8 h-8 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span class="font-tft text-2xl font-bold text-gold">TFT Collection</span>
        </a>

        <div class="flex gap-6 items-center">
            <a href="/index.php"
               class="text-gray-400 hover:text-gold transition font-medium">Accueil</a>
            <a href="/pages/games/index.php"
               class="text-gray-400 hover:text-gold transition font-medium">Jeux</a>

            <?php if (isLoggedIn()): ?>
                <a href="/pages/user/collection.php"
                   class="text-gray-400 hover:text-gold transition font-medium">Ma collection</a>
                <?php if (isAdmin()): ?>
                    <a href="/pages/admin/dashboard.php"
                       class="text-gray-400 hover:text-gold transition font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                        Admin
                    </a>
                    <a href="/pages/games/add.php"
                       class="text-gray-400 hover:text-gold transition font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter
                    </a>
                <?php endif; ?>
                <a href="/pages/user/profile.php"
                   class="border border-gold text-gold px-4 py-1.5 rounded-lg hover:bg-gold hover:text-[#2a1a24] transition font-medium text-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <?= e($_SESSION['user']['username']) ?>
                </a>
                <a href="/pages/auth/logout.php"
                   class="border border-red-500/50 text-red-400 px-4 py-1.5 rounded-lg hover:bg-red-500/20 transition font-medium text-sm">
                    Deconnexion
                </a>
            <?php else: ?>
                <a href="/pages/auth/login.php"
                   class="border border-gold text-gold px-4 py-1.5 rounded-lg hover:bg-gold hover:text-[#2a1a24] transition font-medium text-sm">
                    Connexion
                </a>
                <a href="/pages/auth/register.php"
                   class="bg-linear-to-br from-gold to-gold-dark text-[#2a1a24] font-medium px-4 py-1.5 rounded-lg text-sm hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    Inscription
                </a>
            <?php endif; ?>
        </div>

    </div>
</nav>