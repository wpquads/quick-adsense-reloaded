<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once '../vendor/autoload.php';
require_once '../secrets.php';

$quads_stripe = new \Stripe\StripeClient($quads_stripe_secret_key);
header('Content-Type: application/json');

try {
  // retrieve JSON from POST body
  $quads_jsonStr = file_get_contents('php://input');
  $quads_jsonObj = json_decode($quads_jsonStr);

  if ( is_object( $quads_jsonObj ) && ! empty( $quads_jsonObj->session_id ) ) {

    $quads_session_id = sanitize_text_field( wp_unslash( $quads_jsonObj->session_id ) );

    $quads_session = $quads_stripe->checkout->sessions->retrieve($quads_session_id);

    if ( is_object( $quads_session ) &&  ! empty( $quads_session->status ) && $quads_session->customer_details->email ) {
      echo json_encode(['status' => $quads_session->status, 'customer_email' => $quads_session->customer_details->email]);
      http_response_code(200);
    }
  }
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}