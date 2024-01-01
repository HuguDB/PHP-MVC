<?php

namespace App\Utils;

class SessionManager
{
    public function startSession(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set(string $key, mixed $value): void
    {
        $this->startSession();
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        $this->startSession();
        return $_SESSION[$key] ?? null;
    }

    public function delete(string $key): void
    {
        $this->startSession();
        unset($_SESSION[$key]);
    }
}
