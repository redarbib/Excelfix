<?php

class AuthService
{
    private Database $db;
    private SessionManager $session;

    public function __construct(Database $db, SessionManager $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

    public function loginUser(string $email, string $password): bool
    {
        $email = $this->normalizeEmail($email);
        $stmt = $this->db->getConnection()->prepare(
            'SELECT id, email, password_hash FROM users WHERE LOWER(email) = :email LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $this->session->loginAsUser((int) $user['id'], $user['email']);
            return true;
        }

        return false;
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
