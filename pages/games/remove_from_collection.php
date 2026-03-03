<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$gameId = (int)($_GET['id'] ?? 0);
if (!$gameId) redirect('/pages/user/collection.php');

$db = getDB();
$userId = $_SESSION['user']['id'];

$stmt = $db->prepare('SELECT id, name FROM games WHERE id = ?');
$stmt->execute([$gameId]);
$game = $stmt->fetch();
if (!$game) redirect('/pages/user/collection.php');

$del = $db->prepare('DELETE FROM user_games WHERE user_id = ? AND game_id = ?');
$del->execute([$userId, $gameId]);

setFlash('"' . e($game['name']) . '" retiré de votre collection.', 'success');
redirect('/pages/user/collection.php');