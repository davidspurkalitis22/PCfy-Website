<?php // Cart icon HTML ?>
<li id="header-cart-icon" class="cart-icon nav-cart-icon" style="display: inline-block !important; position: relative; margin-left: 20px; cursor: pointer;">
    <i class="fas fa-shopping-cart" style="font-size: 24px; color: #fff;"></i>
    <span class="cart-count" style="position: absolute; top: -8px; right: -8px; background-color: #e74c3c; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">0</span>
    <div class="cart-dropdown" style="position: absolute; right: 0; top: 100%; width: 320px; background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.2); border-radius: 5px; padding: 15px; z-index: 1000; margin-top: 5px;">
        <div class="cart-items" style="max-height: 320px; overflow-y: auto;">
            <div class="empty-cart" style="text-align: center; padding: 15px 0; color: #6c757d;">Your cart is empty</div>
        </div>
        <div class="cart-subtotal" style="display: flex; justify-content: space-between; padding: 15px 0; font-weight: 600; border-top: 1px solid #eee;">
            <span>Subtotal:</span>
            <span class="subtotal-amount">€0.00</span>
        </div>
        <div class="cart-actions" style="display: flex; flex-direction: column; gap: 10px;">
            <a href="cart.php" class="btn btn-primary" style="width: 100%; text-align: center; margin-bottom: 5px; display: block; padding: 8px 15px; background: #00CCF5; color: #fff; border-radius: 4px; text-decoration: none;">View Cart</a>
            <a href="checkout.php" class="btn btn-secondary" style="width: 100%; text-align: center; display: block; padding: 8px 15px; background: transparent; color: #333; border: 1px solid #ddd; border-radius: 4px; text-decoration: none;">Checkout</a>
        </div>
    </div>
</li>