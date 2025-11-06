<?php
declare(strict_types=1);
$root = realpath(__DIR__ . '/..');
ini_set('session.save_path', $root . '/sessions');
ini_set('session.cookie_path', '/');
session_start();
?>
