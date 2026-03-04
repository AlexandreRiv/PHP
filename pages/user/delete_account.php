<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireLogin();

$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/user/profile.php');
}

verifyCsrf();

$password = $_POST['password'] ?? '';

$db = getDB();
$stmt = $db->prepare('SELECT * FROM user WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    setFlash('Mot de passe incorrect. Suppression annulée.', 'error');
    redirect('/pages/user/profile.php');
}

$db->exec('PRAGMA foreign_keys = ON');
$db->prepare('DELETE FROM user WHERE id = ?')->execute([$userId]);

logoutUser();
secureSessionStart();
setFlash('Votre compte a été supprimé avec succès.', 'success');
redirect('/index.php');
