<form class="online_payment" id='payment-form'>
    <label>
      Card details
    </label>
    <!-- placeholder for Elements -->
    <div id="card-element"></div>
    <br>
    <button id="submit-button" type="submit" class="btn bt-nlg btn-success card-submit">Submit Payment</button>
    <br><br>
    <div id="card-errors" role="alert"></div>
</form>

<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
    var stripe = Stripe('<?php echo $account_payment_gateway->config['publishableKey']; ?>');

    var elements = stripe.elements();

    // Set up Stripe.js and Elements to use in checkout form
    var style = {
    base: {
        color: "#32325d",
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: "antialiased",
        fontSize: "16px",
        "::placeholder": {
        color: "#aab7c4"
        }
    },
    invalid: {
        color: "#fa755a",
        iconColor: "#fa755a"
    },
    };

    var cardElement = elements.create('card', {hidePostalCode: true, style:style});
    cardElement.mount('#card-element');

    var form = document.getElementById('payment-form');

    form.addEventListener('submit', function(event) {
    document.getElementById("submit-button").disabled = true;
    // We don't want to let default form submission happen here,
    // which would refresh the page.
    event.preventDefault();

    stripe.createPaymentMethod({
        type: 'card',
        card: cardElement
        /*billing_details: {
        // Include any additional collected billing details.
        name: 'Jenny Rosen',
        },*/
    }).then(stripePaymentMethodHandler);
    });

    function stripePaymentMethodHandler(result) {
    if (result.error) {
        // Show error in payment form
        //Wait a bit, then reactivate the button
        setTimeout(function (){
            document.getElementById("submit-button").disabled = false;
        }, 3000);

        var displayError = document.getElementById('card-errors');
        if(result.message)
        {
        displayError.textContent = result.message;
        }
        else if(result.error.message)
        {
        displayError.textContent = result.error.message;
        }

    } else {
        // Otherwise send paymentMethod.id to your server (see Step 4)
        fetch("<?php echo route('postCreateOrder', ['event_id' => $event->id]); ?>", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
        body: JSON.stringify({
            paymentMethod: result.paymentMethod.id,
        })
        }).then(function(result) {
        // Handle server response (see Step 4)
        result.json().then(function(json) {
            handleServerResponse(json);
        })
        });
    }
    }

    var retries = 0;

    function handleServerResponse(json)
    {
        if(json.status == "success")
        {
            window.location.href = json.redirectUrl;
        }
        else if(json.status == "error")
        {
            retries = retries + 1;
            var displayError = document.getElementById('card-errors');
            displayError.textContent = json.message + " (" + String(5 - retries) + " retries left)";
            if(retries < 5)
            {
                var btn = document.getElementById("submit-button");
                btn.disabled = false;
            }
            else
            {
                displayError.textContent = "Too much retries, please refresh the page";
            }
        }
        else if(json.error.code == "card_declined")
        {
        displayError.textContent = json.error.message;
        }
    }
</script>

<style type="text/css">
    .StripeElement {
  height: 40px;
  padding: 10px 12px;
  width: 100%;
  color: #32325d;
  background-color: white;
  border: 1px solid transparent;
  border-radius: 4px;

  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}
</style>