<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireAdmin();

$pageTitle = 'Dashboard Admin — TFT Collection';
$db = getDB();

$totalUsers = $db->query('SELECT COUNT(*) as cnt FROM user')->fetch()['cnt'];
$totalGames = $db->query('SELECT COUNT(*) as cnt FROM games')->fetch()['cnt'];
$totalPlaytime = $db->query('SELECT SUM(playtime_hours) as total FROM user_games')->fetch()['total'] ?? 0;
$totalAch = $db->query('SELECT COUNT(*) as cnt FROM user_achievements')->fetch()['cnt'];

$recentUsers = $db->query('SELECT * FROM user ORDER BY created_at DESC LIMIT 5')->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

    <div class="max-w-6xl mx-auto px-4 py-12">

        <div class="flex items-center gap-3 mb-10">
            <h1 class="font-tft text-4xl font-bold text-gold">Dashboard</h1>
            <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full uppercase">Admin</span>
        </div>

        <!-- Stats globales -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-3xl font-bold text-gold"><?= $totalUsers ?></p>
                <p class="text-gray-400 text-xs mt-1">Utilisateurs</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-3xl font-bold text-gold"><?= $totalGames ?></p>
                <p class="text-gray-400 text-xs mt-1">Jeux</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-3xl font-bold text-gold"><?= $totalPlaytime ?>h</p>
                <p class="text-gray-400 text-xs mt-1">Heures jouées</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-5 text-center">
                <p class="font-tft text-3xl font-bold text-gold"><?= $totalAch ?></p>
                <p class="text-gray-400 text-xs mt-1">Succès débloqués</p>
            </div>
        </div>

        <!-- Raccourcis -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
            <a href="/pages/admin/users.php"
               class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 flex items-center gap-4 hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] transition-all">
                <span class="text-4xl text-gold">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
                <div>
                    <p class="font-tft text-lg font-bold text-gold">Gérer les utilisateurs</p>
                    <p class="text-gray-400 text-sm"><?= $totalUsers ?> compte<?= $totalUsers > 1 ? 's' : '' ?>
                        inscrit<?= $totalUsers > 1 ? 's' : '' ?></p>
                </div>
            </a>
            <a href="/pages/games/index.php"
               class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 flex items-center gap-4 hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] transition-all">
                <span class="text-4xl text-gold">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
                <div>
                    <p class="font-tft text-lg font-bold text-gold">Gérer les jeux</p>
                    <p class="text-gray-400 text-sm"><?= $totalGames ?> jeu<?= $totalGames > 1 ? 'x' : '' ?> dans le
                        catalogue</p>
                </div>
            </a>
        </div>

        <!-- Derniers inscrits -->
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl overflow-hidden">
            <div class="border-b border-tft-border px-6 py-4 flex items-center justify-between">
                <h2 class="font-tft text-xl font-bold text-gold">Derniers inscrits</h2>
                <a href="/pages/admin/users.php" class="text-xs text-gold hover:text-gold-light transition">Voir tous
                    →</a>
            </div>
            <div class="divide-y divide-tft-border">
                <?php foreach ($recentUsers as $u): ?>
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-200 truncate"><?= e($u['username']) ?></p>
                            <p class="text-xs text-gray-500"><?= e($u['email']) ?></p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-2 py-0.5 rounded-full">Admin</span>
                            <?php else: ?>
                                <span class="bg-[#96527A]/50 text-[#F99F72] text-xs px-2 py-0.5 rounded-full">Joueur</span>
                            <?php endif; ?>
                            <span class="text-xs text-gray-600"><?= date('d/m/Y', strtotime($u['created_at'])) ?></span>
                            <a href="/pages/admin/edit_user.php?id=<?= $u['id'] ?>"
                               class="text-gold hover:text-gold-light transition text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>