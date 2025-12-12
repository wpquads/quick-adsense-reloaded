<?php

require_once '../vendor/autoload.php';
require_once '../secrets.php';

$quads_stripe = new \Stripe\StripeClient($quads_stripe_secret_key);
header('Content-Type: application/json');

try {
  // retrieve JSON from POST body
  $quads_jsonStr = file_get_contents('php://input');
  $quads_jsonObj = json_decode($quads_jsonStr);

  $quads_session = $quads_stripe->checkout->sessions->retrieve($quads_jsonObj->session_id);

  echo json_encode(['status' => $quads_session->status, 'customer_email' => $quads_session->customer_details->email]);
  http_response_code(200);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}