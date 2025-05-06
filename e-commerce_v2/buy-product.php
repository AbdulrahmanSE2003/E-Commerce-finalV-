<?php
session_start();
require_once 'config.php';

$product = [];
$error = '';
$success = '';

// التعامل مع الفورم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && !isset($_POST['add_to_cart'])) {
        // خزن بيانات المنتج في السيشن مؤقتًا
        $_SESSION['current_product'] = [
            'id' => intval($_POST['product_id']),
            'name' => htmlspecialchars($_POST['product_name']),
            'price' => floatval($_POST['product_price']),
            'image' => htmlspecialchars($_POST['product_image']),
            'description' => htmlspecialchars($_POST['product_description'])
        ];
    }

    if (isset($_POST['add_to_cart'])) {
        if (!isset($_SESSION['user_id'])) {
            $error = "Please login to add products to cart.";
        } elseif (!isset($_POST['product_id'])) {
            $error = "Missing product ID.";
        } else {
            $user_id = intval($_SESSION['user_id']);
            $product_id = intval($_POST['product_id']);

            try {
                // Check if product is already in cart
                $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Already in cart, just update quantity
                    $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
                    $update->bind_param("ii", $user_id, $product_id);
                    $update->execute();
                    $success = "Product quantity updated in cart.";
                } else {
                    // Not in cart, insert new
                    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
                    $insert->bind_param("ii", $user_id, $product_id);
                    $insert->execute();
                    $success = "Product added to cart.";
                }
            } catch (Exception $e) {
                $error = "Error adding to cart: " . $e->getMessage();
            }
        }
    }

}

// تحميل بيانات المنتج من السيشن
if (isset($_SESSION['current_product'])) {
    $product = $_SESSION['current_product'];
} else {
    $error = "No product selected.";
}

// تجهيز المتغيرات
$product_id = $product['id'] ?? 0;
$product_name = htmlspecialchars($product['name'] ?? '');
$product_price = $product['price'] ?? 0;
$product_image = htmlspecialchars($product['image'] ?? '');
$product_description = htmlspecialchars($product['description'] ?? '');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $product_name ?> - Product Details</title>

    <!-- External CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Tektur:wght@400;600&family=Mulish:wght@400;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/buy-product.css" />
</head>

<body>


    <!-- ?Navbar -->
    <nav class="navbar navbar-expand-lg ">
        <div class="container">
            <a href="index.php">
                <span class="navbar-n">Quantum.</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link p-2 p-lg-3" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-2 p-lg-3" href="products.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-2 p-lg-3" href="cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link p-2 p-lg-3" href="index.php#best-seller">Best Deals</a>
                    </li>
                </ul>
                <div class="ms-md-0">
                    <a href="wishlist.php"><i class="fa-regular fa-heart ms-lg-4 p-2 p-lg-3 heart"></i></a>
                    <a href="login.php"><i class="fas fa-arrow-right-to-bracket p-2 p-lg-3 cart"></i></a>
                    <a href="user-dash.php"><i class="fa-regular fa-user p-2 p-lg-3 user"></i></a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Main Product Section -->
    <section class="product-main py-5">
        <div class="container">
            <div class="row">
                <!-- Product Gallery -->
                <div class="col-md-6 mb-4" data-aos="fade-right" data-aos-duration="1000">
                    <div class="product-gallery">
                        <div class="main-image">
                            <img src="<?= $product_image ?>" alt="<?= $product_name ?>" />
                            <div class="zoom-controls">
                                <button class="zoom-btn zoom-in" title="Zoom in"><i class="fas fa-plus"></i></button>
                                <button class="zoom-btn zoom-out" title="Zoom out"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <!-- You can keep the thumbnails if you have multiple images, or remove them -->
                        <div class="thumbnails">
                            <div class="thumbnail-item active">
                                <img src="<?= $product_image ?>" alt="<?= $product_name ?>" />
                            </div>
                            <!-- Add more thumbnails if available -->
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-md-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <h1 class="product-title" data-aos="fade-up" data-aos-duration="800">
                        <?= $product_name ?>
                    </h1>

                    <div class="pricing my-3" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <span class="current-price">$<?= number_format($product_price, 2) ?></span>
                        <span class="original-price text-muted text-decoration-line-through">
                            $<?= number_format($product_price + 100, 2) ?>
                        </span>
                        <span class="discount-badge badge bg-danger">
                            <?php
                            $old = $product_price + 100;
                            $discount = round((($old - $product_price) / $old) * 100);
                            echo "$discount% OFF";
                            ?>
                        </span>
                    </div>

                    <!-- Add to Cart Form -->
                    <form method="POST">
                        <input type="hidden" name="add_to_cart" value="1" />
                        <input type="hidden" name="product_id" value="<?= $product_id ?>" />
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                        </button>
                    </form>

                    <!-- Trust Seals Section (New Position) -->
                    <div class="payment-trust-seals">
                        <span class="accepted-payments">Secure payment via:</span>
                        <div class="payment-icons">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-paypal"></i>
                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="delivery-info" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                        <div class="info-item">
                            <i class="fas fa-truck"></i>
                            <span>Free delivery available</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-box"></i>
                            <span>In stock - Ships within 24 hours</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>3 Year Warranty</span>
                        </div>
                    </div>

                    <!-- Share Product Buttons -->
                    <div class="share-product mt-3">
                        <button class="share-btn main-share-btn" id="mainShareBtn" title="Share"><i class="fa-solid fa-arrow-up-from-bracket"></i></button>
                        <div class="share-actions" id="shareActions">
                            <button class="share-btn" id="copyLinkBtn" title="Copy Link"><i class="fas fa-link"></i></button>
                            <a class="share-btn" id="whatsappShare" title="Share on WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                            <a class="share-btn" id="facebookShare" title="Share on Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a class="share-btn" id="xShare" title="Share on X" target="_blank"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Details Section -->
    <section class="product-details py-5">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up" data-aos-duration="800">
                Product Details
            </h2>

            <!-- Product Description -->
            <div class="product-description mb-4" data-aos="fade-up" data-aos-duration="1000">
                <h3>Overview</h3>
                <p>
                    <?= $product_description ?>
                </p>
            </div>
            <!-- Return Policy Toggle Button (moved inside container, centered, max-width) -->
            <div style="max-width: 900px; margin: 0 auto;">
                <button id="toggleReturnPolicy" class="btn btn-outline-primary show-specs-btn w-100 mb-3" type="button">
                    <i class="fas fa-chevron-down me-2"></i>
                    <span class="toggle-text">Show Return Policy</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Return Policy Section -->
    <section class="return-policy py-5" id="returnPolicySection" style="display:none;">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up" data-aos-duration="800">
                Return Policy
            </h2>
            <div class="policy-content">
                <div class="policy-card" data-aos="fade-up" data-aos-duration="1000">
                    <div class="policy-icon">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <div class="policy-info">
                        <h3>Easy Returns</h3>
                        <p>30-day return policy for all products. Items must be in original condition with all packaging and accessories.</p>
                    </div>
                </div>
                <div class="policy-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <div class="policy-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="policy-info">
                        <h3>Free Return Shipping</h3>
                        <p>Free return shipping for defective items. Standard return shipping rates apply for other returns.</p>
                    </div>
                </div>
                <div class="policy-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="policy-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="policy-info">
                        <h3>Refund Process</h3>
                        <p>Refunds are processed within 5-7 business days after receiving the returned item.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-5">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up" data-aos-duration="800">
                Frequently Asked Questions
            </h2>
            <div class="faq-container">
                <div class="faq-item" data-aos="fade-up" data-aos-duration="1000">
                    <div class="faq-question">
                        <h3>What is the warranty period?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>All our products come with a standard 3-year warranty covering manufacturing defects and hardware failures.</p>
                    </div>
                </div>
                <div class="faq-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <div class="faq-question">
                        <h3>How long does shipping take?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Standard shipping takes 3-5 business days. Express shipping is available for 1-2 day delivery.</p>
                    </div>
                </div>
                <div class="faq-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="faq-question">
                        <h3>Do you ship internationally?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, we ship to most countries worldwide. International shipping rates and delivery times vary by location.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shipping Info Section -->
    <section class="shipping-info py-5">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up" data-aos-duration="800">
                Shipping Information
            </h2>
            <div class="shipping-content">
                <div class="shipping-card" data-aos="fade-up" data-aos-duration="1000">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>Fast Delivery</h3>
                    <p>Orders are processed within 24 hours</p>
                </div>
                <div class="shipping-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <i class="fas fa-box"></i>
                    <h3>Secure Packaging</h3>
                    <p>Items are carefully packaged for safe delivery</p>
                </div>
                <div class="shipping-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Track Your Order</h3>
                    <p>Real-time tracking available for all shipments</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews py-5">
        <div class="container">
            <h2 class="section-title mb-4" data-aos="fade-up" data-aos-duration="800">
                Customer Reviews
            </h2>
            <div class="row">
                <!-- Rating Summary -->
                <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-right" data-aos-duration="1000">
                    <div class="rating-summary">
                        <div class="average-rating mb-4">
                            <h1 class="display-4">4.8</h1>
                            <div class="stars mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p>Based on 1,200 reviews</p>
                        </div>
                        <div class="rating-bars">
                            <div class="rating-bar">
                                <span>5 stars</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 75%"></div>
                                </div>
                                <span>75%</span>
                            </div>
                            <div class="rating-bar">
                                <span>4 stars</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 15%"></div>
                                </div>
                                <span>15%</span>
                            </div>
                            <div class="rating-bar">
                                <span>3 stars</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 5%"></div>
                                </div>
                                <span>5%</span>
                            </div>
                            <div class="rating-bar">
                                <span>2 stars</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 3%"></div>
                                </div>
                                <span>3%</span>
                            </div>
                            <div class="rating-bar">
                                <span>1 star</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 2%"></div>
                                </div>
                                <span>2%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review List -->
                <div class="col-md-8" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="review-filter-container mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">Customer Feedback</h5>
                            <div class="dropdown">
                                <button class="btn btn-filter dropdown-toggle" type="button" id="reviewFilterDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-filter"></i>
                                    <span>Filter Reviews</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="reviewFilterDropdown">
                                    <li>
                                        <button class="dropdown-item active" data-rating="all">
                                            All Reviews
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" data-rating="5">
                                            5 Stars
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" data-rating="4">
                                            4 Stars
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" data-rating="3">
                                            3 Stars
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" data-rating="2">
                                            2 Stars
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" data-rating="1">
                                            1 Star
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="review-carousel-container">
                        <div class="review-carousel">
                            <div class="review-list">
                                <!-- Review Items -->
                                <div class="review-item" data-stars="5">
                                    <div class="review-header">
                                        <img src="images/user-image-with-black-background.png" alt="User avatar"
                                            class="rounded-circle" />
                                        <div class="review-info">
                                            <h6>Mohamed Abdel Fattah</h6>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <span class="review-date">2 days ago</span>
                                        </div>
                                    </div>
                                    <p class="review-text">
                                        Excellent product! Great quality and exactly what I needed. Highly recommended.
                                    </p>
                                </div>

                                <div class="review-item" data-stars="5">
                                    <div class="review-header">
                                        <img src="images/user-image-with-black-background.png" alt="User avatar"
                                            class="rounded-circle" />
                                        <div class="review-info">
                                            <h6>Kareem Mostafa</h6>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <span class="review-date">5 days ago</span>
                                        </div>
                                    </div>
                                    <p class="review-text">
                                        Amazing experience. Fast delivery and the product matched the description
                                        perfectly.
                                    </p>
                                </div>

                                <div class="review-item" data-stars="4.5">
                                    <div class="review-header">
                                        <img src="images/user-image-with-black-background.png" alt="User avatar"
                                            class="rounded-circle" />
                                        <div class="review-info">
                                            <h6>Yassin Hassan</h6>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                            <span class="review-date">1 week ago</span>
                                        </div>
                                    </div>
                                    <p class="review-text">
                                        Very good value for the price. Just a few small things that could be improved.
                                    </p>
                                </div>

                                <div class="review-item" data-stars="5">
                                    <div class="review-header">
                                        <img src="images/user-image-with-black-background.png" alt="User avatar"
                                            class="rounded-circle" />
                                        <div class="review-info">
                                            <h6>Fatma Hussein</h6>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <span class="review-date">2 weeks ago</span>
                                        </div>
                                    </div>
                                    <p class="review-text">
                                        Absolutely satisfied! Great service and the item exceeded my expectations.
                                    </p>
                                </div>

                                <div class="review-item" data-stars="1">
                                    <div class="review-header">
                                        <img src="images/user-image-with-black-background.png" alt="User avatar"
                                            class="rounded-circle" />
                                        <div class="review-info">
                                            <h6>Omar Magdy</h6>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                            <span class="review-date">1 week ago</span>
                                        </div>
                                    </div>
                                    <p class="review-text">
                                        Very disappointed. The item arrived damaged and customer support was unhelpful.
                                    </p>
                                </div>

                                <div class="review-item" data-stars="2">
                                    <div class="review-header">
                                        <img src="images/user-image-with-black-background.png" alt="User avatar"
                                            class="rounded-circle" />
                                        <div class="review-info">
                                            <h6>Ibrahim Gamal</h6>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                            <span class="review-date">3 weeks ago</span>
                                        </div>
                                    </div>
                                    <p class="review-text">
                                        Not worth the money. Quality is below expectations and the product had issues.
                                    </p>
                                </div>

                                <div class="review-item" data-stars="4">
                                    <div class="review-header">
                                        <img src="images/person.jpg.png" alt="User avatar" class="rounded-circle" />
                                        <div class="review-info">
                                            <h6>Heba Ahmed</h6>
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                            <span class="review-date">5 days ago</span>
                                        </div>
                                    </div>
                                    <p class="review-text">
                                        Good overall, but not perfect. There were a few minor flaws, but still worth
                                        considering.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <button class="review-nav-btn prev-btn">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="review-nav-btn next-btn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="related-products py-5 bg-light">
        <div class="container">
            <h2 class="section-title mb-4" data-aos="fade-up" data-aos-duration="800">Related Products</h2>
            
            <?php
            // الحصول على التصنيف الخاص بالمنتج الحالي
            $current_product_id = $product_id;
            $category_query = "SELECT c.id, c.name FROM categories c 
                              JOIN product_category pc ON c.id = pc.category_id 
                              WHERE pc.product_id = ?";
            $stmt = $conn->prepare($category_query);
            $stmt->bind_param("i", $current_product_id);
            $stmt->execute();
            $category_result = $stmt->get_result();
            
            if ($category_row = $category_result->fetch_assoc()) {
                $category_id = $category_row['id'];
                $category_name = $category_row['name'];
                
                // الحصول على منتجات عشوائية من نفس التصنيف
                $related_products_query = "SELECT p.id, p.name, p.price, p.image FROM products p 
                                         JOIN product_category pc ON p.id = pc.product_id 
                                         WHERE pc.category_id = ? AND p.id != ? 
                                         ORDER BY RAND() LIMIT 4";
                
                $stmt = $conn->prepare($related_products_query);
                $stmt->bind_param("ii", $category_id, $current_product_id);
                $stmt->execute();
                $related_products_result = $stmt->get_result();
                
                // عدد المنتجات المرتبطة التي تم العثور عليها
                $related_count = $related_products_result->num_rows;
                
                // إذا كان العدد أقل من 4، نضيف منتجات من فئات أخرى
                if ($related_count < 4) {
                    $more_needed = 4 - $related_count;
                    
                    // احفظ المنتجات الحالية في مصفوفة
                    $related_products = [];
                    while ($product = $related_products_result->fetch_assoc()) {
                        $related_products[] = $product;
                    }
                    
                    // الحصول على معرفات المنتجات التي تم العثور عليها بالفعل
                    $exclude_ids = [$current_product_id];
                    foreach ($related_products as $product) {
                        $exclude_ids[] = $product['id'];
                    }
                    
                    // استعلام للحصول على منتجات إضافية من فئات أخرى
                    $additional_query = "SELECT p.id, p.name, p.price, p.image FROM products p 
                                        WHERE p.id NOT IN (" . implode(',', array_fill(0, count($exclude_ids), '?')) . ") 
                                        ORDER BY RAND() LIMIT ?";
                    
                    $stmt = $conn->prepare($additional_query);
                    
                    // إعداد معاملات الاستعلام
                    $param_types = str_repeat('i', count($exclude_ids)) . 'i';
                    $params = $exclude_ids;
                    $params[] = $more_needed;
                    
                    // الربط الديناميكي للمعاملات
                    $bind_params = array();
                    $bind_params[] = &$param_types;
                    foreach ($params as $key => $value) {
                        $bind_params[] = &$params[$key];
                    }
                    call_user_func_array(array($stmt, 'bind_param'), $bind_params);
                    
                    $stmt->execute();
                    $additional_result = $stmt->get_result();
                    
                    // إضافة المنتجات الإضافية إلى المصفوفة
                    while ($product = $additional_result->fetch_assoc()) {
                        $related_products[] = $product;
                    }
                    
                    // عرض المنتجات من المصفوفة
                    if (count($related_products) > 0) {
                        echo '<div class="row">';
                        
                        $delay = 0;
                        foreach ($related_products as $related_product) {
                            // تنسيق السعر
                            $formatted_price = number_format($related_product['price'], 2);
                            
                            // معالجة مسار الصورة
                            $image_path = $related_product['image'];
                            if (!$image_path || strpos($image_path, 'http') !== 0) {
                                // استخدام صورة افتراضية إذا كان مسار الصورة غير متوفر
                                $image_path = "images/product-placeholder.jpg";
                            }
                            
                            // التحقق مما إذا كان المنتج في المفضلة
                            $in_wishlist = false;
                            if (isset($_SESSION['user_id'])) {
                                $wishlist_check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                                $wishlist_check->bind_param("ii", $_SESSION['user_id'], $related_product['id']);
                                $wishlist_check->execute();
                                $wishlist_result = $wishlist_check->get_result();
                                $in_wishlist = $wishlist_result->num_rows > 0;
                            }
                            
                            $wishlist_class = $in_wishlist ? 'wishlist-heart active' : 'wishlist-heart';
                            
                            echo '
                            <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="' . $delay . '">
                                <div class="product-card">
                                    <img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($related_product['name']) . '" class="img-fluid">
                                    <div class="product-info">
                                        <h5>' . htmlspecialchars($related_product['name']) . '</h5>
                                        <p class="price">$' . $formatted_price . '</p>
                                        <form method="post" action="buy-product.php">
                                            <input type="hidden" name="product_id" value="' . $related_product['id'] . '">
                                            <input type="hidden" name="product_name" value="' . htmlspecialchars($related_product['name']) . '">
                                            <input type="hidden" name="product_price" value="' . $related_product['price'] . '">
                                            <input type="hidden" name="product_image" value="' . htmlspecialchars($image_path) . '">
                                            <button type="submit" class="btn btn-primary">Buy Now</button>
                                        </form>
                                    </div>
                                    <button class="' . $wishlist_class . '" data-product-id="' . $related_product['id'] . '">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>';
                            
                            $delay += 100;
                        }
                        
                        echo '</div>';
                    } else {
                        echo '<p class="text-center">No related products found.</p>';
                    }
                } else {
                    // عرض المنتجات من نفس الفئة إذا كانت كافية
                    echo '<div class="row">';
                    
                    $delay = 0;
                    while ($related_product = $related_products_result->fetch_assoc()) {
                        // تنسيق السعر
                        $formatted_price = number_format($related_product['price'], 2);
                        
                        // معالجة مسار الصورة
                        $image_path = $related_product['image'];
                        if (!$image_path || strpos($image_path, 'http') !== 0) {
                            // استخدام صورة افتراضية إذا كان مسار الصورة غير متوفر
                            $image_path = "images/product-placeholder.jpg";
                        }
                        
                        // التحقق مما إذا كان المنتج في المفضلة
                        $in_wishlist = false;
                        if (isset($_SESSION['user_id'])) {
                            $wishlist_check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
                            $wishlist_check->bind_param("ii", $_SESSION['user_id'], $related_product['id']);
                            $wishlist_check->execute();
                            $wishlist_result = $wishlist_check->get_result();
                            $in_wishlist = $wishlist_result->num_rows > 0;
                        }
                        
                        $wishlist_class = $in_wishlist ? 'wishlist-heart active' : 'wishlist-heart';
                        
                        echo '
                        <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="' . $delay . '">
                            <div class="product-card">
                                <img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($related_product['name']) . '" class="img-fluid">
                                <div class="product-info">
                                    <h5>' . htmlspecialchars($related_product['name']) . '</h5>
                                    <p class="price">$' . $formatted_price . '</p>
                                    <form method="post" action="buy-product.php">
                                        <input type="hidden" name="product_id" value="' . $related_product['id'] . '">
                                        <input type="hidden" name="product_name" value="' . htmlspecialchars($related_product['name']) . '">
                                        <input type="hidden" name="product_price" value="' . $related_product['price'] . '">
                                        <input type="hidden" name="product_image" value="' . htmlspecialchars($image_path) . '">
                                        <button type="submit" class="btn btn-primary">Buy Now</button>
                                    </form>
                                </div>
                                <button class="' . $wishlist_class . '" data-product-id="' . $related_product['id'] . '">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>';
                        
                        $delay += 100;
                    }
                    
                    echo '</div>';
                }
            } else {
                echo '<p class="text-center">Category information not available.</p>';
            }
            ?>

            <!-- See More Button -->
            <div class="text-center mt-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                <a href="products.php" class="btn btn-outline-primary see-more-btn">
                    See More Products
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer pt-5 pb-5 text-center text-md-start">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="info">
                            <a href="index.php">
                                <span class="logo">Quantum.</span>
                            </a>
                            <p class="desc pt-4">
                                I know you'll agree with me. We're smart, fast, and have unlimited
                                shopping.
                            </p>
                            <ul class="ps-0 d-flex gap-4 social">
                                <li><a href=""><i class="fa-brands fa-x-twitter"></i></a></li>
                                <li><a href=""><i class="fa-brands fa-facebook"></i></a></li>
                                <li><a href=""><i class="fa-brands fa-instagram"></i></a></li>
                                <li><a href=""><i class="fa-brands fa-tiktok"></i></a></li>
                            </ul>

                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="info">
                            <ul class="pt-0 pt-sm-5">
                                <li class="ser">Services</li>
                                <li class="lin pt-3">Bonus program</li>
                                <li class="lin pt-3">Gift cards</li>
                                <li class="lin pt-3">Services contacts</li>
                                <li class="lin pt-3">Credit and payment</li>
                                <li class="lin pt-3">Non-cash account</li>
                                <li class="lin pt-3">Payment</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="info">
                            <ul class="pt-sm-5">
                                <li class="ser">Assistance to buyer</li>
                                <li class="lin pt-3">Find an order</li>
                                <li class="lin pt-3">Terms of delivery</li>
                                <li class="lin pt-3">Exchange and returns of goods</li>
                                <li class="lin pt-3">Guarantee</li>
                                <li class="lin pt-3">Frequently asked questions</li>
                                <li class="lin pt-3">Terms of use of the site</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="copyright pt-1">
                    Created By <span>The Debuggers Team</span>
                    <div>&copy; 2025 - <span>Quantum.</span></div>
                </div>
            </div>
        </div>
    </footer>

    <?php if (!empty($success) || !empty($error)): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
            <div class="toast align-items-center text-white <?= $success ? 'bg-success' : 'bg-danger' ?> border-0 show"
                role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $success ?: $error ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            offset: 100,
            duration: 800,
            easing: "ease-in-out",
            once: true,
        });
    </script>
    <script src="product.js"></script>
    <script src="js/buy-product.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('toggleReturnPolicy');
            const section = document.getElementById('returnPolicySection');
            const text = btn.querySelector('.toggle-text');
            const icon = btn.querySelector('i');
            let visible = false;
            btn.addEventListener('click', function() {
                visible = !visible;
                section.style.display = visible ? 'block' : 'none';
                text.textContent = visible ? 'Hide Return Policy' : 'Show Return Policy';
                btn.classList.toggle('active', visible);
            });

            // Share Product Buttons Logic
            const mainShareBtn = document.getElementById('mainShareBtn');
            const shareActions = document.getElementById('shareActions');
            const copyBtn = document.getElementById('copyLinkBtn');
            const whatsappBtn = document.getElementById('whatsappShare');
            const facebookBtn = document.getElementById('facebookShare');
            const xBtn = document.getElementById('xShare');
            const productUrl = window.location.href;

            // Hide actions initially
            shareActions.style.display = 'none';

            mainShareBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (shareActions.style.display === 'none') {
                    shareActions.style.display = 'flex';
                    setTimeout(() => shareActions.classList.add('show'), 10);
                } else {
                    shareActions.classList.remove('show');
                    setTimeout(() => shareActions.style.display = 'none', 200);
                }
            });
            document.addEventListener('click', function(e) {
                if (!shareActions.contains(e.target) && e.target !== mainShareBtn) {
                    shareActions.classList.remove('show');
                    setTimeout(() => shareActions.style.display = 'none', 200);
                }
            });
            if (copyBtn) {
                copyBtn.addEventListener('click', function() {
                    navigator.clipboard.writeText(productUrl);
                    copyBtn.classList.add('copied');
                    setTimeout(() => copyBtn.classList.remove('copied'), 1200);
                });
            }
            if (whatsappBtn) {
                whatsappBtn.href = `https://wa.me/?text=${encodeURIComponent(productUrl)}`;
            }
            if (facebookBtn) {
                facebookBtn.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(productUrl)}`;
            }
            if (xBtn) {
                xBtn.href = `https://x.com/intent/tweet?url=${encodeURIComponent(productUrl)}`;
            }
        });
    </script>
</body>

</html>