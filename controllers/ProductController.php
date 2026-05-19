<?php
// controllers/ProductController.php

class ProductController {
    private Product $productModel;
    private Category $categoryModel;
    private Review $reviewModel;

    public function __construct() {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->reviewModel   = new Review();
    }

    /** Public product listing */
    public function index(): void {
        $filters = [
            'category_id' => (int)($_GET['cat'] ?? 0) ?: null,
            'search'      => trim($_GET['q'] ?? ''),
            'min_price'   => (float)($_GET['min'] ?? 0) ?: null,
            'max_price'   => (float)($_GET['max'] ?? 0) ?: null,
        ];
        $page    = max(1, (int)($_GET['pg'] ?? 1));
        $limit   = 12;
        $offset  = ($page - 1) * $limit;
        $total   = $this->productModel->count($filters);
        $products   = $this->productModel->getAll($filters, $limit, $offset);
        $categories = $this->categoryModel->getAll();
        $pages      = ceil($total / $limit);

        require_once __DIR__ . '/../views/buyer/products.php';
    }

    /** Single product detail */
    public function detail(): void {
        $id      = (int)($_GET['id'] ?? 0);
        $product = $this->productModel->findById($id, true);
        if (!$product) { redirect(BASE_URL . 'index.php?page=products'); }

        $reviews  = $this->reviewModel->getByProduct($id);
        $related  = $this->productModel->getAll(['category_id' => $product['category_id']], 4, 0);

        require_once __DIR__ . '/../views/buyer/product_detail.php';
    }

    /** Seller: add/edit products */
    public function sellerProducts(): void {
        requireRole('seller');
        $products   = $this->productModel->getBySeller(currentUserId());
        $categories = $this->categoryModel->getAll();
        require_once __DIR__ . '/../views/seller/products.php';
    }

    public function addProduct(): void {
        requireRole('seller');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?page=seller_products');
        }
        $price         = (float)$_POST['price'];
        $discountPrice = (float)($_POST['discount_price'] ?? 0);
        if ($discountPrice > 0 && $discountPrice >= $price) {
            flash('product', 'Sale price must be lower than the regular price.', 'error');
            redirect(BASE_URL . 'index.php?page=seller_add_product');
        }
        $image = 'default_product.png';
        if (!empty($_FILES['image']['name'])) {
            $uploaded = uploadImage($_FILES['image']);
            if (!$uploaded) {
                flash('product', 'Invalid image. Max 5MB, JPG/PNG/GIF/WEBP only.', 'error');
                redirect(BASE_URL . 'index.php?page=seller_add_product');
            }
            $image = $uploaded;
        }
        $this->productModel->create([
            'seller_id'      => currentUserId(),
            'category_id'    => (int)$_POST['category_id'],
            'name'           => trim($_POST['name']),
            'description'    => trim($_POST['description']),
            'price'          => (float)$_POST['price'],
            'discount_price' => (float)($_POST['discount_price'] ?? 0),
            'stock'          => (int)$_POST['stock'],
            'image'          => $image,
            'status'         => $_POST['status'] ?? 'active',
        ]);
        flash('product', 'Product added successfully!');
        redirect(BASE_URL . 'index.php?page=seller_products');
    }

    public function editProduct(): void {
        requireRole('seller');
        $id      = (int)($_GET['id'] ?? 0);
        $product = $this->productModel->findById($id);
        if (!$product || $product['seller_id'] !== currentUserId()) {
            redirect(BASE_URL . 'index.php?page=seller_products');
        }
        $categories = $this->categoryModel->getAll();
        require_once __DIR__ . '/../views/seller/edit_product.php';
    }

    public function updateProduct(): void {
        requireRole('seller');
        $id      = (int)$_POST['product_id'];
        $product = $this->productModel->findById($id);
        if (!$product || $product['seller_id'] !== currentUserId()) {
            redirect(BASE_URL . 'index.php?page=seller_products');
        }
        $price         = (float)$_POST['price'];
        $discountPrice = (float)($_POST['discount_price'] ?? 0);
        if ($discountPrice > 0 && $discountPrice >= $price) {
            flash('product', 'Sale price must be lower than the regular price.', 'error');
            redirect(BASE_URL . 'index.php?page=seller_edit_product&id=' . $id);
        }
        $image = $product['image'];
        if (!empty($_FILES['image']['name'])) {
            $uploaded = uploadImage($_FILES['image']);
            if ($uploaded) $image = $uploaded;
        }
        $this->productModel->update($id, [
            'category_id'    => (int)$_POST['category_id'],
            'name'           => trim($_POST['name']),
            'description'    => trim($_POST['description']),
            'price'          => $price,
            'discount_price' => $discountPrice,
            'stock'          => (int)$_POST['stock'],
            'image'          => $image,
            'status'         => $_POST['status'] ?? 'active',
        ]);
        flash('product', 'Product updated!');
        redirect(BASE_URL . 'index.php?page=seller_products');
    }

    public function deleteProduct(): void {
        requireRole('seller');
        $id      = (int)($_GET['id'] ?? 0);
        $product = $this->productModel->findById($id);
        if ($product && $product['seller_id'] === currentUserId()) {
            $this->productModel->delete($id);
            flash('product', 'Product deleted.');
        }
        redirect(BASE_URL . 'index.php?page=seller_products');
    }

    /** Submit review */
    public function addReview(): void {
        requireRole('buyer');
        $productId = (int)$_POST['product_id'];
        $rating    = min(5, max(1, (int)$_POST['rating']));
        $text      = trim($_POST['review_text']);
        $this->reviewModel->create($productId, currentUserId(), $rating, $text);
        flash('review', 'Review submitted!');
        redirect(BASE_URL . 'index.php?page=product_detail&id=' . $productId);
    }
}
