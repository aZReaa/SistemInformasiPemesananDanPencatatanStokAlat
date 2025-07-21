<?php

class Login
{
    private $username;
    private $password;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function authenticate($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $sql = "SELECT u.*, 
                       CASE 
                           WHEN u.role = 'admin' THEN a.nama_admin
                           WHEN u.role = 'pelanggan' THEN p.nama
                           WHEN u.role = 'pemilik' THEN po.nama_pemilik
                       END as nama
                FROM users u
                LEFT JOIN admin a ON u.id = a.user_id AND u.role = 'admin'
                LEFT JOIN pelanggan p ON u.id = p.user_id AND u.role = 'pelanggan'
                LEFT JOIN pemilik_usaha po ON u.id = po.user_id AND u.role = 'pemilik'
                WHERE u.username = ?";

        $user = $this->db->fetch($sql, [$username]);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function createSession($user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];
        
        if ($user['role'] === 'admin') {
            $_SESSION['admin_id'] = $this->getAdminId($user['id']);
        } elseif ($user['role'] === 'pelanggan') {
            $_SESSION['pelanggan_id'] = $this->getPelangganId($user['id']);
        } elseif ($user['role'] === 'pemilik') {
            $_SESSION['pemilik_id'] = $this->getPemilikId($user['id']);
        }
    }

    private function getAdminId($userId)
    {
        $admin = $this->db->fetch("SELECT id_admin FROM admin WHERE user_id = ?", [$userId]);
        return $admin ? $admin['id_admin'] : null;
    }

    private function getPelangganId($userId)
    {
        $pelanggan = $this->db->fetch("SELECT id_pelanggan FROM pelanggan WHERE user_id = ?", [$userId]);
        return $pelanggan ? $pelanggan['id_pelanggan'] : null;
    }

    private function getPemilikId($userId)
    {
        $pemilik = $this->db->fetch("SELECT id_pemilik FROM pemilik_usaha WHERE user_id = ?", [$userId]);
        return $pemilik ? $pemilik['id_pemilik'] : null;
    }

    public static function logout()
    {
        session_destroy();
    }
}