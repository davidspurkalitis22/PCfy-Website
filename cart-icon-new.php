<?php // Cart icon HTML ?>
<li class="cart-icon">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-count">0</span>
    <div class="cart-dropdown">
        <div class="cart-items">
            <!-- Cart items will be added here dynamically -->
            <div class="empty-cart">Your cart is empty</div>
        </div>
        <div class="cart-subtotal">
            <span>Subtotal:</span>
            <span class="subtotal-amount">€0.00</span>
        </div>
        <div class="cart-actions">
            <a href="cart.php" class="btn btn-primary" style="width: 100%; text-align: center;">View Cart</a>
            <a href="checkout.php" class="btn btn-secondary" style="width: 100%; text-align: center;">Checkout</a>
        </div>
    </div>
</li>
