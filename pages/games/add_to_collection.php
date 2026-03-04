<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/games/index.php');
}

verifyCsrf();

$gameId = (int)($_POST['id'] ?? 0);
if (!$gameId) redirect('/pages/games/index.php');

$db = getDB();
$userId = $_SESSION['user']['id'];

$stmt = $db->prepare('SELECT id, name FROM games WHERE id = ?');
$stmt->execute([$gameId]);
$game = $stmt->fetch();
if (!$game) redirect('/pages/games/index.php');

$check = $db->prepare('SELECT id FROM user_games WHERE user_id = ? AND game_id = ?');
$check->execute([$userId, $gameId]);
if ($check->fetch()) {
    setFlash('"' . e($game['name']) . '" est déjà dans votre collection.', 'error');
    redirect('/pages/games/show.php?id=' . $gameId);
}

$stmt = $db->prepare('
    INSERT INTO user_games (user_id, game_id, playtime_hours, added_at)
    VALUES (?, ?, ?, ?)
');
$stmt->execute([
    $userId,
    $gameId,
    randomPlaytime(),
    date('Y-m-d H:i:s'),
]);

setFlash('"' . e($game['name']) . '" ajouté à votre collection !', 'success');
redirect('/pages/games/show.php?id=' . $gameId);