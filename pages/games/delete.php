<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/games/index.php');
}

verifyCsrf();

$id = (int)($_POST['id'] ?? 0);
if (!$id) redirect('/pages/games/index.php');

$db = getDB();
$stmt = $db->prepare('SELECT name FROM games WHERE id = ?');
$stmt->execute([$id]);
$game = $stmt->fetch();
if (!$game) abort(404, 'Ce jeu n\'existe pas.');

$del = $db->prepare('DELETE FROM games WHERE id = ?');
$del->execute([$id]);

setFlash('Jeu "' . e($game['name']) . '" supprimé.', 'success');
redirect('/pages/games/index.php');