<?php

class SessionManager
{
    public function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function loginAsGuest(): void
    {
        $_SESSION['user_id'] = 0;
        $_SESSION['user_email'] = 'gast';
    }

    public function loginAsUser(int $id, string $email): void
    {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_email'] = $email;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function getEmail(): string
    {
        return (string) ($_SESSION['user_email'] ?? '');
    }
}
