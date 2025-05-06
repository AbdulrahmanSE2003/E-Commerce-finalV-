<?php
// بدء جلسة إذا لم تكن قد بدأت بالفعل
session_start();

// التحقق مما إذا كان المستخدم قد سجل الدخول
if (!isset($_SESSION['user_id'])) {
    // إرجاع استجابة JSON تشير إلى أن تسجيل الدخول مطلوب
    echo json_encode(['success' => false, 'error' => 'login_required', 'message' => 'Login required to remove from wishlist']);
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

// حذف المنتج من المفضلة
$delete_query = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("ii", $_SESSION['user_id'], $product_id);

// تنفيذ الاستعلام وإرجاع الاستجابة المناسبة
if ($stmt->execute()) {
    // التحقق من عدد الصفوف المتأثرة
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Product removed from wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found in wishlist']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove product from wishlist: ' . $conn->error]);
}

// إغلاق الاتصال بقاعدة البيانات
$stmt->close();
$conn->close();
?> 