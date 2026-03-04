<?php

/**
 * Charge les variables du fichier .env dans $_ENV.
 */
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        die('Fichier .env introuvable. Copiez .env.example en .env et remplissez les valeurs.');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        if (!empty($key)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}