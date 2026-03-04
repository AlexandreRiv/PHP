<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
logoutUser();
header('Location: /pages/auth/login.php');
exit;