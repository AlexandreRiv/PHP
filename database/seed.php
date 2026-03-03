<?php

/**
 * SEED — Peuple la base de données avec les données initiales.
 * Exécuter UNE SEULE FOIS depuis la racine : php database/seed.php
 * Puis supprimer ou mettre hors du dossier public.
 */

require_once __DIR__ . '/../config/database.php';

$db = getDB();

// ── Création des tables si elles n'existent pas ──────────────────
$schema = file_get_contents(__DIR__ . '/schema.sql');
$db->exec($schema);

echo "✓ Tables créées\n";

// ════════════════════════════════════════════════════════════════
// UTILISATEURS
// ════════════════════════════════════════════════════════════════

$users = [
    [
        'username' => 'Admin',
        'email' => 'admin@oapdn.com',
        'password' => 'admin1234',
        'role' => 'admin',
        'gender' => 'male',
    ],
    [
        'username' => 'Pochi',
        'email' => 'pochi@example.com',
        'password' => 'pochi1234',
        'role' => 'user',
        'gender' => 'male',
    ],
    [
        'username' => 'Sakura',
        'email' => 'sakura@example.com',
        'password' => 'sakura1234',
        'role' => 'user',
        'gender' => 'female',
    ],
];

$stmtUser = $db->prepare('
    INSERT OR IGNORE INTO user (username, email, password_hash, role, gender)
    VALUES (?, ?, ?, ?, ?)
');

$userIds = [];
foreach ($users as $u) {
    $stmtUser->execute([
        $u['username'],
        $u['email'],
        password_hash($u['password'], PASSWORD_BCRYPT),
        $u['role'],
        $u['gender'],
    ]);
    $userIds[$u['username']] = $db->lastInsertId();
    echo "✓ Utilisateur '{$u['username']}' créé (mdp: {$u['password']})\n";
}

// ════════════════════════════════════════════════════════════════
// JEUX (5 obligatoires)
// ════════════════════════════════════════════════════════════════

$games = [
    [
        'name' => 'Hollow Knight: Silksong',
        'type' => 'Action / Aventure',
        'description' => 'Incarnez Hornet, chasseresse et princesse, dans une toute nouvelle aventure à travers un royaume hanté par la soie et les chants. Affronte des boss redoutables et explore des contrées mystérieuses.',
        'image' => '/assets/images/silksong.png',
    ],
    [
        'name' => 'GTA VI',
        'type' => 'Action / Open World',
        'description' => 'Plongez dans un monde ouvert immense inspiré de Vice City, avec une histoire explosive mettant en scène deux protagonistes dans un récit de crime et de survie.',
        'image' => '/assets/images/gta6.png',
    ],
    [
        'name' => 'Elden Ring',
        'type' => 'RPG / Action',
        'description' => 'Explorez un vaste monde créé par Hidetaka Miyazaki et George R.R. Martin. Affrontez des créatures colossales et forgez votre destin dans les Terres Intermédiaires.',
        'image' => '/assets/images/eldenRing.png',
    ],
    [
        'name' => 'Minecraft',
        'type' => 'Sandbox / Survie',
        'description' => 'Construisez, explorez et survivez dans un monde infini généré procéduralement. De la survie en solo aux serveurs multijoueurs, les possibilités sont infinies.',
        'image' => '/assets/images/minecraft.png',
    ],
    [
        'name' => 'Cyberpunk 2077',
        'type' => 'RPG / FPS',
        'description' => 'Incarnez V, un mercenaire dans la mégapole dystopique de Night City. Modifiez votre corps, choisissez vos alliés et écrivez votre propre légende dans un futur sombre.',
        'image' => '/assets/images/cyberpunk.png',
    ],
    [
        'name' => 'The Legend of Zelda: Tears of the Kingdom',
        'type' => 'Action / Aventure',
        'description' => 'Partez à la découverte d\'Hyrule et de ses mystérieux îles célestes. Utilisez de nouveaux pouvoirs pour résoudre des énigmes et vaincre les forces du mal.',
        'image' => '/assets/images/zelda.png',
    ],
    [
        'name' => 'Baldur\'s Gate 3',
        'type' => 'RPG / Tour par tour',
        'description' => 'Un RPG basé sur Donjons & Dragons où chaque choix compte. Formez un groupe, explorez un monde riche et affrontez une menace cosmique dans des aventures épiques.',
        'image' => '/assets/images/bg3.png',
    ],
];

$stmtGame = $db->prepare('
    INSERT OR IGNORE INTO games (name, type, description, image)
    VALUES (?, ?, ?, ?)
');

$gameIds = [];
foreach ($games as $g) {
    $stmtGame->execute([$g['name'], $g['type'], $g['description'], $g['image']]);
    $gameIds[$g['name']] = $db->lastInsertId();
    echo "✓ Jeu '{$g['name']}' créé\n";
}

// ════════════════════════════════════════════════════════════════
// SUCCÈS PAR JEU
// ════════════════════════════════════════════════════════════════

$achievements = [
    'Hollow Knight: Silksong' => [
        ['Premier fil', 'Effectuer votre première attaque avec le fil de soie.', 'common'],
        ['Tisseur de soie', 'Vaincre 100 ennemis avec le fil de soie.', 'uncommon'],
        ['Danse macabre', 'Battre un boss sans prendre le moindre dégât.', 'rare'],
        ['Reine des aiguilles', 'Terminer le jeu en difficulté maximale.', 'legendary'],
        ['Collectionneur', 'Trouver tous les objets cachés du royaume.', 'epic'],
    ],
    'GTA VI' => [
        ['Premiers pas à Vice', 'Arriver à Vice City.', 'common'],
        ['Cambrioleur', 'Réussir votre premier braquage sans alerte.', 'uncommon'],
        ['Fugitif', 'Échapper à une recherche de niveau 5.', 'rare'],
        ['Milliardaire', 'Accumuler 1 milliard de dollars.', 'epic'],
        ['Légende de Vice', 'Terminer toutes les missions principales.', 'legendary'],
    ],
    'Elden Ring' => [
        ['Sans-éclat', 'Arriver aux Terres Intermédiaires.', 'common'],
        ['Seigneur ancestral', 'Vaincre votre premier demi-dieu.', 'uncommon'],
        ['Étoile ancienne', 'Obtenir la fin secrète du jeu.', 'epic'],
        ['Légende vivante', 'Terminer le jeu au niveau 1.', 'legendary'],
        ['Explorateur', 'Découvrir toutes les zones de la carte.', 'rare'],
    ],
    'Minecraft' => [
        ['Bûcheron', 'Couper votre premier arbre.', 'common'],
        ['Diamant !', 'Miner votre premier diamant.', 'uncommon'],
        ['The End', 'Vaincre l\'Ender Dragon.', 'rare'],
        ['Cartographe ultime', 'Explorer 500 chunks différents.', 'epic'],
        ['Dieu du craft', 'Fabriquer chaque objet du jeu.', 'legendary'],
    ],
    'Cyberpunk 2077' => [
        ['Bienvenue à Night City', 'Arriver à Night City.', 'common'],
        ['Netrunner', 'Pirater 50 ennemis.', 'uncommon'],
        ['Chrome et chair', 'Installer 10 implants cybernétiques.', 'rare'],
        ['Légende urbaine', 'Atteindre le niveau de rue maximum.', 'epic'],
        ['Fantôme', 'Terminer une mission principale sans tuer personne.', 'legendary'],
    ],
    'The Legend of Zelda: Tears of the Kingdom' => [
        ['Réveil', 'Quitter l\'île du Sanctuaire.', 'common'],
        ['Ingénieur habile', 'Construire votre premier véhicule fonctionnel.', 'uncommon'],
        ['Dragon chasseur', 'Trouver toutes les larmes du dragon.', 'rare'],
        ['Architecte du ciel', 'Construire une structure de plus de 20 éléments.', 'epic'],
        ['Héros d\'Hyrule', 'Terminer toutes les quêtes secondaires.', 'legendary'],
    ],
    'Baldur\'s Gate 3' => [
        ['Survivant', 'Survivre au nautilöide en crash.', 'common'],
        ['Diplomate', 'Résoudre un conflit sans combat.', 'uncommon'],
        ['Tactician', 'Gagner un combat à 1 contre 4.', 'rare'],
        ['Maître des dés', 'Réussir 10 jets de dés critiques d\'affilée.', 'epic'],
        ['Élu des dieux', 'Terminer le jeu avec toutes les fins débloquées.', 'legendary'],
    ],
];

// ════════════════════════════════════════════════════════════════
// NIVEAUX PAR JEU
// ════════════════════════════════════════════════════════════════

$levels = [
    'Hollow Knight: Silksong' => [
        ['Royaume des brumes',    'easy',    'La zone de départ, idéale pour apprendre les mécaniques de base.'],
        ['Forêt des aiguilles',   'medium',  'Une forêt dense peuplée d\'ennemis agiles et de pièges.'],
        ['Sanctuaire de la soie', 'hard',    'Zone avancée avec des boss redoutables gardant des secrets anciens.'],
        ['Cœur du royaume',       'extreme', 'La zone finale, réservée aux joueurs les plus expérimentés.'],
    ],
    'GTA VI' => [
        ['Vice City Downtown',    'easy',    'Le centre-ville, zone de départ avec des missions d\'introduction.'],
        ['Les Everglades',        'medium',  'Les marais de Floride, terrain hostile avec des gangs locaux.'],
        ['Port industriel',       'hard',    'Zone contrôlée par un cartel, missions de braquage complexes.'],
        ['Quartier fédéral',      'extreme', 'Zone ultra-surveillée, missions furtives à haute difficulté.'],
    ],
    'Elden Ring' => [
        ['Limgrave',              'easy',    'Les plaines de départ des Terres Intermédiaires.'],
        ['Liurnia des Lacs',      'medium',  'Un vaste lac entouré de ruines magiques et de sorciers.'],
        ['Altus Plateau',         'hard',    'Les hautes terres gardées par de puissants chevaliers dorés.'],
        ['Farum Azula',           'extreme', 'Une cité déchue suspendue dans une tempête éternelle.'],
    ],
    'Minecraft' => [
        ['Surface',               'easy',    'Le monde de surface, idéal pour débuter et récolter des ressources.'],
        ['Grottes profondes',     'medium',  'Les cavernes du monde souterrain, riches en minerais rares.'],
        ['Nether',                'hard',    'Une dimension infernale avec des créatures dangereuses.'],
        ['L\'End',                'extreme', 'La dimension finale, domaine de l\'Ender Dragon.'],
    ],
    'Cyberpunk 2077' => [
        ['Watson',                'easy',    'Le quartier de départ de V, zone industrielle en déclin.'],
        ['Westbrook',             'medium',  'Le quartier des night-clubs et des corporations technologiques.'],
        ['City Center',           'hard',    'Le cœur de Night City, contrôlé par Arasaka.'],
        ['Pacifica',              'extreme', 'Zone abandonnée par les autorités, territoire des Animals.'],
    ],
    'The Legend of Zelda: Tears of the Kingdom' => [
        ['Île du Sanctuaire',     'easy',    'L\'île tutoriel flottant au-dessus d\'Hyrule.'],
        ['Plaine d\'Hyrule',      'medium',  'Les vastes plaines centrales parsemées de sanctuaires.'],
        ['Les profondeurs',       'hard',    'Le monde souterrain obscur caché sous Hyrule.'],
        ['Temple du Crépuscule',  'extreme', 'Le donjon final gardé par Ganondorf lui-même.'],
    ],
    'Baldur\'s Gate 3' => [
        ['Côte de la Désolation', 'easy',    'La zone de départ après le crash du nautilöide.'],
        ['Underdark',             'medium',  'Le monde souterrain peuplé de créatures mystérieuses.'],
        ['Moonrise Towers',       'hard',    'La forteresse du culte de l\'Absolu.'],
        ['Baldur\'s Gate',        'extreme', 'La ville finale, théâtre de la confrontation ultime.'],
    ],
];

$stmtLvl = $db->prepare('
    INSERT OR IGNORE INTO levels (game_id, name, difficulty, description)
    VALUES (?, ?, ?, ?)
');

foreach ($levels as $gameName => $levelList) {
    $gid = $gameIds[$gameName] ?? null;
    if (!$gid) continue;
    foreach ($levelList as [$name, $difficulty, $desc]) {
        $stmtLvl->execute([$gid, $name, $difficulty, $desc]);
    }
    echo "✓ Niveaux ajoutés pour '{$gameName}'\n";
}

$stmtAch = $db->prepare('
    INSERT OR IGNORE INTO achievements (game_id, name, description, rarity)
    VALUES (?, ?, ?, ?)
');

$achievementIds = []; // [game_name => [id, id, ...]]
foreach ($achievements as $gameName => $achList) {
    $gid = $gameIds[$gameName] ?? null;
    if (!$gid) continue;
    $achievementIds[$gameName] = [];
    foreach ($achList as [$name, $desc, $rarity]) {
        $stmtAch->execute([$gid, $name, $desc, $rarity]);
        $achievementIds[$gameName][] = $db->lastInsertId();
    }
    echo "✓ Succès ajoutés pour '{$gameName}'\n";
}

// ════════════════════════════════════════════════════════════════
// ATTRIBUTION ALÉATOIRE jeux → utilisateurs
// ════════════════════════════════════════════════════════════════

$stmtUG = $db->prepare('
    INSERT OR IGNORE INTO user_games (user_id, game_id, playtime_hours, added_at, death_date)
    VALUES (?, ?, ?, ?, ?)
');

$stmtUA = $db->prepare('
    INSERT OR IGNORE INTO user_achievements (user_id, achievement_id, unlocked_at)
    VALUES (?, ?, ?)
');

// On attribue des jeux à Pochi et Sakura (pas l'admin)
$playerIds = array_filter($userIds, fn($id) => $id > 0);

foreach ($playerIds as $username => $uid) {
    if ($username === 'Admin') continue;

    // Chaque joueur reçoit entre 3 et 5 jeux aléatoires
    $selectedGames = array_rand($gameIds, rand(3, 5));
    if (!is_array($selectedGames)) $selectedGames = [$selectedGames];

    foreach ($selectedGames as $gameName) {
        $gid = $gameIds[$gameName];
        $playtime = rand(1, 500);
        $addedAt = date('Y-m-d H:i:s', rand(strtotime('-2 years'), time()));
        $deathDate = date('Y-m-d H:i:s', rand(strtotime('-1 year'), time()));

        $stmtUG->execute([$uid, $gid, $playtime, $addedAt, $deathDate]);

        // Débloquer aléatoirement certains succès du jeu
        if (!empty($achievementIds[$gameName])) {
            $toUnlock = array_rand(
                $achievementIds[$gameName],
                rand(1, count($achievementIds[$gameName]))
            );
            if (!is_array($toUnlock)) $toUnlock = [$toUnlock];

            foreach ($toUnlock as $idx) {
                $achId = $achievementIds[$gameName][$idx];
                $unlockedAt = date('Y-m-d H:i:s', rand(strtotime($addedAt), time()));
                $stmtUA->execute([$uid, $achId, $unlockedAt]);
            }
        }
    }
    echo "✓ Jeux & succès attribués à '{$username}'\n";
}

echo "\n══════════════════════════════════════\n";
echo "  Seed terminé avec succès !\n";
echo "══════════════════════════════════════\n";
echo "  Admin  → admin@oapdn.com / admin1234\n";
echo "  Joueur → pochi@example.com / pochi1234\n";
echo "══════════════════════════════════════\n";
echo "  ⚠ Supprimez ce fichier avant la démo !\n\n";