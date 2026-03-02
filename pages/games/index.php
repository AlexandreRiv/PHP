<?php
session_start();
$pageTitle = 'Champions — PHP';

include __DIR__ . '/../../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="font-tft text-4xl font-bold text-center text-gold mb-3">⚔️ Tous les Champions</h1>
    <p class="text-center text-gray-500 mb-10">Consultez les détails de chaque champion de votre collection</p>

    <div class="flex justify-center mb-8">
        <a href="/pages/games/add.php"
           class="bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold tracking-wide px-6 py-3 rounded-lg hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.5)] transition-all">
            + Recruter un Champion
        </a>
    </div>

    <?php
    $games = [
        [
            'name' => 'Yasuo',
            'type' => 'Duelliste',
            'description' => 'Un sabreur agile qui esquive les attaques ennemies.',
            'image' => 'https://via.placeholder.com/400x200/1a0a2e/f0b232?text=⚔+Yasuo',
            'cost' => 1,
            'created_at' => '2025-01-15 14:30:00'
        ],
        [
            'name' => 'Jinx',
            'type' => 'Canonnière',
            'description' => 'Inflige des dégâts massifs de zone avec ses roquettes.',
            'image' => 'https://via.placeholder.com/400x200/1a0a2e/f0b232?text=🔫+Jinx',
            'cost' => 3,
            'created_at' => '2025-02-20 09:15:00'
        ],
        [
            'name' => 'Thresh',
            'type' => 'Gardien',
            'description' => 'Protège les alliés en absorbant les dégâts.',
            'image' => 'https://via.placeholder.com/400x200/1a0a2e/f0b232?text=🛡+Thresh',
            'cost' => 2,
            'created_at' => '2025-03-10 18:00:00'
        ],
        [
            'name' => 'Kayn',
            'type' => 'Assassin',
            'description' => 'Élimine les cibles prioritaires en un éclair.',
            'image' => 'https://via.placeholder.com/400x200/1a0a2e/f0b232?text=🗡+Kayn',
            'cost' => 5,
            'created_at' => '2025-04-05 12:00:00'
        ],
    ];

    $costColors = [
        1 => ['border' => 'border-t-gray-500',   'badge' => 'bg-gray-700 text-gray-300',   'label' => '1 🪙'],
        2 => ['border' => 'border-t-green-500',   'badge' => 'bg-green-900 text-green-300',  'label' => '2 🪙'],
        3 => ['border' => 'border-t-blue-500',    'badge' => 'bg-blue-900 text-blue-300',    'label' => '3 🪙'],
        4 => ['border' => 'border-t-purple-500',  'badge' => 'bg-purple-900 text-purple-300','label' => '4 🪙'],
        5 => ['border' => 'border-t-gold',        'badge' => 'bg-yellow-900 text-yellow-300','label' => '5 🪙'],
    ];
    ?>

    <?php if (empty($games)): ?>
        <p class="text-center text-gray-500 text-lg mt-20">Aucun champion trouvé sur le plateau.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($games as $game):
                $c = $costColors[$game['cost']];
            ?>
                <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border border-t-2 <?= $c['border'] ?> rounded-xl overflow-hidden hover:border-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.15)] hover:-translate-y-1 transition-all">
                    <img src="<?= htmlspecialchars($game['image']) ?>"
                         alt="<?= htmlspecialchars($game['name']) ?>"
                         class="w-full h-44 object-cover opacity-80">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="font-tft text-lg font-bold text-gold"><?= htmlspecialchars($game['name']) ?></h2>
                            <span class="<?= $c['badge'] ?> text-xs font-bold px-2 py-1 rounded-full"><?= $c['label'] ?></span>
                        </div>
                        <span class="inline-block bg-violet-900/50 text-violet-300 text-xs font-medium px-3 py-1 rounded-full mb-3">
                            <?= htmlspecialchars($game['type']) ?>
                        </span>
                        <p class="text-sm text-gray-400 mb-3"><?= htmlspecialchars($game['description']) ?></p>
                        <p class="text-xs text-gray-600">Recruté le <?= date('d/m/Y', strtotime($game['created_at'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
