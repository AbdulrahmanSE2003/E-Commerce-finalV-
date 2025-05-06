<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

// Toggle wishlist (add/remove)
if (isset($_GET['wishlist']) && $user_id) {
    $product_id = (int) $_GET['wishlist'];

    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");

    if (mysqli_num_rows($check) > 0) {
        // Remove from wishlist
        mysqli_query($conn, "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    } else {
        // Add to wishlist
        mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
    }

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Handle AJAX cart addition
if (isset($_POST['add_to_cart'])) {
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }

    $product_id = (int) $_POST['product_id'];

    // Check if product already in cart
    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'");

    if (mysqli_num_rows($check_cart) > 0) {
        // Update quantity
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id = '$user_id' AND product_id = '$product_id'");
    } else {
        // Add to cart
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', 1)");
    }

    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    exit;
}

// Get all categories
$categories = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");

// Default category
$default_category_id = 0;
$default_category_name = "All Products";
$categories_result = mysqli_query($conn, "SELECT id FROM categories WHERE name LIKE '%smartphone%' OR name LIKE '%phone%' LIMIT 1");
if (mysqli_num_rows($categories_result) > 0) {
    $default_cat = mysqli_fetch_assoc($categories_result);
    $default_category_id = $default_cat['id'];
    $default_category_name = "Smartphones";
}

$selected_category = isset($_GET['category']) ? (int) $_GET['category'] : $default_category_id;

if ($selected_category > 0) {
    $product_query = "SELECT p.* FROM products p 
                      JOIN product_category pc ON p.id = pc.product_id
                      WHERE pc.category_id = $selected_category
                      ORDER BY p.id ASC";
} else {
    $product_query = "SELECT * FROM products ORDER BY id ASC";
}

$products = mysqli_query($conn, $product_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-actions button.btn-remove,
        .product-actions a.btn-remove {
            width: 42px;
            height: 42px;
            border: none;
            background: #f5f5f5 !important;
            border-radius: 10px;
            color: #999;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .product-actions a.btn-remove:hover {
            background-color: #ffebeb;
            color: #ff4444;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }
    </style>
</head>

<body>
    <div class="toast-container">
        <div id="cartToast" class="toast align-items-center text-white bg-success" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="index.php">
                <span class="navbar-n">Quantum.</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#best-seller">Best Deals</a></li>
                </ul>
                <div class="ms-md-0">
                    <a href="wishlist.php"><i class="fa-regular fa-heart ms-lg-4 p-2 p-lg-3 heart"></i></a>
                    <a href="login.php"><i class="fas fa-arrow-right-to-bracket p-2 p-lg-3 cart"></i></a>
                    <a href="user-dash.php"><i class="fa-regular fa-user p-2 p-lg-3 user"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <section id="products">
        <div class="container py-4">
            <h1 class="mb-4">Our Products</h1>

            <form method="get" action="" class="category-dropdown mb-4">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="0">All Products</option>
                    <?php while ($cat = mysqli_fetch_assoc($categories)):
                        $selected = $selected_category == $cat['id'] ? 'selected' : '';
                        $cat_name = htmlspecialchars($cat['name']);
                        ?>
                        <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= $cat_name ?></option>
                    <?php endwhile; ?>
                </select>
            </form>

            <div class="product-grid">
                <?php if (mysqli_num_rows($products) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($products)):
                        $product_id = $row['id'];
                        $in_wishlist = false;

                        if ($user_id) {
                            $wish_query = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
                            $in_wishlist = mysqli_num_rows($wish_query) > 0;
                        }
                        ?>


                        <div class="product-card p-0 position-relative" style="height: fit-content;">
                            <form method="POST" action="buy-product.php">
                                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['name']) ?>">
                                <input type="hidden" name="product_price" value="<?= $row['price'] ?>">
                                <input type="hidden" name="product_image" value="<?= htmlspecialchars($row['image']) ?>">
                                <input type="hidden" name="product_description"
                                    value="<?= htmlspecialchars($row['description']) ?>">

                                <div class="position-relative">
                                    <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top"
                                        alt="<?= htmlspecialchars($row['name']) ?>">

                                    <?php if ($user_id): ?>
                                        <a href="?wishlist=<?= $row['id'] ?>"
                                            class="btn-remove position-absolute top-0 end-0 m-2 z-3"
                                            onclick="event.stopPropagation();">
                                            <i class="<?= $in_wishlist ? 'fa-solid text-danger' : 'fa-regular' ?> fa-heart"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body p-2 px-3 pb-3 d-flex flex-column">
                                    <h5 class="card-title text-black" style="height: 50px;">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </h5>
                                    <p class="card-text text-muted small mt-2">
                                        <?= htmlspecialchars(substr($row['description'], 0, 100)) ?>...
                                    </p>
                                    <div class="mt-auto pt-3 d-flex gap-1 justify-content-between align-items-center">
                                        <p class="price col-4 fw-medium fs-5 m-0">$<?= number_format($row['price'], 2) ?></p>
                                        <button style="padding: 0.75rem 1.25rem; font-size: 0.7rem;" type="submit"
                                            class="btn col-7 btn-sm btn-primary" onclick="event.stopPropagation();">
                                            <i class="fa fa-cart-plus me-1"></i> Click to Buy
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>




                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning">No products found in this category.</div>
                    </div>
                <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            const cartToast = new bootstrap.Toast(document.getElementById('cartToast'));

            $('.add-to-cart').click(function () {
                const productId = $(this).data('product-id');

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        add_to_cart: true,
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#cartToast .toast-body').text(response.message);
                            cartToast.show();
                        } else {
                            // If user not logged in, redirect to login
                            if (response.message.includes('login')) {
                                window.location.href = 'login.php';
                            } else {
                                $('#cartToast .toast-body').text(response.message);
                                $('#cartToast').removeClass('bg-success').addClass('bg-danger');
                                cartToast.show();
                            }
                        }
                    },
                    error: function () {
                        $('#cartToast .toast-body').text('Error adding to cart');
                        $('#cartToast').removeClass('bg-success').addClass('bg-danger');
                        cartToast.show();
                    }
                });
            });
        });
    </script>
</body>

</html>