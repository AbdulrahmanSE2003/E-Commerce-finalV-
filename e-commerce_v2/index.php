<?php
include 'config.php';
session_start();

// Handle adding to cart
if (isset($_GET['add'])) {
    $product_id = (int) $_GET['add'];
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id) {
        // Check if product already in cart
        $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'");

        if (mysqli_num_rows($check_cart) > 0) {
            // // Update quantity
            // mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id = '$user_id' AND product_id = '$product_id'");
            $_SESSION['message'] = "Product already in cart";
        } else {
            // Add to cart
            mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', 1)");
            $_SESSION['message'] = "Product added to cart";
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['message'] = "Please login to add to cart";
        header("Location: login.php");
        exit();
    }
}

// Fetch the last 4 products from the database
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id ASC LIMIT 4");
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
    <style>
        div.alert {
            position: absolute;
            z-index: 1021;
            right: 20px;
            top: 1.5rem;
        }
    </style>
</head>

<body class="homepage">
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


    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible absolute z-index-4 fade show text-center mb-0" role="alert">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- ?Main section -->
    <section id="home-main-section" class="homepage-mainScection position-relative">
        <div class="mx-sm-5 mx-2 h-100 d-flex justify-content-end align-items-center me-xxl-5">
            <div class="homepage-introText text-white me-mxl-5">
                <h5 class="m-0">Pro.Beyond</h5>
                <h2>Iphone 16 <span>Pro</span></h2>
                <p>
                    Created to change everything for the better, For
                    everyone
                </p>
                <a href="#four-cols">
                    <button class="my-btn">Shop Now</button>
                </a>
            </div>
        </div>
    </section>

    <!-- ?Multi card section -->
    <section id="multi-cards">
        <div class="row h-100">
            <div class="col-lg-6 d-flex flex-column">
                <!-- INFO: PS5 Section -->
                <div class="row" style="background-color: #fff">
                    <div class="col p-5 d-flex gap-md-0 gap-4 justify-content-between align-items-center">
                        <div
                            class="home-multicards-ps5-pic d-md-flex d-none justify-content-center align-items-center col-4 position-relative h-100">
                            <img src="images/ps5.png" class="w-75" alt="" />
                        </div>
                        <div class="home-multicards-ps5 col-md-8">
                            <h3>Playstation 5</h3>
                            <p>
                                Lightning-fast SSD. 4K 120fps gaming. Haptic
                                feedback.Stunning DualSense controller.
                                Backward compatible. Next-gen immersion
                                redefined.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- INFO: Airpods-->
                <div class="row">
                    <div class="col p-5 d-flex gap-md-0 gap-4 justify-content-between align-items-center"
                        style="background-color: #343434">
                        <div
                            class="home-multicards-airpods-pic d-md-flex d-none justify-content-center align-items-center position-relative col-4 h-100">
                            <img src="images/airpods.png" class="w-100" alt="" />
                        </div>
                        <div class="home-multicards-airpods col-md-8">
                            <h3 class="text-white">
                                Apple <br />
                                Airpods <span class="fw-medium">Max</span>
                            </h3>
                            <p>
                                AirPods Pro. Active Noise Cancellation.
                                Transparency mode. Adaptive EQ. Spatial
                                audio. All in a light, in-ear design.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INFO: Macbook Section -->
            <div class="col-lg-6 d-flex align-items-center flex-lg-row flex-column-reverse justify-content-lg-between justify-content-center"
                style="background-color: #e5e5e5">
                <div
                    class="home-multicards-mac ms-3 col-lg-6 d-flex flex-column align-items-md-start align-items-center">
                    <h3 class="text-start my-3" style="color: var(--primary-dark-color)">
                        Macbook <span style="font-weight: 600">Air</span>
                    </h3>
                    <p class="my-3">
                        MacBook Air. Thin. Light. Powerful. M2 chip. All-day
                        battery. Brilliant Retina display.
                    </p>
                    <a href="products.php">
                        <button class="my-btn my-3">Shop Now</button>
                    </a>
                </div>
                <div
                    class="home-multicards-mac-pic d-flex justify-content-lg-tart justify-content-center align-items-center position-relative col-lg-6 h-100 w-100">
                    <img src="images/macbook.png" alt="" />
                </div>
            </div>
        </div>
    </section>

    <!-- ?Our values -->
    <section id="our-values" class="p-5 px-3">
        <div class="container">
            <h4 class="homepage-sectionTitle">Our values:</h4>
            <div class="row gap-5 justify-content-around mt-5 px-sm-0" style="padding: 0 5rem">
                <div
                    class="benifit d-flex flex-column justify-content-center align-items-center col-xl-2 col-md-4 col-sm-12">
                    <lord-icon src="https://cdn.lordicon.com/byupthur.json" trigger="hover"
                        colors="primary:#121331,secondary:#000000" style="width: 100px; height: 100px">
                    </lord-icon>
                    <h5 class="mt-3 mb-0">Get It Lightning Fast</h5>
                </div>
                <div
                    class="benifit d-flex flex-column justify-content-center align-items-center col-xl-2 col-md-4 col-sm-12">
                    <lord-icon src="https://cdn.lordicon.com/fqbvgezn.json" trigger="hover"
                        colors="primary:#000000,secondary:#000000" style="width: 100px; height: 100px">
                    </lord-icon>
                    <h5 class="mt-3 mb-0">Expert Help, Anytime</h5>
                </div>
                <div
                    class="benifit d-flex flex-column justify-content-center align-items-center col-xl-2 col-md-4 col-sm-12">
                    <lord-icon src="https://cdn.lordicon.com/bsdkzyjd.json" trigger="hover"
                        colors="primary:#000000,secondary:#000000" style="width: 100px; height: 100px">
                    </lord-icon>
                    <h5 class="mt-3 mb-0">Shop Now, Pay Your Way</h5>
                </div>
                <div
                    class="benifit d-flex flex-column justify-content-center align-items-center col-xl-2 col-md-4 col-sm-12">
                    <lord-icon src="https://cdn.lordicon.com/jxhgzthg.json" trigger="hover"
                        colors="primary:#000000,secondary:#000000" style="width: 100px; height: 100px">
                    </lord-icon>
                    <h5 class="mt-3 mb-0">Free returns within 14 days</h5>
                </div>
            </div>
        </div>
    </section>

    <!-- ?Best seller -->


    <!-- ?Best seller -->
    <section id="best-seller" class="p-5 px-3">
        <div class="container">
            <h4 class="homepage-sectionTitle">Best Seller:</h4>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-5">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="col px-sm-3 px-5">
                        <div class="card product-card p-0">
                            <img src="<?php echo $row['image']; ?>" class="card-img-top"
                                alt="<?php echo $row['name']; ?>" />
                            <div class="card-body product-info d-flex flex-column justify-content-space-between p-2">
                                <h5 class="card-title fs-3">
                                    <a href="products.php" class="btn stretched-link text-left fw-normal ">
                                        <?php echo $row['name']; ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted align-self-end px-2">
                                    <?php echo $row['description']; ?>
                                </p>
                            </div>
                            <div class="price-cart p-3 pt-1 pb-4 d-flex flex-column justify-content-center">
                                <p class="card-text ps-2 fw-medium fs-3 text-start">
                                    $<?php echo $row['price']; ?>
                                </p>
                                <a href="?add=<?php echo $row['id']; ?>" class="btn btn-primary fs-5">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    <span>Add to Cart</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- ?4 Cols -->
    <section id="four-cols">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center gap-0">
                <div class="col-xl-3 col-md-6 col bg-white homepage-4cols-card">
                    <div class="homepage-4cols-card-pic w-100">
                        <img src="images/watch.png" alt="" />
                    </div>
                    <div
                        class="homepage-4cols-card-text mt-5 d-flex flex-column align-items-xl-start align-items-center">
                        <h5>
                            Popular <span class="fw-bold">Products</span>
                        </h5>
                        <p>
                            Explore our best-selling items that customers
                            love for their quality and performance.
                        </p>
                        <a href="products.php">
                            <button class="my-btn mb-4 mt-3">
                                Shop Now
                            </button>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col homepage-4cols-card" style="background-color: #f4f4f4">
                    <div class="homepage-4cols-card-pic w-100">
                        <img src="images/ipad.png" alt="" />
                    </div>
                    <div
                        class="homepage-4cols-card-text align-items-xl-start align-items-center mt-5 d-flex flex-column">
                        <h5>Ipad <span class="fw-bold">Pro</span></h5>
                        <p>
                            Apple’s iPad Pro: Brilliant display, M2 chip,
                            and all-day battery life.
                        </p>
                        <a href="products.php">
                            <button class="my-btn mb-4 mt-3">
                                Shop Now
                            </button>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col homepage-4cols-card" style="background-color: #d9d9d9">
                    <div class="homepage-4cols-card-pic w-100">
                        <img src="images/samsung.png" alt="" />
                    </div>
                    <div
                        class="homepage-4cols-card-text align-items-xl-start align-items-center mt-5 d-flex flex-column">
                        <h5>Samsung <span class="fw-bold">Galaxy</span></h5>
                        <p>
                            Unfold productivity with a tablet-sized screen
                            that fits your pocket.
                        </p>
                        <a href="products.php">
                            <button class="my-btn mb-4 mt-3">
                                Shop Now
                            </button>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col homepage-4cols-card text-white" style="background-color: #343434">
                    <div class="homepage-4cols-card-pic w-100">
                        <img src="images/macbook_2.png" alt="" />
                    </div>
                    <div
                        class="homepage-4cols-card-text align-items-xl-start align-items-center mt-5 d-flex flex-column">
                        <h5>Macbook <span class="fw-bold">Pro</span></h5>
                        <p>
                            Ultra-thin, lightweight, and powerful for work
                            or creativity.
                        </p>
                        <a href="products.php">
                            <button id="home-special-card" class="my-btn mb-4 mt-3">
                                Shop Now
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- INFO: Sales -->
    <section id="homepage-sales" class="position-relative">
        <div class="container-fluid w-100 h-100">
            <div
                class="row d-flex flex-column justify-content-center align-items-center w-100 h-100 text-center text-white">
                <h3 class="my-3">
                    Big Summer <span class="fw-medium">Sale</span>
                </h3>
                <p class="my-3">
                    Exclusive discounts await! Upgrade your tech for less
                    during our flash sale.
                </p>
                <a href="products.php">
                    <button class="my-btn mt-2">Shop Now</button>
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

    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"
        integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous">
        </script>
    <script src="https://kit.fontawesome.com/9a16a36fbb.js" crossorigin="anonymous"></script>

</body>

</html>