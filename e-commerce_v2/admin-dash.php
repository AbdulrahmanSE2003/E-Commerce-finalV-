<?php
// Database connection (replace with your credentials)
$db = new mysqli('localhost', 'root', '', 'ecommerce_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get statistics
$total_customers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetch_row()[0];
$total_orders = $db->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$total_products = $db->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$total_categories = $db->query("SELECT COUNT(*) FROM categories")->fetch_row()[0];

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        // Add new product
        $stmt = $db->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $_POST['name'], $_POST['price'], $_POST['stock']);
        $stmt->execute();
    } elseif (isset($_POST['update_stock'])) {
        // Update stock - fixed logic
        $product_id = (int) $_POST['product_id'];
        // Get current stock
        $result = $db->query("SELECT stock FROM products WHERE id = $product_id");
        if ($result && $result->num_rows > 0) {
            $current_stock = $result->fetch_row()[0];
            $change = 0;

            if (isset($_POST['increase'])) {
                $change = 1;
            } elseif (isset($_POST['decrease'])) {
                $change = -1;
            }

            $new_stock = $current_stock + ($change);
            // Ensure stock doesn't go negative
            $new_stock = max(0, $new_stock);

            // Update the stock
            $db->query("UPDATE products SET stock = $new_stock WHERE id = $product_id");
            echo '<h1>$new_stock</h1>';
        }
    } elseif (isset($_POST['delete_product'])) {
        // Delete product
        $product_id = (int) $_POST['product_id'];

        // First delete from wishlist
        $db->query("DELETE FROM wishlist WHERE product_id = $product_id");

        // Then delete the product
        $db->query("DELETE FROM products WHERE id = $product_id");

        // Check for errors
        if ($db->error) {
            die("Error deleting product: " . $db->error);
        }
    }

    // Redirect to avoid form resubmission
    header("Location: admin-dash.php");
    exit();
}

// Get products
$products = $db->query("SELECT id, name, price, stock FROM products ORDER BY id")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,200..1000;1,200..1000&family=Tektur:wght@400..900&display=swap');

        .stat-card {
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <main class="col-md-12 px-md-4 py-4">
                <h1 class="h2 mb-4 text-center" style="font-family: Tektur, sans-serif;">Admin Dashboard</h1>

                <!-- Stats Cards Row -->
                <div class="row mb-4">
                    <!-- Total Customers -->
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Customers</h6>
                                        <h2 class="mb-0"><?= $total_customers ?></h2>
                                    </div>
                                    <i class="fas fa-users fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Orders -->
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Orders</h6>
                                        <h2 class="mb-0"><?= $total_orders ?></h2>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Products -->
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Products</h6>
                                        <h2 class="mb-0"><?= $total_products ?></h2>
                                    </div>
                                    <i class="fas fa-boxes fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Categories -->
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Categories</h6>
                                        <h2 class="mb-0"><?= $total_categories ?></h2>
                                    </div>
                                    <i class="fas fa-tags fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Products Section -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Product Inventory</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addProductModal">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= $product['id'] ?></td>
                                            <td><?= htmlspecialchars($product['name']) ?></td>
                                            <td>$<?= number_format($product['price'], 2) ?></td>
                                            <td>
                                                <form method="post" class="d-flex align-items-center">
                                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                    <input type="hidden" name="update_stock" value="1">

                                                    <!-- Decrease button -->
                                                    <button type="submit" name="decrease"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-minus"></i>
                                                    </button>

                                                    <span class="mx-2 text-center"
                                                        style="width: 20px;"><?= $product['stock'] ?></span>

                                                    <!-- Increase button -->
                                                    <button type="submit" name="increase"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <form method="post"
                                                    onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                    <button type="submit" name="delete_product"
                                                        class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <a href="index.php" class="text-center mb-4">
                <button class="btn btn-lg btn-success">Home Page</button>
            </a>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="productPrice" name="price"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Initial Stock</label>
                            <input type="number" class="form-control" id="productStock" name="stock" value="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_product" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>