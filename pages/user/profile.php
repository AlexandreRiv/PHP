<?php
session_start();
$pageTitle = 'Mon Profil — PHP';


$user = $_SESSION['user'] ?? [
    'id'         => 1,
    'username'   => 'Pochi',
    'email'      => 'Pochi@example.com',
    'role'       => 'user',
    'gender'     => 'male',
    'created_at' => '2025-01-15 14:30:00',
];


$stats = [
    'games'        => 12,
    'playtime'     => 347,
    'achievements' => 28,
];

include __DIR__ . '/../../includes/header.php';
?>

<div class="max-w-5xl mx-auto px-4 py-12">

    <div class="relative bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-2xl overflow-hidden">
        <div class="h-36 bg-linear-to-r from-gold/20 via-tft-nav-mid to-gold-dark/20"></div>

        <div class="px-6 pb-8 -mt-16 relative z-10">
            <div class="flex flex-col sm:flex-row items-center sm:items-end gap-5">
                <div class="w-28 h-28 rounded-full border-4 border-tft-card-deep bg-linear-to-br from-gold to-gold-dark flex items-center justify-center text-5xl shadow-[0_0_30px_rgba(254,137,94,0.3)]">
                    <?php
                    $avatarEmoji = match ($user['gender']) {
                        'female' => '👩‍💻',
                        'other'  => '🧑‍💻',
                        default  => '👨‍💻',
                    };
                    echo $avatarEmoji;
                    ?>
                </div>

                <div class="text-center sm:text-left flex-1">
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <h1 class="font-tft text-3xl font-bold text-gold"><?= htmlspecialchars($user['username']) ?></h1>
                        <?php if ($user['role'] === 'admin'): ?>
                            <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Admin</span>
                        <?php else: ?>
                            <span class="bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full">Joueur</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-400 text-sm mt-1"><?= htmlspecialchars($user['email']) ?></p>
                </div>

                <a href="/pages/user/edit_profile.php"
                   class="bg-linear-to-br from-gold to-gold-dark text-[#2a1a24] font-tft font-bold tracking-wide px-5 py-2 rounded-lg text-sm hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
                    ✏️ Modifier le profil
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mt-8">
        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] transition-all">

            <p class="font-tft text-3xl font-bold text-gold"><?= $stats['games'] ?></p>
            <p class="text-gray-400 text-sm mt-1">Jeux possédés</p>
        </div>

        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] transition-all">
            <p class="font-tft text-3xl font-bold text-gold"><?= $stats['playtime'] ?>h</p>
            <p class="text-gray-400 text-sm mt-1">Temps de jeu total</p>
        </div>

        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl p-6 text-center hover:border-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.15)] transition-all">
            <p class="font-tft text-3xl font-bold text-gold"><?= $stats['achievements'] ?></p>
            <p class="text-gray-400 text-sm mt-1">Succès débloqués</p>
        </div>
    </div>

    <div class="mt-8 bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl overflow-hidden">
        <div class="border-b border-tft-border px-6 py-4">
            <h2 class="font-tft text-xl font-bold text-gold"> Informations du compte</h2>
        </div>

        <div class="divide-y divide-tft-border">
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                <span class="text-gray-500 text-sm sm:w-48 shrink-0">Nom d'utilisateur</span>
                <span class="text-gray-200 font-medium"><?= htmlspecialchars($user['username']) ?></span>
            </div>
            <!-- Email -->
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                <span class="text-gray-500 text-sm sm:w-48 shrink-0">Adresse e-mail</span>
                <span class="text-gray-200 font-medium"><?= htmlspecialchars($user['email']) ?></span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                <span class="text-gray-500 text-sm sm:w-48 shrink-0">Genre</span>
                <span class="text-gray-200 font-medium">
                    <?= match ($user['gender']) {
                        'male'   => ' Homme',
                        'female' => ' Femme',
                        'other'  => ' Autre',
                        default  => 'Non renseigné',
                    } ?>
                </span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                <span class="text-gray-500 text-sm sm:w-48 shrink-0">Rôle</span>
                <span class="text-gray-200 font-medium">
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full">Admin</span>
                    <?php else: ?>
                        <span class="bg-[#96527A]/50 text-[#F99F72] text-xs font-medium px-3 py-1 rounded-full">Joueur</span>
                    <?php endif; ?>
                </span>
            </div>
            <!-- Date inscription -->
            <div class="flex flex-col sm:flex-row sm:items-center px-6 py-4 gap-2">
                <span class="text-gray-500 text-sm sm:w-48 shrink-0">Membre depuis</span>
                <span class="text-gray-200 font-medium"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-wrap gap-4 justify-center">
        <a href="/pages/user/edit_profile.php"
           class="border border-gold text-gold px-6 py-2.5 rounded-lg hover:bg-gold hover:text-[#2a1a24] transition font-medium text-sm">
            Modifier le profil
        </a>
        <a href="/pages/games/index.php"
           class="border border-tft-border text-gray-400 px-6 py-2.5 rounded-lg hover:border-gold hover:text-gold transition font-medium text-sm">
             Mes jeux
        </a>
        <a href="/pages/auth/logout.php"
           class="border border-red-500/50 text-red-400 px-6 py-2.5 rounded-lg hover:bg-red-500/20 hover:text-red-300 transition font-medium text-sm">
             Déconnexion
        </a>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

