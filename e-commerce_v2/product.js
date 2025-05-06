// === Constants ===
const ANIMATION_DURATION = 300;
const SWIPE_THRESHOLD = 50;
const NOTIFICATION_DURATION = 3000;
const WISHLIST_POPUP_DURATION = 2000;

// === DOM Elements ===
const elements = {
    // Product Gallery
    mainImage: document.querySelector(".main-image img"),
    mainImageContainer: document.querySelector(".main-image"),
    thumbnails: document.querySelectorAll(".thumbnail-item"),
    zoomInBtn: document.querySelector(".zoom-in"),
    zoomOutBtn: document.querySelector(".zoom-out"),

    // Technical Specifications
    specsBtn: document.querySelector(".show-specs-btn"),
    specsContent: document.querySelector(".specifications-content"),
    specsCards: document.querySelectorAll(".specs-card"),

    // Product Options
    colorButtons: document.querySelectorAll(".color-btn"),
    selectedColorText: document.querySelector(".selected-color"),
    memoryButtons: document.querySelectorAll(".memory-selection .btn"),

    // Action Buttons
    addToCartBtn: document.querySelector(".action-buttons .btn-primary"),
    wishlistHearts: document.querySelectorAll(".wishlist-heart"),
    wishlistButtons: document.querySelectorAll(".wishlist-btn"),
    wishlistPopup: document.querySelector(".wishlist-popup"),

    // Reviews
    filterButtons: document.querySelectorAll(".dropdown-item[data-rating]"),
    reviewCarousel: document.querySelector(".review-carousel-container"),
    reviewItems: document.querySelectorAll(".review-item"),
    ratingBars: document.querySelectorAll(".rating-bar .progress-bar"),
};

// === State Management ===
const state = {
    currentImageIndex: 0,
    isAnimating: false,
    isDragging: false,
    touchStartX: 0,
    touchEndX: 0,
    currentTranslateX: 0,
    prevTranslateX: 0,
    currentReviewIndex: 0,
    isReviewTransitioning: false,
    imageScale: 1,
    minScale: 1,
    maxScale: 1.5,
    scaleStep: 0.1,
};

// === Product Gallery Module ===
const productGallery = {
    init() {
        this.addSwipeIndicators();
        this.setupTouchEvents();
        this.setupThumbnailEvents();
        this.setupZoomControls();
    },

    addSwipeIndicators() {
        const indicators = `
            <div class="swipe-indicator left">
                <i class="fas fa-chevron-left"></i>
            </div>
            <div class="swipe-indicator right">
                <i class="fas fa-chevron-right"></i>
            </div>
        `;
        elements.mainImageContainer.insertAdjacentHTML("beforeend", indicators);
    },

    setupTouchEvents() {
        elements.mainImageContainer.addEventListener(
            "touchstart",
            this.handleTouchStart
        );
        elements.mainImageContainer.addEventListener(
            "touchmove",
            this.handleTouchMove
        );
        elements.mainImageContainer.addEventListener(
            "touchend",
            this.handleTouchEnd
        );
    },

    handleTouchStart(event) {
        if (state.isAnimating) return;
        state.touchStartX = event.touches[0].clientX;
        state.isDragging = true;
        elements.mainImageContainer.classList.add("swiping");
    },

    handleTouchMove(event) {
        if (!state.isDragging) return;

        state.touchEndX = event.touches[0].clientX;
        const diffX = state.touchEndX - state.touchStartX;
        state.currentTranslateX = state.prevTranslateX + diffX;

        // Limit swipe distance
        if (
            Math.abs(state.currentTranslateX) >
            elements.mainImageContainer.offsetWidth / 3
        ) {
            state.currentTranslateX =
                (elements.mainImageContainer.offsetWidth / 3) *
                (state.currentTranslateX < 0 ? -1 : 1);
        }

        elements.mainImage.style.transform = `translateX(${state.currentTranslateX}px)`;
    },

    handleTouchEnd() {
        if (!state.isDragging) return;
        state.isDragging = false;
        elements.mainImageContainer.classList.remove("swiping");

        const diff = state.touchEndX - state.touchStartX;

        if (Math.abs(diff) > SWIPE_THRESHOLD) {
            if (diff > 0 && state.currentImageIndex > 0) {
                this.changeImage(state.currentImageIndex - 1);
            } else if (
                diff < 0 &&
                state.currentImageIndex < elements.thumbnails.length - 1
            ) {
                this.changeImage(state.currentImageIndex + 1);
            } else {
                this.resetImagePosition();
            }
        } else {
            this.resetImagePosition();
        }

        state.prevTranslateX = 0;
        state.currentTranslateX = 0;
    },

    setupZoomControls() {
        if (!elements.zoomInBtn || !elements.zoomOutBtn) return;

        elements.zoomInBtn.addEventListener("click", this.zoomIn.bind(this));
        elements.zoomOutBtn.addEventListener("click", this.zoomOut.bind(this));
        
        // Update buttons state initially
        this.updateZoomButtonsState();
    },

    zoomIn() {
        if (state.imageScale >= state.maxScale) return;
        state.imageScale += state.scaleStep;
        if (state.imageScale > state.maxScale) state.imageScale = state.maxScale;
        this.applyZoom();
        this.updateZoomButtonsState();
    },

    zoomOut() {
        if (state.imageScale <= state.minScale) return;
        state.imageScale -= state.scaleStep;
        if (state.imageScale < state.minScale) state.imageScale = state.minScale;
        this.applyZoom();
        this.updateZoomButtonsState();
    },

    applyZoom() {
        if (!elements.mainImage) return;
        elements.mainImage.style.transform = `scale(${state.imageScale})`;
    },

    updateZoomButtonsState() {
        elements.zoomInBtn.classList.toggle("disabled", state.imageScale >= state.maxScale);
        elements.zoomOutBtn.classList.toggle("disabled", state.imageScale <= state.minScale);
    },

    resetImagePosition() {
        elements.mainImage.classList.add("swipe-transition");
        elements.mainImage.style.transform = "translateX(0)";
        setTimeout(
            () => elements.mainImage.classList.remove("swipe-transition"),
            ANIMATION_DURATION
        );
    },

    changeImage(newIndex) {
        if (state.isAnimating) return;
        state.isAnimating = true;

        // Reset scale when changing image
        state.imageScale = 1;

        const goingRight = newIndex > state.currentImageIndex;
        const direction = goingRight ? -1 : 1;

        elements.mainImage.classList.add("swipe-transition");
        elements.mainImage.style.transform = `translateX(${direction * 100}%)`;

        setTimeout(() => {
            state.currentImageIndex = newIndex;
            elements.mainImage.src =
                elements.thumbnails[state.currentImageIndex].querySelector(
                    "img"
                ).src;

            elements.thumbnails.forEach((t) => t.classList.remove("active"));
            elements.thumbnails[state.currentImageIndex].classList.add(
                "active"
            );

            elements.mainImage.style.transition = "none";
            elements.mainImage.style.transform = `translateX(${
                -direction * 100
            }%)`;

            elements.mainImage.offsetHeight; // Force reflow

            elements.mainImage.style.transition = "";
            elements.mainImage.style.transform = "translateX(0)";

            setTimeout(() => {
                elements.mainImage.classList.remove("swipe-transition");
                state.isAnimating = false;
            }, ANIMATION_DURATION);
        }, ANIMATION_DURATION);
    },

    setupThumbnailEvents() {
        elements.thumbnails.forEach((thumb, index) => {
            thumb.addEventListener("click", () => {
                if (state.isAnimating || thumb.classList.contains("active"))
                    return;
                this.changeImage(index);
            });
        });
    },
};

// === Technical Specifications Module ===
const technicalSpecs = {
    init() {
        if (!elements.specsBtn || !elements.specsContent) return;

        elements.specsBtn.addEventListener("click", () => {
            this.toggleSpecs();
        });
    },

    toggleSpecs() {
        elements.specsBtn.classList.toggle("active");

        if (!elements.specsContent.classList.contains("show")) {
            elements.specsCards.forEach((card) => {
                card.style.opacity = "0";
                card.style.transform = "translateY(30px)";
            });
        }

        elements.specsContent.classList.toggle("show");

        const buttonText = elements.specsBtn.querySelector("span");
        buttonText.textContent = elements.specsContent.classList.contains(
            "show"
        )
            ? "Hide Technical Specifications"
            : "Show Technical Specifications";

        if (elements.specsContent.classList.contains("show")) {
            elements.specsCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, 100 + index * 100);
            });
        }
    },
};

// === Product Options Module ===
const productOptions = {
    init() {
        this.setupColorSelection();
        this.setupMemorySelection();
    },

    setupColorSelection() {
        elements.colorButtons.forEach((btn) => {
            btn.addEventListener("click", () => {
                elements.colorButtons.forEach((b) =>
                    b.classList.remove("active")
                );
                btn.classList.add("active");
                const color = btn.dataset.color;
                elements.selectedColorText.textContent = `(${
                    color.charAt(0).toUpperCase() + color.slice(1)
                })`;
            });
        });
    },

    setupMemorySelection() {
        elements.memoryButtons.forEach((btn) => {
            btn.addEventListener("click", () => {
                elements.memoryButtons.forEach((b) =>
                    b.classList.remove("active")
                );
                btn.classList.add("active");
            });
        });
    },
};

// === Wishlist Module ===
const wishlist = {
    init() {
        this.setupWishlistHearts();
        this.setupWishlistButtons();
    },

    setupWishlistHearts() {
        elements.wishlistHearts.forEach((heart) => {
            heart.addEventListener("click", () => {
                this.handleWishlistAction(heart, false);
            });
        });
    },

    setupWishlistButtons() {
        elements.wishlistButtons.forEach((button) => {
            button.addEventListener("click", () => {
                this.handleWishlistAction(button, true);
            });
        });
    },

    handleWishlistAction(element, isButton) {
        const isActive = element.classList.toggle("active");

        if (isButton) {
            const icon = element.querySelector("i");
            if (isActive) {
                icon.classList.remove("far");
                icon.classList.add("fas");
                element.setAttribute("data-tooltip", "Remove from Wishlist");
            } else {
                icon.classList.remove("fas");
                icon.classList.add("far");
                element.setAttribute("data-tooltip", "Add to Wishlist");
            }
        }

        this.showPopup(isActive);
    },

    showPopup(isAdded) {
        const message = elements.wishlistPopup.querySelector(".popup-message");
        message.textContent = isAdded
            ? "Added to wishlist"
            : "Removed from wishlist";

        elements.wishlistPopup.classList.add("show");

        setTimeout(() => {
            elements.wishlistPopup.classList.remove("show");
        }, WISHLIST_POPUP_DURATION);
    },
};

// === Reviews Module ===
const reviews = {
    init() {
        this.initializeRatingBars();
        this.setupReviewFilter();
        this.setupReviewCarousel();
    },

    initializeRatingBars() {
        elements.ratingBars.forEach((bar) => {
            const width = bar.style.width;
            bar.style.width = "0";
            setTimeout(() => {
                bar.style.transition = "width 1s ease-out";
                bar.style.width = width;
            }, 100);
        });
    },

    setupReviewFilter() {
        if (!elements.filterButtons.length) return;

        elements.filterButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const selectedRating = button.getAttribute("data-rating");
                this.filterReviews(selectedRating);
            });
        });
    },

    filterReviews(selectedRating) {
        elements.filterButtons.forEach((btn) => btn.classList.remove("active"));
        const activeButton = document.querySelector(
            `.dropdown-item[data-rating="${selectedRating}"]`
        );
        activeButton.classList.add("active");

        const filterButtonText = document.querySelector(".btn-filter span");
        filterButtonText.textContent =
            selectedRating === "all"
                ? "Filter Reviews"
                : `${selectedRating} Stars`;

        let visibleReviews =
            selectedRating === "all"
                ? Array.from(elements.reviewItems)
                : Array.from(elements.reviewItems).filter((review) => {
                      const reviewRating = parseFloat(
                          review.getAttribute("data-stars")
                      );
                      return reviewRating === parseFloat(selectedRating);
                  });

        elements.reviewItems.forEach((review) => {
            review.classList.remove("active-review");
            review.style.display = "none";
        });

        visibleReviews.forEach((review, index) => {
            review.style.display = "";
            if (index === 0) {
                review.classList.add("active-review");
            }
        });

        this.updateNavigationButtons(visibleReviews);
        this.handleNoReviews(visibleReviews, selectedRating);

        state.currentReviewIndex = 0;
        window.visibleReviews = visibleReviews;
    },

    updateNavigationButtons(visibleReviews) {
        const nextBtn = elements.reviewCarousel.querySelector(".next-btn");
        const prevBtn = elements.reviewCarousel.querySelector(".prev-btn");

        if (nextBtn) {
            nextBtn.style.display =
                visibleReviews.length > 1 ? "block" : "none";
        }
        if (prevBtn) {
            prevBtn.style.display = "none";
        }
    },

    handleNoReviews(visibleReviews, selectedRating) {
        let noReviewsMessage = elements.reviewCarousel.querySelector(
            ".no-reviews-message"
        );

        if (visibleReviews.length === 0) {
            if (!noReviewsMessage) {
                noReviewsMessage = document.createElement("div");
                noReviewsMessage.className = "no-reviews-message";
                noReviewsMessage.style.textAlign = "center";
                noReviewsMessage.style.padding = "2rem";
                elements.reviewCarousel.appendChild(noReviewsMessage);
            }
            noReviewsMessage.textContent = `No ${selectedRating} star reviews found.`;
            noReviewsMessage.style.display = "";
        } else if (noReviewsMessage) {
            noReviewsMessage.style.display = "none";
        }
    },

    setupReviewCarousel() {
        if (!elements.reviewCarousel) return;

        const nextBtn = elements.reviewCarousel.querySelector(".next-btn");
        const prevBtn = elements.reviewCarousel.querySelector(".prev-btn");

        if (nextBtn)
            nextBtn.addEventListener("click", () => this.showNextReview());
        if (prevBtn)
            prevBtn.addEventListener("click", () => this.showPrevReview());

        window.visibleReviews = Array.from(elements.reviewItems);
        this.updateCarousel();
    },

    updateCarousel() {
        const visibleReviews =
            window.visibleReviews ||
            Array.from(elements.reviewItems).filter(
                (item) => item.style.display !== "none"
            );
        if (visibleReviews.length === 0) return;

        elements.reviewItems.forEach((item) =>
            item.classList.remove("active-review")
        );

        if (visibleReviews[state.currentReviewIndex]) {
            visibleReviews[state.currentReviewIndex].classList.add(
                "active-review"
            );
        }

        const nextBtn = elements.reviewCarousel.querySelector(".next-btn");
        const prevBtn = elements.reviewCarousel.querySelector(".prev-btn");

        if (prevBtn)
            prevBtn.style.display =
                state.currentReviewIndex === 0 ? "none" : "block";
        if (nextBtn)
            nextBtn.style.display =
                state.currentReviewIndex >= visibleReviews.length - 1
                    ? "none"
                    : "block";
    },

    showNextReview() {
        const visibleReviews =
            window.visibleReviews ||
            Array.from(elements.reviewItems).filter(
                (item) => item.style.display !== "none"
            );
        if (
            state.isReviewTransitioning ||
            state.currentReviewIndex >= visibleReviews.length - 1
        )
            return;

        state.isReviewTransitioning = true;
        state.currentReviewIndex++;
        this.updateCarousel();

        setTimeout(() => {
            state.isReviewTransitioning = false;
        }, ANIMATION_DURATION);
    },

    showPrevReview() {
        if (state.isReviewTransitioning || state.currentReviewIndex <= 0)
            return;

        state.isReviewTransitioning = true;
        state.currentReviewIndex--;
        this.updateCarousel();

        setTimeout(() => {
            state.isReviewTransitioning = false;
        }, ANIMATION_DURATION);
    },
};

// === Cart Module ===
const cart = {
    init() {
        if (!elements.addToCartBtn) return;

        elements.addToCartBtn.addEventListener("click", () => {
            const selectedColor =
                document.querySelector(".color-btn.active").dataset.color;
            const selectedMemory = document.querySelector(
                ".memory-selection .btn.active"
            ).textContent;
            const message = `Added to cart: RTX 4080 - ${selectedColor} - ${selectedMemory}`;
            this.showNotification(message);
        });
    },

    showNotification(message) {
        const notification = document.createElement("div");
        notification.className = "notification";
        notification.textContent = message;

        Object.assign(notification.style, {
            position: "fixed",
            bottom: "20px",
            right: "20px",
            backgroundColor: "var(--primary-color)",
            color: "white",
            padding: "1rem 2rem",
            borderRadius: "var(--border-radius)",
            boxShadow: "0 4px 12px var(--shadow)",
            zIndex: "1000",
            transform: "translateX(100%)",
            opacity: "0",
            transition: "all 0.3s cubic-bezier(0.4, 0, 0.2, 1)",
        });

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = "translateX(0)";
            notification.style.opacity = "1";
        }, 10);

        setTimeout(() => {
            notification.style.transform = "translateX(100%)";
            notification.style.opacity = "0";
            setTimeout(() => notification.remove(), ANIMATION_DURATION);
        }, NOTIFICATION_DURATION);
    },
};

// === FAQ Module ===
const faq = {
    init() {
        this.setupFAQItems();
    },

    setupFAQItems() {
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                
                // Close all other items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                
                // Toggle current item
                item.classList.toggle('active');
            });
        });
    }
};

// === Initialization ===
document.addEventListener("DOMContentLoaded", () => {
    // Initialize all modules
    productGallery.init();
    technicalSpecs.init();
    productOptions.init();
    wishlist.init();
    reviews.init();
    cart.init();
    faq.init();
});

// Return Policy Card Layout Fix
function fixReturnPolicyLayout() {
    const returnPolicySection = document.getElementById('returnPolicySection');
    if (returnPolicySection) {
        const policyContent = returnPolicySection.querySelector('.policy-content');
        const policyCards = returnPolicySection.querySelectorAll('.policy-card');
        
        if (policyContent && policyCards.length > 0) {
            // Force re-layout if needed
            policyContent.style.display = 'flex';
            policyContent.style.justifyContent = 'center';
            
            // Make sure cards have proper sizing
            policyCards.forEach(card => {
                card.style.flexBasis = 'calc(33.333% - 1rem)';
                card.style.margin = '0';
            });
        }
    }
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    setupImageZoom();
    setupThumbnailNavigation();
    
    // Set up FAQ items
    const faqItems = document.querySelectorAll(".faq-item");
    faqItems.forEach(item => {
        const question = item.querySelector(".faq-question");
        question.addEventListener("click", () => {
            item.classList.toggle("active");
        });
    });

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

// If there are any images that might load late, fix layout after load
window.addEventListener('load', fixReturnPolicyLayout);
