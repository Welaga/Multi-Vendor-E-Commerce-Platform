<?php
// controllers/CartController.php

class CartController {
    private Cart $cartModel;
    private Coupon $couponModel;

    public function __construct() {
        $this->cartModel   = new Cart();
        $this->couponModel = new Coupon();
    }

    public function index(): void {
        requireRole('buyer');
        $data  = $this->cartModel->getTotals(currentUserId());
        $items = $data['items'];
        $subtotal = $data['subtotal'];
        require_once __DIR__ . '/../views/buyer/cart.php';
    }

    public function add(): void {
        requireLogin();
        $productId = (int)$_POST['product_id'];
        $qty       = max(1, (int)($_POST['qty'] ?? 1));
        $this->cartModel->addOrUpdate(currentUserId(), $productId, $qty);
        flash('cart', 'Item added to cart!');
        $fallback = BASE_URL . 'index.php?page=products';
        $ref      = $_SERVER['HTTP_REFERER'] ?? $fallback;
        // Reject cross-origin referers to prevent open redirect abuse
        if (parse_url($ref, PHP_URL_HOST) !== parse_url(BASE_URL, PHP_URL_HOST)) {
            $ref = $fallback;
        }
        redirect($ref);
    }

    public function update(): void {
        requireRole('buyer');
        $productId = (int)$_POST['product_id'];
        $qty       = max(1, (int)$_POST['quantity']);
        $this->cartModel->updateQty(currentUserId(), $productId, $qty);
        redirect(BASE_URL . 'index.php?page=cart');
    }

    public function remove(): void {
        requireRole('buyer');
        $productId = (int)$_GET['id'];
        $this->cartModel->remove(currentUserId(), $productId);
        redirect(BASE_URL . 'index.php?page=cart');
    }

    public function applyCoupon(): void {
        requireRole('buyer');
        $code  = trim($_POST['coupon_code'] ?? '');
        $total = (float)($_POST['subtotal'] ?? 0);
        if ($code === '' || strlen($code) > 50 || !preg_match('/^[A-Za-z0-9_\-]+$/', $code)) {
            flash('coupon', 'Invalid coupon code.', 'error');
            redirect(BASE_URL . 'index.php?page=cart');
        }
        [$discount, $message] = $this->couponModel->apply($code, $total);
        if ($discount > 0) {
            $_SESSION['coupon'] = ['code' => $code, 'discount' => $discount];
            flash('coupon', $message . ' You save ' . currency($discount));
        } else {
            unset($_SESSION['coupon']);
            flash('coupon', $message, 'error');
        }
        redirect(BASE_URL . 'index.php?page=cart');
    }
}
