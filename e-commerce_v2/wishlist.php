<?php
session_start();
// التحقق من إذا كان الـ user_id موجود في الجلسة
if (!isset($_SESSION['user_id'])) {
    // لو مفيش تسجيل دخول، توجّه المستخدم إلى صفحة تسجيل الدخول
    header("Location: login.php");
    exit();
}

require_once 'config.php'; // Adjust this path as needed

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'ecommerce_db';
// Handle Clear Wishlist request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_wishlist'])) {
    try {
        $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
}

// Handle Remove Item request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    try {
        $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verify the item belongs to the user
        $stmt = $pdo->prepare("SELECT user_id FROM wishlist WHERE id = :id");
        $stmt->bindParam(':id', $_POST['item_id'], PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result || $result['user_id'] != $_SESSION['user_id']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        // Delete the item
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id = :id");
        $stmt->bindParam(':id', $_POST['item_id'], PDO::PARAM_INT);
        $stmt->execute();

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
}

// Normal page load - fetch wishlist items
$user_id = $_SESSION['user_id'];
$wishlist_items = [];
$item_count = 0;
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'ecommerce_db';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // In the PHP section where you fetch wishlist items
    $query = "SELECT w.id as id, p.id as product_id, p.name, p.price, p.image, p.stock, p.description
FROM wishlist w
JOIN products p ON w.product_id = p.id
WHERE w.user_id = :user_id
ORDER BY w.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $item_count = count($wishlist_items);

} catch (PDOException $e) {
    // Handle database error
    error_log("Database error: " . $e->getMessage());
    $error_message = "Unable to load wishlist. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- !Bootstrap Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- !Css Link -->
    <link rel="stylesheet" href="css/style.css">

    <title>Home Page</title>

    <link rel="stylesheet" href="css/wishlist.css">
</head>

<body class="wishlist">


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
    <section id="wishlist" style="margin-top: var(--nav-height)">
        <!-- === Main Container === -->
        <div class="container-fluid py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <!-- === Page Header === -->
                    <div class="page-header" data-aos="fade-down" data-aos-duration="1000">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h1 class="page-title">My Wishlist</h1>
                                <div class="title-divider"></div>
                                <p class="page-subtitle">Your favorite products</p>
                            </div>
                            <div class="items-counter">
                                <span class="items-count">0</span>
                                <span class="items-text">Items</span>
                            </div>
                        </div>
                    </div>

                    <!-- === Search Bar === -->
                    <div class="search-container mb-4" data-aos="fade-up" data-aos-duration="800">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" id="searchInput"
                                placeholder="Search your wishlist...">
                        </div>
                    </div>

                    <!-- === Wishlist Content === -->
                    <div class="wishlist-content">
                        <!-- === Loading Spinner === -->
                        <div class="loading-spinner" id="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <!-- === Empty State === -->
                        <div class="empty-state text-center py-5" id="empty-state" data-aos="fade-up"
                            data-aos-duration="1000">
                            <div class="empty-icon mb-4">
                                <i class="fas fa-heart-broken"></i>
                            </div>
                            <div class="empty-content">
                                <h3 class="mb-3">Your wishlist is empty</h3>
                                <p class="text-muted mb-4">Start browsing and add products you love! ❤️</p>
                                <div class="empty-actions">
                                    <a href="products.php" class="btn btn-primary btn-lg start-shopping-btn">
                                        <i class="fas fa-shopping-bag me-2"></i>
                                        Start Shopping
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- === Products Grid === -->
                        <div class="products-grid" id="products-grid">
                            <!-- Product cards will be dynamically added here -->
                        </div>

                        <!-- === Clear Wishlist Button === -->
                        <div class="clear-wishlist-container text-center mt-5" data-aos="fade-up"
                            data-aos-duration="1000">
                            <button class="btn btn-clear-wishlist" data-bs-toggle="modal"
                                data-bs-target="#clearWishlistModal">
                                <i class="fas fa-trash-alt me-2"></i>
                                Clear Wishlist
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === Clear Wishlist Modal === -->
        <div class="modal fade" id="clearWishlistModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <div class="modal-icon mb-3">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4 class="modal-title mb-3">Clear Wishlist</h4>
                        <p class="text-muted mb-4">Are you sure you want to delete all your wishlist items?</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmClear">Delete All</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === Delete Confirmation Modal === -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <div class="modal-icon mb-3">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <h4 class="modal-title mb-3">Remove from Wishlist</h4>
                        <p class="text-muted mb-4">Are you sure you want to remove this product from your wishlist?</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDelete">Remove</button>
                        </div>
                    </div>
                </div>
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

    <!-- Scripts -->
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"
        integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous">
        </script>
    <script src="https://kit.fontawesome.com/9a16a36fbb.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            offset: 100,
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get the wishlist data from PHP
            const wishlistItems = <?php echo json_encode($wishlist_items); ?>;
            const itemCount = <?php echo $item_count; ?>;
            const productsGrid = document.getElementById('products-grid');
            const emptyState = document.getElementById('empty-state');
            const loadingSpinner = document.getElementById('loading-spinner');
            const itemsCountElement = document.querySelector('.items-count');

            // Update item count
            itemsCountElement.textContent = itemCount;

            // Show/hide empty state based on item count
            if (itemCount > 0) {
                emptyState.style.display = 'none';
                renderWishlistItems(wishlistItems);
            } else {
                emptyState.style.display = 'block';
                productsGrid.innerHTML = '';
            }

            // Function to render wishlist items
            function renderWishlistItems(items) {
                productsGrid.innerHTML = '';

                items.forEach(item => {
                    // Inside the renderWishlistItems function in the JavaScript section
                    const productCard = document.createElement('div');
                    productCard.className = 'product-card';
                    productCard.setAttribute('data-id', item.id);
                    productCard.setAttribute('data-product-id', item.product_id);
                    productCard.innerHTML = `
    <div class="product-image">
        <img src="${item.image}" alt="${item.name}">
    </div>
    <div class="product-details">
        <h3 class="product-title fw-normal overflow-hidden" style=" font-family: 'Mulish', sans-serif;">${item.name}</h3>
        <div class="product-price my-3 fw-medium">
            <span class="current-price">$${item.price}</span>
        </div>
        <div class="product-actions">
            <form action="buy-product.php" method="POST">
                <input type="hidden" name="product_id" value="${item.product_id}">
                <input type="hidden" name="product_name" value="${item.name}">
                <input type="hidden" name="product_price" value="${item.price}">
                <input type="hidden" name="product_image" value="${item.image}">
                <input type="hidden" name="product_description" value="${item.description}">
                <button type="submit" class="btn btn-buy">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Buy Product
                </button>
            </form>
            <button class="btn-remove" data-id="${item.id}">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
`;
                    productsGrid.appendChild(productCard);
                });
            }

            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase();
                const filteredItems = wishlistItems.filter(item =>
                    item.name.toLowerCase().includes(searchTerm)
                );
                renderWishlistItems(filteredItems);
            });

            // Clear wishlist confirmation
            document.getElementById('confirmClear').addEventListener('click', function () {
                loadingSpinner.style.display = 'block';

                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'clear_wishlist=true'
                })
                    .then(response => response.json())
                    .then(data => {
                        loadingSpinner.style.display = 'none';
                        if (data.success) {
                            // Show success message and refresh the page
                            showToast('Wishlist cleared successfully');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast(data.message || 'Error clearing wishlist', 'error');
                        }
                    })
                    .catch(error => {
                        loadingSpinner.style.display = 'none';
                        showToast('Network error', 'error');
                    });
            });

            // Remove item functionality (using event delegation)
            document.addEventListener('click', function (e) {
                if (e.target.closest('.btn-remove')) {
                    const itemId = e.target.closest('.btn-remove').getAttribute('data-id');
                    if (confirm('Are you sure you want to remove this item from your wishlist?')) {
                        loadingSpinner.style.display = 'block';

                        fetch(window.location.href, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `remove_item=true&item_id=${itemId}`
                        })
                            .then(response => response.json())
                            .then(data => {
                                loadingSpinner.style.display = 'none';
                                if (data.success) {
                                    // Show success message and refresh the page
                                    showToast('Item removed from wishlist');
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    showToast(data.message || 'Error removing item', 'error');
                                }
                            })
                            .catch(error => {
                                loadingSpinner.style.display = 'none';
                                showToast('Network error', 'error');
                            });
                    }
                }
            });

            // Helper function to show toast notifications
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`;
                toast.innerHTML = `
                    <div class="toast-content">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        <span>${message}</span>
                    </div>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('show');
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                }, 100);
            }
        });
    </script>
</body>

</html>