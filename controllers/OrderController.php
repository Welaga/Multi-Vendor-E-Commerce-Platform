<?php
// controllers/OrderController.php

class OrderController {
    private Order $orderModel;
    private Cart $cartModel;
    private Coupon $couponModel;

    public function __construct() {
        $this->orderModel  = new Order();
        $this->cartModel   = new Cart();
        $this->couponModel = new Coupon();
    }

    public function checkout(): void {
        requireRole('buyer');
        $data  = $this->cartModel->getTotals(currentUserId());
        $items = $data['items'];
        if (empty($items)) {
            flash('cart', 'Your cart is empty.', 'error');
            redirect(BASE_URL . 'index.php?page=cart');
        }
        $subtotal = $data['subtotal'];
        $coupon   = $_SESSION['coupon'] ?? null;
        $discount = $coupon['discount'] ?? 0;
        $total    = max(0, $subtotal - $discount);
        require_once __DIR__ . '/../views/buyer/checkout.php';
    }

    public function placeOrder(): void {
        requireRole('buyer');
        $address = trim($_POST['shipping_address'] ?? '');
        if (!$address) {
            flash('checkout', 'Shipping address is required.', 'error');
            redirect(BASE_URL . 'index.php?page=checkout');
        }

        $data     = $this->cartModel->getTotals(currentUserId());
        $items    = $data['items'];
        $subtotal = $data['subtotal'];
        $coupon   = $_SESSION['coupon'] ?? null;
        $discount = $coupon['discount'] ?? 0;
        $final    = max(0, $subtotal - $discount);

        if (empty($items)) {
            redirect(BASE_URL . 'index.php?page=cart');
        }

        $orderId = $this->orderModel->create([
            'buyer_id'         => currentUserId(),
            'total_amount'     => $subtotal,
            'discount_amount'  => $discount,
            'final_amount'     => $final,
            'coupon_code'      => $coupon['code'] ?? null,
            'shipping_address' => $address,
            'payment_method'   => $_POST['payment_method'] ?? 'cash_on_delivery',
        ], $items);

        if ($orderId) {
            if ($coupon) {
                $this->couponModel->incrementUsage($coupon['code']);
                unset($_SESSION['coupon']);
            }
            $this->cartModel->clear(currentUserId());
            flash('order', 'Order placed successfully! Order #' . $orderId);
            redirect(BASE_URL . 'index.php?page=order_detail&id=' . $orderId);
        } else {
            flash('checkout', 'Failed to place order. Please try again.', 'error');
            redirect(BASE_URL . 'index.php?page=checkout');
        }
    }

    public function myOrders(): void {
        requireRole('buyer');
        $orders = $this->orderModel->getByBuyer(currentUserId());
        require_once __DIR__ . '/../views/buyer/orders.php';
    }

    public function orderDetail(): void {
        requireLogin();
        $id    = (int)($_GET['id'] ?? 0);
        $order = $this->orderModel->findById($id);
        if (!$order) { redirect(BASE_URL . 'index.php'); }

        $role = currentRole();
        if ($role === 'buyer' && $order['buyer_id'] !== currentUserId()) {
            redirect(BASE_URL . 'index.php');
        }
        if ($role === 'seller') {
            // Sellers may only view orders that contain their own products
            $db   = Database::connect();
            $stmt = $db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ? AND seller_id = ?");
            $stmt->execute([$id, currentUserId()]);
            if ((int)$stmt->fetchColumn() === 0) redirect(BASE_URL . 'index.php');
        }

        $items = $this->orderModel->getItems($id);
        require_once __DIR__ . '/../views/buyer/order_detail.php';
    }

    /** Seller view orders */
    public function sellerOrders(): void {
        requireRole('seller');
        $orders = $this->orderModel->getBySeller(currentUserId());
        $earnings = $this->orderModel->getSellerEarnings(currentUserId());
        require_once __DIR__ . '/../views/seller/orders.php';
    }

    /** Admin: update order status */
    public function updateStatus(): void {
        requireRole('admin');
        $id     = (int)$_POST['order_id'];
        $status = $_POST['status'];
        $this->orderModel->updateStatus($id, $status);
        flash('order', 'Order status updated.');
        redirect(BASE_URL . 'index.php?page=admin_orders');
    }

    /** Admin: mark seller payout */
    public function markPayout(): void {
        requireRole('admin');
        $sellerId = (int)$_GET['seller_id'];
        $this->orderModel->markPayout($sellerId);
        flash('payout', 'Payout marked as completed.');
        redirect(BASE_URL . 'index.php?page=admin_payouts');
    }
}
