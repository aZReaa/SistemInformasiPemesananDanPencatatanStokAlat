<?php

class AuthController
{
    private $login;
    private $pelanggan;

    public function __construct()
    {
        $this->login = new Login();
        $this->pelanggan = new Pelanggan();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Username dan password harus diisi';
                header('Location: /hakikah/login');
                exit;
            }

            $user = $this->login->authenticate($username, $password);

            if ($user) {
                $this->login->createSession($user);
                $_SESSION['success'] = 'Login berhasil';
                
                switch ($user['role']) {
                    case 'admin':
                        header('Location: /hakikah/dashboard');
                        break;
                    case 'pelanggan':
                        header('Location: /hakikah/dashboard');
                        break;
                    case 'pemilik':
                        header('Location: /hakikah/dashboard');
                        break;
                    default:
                        header('Location: /hakikah/');
                }
                exit;
            } else {
                $_SESSION['error'] = 'Username atau password salah';
                header('Location: /hakikah/login');
                exit;
            }
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama' => trim($_POST['nama'] ?? ''),
                'alamat' => trim($_POST['alamat'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'no_hp' => trim($_POST['no_hp'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? ''
            ];

            $errors = $this->validateRegistration($data);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
                header('Location: /hakikah/register');
                exit;
            }

            try {
                $pelangganId = $this->pelanggan->register($data);
                
                if ($pelangganId) {
                    $_SESSION['success'] = 'Registrasi berhasil! Silakan login dengan akun Anda.';
                    header('Location: /hakikah/login');
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
                $_SESSION['old_data'] = $data;
                header('Location: /hakikah/register');
                exit;
            }
        }
    }

    private function validateRegistration($data)
    {
        $errors = [];

        if (empty($data['nama'])) {
            $errors[] = 'Nama harus diisi';
        }

        if (empty($data['alamat'])) {
            $errors[] = 'Alamat harus diisi';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email harus diisi';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }

        if (empty($data['no_hp'])) {
            $errors[] = 'Nomor HP harus diisi';
        }

        if (empty($data['username'])) {
            $errors[] = 'Username harus diisi';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password harus diisi';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }

        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Konfirmasi password tidak cocok';
        }

        $db = Database::getInstance();
        $existingUser = $db->fetch("SELECT id FROM users WHERE username = ?", [$data['username']]);
        if ($existingUser) {
            $errors[] = 'Username sudah digunakan';
        }

        $existingEmail = $db->fetch("SELECT id_pelanggan FROM pelanggan WHERE email = ?", [$data['email']]);
        if ($existingEmail) {
            $errors[] = 'Email sudah terdaftar';
        }

        return $errors;
    }

    public function logout()
    {
        Login::logout();
        $_SESSION['success'] = 'Logout berhasil';
        header('Location: /hakikah/');
        exit;
    }
}