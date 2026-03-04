<?php

const DB_PATH = __DIR__ . '/../database/database.sqlite';

/**
 * Retourne une instance PDO SQLite (singleton).
 */
function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON;');
        } catch (PDOException $e) {
            http_response_code(500);
            die('<div style="font-family:sans-serif;padding:2rem;color:#e55;">
                <h1>Erreur base de données</h1>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
            </div>');
        }
    }

    return $pdo;
}