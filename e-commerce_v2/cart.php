<?php
session_start();
// Debug code to verify form submission
error_log("POST data received: " . print_r($_POST, true));
file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'ecommerce_db';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $user_id = $_SESSION['user_id'];

    try {
        $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['remove_item'])) {
            // Remove item from cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
            $stmt->bindParam(':id', $_POST['item_id'], PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true]);
            exit();
        } elseif (isset($_POST['update_quantity'])) {
            // Update item quantity
            $stmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE id = :id AND user_id = :user_id");
            $stmt->bindParam(':quantity', $_POST['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $_POST['item_id'], PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true]);
            exit();
        } elseif (isset($_POST['clear_cart'])) {
            // Clear entire cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true]);
            exit();
        } elseif (isset($_POST['get_cart'])) {
            // Get all cart items
            $stmt = $pdo->prepare("
                SELECT c.id, p.id as product_id, p.name, p.price, p.image, p.stock, c.quantity, 
                       (p.price * c.quantity) as item_total
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :user_id
                ORDER BY c.added_at DESC
            ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate totals
            $subtotal = 0;
            $item_count = 0;
            foreach ($cart_items as $item) {
                $subtotal += $item['item_total'];
                $item_count += $item['quantity'];
            }

            echo json_encode([
                'success' => true,
                'cart_items' => $cart_items,
                'subtotal' => $subtotal,
                'item_count' => $item_count
            ]);
            exit();
        } elseif (isset($_POST['move_to_wishlist'])) {
            // Verify the item belongs to the user
            $stmt = $pdo->prepare("SELECT * FROM cart WHERE id = :id AND user_id = :user_id");
            $stmt->bindParam(':id', $_POST['item_id'], PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cartItem) {
                echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
                exit();
            }

            // Check if item already exists in wishlist
            $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $cartItem['product_id'], PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch()) {
                // Item already in wishlist - just remove from cart
                $stmt = $pdo->prepare("DELETE FROM cart WHERE id = :id");
                $stmt->bindParam(':id', $_POST['item_id'], PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(['success' => true, 'message' => 'Item already in wishlist - removed from cart']);
                exit();
            }

            // Add to wishlist
            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $cartItem['product_id'], PDO::PARAM_INT);
            $stmt->execute();

            // Remove from cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = :id");
            $stmt->bindParam(':id', $_POST['item_id'], PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true]);
            exit();
        } elseif (isset($_POST['place_order'])) {
            error_log("Starting order processing");

            try {
                // Validate required fields
                $required = [
                    'first_name' => 'First name is required',
                    'last_name' => 'Last name is required',
                    'phone1' => 'Primary phone is required',
                    'phone2' => 'Secondary phone is required',
                    'address' => 'Address is required',
                    'city' => 'City is required'
                ];

                $errors = [];
                $data = [];

                foreach ($required as $field => $error) {
                    if (empty($_POST[$field])) {
                        $errors[] = $error;
                    } else {
                        $data[$field] = filter_input(INPUT_POST, $field, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    }
                }

                if (!empty($errors)) {
                    throw new Exception(implode("\n", $errors));
                }

                // Begin transaction
                $pdo->beginTransaction();

                // Get cart items
                $stmt = $pdo->prepare("
                    SELECT c.product_id, p.price, c.quantity, p.name as product_name 
                    FROM cart c 
                    JOIN products p ON c.product_id = p.id 
                    WHERE c.user_id = :user_id
                ");
                $stmt->execute([':user_id' => $user_id]);
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($cart_items)) {
                    throw new Exception("Your cart is empty");
                }

                // Calculate total and product names
                $total = 0;
                $product_names = [];
                foreach ($cart_items as $item) {
                    $total += $item['price'] * $item['quantity'];
                    $product_names[] = "{$item['product_name']} (×{$item['quantity']})";
                }

                // Insert order
                $stmt = $pdo->prepare("
                    INSERT INTO orders (
                        user_id, 
                        product_name, 
                        status, 
                        total_amount
                    ) VALUES (
                        :user_id, 
                        :product_name, 
                        'Pending', 
                        :total_amount
                    )
                ");

                $inserted = $stmt->execute([
                    ':user_id' => $user_id,
                    ':product_name' => substr(implode(', ', $product_names), 0, 255),
                    ':total_amount' => $total
                ]);

                if (!$inserted) {
                    throw new Exception("Failed to create order: " . implode(', ', $stmt->errorInfo()));
                }

                $order_id = $pdo->lastInsertId();

                // Update stock and clear cart
                foreach ($cart_items as $item) {
                    $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? 
                                        WHERE id = ? AND stock >= ?");
                    $stmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);

                    if ($stmt->rowCount() === 0) {
                        throw new Exception("Insufficient stock for {$item['product_name']}");
                    }
                }

                // Clear cart
                $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

                // Commit transaction
                if (!$pdo->commit()) {
                    throw new Exception("Transaction commit failed");
                }

                // Success response
                echo json_encode([
                    'success' => true,
                    'order_id' => $order_id,
                    'message' => 'Order placed successfully!'
                ]);
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("Order processing failed: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit();
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
}

// Normal page load - fetch cart items
$user_id = $_SESSION['user_id'];
$cart_items = [];
$item_count = 0;
$subtotal = 0;

try {
    $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT c.id, p.id as product_id, p.name, p.description, p.price, p.image, p.stock, c.quantity, 
               (p.price * c.quantity) as item_total
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = :user_id
        ORDER BY c.added_at DESC
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $item_count = 0;
    $subtotal = 0;

    foreach ($cart_items as $item) {
        $subtotal += $item['item_total'];
        $item_count += $item['quantity'];
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error_message = "Unable to load cart. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&family=Tektur:wght@400..900&display=swap"
        rel="stylesheet" />
    <!-- !Bootstrap Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- !Css Link -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="cart-page mulish">
    <!-- SECTION - Start Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="index.php">
                <span class="logo">Quantum.</span>
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
                    <a href="user-dash.php"><i class="fa-regular fa-user p-2 p-lg-3 user"></i></a>
                </div>
            </div>
        </div>
    </nav>
    <!-- SECTION - End Navbar -->
    <!-- SECTION - Start Cart -->
    <section id="cart" class="py-3 position-relative" style="margin-top: var(--nav-height)">
        <h1 class="sec-title fw-bold position-relative">
            Cart <span class="num-items">(<?php echo $item_count ?>
                <?php echo $item_count == 1 ? 'item' : 'items' ?>)</span>
        </h1>
        <div class="container position-relative p-1 d-flex">
            <?php if (count($cart_items) == 0): ?>
                <div id="cart-empty" class="d-flex flex-column gap-3 align-items-center w-100">
                    <i class="fa-solid fa-cart-shopping fa-3x"></i>
                    <h2 class="fw-bold">Your Cart is Empty</h2>
                    <p class="fw-bold">Add items to it now</p>
                </div>
            <?php else: ?>
                <div id="cart-container"
                    class="row mt-5 mx-auto p-1 g-4 d-flex flex-column justify-content-center align-items-center">
                    <?php foreach ($cart_items as $item): ?>
                        <div id="item-in-cart" class="d-flex p-3" data-item-id="<?php echo $item['id'] ?>">
                            <figure class="">
                                <a href="#" class="btn"><img src="<?php echo $item['image'] ?>" class="w-100"
                                        alt="<?php echo $item['name'] ?>" /></a>
                            </figure>
                            <div class="item-details">
                                <figcaption class="d-flex justify-content-between ps-3">
                                    <div class="left-side-details p-2">
                                        <h5 class="item-name fw-bold"><?php echo $item['name'] ?></h5>
                                        <p class="item-desc"><?php echo $item['description'] ?></p>
                                        <p class="delivery-date">Get It by
                                            <span><?php echo date('D, M j', strtotime('+3 days')) ?></span>
                                        </p>
                                        <p class="sold-by">Sold By <span
                                                class="text-uppercase"><?php echo explode(' ', $item['name'])[0] ?></span></p>
                                        <p class="warranty"><i class="fa-solid fa-award"></i> 2 Years Warranty</p>


                                        <div class="btns-holder d-flex gap-1">
                                            <div class="left-btn">
                                                <button
                                                    class="removeBtn btn d-flex justify-content-start align-items-center gap-2"
                                                    data-item-id="<?php echo $item['id'] ?>">
                                                    <lord-icon src="https://cdn.lordicon.com/skkahier.json" trigger="hover"
                                                        style="width: 33px; height: 33px">
                                                    </lord-icon>
                                                    <span>Remove</span>
                                                </button>
                                            </div>



                                            <div class="right-btn">
                                                <a
                                                    class="moveToWishlistBtn btn d-flex justify-content-start align-items-center gap-2">
                                                    <lord-icon src="https://cdn.lordicon.com/ulnswmkk.json" trigger="hover"
                                                        style="width: 33px; height: 33px">
                                                    </lord-icon>
                                                    <span>Move to Wishlist</span>
                                                </a>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="right-side-details d-flex flex-column align-items-end gap-3">
                                        <div class="holder">
                                            <div class="prices pb-lg-5 d-flex flex-column align-items-end px-3">
                                                <span
                                                    class="item-price fw-bolder fs-3">$<?php echo number_format($item['price'], 2) ?></span>
                                                <span
                                                    class="item-old-price text-decoration-line-through text-black-50">$<?php echo number_format($item['price'] * 1.2, 2) ?></span>
                                            </div>
                                            <span class="mt-3 pe-2"><i class="truck-icon fa-solid fa-truck"></i> <i
                                                    class="fw-bold">Free Delivery</i></span>
                                        </div>
                                        <span class="item-quantity ps-2 d-flex gap-1 mt-lg-auto">
                                            <span class="pe-2">Qty:</span>
                                            <span class="decreaseBtn custom-btn btn fs-5 fw-bold">−</span>
                                            <input onkeydown="return false;" class="text-center quantity-input" type="number"
                                                value="<?php echo $item['quantity'] ?>" min="1"
                                                max="<?php echo $item['stock'] ?>" readonly />
                                            <span class="increaseBtn custom-btn btn fs-5 fw-bold">+</span>
                                        </span>
                                    </div>
                                </figcaption>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="order-summary" class="mt-5 p-3">
                    <h3 class="fw-bold fs-4 pb-3">Order Summary</h3>
                    <div id="order-details" class="d-flex flex-column gap-3">
                        <div class="order-details-item d-flex justify-content-between">
                            <span>Subtotal <span>(<?php echo $item_count ?>
                                    <?php echo $item_count == 1 ? 'item' : 'items' ?>)</span></span>
                            <span id="subtotal">$<?php echo number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="order-details-item d-flex justify-content-between pb-2">
                            <span>Shipping</span>
                            <span id="shipping" class="text-uppercase fw-bold text-success">free</span>
                        </div>
                        <div id="total-price" class="order-details-item d-flex justify-content-between fs-4 fw-bold pt-3">
                            <span>Total</span>
                            <span id="total">$<?php echo number_format($subtotal, 2) ?></span>
                        </div>
                        <button id="checkOutBtn" class="btn text-uppercase mt-3">checkout</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        <div id="popup">
            <div id="popup-container">
                <span id="closePopupBtn"><i class="fa-solid fa-xmark"></i></span>
                <h5>Complete Your Order</h5>
                <form id="shippingForm" method="POST">
                    <div class="form-element">
                        <div class="input-label-holder">
                            <label for="firstName">First Name*</label>
                            <input class="form-control" name="first_name" type="text" id="firstName" required
                                placeholder="Enter your first name" />
                            <div class="invalid-feedback">Please enter your first name</div>
                        </div>
                        <div class="input-label-holder">
                            <label for="lastName">Last Name*</label>
                            <input class="form-control" name="last_name" type="text" id="lastName" required
                                placeholder="Enter your last name" />
                            <div class="invalid-feedback">Please enter your last name</div>
                        </div>
                    </div>

                    <div class="form-element">
                        <div class="input-label-holder">
                            <label for="phoneNumber1">Primary Phone*</label>
                            <input class="form-control" name="phone1" type="tel" id="phoneNumber1" required
                                placeholder="Ex: +201234567890" />
                            <div class="invalid-feedback">Please enter a valid phone number</div>
                        </div>
                        <div class="input-label-holder">
                            <label for="phoneNumber2">Secondary Phone*</label>
                            <input class="form-control" name="phone2" type="tel" id="phoneNumber2" required
                                placeholder="Ex: +201234567890" />
                            <div class="invalid-feedback">Please enter a valid phone number</div>
                        </div>
                    </div>

                    <div class="form-element">
                        <div class="input-label-holder address">
                            <label for="address">Shipping Address*</label>
                            <input class="form-control" name="address" id="address" required rows="3"
                                placeholder="Building, Street, Area, City"></input>
                            <div class="invalid-feedback">Please enter your complete address</div>
                        </div>
                        <div class="input-label-holder select">
                            <label for="city">City*</label>
                            <select name="city" id="city" required class="form-control">
                                <option value="">Select your city</option>
                                <option value="Cairo">Cairo</option>
                                <option value="Alexandria">Alexandria</option>
                                <option value="Giza">Giza</option>
                                <option value="Luxor">Luxor</option>
                                <option value="Aswan">Aswan</option>
                            </select>
                            <div class="invalid-feedback">Please select your city</div>
                        </div>
                    </div>

                    <div class="input-label-holder comment">
                        <label for="notes">Order Notes (Optional)</label>
                        <textarea class="form-control resize-none" name="comment" id="notes" rows="2"
                            placeholder="Special instructions for delivery"></textarea>
                    </div>


                    <button type="submit" id="placeOrderBtn" class="btn btn-primary w-100 mt-3 py-2 fw-bold">
                        <i class="fas fa-lock me-2"></i> Place Order Securely
                    </button>

                    <div class="security-info mt-2 text-center small text-muted">
                        <i class="fas fa-shield-alt me-1"></i> Your information is secured with SSL encryption
                    </div>
                </form>
            </div>
        </div>

    </section>
    <!-- SECTION - End Cart -->

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


    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script>
        //? This is for cart page
        if (document.body.classList.contains("cart-page")) {
            // DOM Elements
            const closePopup = document.getElementById("closePopupBtn");
            const checkOutBtn = document.getElementById("checkOutBtn");
            const cartContainer = document.getElementById("cart-container");

            // Add this function definition near the top of your script section
            function loadCart() {
                fetch('cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'get_cart=true'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            renderCart(data.cart_items, data.subtotal, data.item_count);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to load cart'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load cart'
                        });
                    });
            }

            // Add this function after loadCart()
            function renderCart(items, subtotal, itemCount) {
                const cartContainer = document.getElementById('cart-container');
                const orderSummary = document.getElementById('order-summary');

                // Update item count in title
                document.querySelector('.num-items').textContent = `(${itemCount} ${itemCount === 1 ? 'item' : 'items'})`;

                // Update order summary
                document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
                document.getElementById('total').textContent = `$${subtotal.toFixed(2)}`;
                document.querySelector('.order-details-item span span').textContent = `(${itemCount} ${itemCount === 1 ? 'item' : 'items'})`;

                if (items.length === 0) {
                    // Create empty cart message if it doesn't exist
                    let emptyCart = document.getElementById('cart-empty');
                    if (!emptyCart) {
                        emptyCart = document.createElement('div');
                        emptyCart.id = 'cart-empty';
                        emptyCart.className = 'd-flex flex-column gap-3 align-items-center w-100';
                        emptyCart.innerHTML = `
                <i class="fa-solid fa-cart-shopping fa-3x"></i>
                <h2 class="fw-bold">Your Cart is Empty</h2>
                <p class="fw-bold">Add items to it now</p>
            `;
                    }

                    cartContainer.innerHTML = '';
                    cartContainer.appendChild(emptyCart);
                    orderSummary.style.display = 'none';
                    return;
                }

                orderSummary.style.display = 'block';
                cartContainer.innerHTML = '';

                // Render each cart item
                items.forEach(item => {
                    const cartItem = document.createElement('div');
                    cartItem.className = 'd-flex p-3';
                    cartItem.id = 'item-in-cart';
                    cartItem.dataset.itemId = item.id;

                    cartItem.innerHTML = `
            <figure class="">
                <a href="#" class="btn"><img src="../images/products/${item.image}" class="w-100" alt="${item.name}"/></a>
            </figure>
            <div class="item-details">
                <figcaption class="d-flex justify-content-between ps-3">
                    <div class="left-side-details p-2">
                        <p class="item-desc">${item.name}</p>
                        <p class="delivery-date">Get It by <span>${getDeliveryDate()}</span></p>
                        <p class="sold-by">Sold By <span class="text-uppercase">${item.name.split(' ')[0]}</span></p>
                        <p class="warranty"><i class="fa-solid fa-award"></i> 2 Years Warranty</p>
                        <div class="btns-holder d-flex gap-3">
                            <div class="left-btn">
                                <button class="removeBtn btn d-flex justify-content-start align-items-center gap-2" data-item-id="${item.id}">
                                    <lord-icon src="https://cdn.lordicon.com/skkahier.json" trigger="hover" style="width: 33px; height: 33px"></lord-icon>
                                    <span>Remove</span>
                                </button>
                            </div>
                            <div class="right-btn">
                                <button class="moveToWishlistBtn btn d-flex justify-content-start align-items-center gap-2">
                                    <lord-icon src="https://cdn.lordicon.com/ulnswmkk.json" trigger="hover" style="width: 33px; height: 33px"></lord-icon>
                                    <span>Move to Wishlist</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="right-side-details d-flex flex-column align-items-end gap-3">
                        <div class="holder">
                            <div class="prices pb-lg-5 d-flex flex-column align-items-end px-3">
                                <span class="item-price fw-bolder fs-3">$${item.price.toFixed(2)}</span>
                                <span class="item-old-price text-decoration-line-through text-black-50">$${(item.price * 1.2).toFixed(2)}</span>
                            </div>
                            <span class="mt-3 pe-2"><i class="truck-icon fa-solid fa-truck"></i> <i class="fw-bold">Free Delivery</i></span>
                        </div>
                        <span class="item-quantity ps-2 d-flex gap-1 mt-lg-auto">
                            <span class="pe-2">Qty:</span>
                            <button class="decreaseBtn custom-btn btn fs-5 fw-bold">−</button>
                            <input onkeydown="return false;" class="text-center quantity-input" type="number" value="${item.quantity}" min="1" max="${item.stock}" readonly />
                            <button class="increaseBtn custom-btn btn fs-5 fw-bold">+</button>
                        </span>
                    </div>
                </figcaption>
            </div>
        `;

                    cartContainer.appendChild(cartItem);
                });

                // Re-attach event listeners to new elements
                setupCartItemEvents();
            }

            // Helper function for delivery date
            function getDeliveryDate() {
                const date = new Date();
                date.setDate(date.getDate() + 3);
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric'
                });
            }
            // Checkout popup
            checkOutBtn.addEventListener("click", () => {
                document.body.style.overflow = "hidden";
                document.getElementById("popup").classList.add("active");
                document.getElementById("popup-container").classList.add("active");
            });

            closePopup.addEventListener("click", () => {
                document.body.style.overflow = "auto";
                document.getElementById("popup").classList.remove("active");
                document.getElementById("popup-container").classList.remove("active");
            });

            // Quantity controls
            document.querySelectorAll('.quantity-input').forEach(input => {
                const itemId = input.closest('#item-in-cart').dataset.itemId;
                const decreaseBtn = input.previousElementSibling;
                const increaseBtn = input.nextElementSibling;

                decreaseBtn.addEventListener('click', () => {
                    if (input.value > 1) {
                        input.value = parseInt(input.value) - 1;
                        updateQuantity(itemId, input.value);
                    }
                });

                increaseBtn.addEventListener('click', () => {
                    if (input.value < parseInt(input.max)) {
                        input.value = parseInt(input.value) + 1;
                        updateQuantity(itemId, input.value);
                    }
                });
            });





            // Remove item with confirmation
            document.addEventListener('click', function(e) {
                if (e.target.closest('.removeBtn')) {
                    e.preventDefault();
                    const itemId = e.target.closest('.removeBtn').dataset.itemId;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will remove the item from your cart",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('cart.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `remove_item=true&item_id=${itemId}`
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Removed!',
                                            text: 'Item has been removed from your cart',
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => {
                                            loadCart(); // Refresh the cart after removal
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message || 'Failed to remove item'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'An error occurred while removing the item'
                                    });
                                });
                        }
                    });
                }
            });


            // Update quantity
            function updateQuantity(itemId, quantity) {
                fetch('cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `update_quantity=true&item_id=${itemId}&quantity=${quantity}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error(data.message);
                            location.reload(); // Refresh to get correct values
                        } else {
                            // Update the totals on the page without reloading
                            fetch('cart.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'get_cart=true'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        document.querySelector('.num-items').textContent = `(${data.item_count} ${data.item_count === 1 ? 'item' : 'items'})`;
                                        document.getElementById('subtotal').textContent = `$${data.subtotal.toFixed(2)}`;
                                        document.getElementById('total').textContent = `$${data.subtotal.toFixed(2)}`;
                                        document.querySelector('.order-details-item span span').textContent = `(${data.item_count} ${data.item_count === 1 ? 'item' : 'items'})`;
                                    }
                                });
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            document.querySelectorAll('.moveToWishlistBtn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const itemId = this.closest('#item-in-cart').dataset.itemId;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will move the item to your wishlist",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, move it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('cart.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `move_to_wishlist=true&item_id=${itemId}`
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Moved!',
                                            text: data.message || 'Item moved to wishlist',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                        loadCart(); // Now this function is defined
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message || 'Failed to move item'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'An error occurred'
                                    });
                                });
                        }
                    });
                });
            });



            function setupValidation() {
                const fName = document.getElementById("firstName");
                const lName = document.getElementById("lastName");
                const phoneNumber1 = document.getElementById("phoneNumber1");
                const phoneNumber2 = document.getElementById("phoneNumber2");
                const address = document.getElementById("address");
                const city = document.getElementById("city");
                const form = document.getElementById("shippingForm");

                const nameRegex = /^[A-Za-z\s]{2,}$/;
                const phoneRegex = /^\+?[0-9\s\-]{10,}$/;
                const addressRegex = /^[A-Za-z0-9\s,.\-\/#]{5,}$/;

                function validateInput(input, regex, errorMessage) {
                    const isValid = regex.test(input.value.trim());
                    if (!isValid) {
                        input.classList.add("is-invalid");
                        if (errorMessage) {
                            input.nextElementSibling.textContent = errorMessage;
                        }
                    } else {
                        input.classList.remove("is-invalid");
                    }
                    return isValid;
                }

                // Real-time validation
                fName.addEventListener("blur", () => validateInput(fName, nameRegex, "Please enter a valid name"));
                lName.addEventListener("blur", () => validateInput(lName, nameRegex, "Please enter a valid name"));
                phoneNumber1.addEventListener("blur", () => validateInput(phoneNumber1, phoneRegex, "Please enter a valid phone number"));
                phoneNumber2.addEventListener("blur", () => validateInput(phoneNumber2, phoneRegex, "Please enter a valid phone number"));
                address.addEventListener("blur", () => validateInput(address, addressRegex, "Please enter a valid address"));

                // City is a select element - special handling
                city.addEventListener("change", function() {
                    const isValid = this.value !== "";
                    if (!isValid) {
                        this.classList.add("is-invalid");
                        this.nextElementSibling.textContent = "Please select a city";
                    } else {
                        this.classList.remove("is-invalid");
                    }
                    return isValid;
                });
            }




            function validateAllFields() {
                let isValid = true;
                const form = document.getElementById("shippingForm");

                // Validate required fields
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add("is-invalid");
                        isValid = false;
                    }
                });

                // Special validation for city (select element)
                const city = document.getElementById("city");
                if (city.value === "") {
                    city.classList.add("is-invalid");
                    city.nextElementSibling.textContent = "Please select a city";
                    isValid = false;
                }

                return isValid;
            }

            setupValidation();


            // Order submission function with loading state and immediate popup
            function setupOrderSubmission() {
                const form = document.getElementById("shippingForm");

                form.addEventListener("submit", async function(e) {
                    e.preventDefault();

                    // Validate form first
                    if (!validateAllFields()) return;

                    // Set loading state
                    const submitBtn = document.getElementById("placeOrderBtn");
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Processing Order...
        `;

                    try {
                        // Submit order data
                        const formData = new FormData(this);
                        formData.append('place_order', 'true');

                        const response = await fetch('cart.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Show immediate success popup
                            Swal.fire({
                                title: 'Order Successful!',
                                html: `
                        <div class="text-center p-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem"></i>
                            <p class="mt-3">Your order #${data.order_id} has been placed</p>
                        </div>
                    `,
                                showConfirmButton: false,
                                timer: 2000,
                                willClose: () => {
                                    submitBtn.innerHTML = originalBtnText;
                                    window.location.reload();
                                }
                            });
                        } else {
                            throw new Error(data.message || 'Order submission failed');
                        }
                    } catch (error) {
                        // Error handling
                        submitBtn.innerHTML = originalBtnText;
                        await Swal.fire({
                            title: 'Error',
                            text: error.message,
                            icon: 'error'
                        });
                    } finally {
                        submitBtn.disabled = false;
                    }
                });
            }

            // Initialize when page loads - THIS IS THE CORRECT WAY TO CALL IT
            document.addEventListener('DOMContentLoaded', function() {
                if (document.getElementById("shippingForm")) {
                    setupOrderSubmission();
                }
            });





        }
    </script>
</body>

</html>