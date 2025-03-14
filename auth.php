<?php
session_start();

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function login($username, $password) {
    if ($username === 'admin' && $password === 'password123') {
        $_SESSION['user_id'] = 1; 
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
}
?>
