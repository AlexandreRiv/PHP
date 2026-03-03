<?php

require_once __DIR__ . '/../config/database.php';

function e(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function setFlash(string $message, string $type = 'info'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function randomPlaytime(): int {
    return random_int(1, 500);
}

function randomDeathDate(): string {
    $timestamp = random_int(strtotime('-2 years'), time());
    return date('Y-m-d H:i:s', $timestamp);
}

function rarityConfig(string $rarity): array {
    return match($rarity) {
        'uncommon'  => ['label' => 'Peu commun',  'color' => 'text-green-400',  'bg' => 'bg-green-500/20',  'border' => 'border-green-500/30'],
        'rare'      => ['label' => 'Rare',         'color' => 'text-blue-400',   'bg' => 'bg-blue-500/20',   'border' => 'border-blue-500/30'],
        'epic'      => ['label' => 'Épique',       'color' => 'text-purple-400', 'bg' => 'bg-purple-500/20', 'border' => 'border-purple-500/30'],
        'legendary' => ['label' => 'Légendaire',   'color' => 'text-gold',       'bg' => 'bg-gold/20',       'border' => 'border-gold/30'],
        default     => ['label' => 'Commun',       'color' => 'text-gray-400',   'bg' => 'bg-gray-500/20',   'border' => 'border-gray-500/30'],
    };
}