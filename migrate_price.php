<?php
/**
 * Migration: Ajouter la colonne 'price' à la table 'games'.
 * Exécuter: php migrate_price.php
 * Ou ouvrir dans le navigateur: http://localhost/migrate_price.php
 *
 * Ce script est idempotent — il peut être lancé plusieurs fois sans risque.
 */

$dbPath = __DIR__ . '/database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "Base de donnees introuvable: $dbPath\n";
    exit(1);
}

$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Ajouter la colonne si elle n'existe pas
try {
    $db->query('SELECT price FROM games LIMIT 1');
    echo "Colonne 'price' deja presente.\n";
} catch (Exception $e) {
    $db->exec('ALTER TABLE games ADD COLUMN price REAL NOT NULL DEFAULT 0');
    echo "Colonne 'price' ajoutee avec succes.\n";
}

// 2. Mettre des prix par défaut si les jeux existent avec price = 0
$prices = [
    'Hollow Knight: Silksong' => 29.99,
    'GTA VI' => 69.99,
    'Elden Ring' => 49.99,
    'Minecraft' => 23.95,
    'Cyberpunk 2077' => 39.99,
    'The Legend of Zelda: Tears of the Kingdom' => 59.99,
    "Baldur's Gate 3" => 59.99,
];

$stmt = $db->prepare('UPDATE games SET price = ? WHERE id = ? AND price = 0');
$games = $db->query('SELECT id, name FROM games')->fetchAll(PDO::FETCH_ASSOC);
foreach ($games as $game) {
    if (isset($prices[$game['name']])) {
        $stmt->execute([$prices[$game['name']], $game['id']]);
        echo "Prix mis a jour pour '{$game['name']}': {$prices[$game['name']]} EUR\n";
    }
}

echo "\nMigration terminee.\n";
ob_flush();
