<?php

/**
 * Vérifie si l'utilisateur est connecté.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

/**
 * Vérifie si l'utilisateur est administrateur.
 */
function isAdmin(): bool {
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

/**
 * Vérifie que l'utilisateur stocké en session existe toujours en base.
 * Si ce n'est pas le cas (ex : base recréée), on le déconnecte.
 */
function validateSession(): void {
    if (!isLoggedIn()) return;

    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM user WHERE id = ?');
    $stmt->execute([$_SESSION['user']['id']]);
    if (!$stmt->fetch()) {
        logoutUser();
        secureSessionStart();
    }
}

/**
 * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
 */
function requireLogin(): void {
    validateSession();
    if (!isLoggedIn()) {
        header('Location: /pages/auth/login.php');
        exit;
    }
}

/**
 * Redirige vers l'accueil si l'utilisateur n'est pas admin.
 */
function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /index.php');
        exit;
    }
}

/**
 * Connecte un utilisateur et stocke ses données en session.
 */
function loginUser(array $user): void {
    $_SESSION['user'] = [
        'id'         => $user['id'],
        'username'   => $user['username'],
        'email'      => $user['email'],
        'role'       => $user['role'],
        'created_at' => $user['created_at'],
    ];
}

/**
 * Déconnecte l'utilisateur.
 */
function logoutUser(): void {
    $_SESSION = [];
    session_destroy();
}