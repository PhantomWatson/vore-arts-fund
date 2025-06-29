const stripe = window?.stripe;
const amount = window?.stripeAmount;
const payerName = window?.payerName;
const projectId = window?.projectId;
const transactionType = window?.transactionType;
const TRANSACTION_TYPE_DONATION = 1;
const TRANSACTION_TYPE_LOAN_REPAYMENT = 2;

let elements;

initialize(amount, payerName);
checkStatus();

const paymentForm = document.getElementById('payment-form');
paymentForm.addEventListener('submit', handleSubmit);

// Fetches a payment intent and captures the client secret
async function initialize(amount, payerName) {
  // Check to make sure that Stripe has loaded
  if (stripe === undefined) {
    showMessage(
      'There was an error loading the payment information form. Please go back and try again.',
      'alert alert-danger'
    );
    console.error('stripe is undefined');
    setPageLoading(false);
    return;
  }

  // Validate arguments
  if (amount === undefined || amount <= 0) {
    showMessage(
      'The amount for this transaction is missing or zero.',
      'alert alert-danger'
    );
    console.error('Invalid amount:', amount);
    setPageLoading(false);
    return;
  }
  if (
    transactionType === undefined
    || ![TRANSACTION_TYPE_DONATION, TRANSACTION_TYPE_LOAN_REPAYMENT].includes(transactionType)
  ) {
    showMessage(
      'The transaction type for this transaction is invalid.',
      'alert alert-danger'
    );
    console.error('Invalid transaction type:', transactionType);
    setPageLoading(false);
    return;
  }

  const { result } = await fetch('/api/stripe/create-payment-intent', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      amount,
      payerName,
      transactionType,
      projectId,
    }),
  }).then((r) => r.json());

  // https://stripe.com/docs/elements/appearance-api
  const appearance = {
    theme: 'stripe',

    variables: {
      colorPrimary: '#0570de',
      colorBackground: '#ffffff',
      colorText: '#30313d',
      //colorSuccess: '',
      //colorWarning: '',
      colorDanger: '#df1b41',
      fontFamily: 'Ideal Sans, system-ui, sans-serif',
      spacingUnit: '2px',
      borderRadius: '4px',
    },
  };

  elements = stripe.elements({ clientSecret: result.clientSecret }); // TODO: appearance

  const linkAuthenticationElement = elements.create('linkAuthentication');
  linkAuthenticationElement.mount('#link-authentication-element');

  const paymentElementOptions = {
    layout: 'tabs',
  };

  const paymentElement = elements.create('payment', paymentElementOptions);
  paymentElement.mount('#payment-element');
  paymentElement.on('ready', () => {
    setPageLoading(false);
    showForm();
  });
}

async function handleSubmit(e) {
  e.preventDefault();
  setSubmitLoading(true);

  const { error } = await stripe.confirmPayment({
    elements,
    confirmParams: {
      // Make sure to change this to your payment completion page
      return_url: window.stripeReturnUrl,
      //receipt_email: email, // TODO: Appears to be automatically collected by Stripe's form?
    },
  });

  // This point will only be reached if there is an immediate error when
  // confirming the payment. Otherwise, your customer will be redirected to
  // your `return_url`. For some payment methods like iDEAL, your customer will
  // be redirected to an intermediate site first to authorize the payment, then
  // redirected to the `return_url`.
  if (error.type === 'card_error' || error.type === 'validation_error') {
    showMessage(error.message, 'alert alert-danger');
  } else {
    showMessage('An unexpected error occurred.', 'alert alert-danger');
  }

  setSubmitLoading(false);
}

// Fetches the payment intent status after payment submission
async function checkStatus() {
  const clientSecret = new URLSearchParams(window.location.search).get(
    'payment_intent_client_secret'
  );

  if (!clientSecret) {
    return;
  }

  const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

  switch (paymentIntent.status) {
    case 'succeeded':
      showMessage('Payment succeeded!', 'alert alert-success');
      break;
    case 'processing':
      showMessage('Your payment is processing.', 'alert alert-info');
      break;
    case 'requires_payment_method':
      showMessage('Your payment was not successful. Please try again.', 'alert alert-danger');
      break;
    default:
      showMessage('Something went wrong.', 'alert alert-danger');
      break;
  }
}

// ------- UI helpers -------

function showMessage(messageText, classes = '') {
  const messageContainer = document.querySelector('#payment-message');

  messageContainer.className = classes;
  messageContainer.textContent = messageText;
}

function setPageLoading(isLoading) {
  if (isLoading) {
    return;
  }
  document.getElementById('page-loading-indicator').style.display = 'none';
}

function showForm() {
  paymentForm.style.display = 'block';
}

// Show a spinner on payment submission
function setSubmitLoading(isLoading) {
  if (isLoading) {
    // Disable the button and show a spinner
    document.querySelector('#submit').disabled = true;
    document.querySelector('#submit-loading-indicator').style.display = 'inline-block';
  } else {
    document.querySelector('#submit').disabled = false;
    document.querySelector('#submit-loading-indicator').style.display = 'none';
  }
}
