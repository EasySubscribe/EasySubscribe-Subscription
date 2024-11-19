<?php
/*
Plugin Name: My Stripe Plugin
Description: Plugin per integrare Stripe.
Version: 1.0
Author: Antonio Rausa
*/

// Includi Stripe SDK
require_once plugin_dir_path(__FILE__) . 'stripe-php-16.1.1/init.php';

// Ora puoi usare le funzioni di Stripe qui
\Stripe\Stripe::setApiKey('sk_test_51Q5WXyRun27ngB3YxVx3lFEMZ2rIpemmPI0MfN0zcmmtVhRr3RelYJX1Qp2Rrk6gcLDOT4lwoYV9o8vcRNXxit83003bYF7F4a');