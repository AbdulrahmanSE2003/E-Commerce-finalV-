// إدارة أزرار المفضلة في قسم المنتجات المرتبطة
document.addEventListener('DOMContentLoaded', function() {
    // تحديد جميع أزرار المفضلة
    const wishlistButtons = document.querySelectorAll('.wishlist-heart');
    
    // إضافة مستمع أحداث لكل زر
    wishlistButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // الحصول على معرف المنتج
            const productId = this.getAttribute('data-product-id');
            
            // تبديل حالة الزر
            this.classList.toggle('active');
            
            // معالجة الإضافة أو الإزالة من المفضلة
            if (this.classList.contains('active')) {
                // إضافة إلى المفضلة
                addToWishlist(productId, this);
            } else {
                // إزالة من المفضلة
                removeFromWishlist(productId, this);
            }
        });
    });
    
    // دالة لإضافة منتج إلى المفضلة
    function addToWishlist(productId, button) {
        // إرسال طلب AJAX لإضافة المنتج إلى المفضلة
        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // عرض النافذة المنبثقة للإضافة
                showWishlistPopup('Product added to wishlist!');
            } else {
                // إذا كان هناك خطأ، إزالة فئة 'active'
                button.classList.remove('active');
                
                if (data.error === 'login_required') {
                    // إعادة التوجيه إلى صفحة تسجيل الدخول
                    window.location.href = 'login.php';
                } else {
                    showWishlistPopup('Error: ' + data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.classList.remove('active');
            showWishlistPopup('Error adding to wishlist');
        });
    }
    
    // دالة لإزالة منتج من المفضلة
    function removeFromWishlist(productId, button) {
        // إرسال طلب AJAX لإزالة المنتج من المفضلة
        fetch('remove_from_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // عرض النافذة المنبثقة للإزالة
                showWishlistPopup('Product removed from wishlist!');
            } else {
                // إذا كان هناك خطأ، إعادة فئة 'active'
                button.classList.add('active');
                showWishlistPopup('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.classList.add('active');
            showWishlistPopup('Error removing from wishlist');
        });
    }
    
    // دالة لعرض النافذة المنبثقة
    function showWishlistPopup(message) {
        // التحقق مما إذا كانت النافذة المنبثقة موجودة بالفعل
        let popup = document.querySelector('.wishlist-popup');
        
        // إذا لم تكن موجودة، أنشئ واحدة جديدة
        if (!popup) {
            popup = document.createElement('div');
            popup.className = 'wishlist-popup';
            document.body.appendChild(popup);
        }
        
        // تعيين الرسالة
        popup.textContent = message;
        
        // إظهار النافذة المنبثقة
        popup.classList.add('show');
        
        // إخفاء النافذة المنبثقة بعد 3 ثوانٍ
        setTimeout(function() {
            popup.classList.remove('show');
        }, 3000);
    }
}); 