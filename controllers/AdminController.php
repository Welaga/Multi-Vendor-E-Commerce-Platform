<?php
// controllers/AdminController.php

class AdminController {
    private User $userModel;
    private SellerProfile $sellerModel;
    private Product $productModel;
    private Category $categoryModel;
    private Order $orderModel;
    private Coupon $couponModel;

    public function __construct() {
        $this->userModel     = new User();
        $this->sellerModel   = new SellerProfile();
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->orderModel    = new Order();
        $this->couponModel   = new Coupon();
    }

    public function dashboard(): void {
        requireRole('admin');
        $stats = [
            'total_users'    => $this->userModel->count(),
            'total_sellers'  => $this->userModel->count('seller'),
            'total_buyers'   => $this->userModel->count('buyer'),
            'total_products' => count($this->productModel->adminGetAll()),
            'pending_sellers'=> count($this->sellerModel->getPending()),
            'platform_stats' => $this->orderModel->platformStats(),
        ];
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function users(): void {
        requireRole('admin');
        $role  = $_GET['role'] ?? null;
        $users = $this->userModel->getAll($role);
        require_once __DIR__ . '/../views/admin/users.php';
    }

    public function toggleUserStatus(): void {
        requireRole('admin');
        $id   = (int)$_GET['id'];
        $user = $this->userModel->findById($id);
        if ($user) {
            $new = $user['status'] === 'active' ? 'inactive' : 'active';
            $this->userModel->setStatus($id, $new);
        }
        flash('users', 'User status updated.');
        redirect(BASE_URL . 'index.php?page=admin_users');
    }

    public function deleteUser(): void {
        requireRole('admin');
        $id = (int)$_GET['id'];
        if ($id !== currentUserId()) { // Can't delete self
            $this->userModel->delete($id);
            flash('users', 'User deleted.');
        }
        redirect(BASE_URL . 'index.php?page=admin_users');
    }

    public function sellers(): void {
        requireRole('admin');
        $sellers = $this->sellerModel->getAll();
        require_once __DIR__ . '/../views/admin/sellers.php';
    }

    public function approveSeller(): void {
        requireRole('admin');
        $userId  = (int)$_GET['id'];
        $action  = $_GET['action'] ?? '';
        $allowed = ['approved', 'pending', 'rejected'];
        if (!in_array($action, $allowed, true)) {
            flash('sellers', 'Invalid action.', 'error');
            redirect(BASE_URL . 'index.php?page=admin_sellers');
        }
        $this->sellerModel->setApproval($userId, $action);
        flash('sellers', 'Seller ' . $action . '.');
        redirect(BASE_URL . 'index.php?page=admin_sellers');
    }

    // ---- Categories ----
    public function categories(): void {
        requireRole('admin');
        $categories = $this->categoryModel->getAll(false);
        require_once __DIR__ . '/../views/admin/categories.php';
    }

    public function addCategory(): void {
        requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $icon = trim($_POST['icon']);
            if ($name === '' || !preg_match('/^bi-[a-z0-9\-]+$/', $icon)) {
                flash('cat', 'Invalid category name or icon.', 'error');
                redirect(BASE_URL . 'index.php?page=admin_categories');
            }
            $this->categoryModel->create(['name' => $name, 'icon' => $icon]);
            flash('cat', 'Category added!');
        }
        redirect(BASE_URL . 'index.php?page=admin_categories');
    }

    public function deleteCategory(): void {
        requireRole('admin');
        $this->categoryModel->delete((int)$_GET['id']);
        flash('cat', 'Category deleted.');
        redirect(BASE_URL . 'index.php?page=admin_categories');
    }

    // ---- Products ----
    public function products(): void {
        requireRole('admin');
        $products = $this->productModel->adminGetAll();
        require_once __DIR__ . '/../views/admin/products.php';
    }

    // ---- Orders ----
    public function orders(): void {
        requireRole('admin');
        $orders = $this->orderModel->getAll();
        require_once __DIR__ . '/../views/admin/orders.php';
    }

    // ---- Coupons ----
    public function coupons(): void {
        requireRole('admin');
        $coupons = $this->couponModel->getAll();
        require_once __DIR__ . '/../views/admin/coupons.php';
    }

    public function addCoupon(): void {
        requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $expiry = strtotime($_POST['expiry_date'] ?? '');
            if (!$expiry || $expiry < strtotime('today')) {
                flash('coupon', 'Expiry date must be today or in the future.', 'error');
                redirect(BASE_URL . 'index.php?page=admin_coupons');
            }
            $this->couponModel->create($_POST);
            flash('coupon', 'Coupon created!');
        }
        redirect(BASE_URL . 'index.php?page=admin_coupons');
    }

    public function deleteCoupon(): void {
        requireRole('admin');
        $this->couponModel->delete((int)$_GET['id']);
        flash('coupon', 'Coupon deleted.');
        redirect(BASE_URL . 'index.php?page=admin_coupons');
    }

    // ---- Payouts ----
    public function payouts(): void {
        requireRole('admin');
        $db   = Database::connect();
        $stmt = $db->query("
            SELECT se.seller_id, u.name AS seller_name, sp.shop_name,
                   SUM(se.net_earning) AS total_earned,
                   SUM(CASE WHEN se.payout_status = 'pending' THEN se.net_earning ELSE 0 END) AS pending_amount,
                   SUM(CASE WHEN se.payout_status = 'completed' THEN se.net_earning ELSE 0 END) AS paid_out
            FROM seller_earnings se
            JOIN users u ON se.seller_id = u.id
            LEFT JOIN seller_profiles sp ON u.id = sp.user_id
            GROUP BY se.seller_id
            ORDER BY pending_amount DESC
        ");
        $sellers = $stmt->fetchAll();
        require_once __DIR__ . '/../views/admin/payouts.php';
    }
}
