<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$pageTitle = 'Mon Profil — OAPDN';
$db = getDB();
$userId = $_SESSION['user']['id'];

$stmt = $db->prepare('SELECT * FROM user WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();
if (!$user) {
    logoutUser();
    redirect('/pages/auth/login.php');
}

$stmtGames = $db->prepare('
    SELECT g.*, ug.playtime_hours, ug.added_at, ug.death_date
    FROM user_games ug
    JOIN games g ON g.id = ug.game_id
    WHERE ug.user_id = ?
    ORDER BY ug.added_at DESC
');
$stmtGames->execute([$userId]);
$userGames = $stmtGames->fetchAll();

$totalPlaytime = array_sum(array_column($userGames, 'playtime_hours'));

$stmtAch = $db->prepare('SELECT COUNT(*) as cnt FROM user_achievements WHERE user_id = ?');
$stmtAch->execute([$userId]);
$totalAchievements = $stmtAch->fetch()['cnt'];

include __DIR__ . '/../../includes/header.php';
?>

    <div class="max-w-5xl mx-auto px-4 py-12">

        <!-- Carte profil -->
        <div class="relative bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-2xl overflow-hidden mb-8">
            <div class="h-36 bg-linear-to-r from-gold/20 via-tft-nav-mid to-gold-dark/20"></div>
            <div class="px-6 pb-8 -mt-16 relative z-10">
                <div class="flex flex-col sm:flex-row items-center sm:items-end gap-5">
                    <div class="w-28 h-28 rounded-full border-4 border-tft-card-deep bg-linear-to-br from-gold to-gold-dark flex items-center justify-center text-5xl shadow-[0_0_30px_rgba(254,137,94,0.3)]">
                        <?= match ($user['gender']) {
                            'female' => '👩‍💻',
                            'other' => '🧑‍💻',
                            default => '👨‍💻'
                        } ?>
                    </div>
                    <div class="text-center sm:text-left flex-1">
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <h1 class="font-tft text-3xl font-bold text-gold"><?= e($user['username']) ?></h1>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full uppercase">Admin</span>
                            <?php else: ?>
                                <span class="bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full">Joueur</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-400 text-sm mt-1"><?= e($user['email']) ?></p>
                        <p class="text-gray-600 text-xs mt-0.5">Membre depuis
                            le <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                    </div>
                    <a href="/pages/user/edit_profile.php"
                       class="bg-linear-to-br from-gold to-gold-dark text-[#2a1a24] font-tft font-bold px-5 py-2 rounded-lg text-sm hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                        ✏️ Modifier le profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold transition-all">
                <p class="font-tft text-3xl font-bold text-gold"><?= count($userGames) ?></p>
                <p class="text-gray-400 text-sm mt-1">Jeux possédés</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold transition-all">
                <p class="font-tft text-3xl font-bold text-gold"><?= $totalPlaytime ?>h</p>
                <p class="text-gray-400 text-sm mt-1">Temps de jeu total</p>
            </div>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold transition-all">
                <p class="font-tft text-3xl font-bold text-gold"><?= $totalAchievements ?></p>
                <p class="text-gray-400 text-sm mt-1">Succès débloqués</p>
            </div>
        </div>

        <!-- Infos compte -->
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl overflow-hidden mb-8">
            <div class="border-b border-tft-border px-6 py-4">
                <h2 class="font-tft text-xl font-bold text-gold">Informations du compte</h2>
            </div>
            <div class="divide-y divide-tft-border">
                <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                    <span class="text-gray-500 text-sm sm:w-48 shrink-0">Nom d'utilisateur</span>
                    <span class="text-gray-200 font-medium"><?= e($user['username']) ?></span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                    <span class="text-gray-500 text-sm sm:w-48 shrink-0">Adresse e-mail</span>
                    <span class="text-gray-200 font-medium"><?= e($user['email']) ?></span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                    <span class="text-gray-500 text-sm sm:w-48 shrink-0">Genre</span>
                    <span class="text-gray-200 font-medium">
                    <?= match ($user['gender']) {
                        'male' => '👨 Homme',
                        'female' => '👩 Femme',
                        default => '🧑 Autre'
                    } ?>
                </span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                    <span class="text-gray-500 text-sm sm:w-48 shrink-0">Rôle</span>
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full">Admin</span>
                    <?php else: ?>
                        <span class="bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full">Joueur</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Jeux récents -->
        <?php if (!empty($userGames)): ?>
            <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl overflow-hidden mb-8">
                <div class="border-b border-tft-border px-6 py-4 flex items-center justify-between">
                    <h2 class="font-tft text-xl font-bold text-gold">Mes jeux récents</h2>
                    <a href="/pages/games/index.php" class="text-xs text-gold hover:text-gold-light transition">Voir le
                        catalogue →</a>
                </div>
                <div class="divide-y divide-tft-border">
                    <?php foreach (array_slice($userGames, 0, 5) as $ug): ?>
                        <div class="flex items-center gap-4 px-6 py-4">
                            <?php if ($ug['image']): ?>
                                <img src="<?= e($ug['image']) ?>" alt="<?= e($ug['name']) ?>"
                                     class="w-12 h-12 rounded-lg object-cover shrink-0">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-lg bg-tft-dark flex items-center justify-center text-2xl shrink-0">
                                    🎮
                                </div>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-200 truncate"><?= e($ug['name']) ?></p>
                                <p class="text-xs text-gray-500">Ajouté
                                    le <?= date('d/m/Y', strtotime($ug['added_at'])) ?></p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-tft font-bold text-gold"><?= $ug['playtime_hours'] ?>h</p>
                                <p class="text-xs text-gray-500">de jeu</p>
                            </div>
                            <?php if ($ug['death_date']): ?>
                                <div class="text-right shrink-0">
                                    <p class="text-xs text-red-400">
                                        💀 <?= date('d/m/Y', strtotime($ug['death_date'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="/pages/user/edit_profile.php"
               class="border border-gold text-gold px-6 py-2.5 rounded-lg hover:bg-gold hover:text-tft-dark transition font-medium text-sm">
                ✏️ Modifier le profil
            </a>
            <a href="/pages/games/index.php"
               class="border border-tft-border text-gray-400 px-6 py-2.5 rounded-lg hover:border-gold hover:text-gold transition font-medium text-sm">
                🎮 Catalogue
            </a>
            <a href="/pages/auth/logout.php"
               class="border border-red-500/50 text-red-400 px-6 py-2.5 rounded-lg hover:bg-red-500/20 hover:text-red-300 transition font-medium text-sm">
                Déconnexion
            </a>
        </div>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>