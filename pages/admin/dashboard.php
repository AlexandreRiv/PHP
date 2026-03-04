<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireAdmin();

$pageTitle = 'Dashboard Admin — OAPDN';
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
                <span class="text-4xl">👥</span>
                <div>
                    <p class="font-tft text-lg font-bold text-gold">Gérer les utilisateurs</p>
                    <p class="text-gray-400 text-sm"><?= $totalUsers ?> compte<?= $totalUsers > 1 ? 's' : '' ?>
                        inscrit<?= $totalUsers > 1 ? 's' : '' ?></p>
                </div>
            </a>
            <a href="/pages/games/index.php"
               class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 flex items-center gap-4 hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] transition-all">
                <span class="text-4xl">🎮</span>
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
                               class="text-gold hover:text-gold-light transition text-sm">✏️</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>