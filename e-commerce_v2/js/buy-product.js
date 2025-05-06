// Return Policy Card Layout Fix
function fixReturnPolicyLayout() {
    const returnPolicySection = document.getElementById('returnPolicySection');
    if (returnPolicySection) {
        const policyContent = returnPolicySection.querySelector('.policy-content');
        const policyCards = returnPolicySection.querySelectorAll('.policy-card');
        
        if (policyContent && policyCards.length > 0) {
            // Force re-layout if needed
            policyContent.style.display = 'flex';
            policyContent.style.flexWrap = 'wrap';
            policyContent.style.justifyContent = 'center';
            
            // Make sure cards have proper sizing
            policyCards.forEach(card => {
                card.style.flexBasis = 'calc(33.333% - 1rem)';
                card.style.margin = '0';
                card.style.minHeight = '150px'; // Ensure minimum height
            });
            
            // Match heights of all cards
            let maxHeight = 0;
            policyCards.forEach(card => {
                const height = card.offsetHeight;
                if (height > maxHeight) {
                    maxHeight = height;
                }
            });
            
            if (maxHeight > 0) {
                policyCards.forEach(card => {
                    card.style.height = `${maxHeight}px`;
                });
            }
        }
    }
}

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

    // Fix Return Policy Layout
    fixReturnPolicyLayout();
    
    // Also fix layout after the Return Policy section is shown
    const toggleReturnPolicy = document.getElementById('toggleReturnPolicy');
    if (toggleReturnPolicy) {
        toggleReturnPolicy.addEventListener('click', () => {
            // Give it a small delay to ensure the section is visible first
            setTimeout(fixReturnPolicyLayout, 100);
        });
    }
});

// Fix layout on window resize
window.addEventListener('resize', fixReturnPolicyLayout);

// If there are any images that might load late, fix layout after load
window.addEventListener('load', fixReturnPolicyLayout); 