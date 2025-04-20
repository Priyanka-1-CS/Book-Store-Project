// Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', function () {

    // Add smooth scrolling to top when clicking on the "Proceed to Checkout" button
    const checkoutBtn = document.querySelector('.cart-total .btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function (event) {
            // Smooth scroll to the top of the page
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Animation on hover for all cart items (box hover effect)
    const cartItems = document.querySelectorAll('.shopping-cart .box');
    cartItems.forEach(function (item) {
        item.addEventListener('mouseenter', function () {
            item.style.transform = 'translateY(-10px)';
            item.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
        });
        item.addEventListener('mouseleave', function () {
            item.style.transform = 'translateY(0)';
            item.style.boxShadow = '0 2px 12px rgba(0, 0, 0, 0.1)';
        });
    });

    // Confirmation pop-ups for delete actions
    const deleteBtns = document.querySelectorAll('.fas.fa-times');
    deleteBtns.forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            if (!confirm('Are you sure you want to remove this item from the cart?')) {
                event.preventDefault();
            }
        });
    });

    // Confirmation for "Delete All" action
    const deleteAllBtn = document.querySelector('.delete-btn');
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', function (event) {
            if (!confirm('Are you sure you want to remove all items from the cart?')) {
                event.preventDefault();
            }
        });
    }

    // Handling the "update" button animation
    const updateButtons = document.querySelectorAll('input[type="submit"][name="update_cart"]');
    updateButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const cartItem = btn.closest('.box');
            const spinner = document.createElement('div');
            spinner.classList.add('spinner');
            spinner.innerHTML = '<div class="loader"></div>';
            cartItem.appendChild(spinner);

            setTimeout(() => {
                spinner.remove(); // Remove spinner after a short delay to simulate update
                alert('Cart updated successfully!');
            }, 1200);
        });
    });

    // Input field focus effect
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(function (input) {
        input.addEventListener('focus', function () {
            input.style.borderColor = '#2c6b38'; // Forest Green when focused
        });

        input.addEventListener('blur', function () {
            input.style.borderColor = '#ccc'; // Default border color
        });
    });

    // Disabled button behavior (when cart is empty or no items selected)
    const disabledBtns = document.querySelectorAll('.delete-btn.disabled, .btn.disabled');
    disabledBtns.forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
        });
    });

    // Smoothly hide the empty cart message after a few seconds of showing
    const emptyCartMessage = document.querySelector('.empty');
    if (emptyCartMessage) {
        setTimeout(function () {
            emptyCartMessage.style.opacity = '0';
            setTimeout(function () {
                emptyCartMessage.style.display = 'none';
            }, 500);
        }, 3000);
    }
});



// For about.php
// Wait for DOM to fully load
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Animate "Why choose us" section on scroll
    const contentSection = document.querySelector('.homea .content');
    
    const revealContent = () => {
        const rect = contentSection.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            contentSection.classList.add('visible');
        }
    };
    
    window.addEventListener('scroll', revealContent);
    revealContent(); // In case it's already in view

    // 2. Shrink header on scroll
    const header = document.querySelector('.header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 60) {
            header.classList.add('shrink');
        } else {
            header.classList.remove('shrink');
        }
    });

    // 3. Smooth scroll to sections
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetEl = document.getElementById(targetId);
            if (targetEl) {
                targetEl.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // 4. Back to Top Button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.textContent = '↑';
    backToTopBtn.id = 'backToTop';
    document.body.appendChild(backToTopBtn);

    window.addEventListener('scroll', () => {
        backToTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
    });

    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

