@extends('layouts.app')

@section('css')
    <style>

form {
  width: 100%;
  min-width: 500px;
  align-self: center;
  box-shadow: 0px 0px 0px 0.5px rgba(50, 50, 93, 0.1),
    0px 2px 5px 0px rgba(50, 50, 93, 0.1), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.07);
  border-radius: 7px;
  padding: 40px;
  margin: 5% auto;
}

.hidden {
  display: none;
}

#payment-message {
  color: rgb(105, 115, 134);
  font-size: 16px;
  line-height: 20px;
  padding-top: 12px;
  text-align: center;
}

#payment-element {
  margin-bottom: 24px;
}

/* Buttons and links */
button {
  background: #5469d4;
  font-family: Arial, sans-serif;
  color: #ffffff;
  border-radius: 4px;
  border: 0;
  padding: 12px 16px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  display: block;
  transition: all 0.2s ease;
  box-shadow: 0px 4px 5.5px 0px rgba(0, 0, 0, 0.07);
  width: 100%;
}
button:hover {
  filter: contrast(115%);
}
button:disabled {
  opacity: 0.5;
  cursor: default;
}

/* spinner/processing state, errors */
.spinner,
.spinner:before,
.spinner:after {
  border-radius: 50%;
}
.spinner {
  color: #ffffff;
  font-size: 22px;
  text-indent: -99999px;
  margin: 0px auto;
  position: relative;
  width: 20px;
  height: 20px;
  box-shadow: inset 0 0 0 2px;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
}
.spinner:before,
.spinner:after {
  position: absolute;
  content: "";
}
.spinner:before {
  width: 10.4px;
  height: 20.4px;
  background: #5469d4;
  border-radius: 20.4px 0 0 20.4px;
  top: -0.2px;
  left: -0.2px;
  -webkit-transform-origin: 10.4px 10.2px;
  transform-origin: 10.4px 10.2px;
  -webkit-animation: loading 2s infinite ease 1.5s;
  animation: loading 2s infinite ease 1.5s;
}
.spinner:after {
  width: 10.4px;
  height: 10.2px;
  background: #5469d4;
  border-radius: 0 10.2px 10.2px 0;
  top: -0.1px;
  left: 10.2px;
  -webkit-transform-origin: 0px 10.2px;
  transform-origin: 0px 10.2px;
  -webkit-animation: loading 2s infinite ease;
  animation: loading 2s infinite ease;
}

.card-header {
        border-top: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
        background: #fff;
        border-left: 1px solid rgba(0,0,0,.125);
        border-right: 1px solid rgba(0,0,0,.125);
    }

@-webkit-keyframes loading {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes loading {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@media only screen and (max-width: 600px) {
  form {
    width: 80vw;
    min-width: initial;
  }
}
    </style>
@endsection

@section('content')

<div class="container text-center">
  <div class="card-header">
    <h5>Amount: {{ $settings->currency ?? 'USD' }} {{$invoice->price}}  for: {{ $student->first_name }} {{ $student->last_name }}</h5>
  </div>
<input type="hidden" name="invoice" value="{{ $invoice->id }}" id="inv_id">

<form id="payment-form" style="background: #fff">
    @csrf
    
    <div id="payment-element">
      <!--Stripe.js injects the Payment Element-->
    </div>
    <button id="submit">
      <div class="spinner hidden" id="spinner"></div>
      <span id="button-text">Pay now</span>
    </button>
    <div id="payment-message" class="hidden"></div>
</form>
</div>
@endsection

@section('js')

<script src="https://js.stripe.com/v3/" ></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script >



$.ajax({
    type: "GET",
    url: "{{ route('getkeytwo') }}",
    processData: false,
    contentType: false,
    cache: false,
               
  error: function (err) {
    console.log(err)
  },
  success: function (response) {
    console.log(response)
    const stripe = Stripe(response);

    // The items the customer wants to buy
    var invoice = document.querySelector('#inv_id').value;


    const items = [{id:invoice, 'name': 'schooltrip'}];
    

    let elements;
    var amount = 0;

    initialize();
    checkStatus();

    document
      .querySelector("#payment-form")
      .addEventListener("submit", handleSubmit);

    // Fetches a payment intent and captures the client secret
    async function initialize() {

      const url = "{{route('checkout')}}";
      const { clientSecret } = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json", 'X-CSRF-Token': document.querySelector('input[name=_token]').value },
        body: JSON.stringify({ items }),
      }).then((r) => r.json());

      

      
      elements = stripe.elements({ clientSecret });

      const paymentElementOptions = {
        layout: "tabs",
      };

      const paymentElement = elements.create("payment", paymentElementOptions);
      paymentElement.mount("#payment-element");
    }

    async function handleSubmit(e) {
      e.preventDefault();
      setLoading(true);
      var invoices = document.querySelector('#inv_id').value;

      const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          // Make sure to change this to your payment completion page
          

          return_url: "{{route('chekout_updatetrip',['id'=>$invoice->id,'student_id'=>$student->id])}}",
        },
      });

      // This point will only be reached if there is an immediate error when
      // confirming the payment. Otherwise, your customer will be redirected to
      // your `return_url`. For some payment methods like iDEAL, your customer will
      // be redirected to an intermediate site first to authorize the payment, then
      // redirected to the `return_url`.
      if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
      } else {
        showMessage("An unexpected error occurred.");
      }

      setLoading(false);
    }

    // Fetches the payment intent status after payment submission
    async function checkStatus() {
      const clientSecret = new URLSearchParams(window.location.search).get(
        "payment_intent_client_secret"
      );

      if (!clientSecret) {
        return;
      }

      const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

      switch (paymentIntent.status) {
        case "succeeded":
          
          showMessage("Payment succeeded!");
          break;
        case "processing":
          Swal.fire("Your payment is processing.");
          break;
        case "requires_payment_method":
          Swal.fire("Your payment was not successful, please try again.");
          break;
        default:
          Swal.fire("Something went wrong.");
          break;
      }
    }

    // ------- UI helpers -------

    function showMessage(messageText) {
      const messageContainer = document.querySelector("#payment-message");

      messageContainer.classList.remove("hidden");
      messageContainer.textContent = messageText;

      setTimeout(function () {
        messageContainer.classList.add("hidden");
        messageText.textContent = "";
      }, 4000);
    }

    // Show a spinner on payment submission
    function setLoading(isLoading) {
      if (isLoading) {
        // Disable the button and show a spinner
        document.querySelector("#submit").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#button-text").classList.add("hidden");
      } else {
        document.querySelector("#submit").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#button-text").classList.remove("hidden");
      }
    }
  }   
});

   

</script>
@endsection