<?php
session_start();
require_once 'config.php'; // Create this file with your DB connection details

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Get last 5 orders
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5";
$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
    // echo print_r($orders);
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

</head>

<body>
    <!-- ?Navbar -->
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
                    <a href="login.php"><i class="fas fa-arrow-right-to-bracket p-2 p-lg-3 cart"></i></a>
                    <a href="user-dash.php"><i class="fa-regular fa-user p-2 p-lg-3 user"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <section id="user-dash-mainSection">
        <div class="container-fluid h-100">
            <div class="row d-flex h-100 gap-4 justify-content-md-between justify-content-center">
                <!-- ?Side bar -->
                <aside class="user-dash-aside col-2 userdash-white shadow-sm user-dash-border">
                    <div class="user-dash-tabs">
                        <ul class="list-unstyled">
                            <li>
                                <a href="index.php" class="d-flex gap-2 align-items-center text-black"
                                    style="text-decoration: none">
                                    <div class="userdash-aside-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-house-icon lucide-house">
                                            <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                                            <path
                                                d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                        </svg>
                                    </div>
                                    <div class="userdash-aside-tab">
                                        Home
                                    </div>
                                </a>
                            </li>
                            <li class="d-flex gap-2 align-items-center user-dash-aside-active">
                                <div class="userdash-aside-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-user-round-icon lucide-user-round">
                                        <circle cx="12" cy="8" r="5" />
                                        <path d="M20 21a8 8 0 0 0-16 0" />
                                    </svg>
                                </div>
                                <div class="userdash-aside-tab">
                                    Account
                                </div>
                            </li>
                            <li class="d-flex gap-2 align-items-center">
                                <div class="userdash-aside-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-headset-icon lucide-headset">
                                        <path
                                            d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 1 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z" />
                                        <path d="M21 16v2a4 4 0 0 1-4 4h-5" />
                                    </svg>
                                </div>
                                <div class="userdash-aside-tab">FAQ</div>
                            </li>
                            <li class="d-flex gap-2 align-items-center">
                                <div class="userdash-aside-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-package-icon lucide-package">
                                        <path
                                            d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z" />
                                        <path d="M12 22V12" />
                                        <polyline points="3.29 7 12 12 20.71 7" />
                                        <path d="m7.5 4.27 9 5.15" />
                                    </svg>
                                </div>
                                <div class="userdash-aside-tab">Orders</div>
                            </li>
                            <li class="d-flex gap-2 align-items-center">
                                <a href="logout.php" class="d-flex gap-2 align-items-center text-black">
                                    <div class="userdash-aside-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-log-out-icon lucide-log-out">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            <polyline points="16 17 21 12 16 7" />
                                            <line x1="21" x2="9" y1="12" y2="12" />
                                        </svg>
                                    </div>
                                    <div class="userdash-aside-tab">Log out</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside>
                <div class="row userdash-feed col d-flex flex-column align-items-center pe-md-4 gap-3">
                    <div class="userdash-title userdash-white user-dash-border border-top-0"></div>
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

    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"
        integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous">
        </script>
    <script src="https://kit.fontawesome.com/9a16a36fbb.js" crossorigin="anonymous"></script>
    <script>
        // ---------------------------------------------------------------
        // ?Getting data when the page loads
        window.onload = () => {
            changePageFeed(title, account);
        };

        // ---------------------------------------------------------------
        // ?Active Tabs UserDashboard
        let userDashTabs = document.querySelectorAll(".user-dash-aside ul li");
        userDashTabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                userDashTabs.forEach((tab) => {
                    tab.classList.remove("user-dash-aside-active");
                });
                tab.classList.add("user-dash-aside-active");
                console.log(tab);
                title = tab.querySelector(".userdash-aside-tab").textContent;
                if (title) userDashTitle.textContent = title;
                switch (title.trim()) {
                    case "FAQ":
                        changePageFeed(title, faq);
                        break;
                    case "Account":
                        changePageFeed(title, account);
                        break;
                    case "Orders":
                        changePageFeed(title, table);
                        break;
                    default:
                        changePageFeed(title, account);
                        break;
                }
            });
        });

        // ---------------------------------------------------------------
        // ?Making title active
        let userDashTitle = document.querySelector(".userdash-title");
        let title = document.querySelector(
            ".user-dash-aside-active .userdash-aside-tab"
        ).textContent;
        userDashTitle.textContent = title;

        // ---------------------------------------------------------------
        // ?Changing Page Feed
        let dashboardFeed = document.querySelector(".userdash-feed");

        // INFO: FAQ HTML
        let faq = `<div id="faq-section" class="container my-5">
                            <h2 class="text-center mb-4">
                                Frequently Asked Questions
                            </h2>
                            <div
                                class="faq-item user-dash-border userdash-white p-3 mb-2"
                            >
                                <h5
                                    class="faq-question d-flex justify-content-between align-items-center"
                                >
                                    What is your return policy?
                                    <span class="toggle-icon">+</span>
                                </h5>
                                <p
                                    class="faq-answer mt-3 px-3"
                                    style="display: none"
                                >
                                    Our return policy allows you to return items
                                    within 30 days of purchase. Please ensure
                                    the items are in their original condition.
                                </p>
                            </div>
                            <div
                                class="faq-item user-dash-border userdash-white p-3 mb-2"
                            >
                                <h5
                                    class="faq-question d-flex justify-content-between align-items-center"
                                >
                                    How can I track my order?
                                    <span class="toggle-icon">+</span>
                                </h5>
                                <p
                                    class="faq-answer mt-3 px-3"
                                    style="display: none"
                                >
                                    You can track your order using the tracking
                                    link sent to your email after the order is
                                    shipped.
                                </p>
                            </div>
                            <div
                                class="faq-item user-dash-border userdash-white p-3 mb-2"
                            >
                                <h5
                                    class="faq-question d-flex justify-content-between align-items-center"
                                >
                                    Do you offer international shipping?
                                    <span class="toggle-icon">+</span>
                                </h5>
                                <p
                                    class="faq-answer mt-3 px-3 px-3"
                                    style="display: none"
                                >
                                    Yes, we offer international shipping to
                                    select countries. Shipping fees and delivery
                                    times vary by location.
                                </p>
                            </div>
                            <div
                                class="faq-item user-dash-border userdash-white p-3 mb-2"
                            >
                                <h5
                                    class="faq-question d-flex justify-content-between align-items-center"
                                >
                                    How can I return a product?
                                    <span class="toggle-icon">+</span>
                                </h5>
                                <p
                                    class="faq-answer mt-3 px-3"
                                    style="display: none"
                                >
                                    You can request a product return within 14
                                    days of receiving it through the 'My Orders'
                                    page, provided the product is in its
                                    original condition.
                                </p>
                            </div>
                            <div
                                class="faq-item user-dash-border userdash-white p-3 mb-2"
                            >
                                <h5
                                    class="faq-question d-flex justify-content-between align-items-center"
                                >
                                    What is the estimated delivery time?
                                    <span class="toggle-icon">+</span>
                                </h5>
                                <p
                                    class="faq-answer mt-3 px-3"
                                    style="display: none"
                                >
                                    Delivery time ranges between 2-5 business
                                    days within the city and 5-14 days for other
                                    regions, depending on the delivery location
                                    and shipping company.
                                </p>
                            </div>
</div>`;

        // INFO: Account HTML

        let account = `<div class="userdash-info userdash-white user-dash-border d-flex flex-column align-items-start justify-content-center shadow-sm">
    <div class="userdash-info-pic">
        <img id="userdash-accImage" src="images/person.jpg" class="rounded-4" alt="Profile Image">
    </div>
    <div class="userdash-info-data mt-3">
        <h4 id="userdash-accountName" class="my-2 fw-medium">
            <?php
            $fullName = '';
            if (!empty($user['first_name']) || !empty($user['last_name'])) {
                $fullName = trim($user['first_name'] . ' ' . $user['last_name']);
            }
            echo htmlspecialchars(!empty($fullName) ? $fullName : $user['username']);
            ?>
        </h4>
        <p id="creation-date" class="muted fw-normal opacity-75">
            Account Created: <span class="fw-semibold text-black">
                <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
            </span>
        </p>
    </div>
</div>
<div class="userdash-info-email userdash-white user-dash-border">
    <h5 class="fw-semibold fs-4 my-3">General Information</h5>
    <div class="userdash-info-out d-flex align-items-center justify-content-between flex-wrap">
        <div class="col-lg-6 col d-flex flex-column gap-2">
            <label class="fw-medium" for="userdash-username">Username</label>
            <input type="text" id="userdash-username" disabled readonly 
                value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>
        <div class="col-lg-6 col d-flex flex-column gap-2">
            <label class="fw-medium" for="phone">Phone</label>
            <input type="text" id="phone" disabled readonly 
                value="<?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not provided'; ?>">
        </div>
        <div class="col-lg-6 col d-flex flex-column gap-2 align-self-start">
            <label class="fw-medium" for="email">Email</label>
            <input type="text" id="email" disabled readonly 
                value="<?php echo htmlspecialchars($user['email']); ?>">
        </div> 
        <div class="col-lg-6 col d-flex flex-column gap-2">
            <label class="fw-medium" for="full-name">Full Name</label>
            <input type="text" id="full-name" disabled readonly 
            value="<?php
            echo !empty($fullName) ? htmlspecialchars($fullName) : 'Not provided';
            ?>">
        </div>
    </div>
</div>`;

        // INFO: Table HTML
        let table = `
<div id="userdash-orders" class="container my-5">
    <h2 class="text-center mb-4">Latest Orders</h2>
</div>
<div class="userdash-orderTables">
    <table class="table table-hover table-bordered table-group-divider table-responsive userdash-white">
        <thead>
            <th scope="col">#Order ID</th>
            <th scope="col">Title</th>
            <th scope="col">Status</th>
            <th scope="col">Price</th>
            <th scope="col">Date</th>
        </thead>
        <tbody id="userdash-table">
            <?php foreach ($orders as $order): ?>
            <tr>
                <th scope="row"><?php echo $order['id']; ?></th>
                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                <td>
                    <span class="badge text-dark <?php
                    echo $order['status'] === 'Delivered' ? 'bg-success' :
                        ($order['status'] === 'Shipped' ? 'bg-warning' : ($order['status'] === 'Processing' ? 'bg-info' : 'bg-danger'));
                    ?>">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                </td>
                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
            <tr>
                <td colspan="5" class="text-center">No orders found</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>`;

        // ---------------------------------------------------------------
        // INFO:FAQ Section with Event Delegation
        document.addEventListener("click", (ev) => {
            const question = ev.target.closest(".faq-question");
            if (question) {
                const answer = question.nextElementSibling;
                const icon = question.querySelector(".toggle-icon");
                if (answer.style.display === "none" || answer.style.display === "") {
                    answer.style.display = "block";
                    icon.textContent = "-";
                } else {
                    answer.style.display = "none";
                    icon.textContent = "+";
                }
            }
        });

        // NOTE:Functions

        // INFO:Changing feed dynamically
        function changePageFeed(title, ele) {
            dashboardFeed.innerHTML =
                `<div class="userdash-title userdash-white user-dash-border border-top-0">
                ${title}
            </div>` + ele;
        }

    </script>
</body>

</html>