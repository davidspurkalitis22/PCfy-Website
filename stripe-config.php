<?php
// Stripe API configuration 
// Using TEST keys since live account is not fully activated yet
define('STRIPE_API_KEY', 'sk_test_51RGO9EIh1kNprMB5RPOzC0Lq95lL3NnNNwUoRjicbiqCE87ZDJZaDLoP1R4eYIJ04ZQgJrVbGLV78D11EGeA3CFd00gblabO45'); // Test Secret key
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51RGO9EIh1kNprMB5aW6QMkQ2wReSFicQgnx9MaFXETr33LqSG05Kh8w3jO1JBEvQ6EtQOr9bCD2rX2suVonECmhw00CRonV2Bn'); // Test Publishable key
define('STRIPE_WEBHOOK_SECRET', 'YOUR_STRIPE_WEBHOOK_SECRET'); // Optional: For webhook signature verification

// Currency configuration
define('STRIPE_CURRENCY', 'EUR');

// Set to true for test mode
define('STRIPE_TEST_MODE', true);

// Production URLs - set to actual hosting domain
define('PAYMENT_SUCCESS_URL', 'https://g00420243.webhosting.atu.ie/payment-success.php');
define('PAYMENT_CANCEL_URL', 'https://g00420243.webhosting.atu.ie/payment-cancel.php');

// Live keys (currently unavailable for charges)
// define('STRIPE_API_KEY', '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');
// define('STRIPE_PUBLISHABLE_KEY', 'pk_live_51RGO95I0DvZnRDPxKcsERowkgN1pRIbLCfUY6CfWt83dOZpLWngrdal4Asv3sr0waGLS2ef9BvF42lnCkALRYPEG00NF9R6CXs');

// URLs for LIVE mode (for future use)
// define('PAYMENT_SUCCESS_URL', 'https://g00420243.webhosting.atu.ie/payment-success.php');
// define('PAYMENT_CANCEL_URL', 'https://g00420243.webhosting.atu.ie/payment-cancel.php');
?> 