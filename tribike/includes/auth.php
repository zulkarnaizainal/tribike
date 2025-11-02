<?php
if (session_status() == PHP_SESSION_NONE) session_start();
function is_logged_in() { return !empty($_SESSION['user_id']); }
function require_login() { if (!is_logged_in()) { header('Location: login.php'); exit; } }
function current_user($pdo=null) {
    if (!is_logged_in()) return null;
    if (!$pdo) return null;
    $stmt = $pdo->prepare('SELECT id,name,email,is_admin FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
?>