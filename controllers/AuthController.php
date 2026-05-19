<?php
// controllers/AuthController.php

class AuthController {
    private User $userModel;
    private SellerProfile $sellerModel;

    public function __construct() {
        $this->userModel   = new User();
        $this->sellerModel = new SellerProfile();
    }

    public function showLogin(): void {
        if (isLoggedIn()) $this->redirectByRole();
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            flash('auth', 'Please fill in all fields.', 'error');
            redirect(BASE_URL . 'index.php?page=login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('auth', 'Invalid email or password.', 'error');
            redirect(BASE_URL . 'index.php?page=login');
        }

        if ($user['status'] === 'inactive') {
            flash('auth', 'Your account has been deactivated.', 'error');
            redirect(BASE_URL . 'index.php?page=login');
        }

        // For sellers, check approval
        if ($user['role_name'] === 'seller') {
            $profile = $this->sellerModel->getByUserId($user['id']);
            if ($profile && $profile['approval_status'] === 'pending') {
                flash('auth', 'Your seller account is pending approval.', 'warning');
                redirect(BASE_URL . 'index.php?page=login');
            }
            if ($profile && $profile['approval_status'] === 'rejected') {
                flash('auth', 'Your seller account has been rejected.', 'error');
                redirect(BASE_URL . 'index.php?page=login');
            }
        }

        // Set session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email']= $user['email'];
        $_SESSION['role']      = $user['role_name'];

        $this->redirectByRole();
    }

    public function showRegister(): void {
        if (isLoggedIn()) $this->redirectByRole();
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function register(): void {
        $name      = trim($_POST['name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $confirm   = $_POST['confirm_password'] ?? '';
        $type      = $_POST['account_type'] ?? 'buyer'; // buyer or seller
        $shopName  = trim($_POST['shop_name'] ?? '');

        // Validation
        if (!$name || !$email || !$password) {
            flash('auth', 'All fields are required.', 'error');
            redirect(BASE_URL . 'index.php?page=register');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('auth', 'Invalid email address.', 'error');
            redirect(BASE_URL . 'index.php?page=register');
        }
        if (strlen($password) < 6) {
            flash('auth', 'Password must be at least 6 characters.', 'error');
            redirect(BASE_URL . 'index.php?page=register');
        }
        if ($password !== $confirm) {
            flash('auth', 'Passwords do not match.', 'error');
            redirect(BASE_URL . 'index.php?page=register');
        }
        if ($this->userModel->findByEmail($email)) {
            flash('auth', 'Email already registered.', 'error');
            redirect(BASE_URL . 'index.php?page=register');
        }
        if ($type === 'seller' && empty($shopName)) {
            flash('auth', 'Shop name is required for sellers.', 'error');
            redirect(BASE_URL . 'index.php?page=register');
        }

        $roleId = $this->userModel->getRoleId($type === 'seller' ? 'seller' : 'buyer');
        $userId = $this->userModel->create([
            'role_id'  => $roleId,
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'phone'    => $_POST['phone'] ?? '',
            'status'   => 'active',
        ]);

        if ($type === 'seller') {
            $this->sellerModel->create($userId, $shopName, '');
            flash('auth', 'Registration successful! Please wait for admin approval.', 'info');
        } else {
            flash('auth', 'Registration successful! You can now log in.', 'success');
        }

        redirect(BASE_URL . 'index.php?page=login');
    }

    public function logout(): void {
        session_destroy();
        redirect(BASE_URL . 'index.php?page=login');
    }

    private function redirectByRole(): void {
        match ($_SESSION['role'] ?? '') {
            'admin'  => redirect(BASE_URL . 'index.php?page=admin_dashboard'),
            'seller' => redirect(BASE_URL . 'index.php?page=seller_dashboard'),
            default  => redirect(BASE_URL . 'index.php'),
        };
    }
}
