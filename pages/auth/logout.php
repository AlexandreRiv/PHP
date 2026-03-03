<?php
session_start();
require_once __DIR__ . '/../../includes/auth.php';
logoutUser();
header('Location: /pages/auth/login.php');
exit;