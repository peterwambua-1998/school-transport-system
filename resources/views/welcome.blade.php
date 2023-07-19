<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AY9rki3OauJzbM855zpSOS9wtQVHFwIsA6H1-HAbPsjhUCgUapchE15D4UH-39ztaOlTD4yNP7NbBYN3&currency=USD"></script>
</head>
<body>
    <div id="paypal-button-container"></div>
    
    <script>
        const csrfToken = '{{csrf_token()}}';
        console.log(csrfToken);
        paypal.Buttons({
            createOrder() {
                return fetch('/paypal-order', {
                    method: "POST",
                    headers: {
                        "X-CSRF-Token": csrfToken,
                        "Content-Type": "application/json",
                    },
                    body:JSON.stringify({
                        amount:"20000"
                    }),
                })
                .then(function(response) {
                    var j = response.json();
                    console.log(j);
                    return j;
                })
                .then((order) => order.id);
            },
            
            onApprove(data) {
                return fetch("/capture-paypal-order", {
                    method: "POST",
                    headers: {
                        "X-CSRF-Token": csrfToken,
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        orderID: data.orderID
                    })
                })
                .then(function(response) {
                    var x = response;
                    console.log(response);
                    return x;
                })
                .then((orderData) => {
                    console.log(orderData);

                })
            }
        }).render('#paypal-button-container');

        function updateFeePayment() {
            let f = new FormData;
            f.append('_token','{{csrf_token()}}');
            f.append('_token','{{csrf_token()}}');

            $.ajax({
                type: 'POST',
                url: '/update-payment'
                processData: false,
                cache: false,

            })
        }

    </script>
</body>
</html>