// This is your test secret API key.
const stripe = Stripe("pk_test_51QWtjGD7rdLSMTejjccGOqFyFFLNF1d7Tzw5dvZlbelJxpAmp9e9Pa7gAdfOWSxwRWhdLgoq2mK1bP9oef9UoWeu00BOHyC2Pt");

initialize();

// Create a Checkout Session
async function initialize() {
  const fetchClientSecret = async () => {
    const response = await fetch("/checkout.php", {
      method: "POST",
    });
    const { clientSecret } = await response.json();
    return clientSecret;
  };

  const checkout = await stripe.initEmbeddedCheckout({
    fetchClientSecret,
  });

  // Mount Checkout
  checkout.mount('#checkout');
}