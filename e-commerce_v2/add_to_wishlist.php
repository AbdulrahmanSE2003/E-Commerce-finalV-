<?php
// بدء جلسة إذا لم تكن قد بدأت بالفعل
session_start();

// التحقق مما إذا كان المستخدم قد سجل الدخول
if (!isset($_SESSION['user_id'])) {
    // إرجاع استجابة JSON تشير إلى أن تسجيل الدخول مطلوب
    echo json_encode(['success' => false, 'error' => 'login_required', 'message' => 'Login required to add to wishlist']);
    exit;
}

// الحصول على معرف المنتج من الطلب
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// التحقق من أن معرف المنتج صالح
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// الاتصال بقاعدة البيانات
require_once 'includes/db_connection.php';

// التحقق مما إذا كان المنتج موجودًا بالفعل في المفضلة
$check_query = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
$stmt->execute();
$result = $stmt->get_result();

// إذا كان المنتج موجودًا بالفعل، أرجع نجاحًا (لتجنب الإدخالات المكررة)
if ($result->num_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Product already in wishlist']);
    exit;
}

// إضافة المنتج إلى المفضلة
$insert_query = "INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($insert_query);
$stmt->bind_param("ii", $_SESSION['user_id'], $product_id);

// تنفيذ الاستعلام وإرجاع الاستجابة المناسبة
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added to wishlist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product to wishlist: ' . $conn->error]);
}

// إغلاق الاتصال بقاعدة البيانات
$stmt->close();
$conn->close();
?> 