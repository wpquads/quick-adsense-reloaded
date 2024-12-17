<?php

require_once '../vendor/autoload.php';
require_once '../secrets.php';

$stripe = new \Stripe\StripeClient($stripeSecretKey);
header('Content-Type: application/json');

try {
  // retrieve JSON from POST body
  $jsonStr = file_get_contents('php://input');
  $jsonObj = json_decode($jsonStr);

  $session = $stripe->checkout->sessions->retrieve($jsonObj->session_id);

  echo json_encode(['status' => $session->status, 'customer_email' => $session->customer_details->email]);
  http_response_code(200);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}