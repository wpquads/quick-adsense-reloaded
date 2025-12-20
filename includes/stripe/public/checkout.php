<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once '../vendor/autoload.php';
require_once '../secrets.php';

$quads_stripe = new \Stripe\StripeClient($quads_stripe_secret_key);
header('Content-Type: application/json');

$QUADS_YOUR_DOMAIN = 'http://localhost:4242';

$quads_checkout_session = $quads_stripe->checkout->sessions->create([
  'ui_mode' => 'embedded',
  'line_items' => [[
    # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
    'price' => '{{PRICE_ID}}',
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'return_url' => $QUADS_YOUR_DOMAIN . '/return.html?session_id={CHECKOUT_SESSION_ID}',
]);

  echo json_encode(array('clientSecret' => $quads_checkout_session->client_secret));