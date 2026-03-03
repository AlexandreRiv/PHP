<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('/pages/admin/users.php');

if ($id === $_SESSION['user']['id']) {
    setFlash('Vous ne pouvez pas supprimer votre propre compte.', 'error');
    redirect('/pages/admin/users.php');
}

$db = getDB();
$stmt = $db->prepare('SELECT username FROM user WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) redirect('/pages/admin/users.php');

$db->prepare('DELETE FROM user WHERE id = ?')->execute([$id]);

setFlash('Utilisateur "' . e($user['username']) . '" supprimé.', 'success');
redirect('/pages/admin/users.php');
