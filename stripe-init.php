<?php
/**
 * Simple Stripe API Wrapper
 * 
 * A lightweight implementation of the essential Stripe API functionality
 * for checkout sessions without requiring the full Stripe PHP library.
 */

// Define global Stripe API functions
if (!function_exists('stripe_set_api_key')) {
    
    /**
     * Global API key for Stripe API calls
     */
    $GLOBALS['stripe_api_key'] = '';
    
    /**
     * Set the API key for Stripe API calls
     * 
     * @param string $apiKey The Stripe API key
     */
    function stripe_set_api_key($apiKey) {
        $GLOBALS['stripe_api_key'] = $apiKey;
    }
    
    /**
     * Make an API request to Stripe
     * 
     * @param string $method The HTTP method (get, post)
     * @param string $endpoint The API endpoint
     * @param array $params The request parameters
     * @return object The response object
     */
    function stripe_request($method, $endpoint, $params = null) {
        $curl = curl_init();
        
        $url = 'https://api.stripe.com/v1/' . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $GLOBALS['stripe_api_key'],
            'Stripe-Version: 2022-11-15',
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 80,
            CURLOPT_SSL_VERIFYPEER => true
        ];
        
        if ($method === 'post') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        }
        
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        
        if ($error) {
            throw new Exception("Stripe API Error: " . $error);
        }
        
        $result = json_decode($response);
        
        if (isset($result->error)) {
            throw new Exception("Stripe API Error: " . $result->error->message);
        }
        
        return $result;
    }
    
    /**
     * Create a checkout session
     * 
     * @param array $params The session parameters
     * @return object The session object
     */
    function stripe_checkout_session_create($params) {
        return stripe_request('post', 'checkout/sessions', $params);
    }
    
    /**
     * Retrieve a checkout session
     * 
     * @param string $id The session ID
     * @return object The session object
     */
    function stripe_checkout_session_retrieve($id) {
        return stripe_request('get', "checkout/sessions/{$id}");
    }
    
    /**
     * Class wrapper for Stripe interactions
     */
    class StripeAPI {
        /**
         * Set the API key
         * 
         * @param string $apiKey The Stripe API key
         */
        public static function setApiKey($apiKey) {
            stripe_set_api_key($apiKey);
        }
    }
    
    /**
     * Stripe client for API access
     */
    class StripeClient {
        /**
         * Constructor
         * 
         * @param string $apiKey The Stripe API key
         */
        public function __construct($apiKey) {
            StripeAPI::setApiKey($apiKey);
        }
        
        /**
         * Access checkout sessions
         */
        public $checkout;
        
        /**
         * Magic method for accessing properties
         */
        public function __get($name) {
            if ($name === 'checkout') {
                $this->checkout = new StdClass();
                $this->checkout->sessions = new StripeCheckoutSessions();
                return $this->checkout;
            }
            return null;
        }
    }
    
    /**
     * Stripe checkout sessions access
     */
    class StripeCheckoutSessions {
        /**
         * Retrieve a session
         * 
         * @param string $id The session ID
         * @return object The session object
         */
        public function retrieve($id) {
            return stripe_checkout_session_retrieve($id);
        }
    }
    
    /**
     * Add compatibility with Stripe namespace format
     */
    class Stripe {
        public static function setApiKey($apiKey) {
            stripe_set_api_key($apiKey);
        }
    }
    
    class Stripe_Checkout_Session {
        public static function create($params) {
            return stripe_checkout_session_create($params);
        }
    }
}

// Map Stripe namespace classes for compatibility with existing code
if (!class_exists('\\Stripe\\Stripe')) {
    class_alias('Stripe', 'Stripe\\Stripe');
    class_alias('Stripe_Checkout_Session', 'Stripe\\Checkout\\Session');
}
?> 