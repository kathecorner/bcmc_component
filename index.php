<?php

// 1. prepare api request to adyen library
// 2. get all payment methods for this shopper
// SGD, SG

$url = "https://checkout-test.adyen.com/v66/paymentMethods";

$payload = array(
  "merchantAccount" => "KenjiW",
  "amount" => [
    "value" => 100,
    "currency" => "EUR",
    ]
    //"shopperReference" => "Shopper_16082021_1"
);

$curl_http_header = array(
   "X-API-Key: AQEyhmfxL4PJahZCw0m/n3Q5qf3VaY9UCJ1+XWZe9W27jmlZiv4PD4jhfNMofnLr2K5i8/0QwV1bDb7kfNy1WIxIIkxgBw==-lUKXT9IQ5GZ6d6RH4nnuOG4Bu//eJZxvoAOknIIddv4=-<anpTLkW{]ZgGy,7",
   "Content-Type: application/json"
);

$curl = curl_init();

curl_setopt_array(
    $curl,
    [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => $curl_http_header,
        CURLOPT_VERBOSE        => true
    ]
);

$paymentmethodsrequestresponse = json_encode(curl_exec($curl));

curl_close($curl);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Adyen card component</title>
    <link rel="stylesheet"
 href="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/4.7.0/adyen.css"
 integrity="sha384-dkJjySvUD62j8VuK62Z0VF1uIsoa+APxWLDHpTjBRwQ95VxNl7oaUvCL+5WXG8lh"
 crossorigin="anonymous">

 <script src="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/4.7.0/adyen.js"
 integrity="sha384-Hmnh/5ShP0Q8iCjGV2U/6XFi7jiiFys4fsh7UrCH1JT1PV1ThZ9czMnbbyjzxuhU"
 crossorigin="anonymous"></script>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js" charset="utf-8"></script>

  </head>
  <body>

    <h2>BCMC Component</h2>

    <!-- Embed the Adyen Web script element above any other JavaScript in your checkout page. -->

     <div id="bcmc-container"></div>

    <script type="text/javascript">

    var availablePaymentMethods = JSON.parse( <?php echo $paymentmethodsrequestresponse; ?> );

    function makePayment(state) {
          const prom_data = state;
          return new Promise(
              function (resolve,reject) {
                  $.ajax(
                      {
                          type: "POST",
                          url: "/processpayment.php",
                          data: prom_data,
                          success: function (response) {
                              resolve(response);
                          }
                      }
                  );
              }
          );

      }

      function showFinalResult(data){
          //console.log(JSON.parse(data.resultCode));
          //var responseData = JSON.parse(data);
          var responseData = data;

          if(responseData.resultCode == "Authorised"){
              alert('PAYMENT SUCCESSFUL!');
              //window.location.href = 'http://127.0.0.1:8080/return.php';
              window.location.href = '../showResults.php';
          }
      }

      function show3DSResult(data){
        if(data.resultCode == "Authorised"){

            alert(data.resultCode);

            var response_list = data;
            var response_list_all;

            for (var i=0; i<response_list.length;i++){
              response_list_all += '<li>' + response_list[i] + '</li>';
            }
            //document.getElementById('response_list_all').innerHTML = response_list_all;
            document.write(data.resultCode);
        }
        /*
        console.log("makeAdditionalDetails_2(data)");*/
      }

      function makeAdditionalDetails(state){
        //alert('makeAdditionalDetials');

        const detail_data = state;
        return new Promise(
          function (resolve,reject){
            $.ajax(
              {
                type: "POST",
                url: "additionaldetails.php",
                data: detail_data,
                success: function (response) {
                  resolve(response);
                  console.log(response);
                }
              }
            );
            }
            )
          }

      var configuration = {
        paymentMethodsResponse : availablePaymentMethods,
        clientKey: "test_RKKBP5GHOFFUFJJMJHOJAG7ZIIJKBMI6",
        locale: "en-US",
        showPayButton: true,
        environment: "test",
        billingAddressRequired: true,//added on Aug30
        hasHolderName: true,
        holderNameRequired: true,
        enableStoreDetails: true,
        name: 'Bancontact card',
        brands: ['bcmc'],
        onSubmit: (state,dropin)=>{
            makePayment(state.data)
                .then(response => {
                    var responseData = response.action;
                    console.log(response);
                    if(response.action) {
                        dropin.handleAction(response.action);
                    }
                    else{
                        showFinalResult(response);
                    }
                })
                .catch(error => {
                    console.log(error);
                    throw Error(error);
                });
        },
        onAdditionalDetails: (state,dropin)=>{
          //alert('onAdditionalDetails called.');
          $a_params = state.data;
          makeAdditionalDetails(state.data)
            .then(response => {
              var responseDetail = response.action;
              console.log(response);
              if(response.action) {
                //alert('action received.');
                dropin.handleAction(response.action);
                //show3DSResult(response);
              }
              else{
                show3DSResult(response);
              }
            })
            .catch(error => {
              console.log(error);
              throw Error(error);
            });
        },
        // Events
        onChange: function(state,component){
            /*if(state.isValid){
                makePayment(state.data)
                    .then(response =>{
                        var responseData = response.action;
                        console.log(response);
                        if(response.action){
                            //back the action object to front
                            //alert("here");
                            checkout.createFromAction(response.action).mount('#my-container');
                            console.log(state.data);

                            /*makeAdditionalDetails(state.data)

                              .then(response =>{
                                var responseDetails = response.action;
                                //console.log(response.action);
                                show3DSResult(response);
                              })
                        }
                        else{
                            showFinalResult(response);
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        //throw Error(error);
                    });
            }*/

        },

  onPaymentCompleted: (result, component) => {
      console.info(result, component);
  },
  onError: (error, component) => {
      console.error(error.name, error.message, error.stack, component);
  },
  paymentMethodsResponse :availablePaymentMethods,
  //onAdditionalDetails: handleOnAdditionalDetails,

  paymentMethodsConfiguration: {
    hasHolderName: true,
    holderNameRequired: true,
    billingAddressRequired: true
  },
  action: (result) =>{},
};


const bcmcConfiguration = {
    clientKey: "test_RKKBP5GHOFFUFJJMJHOJAG7ZIIJKBMI6",
    hasHolderName: true,
    holderNameRequired: true,
    enableStoreDetails: true,
    name: 'Bancontact card'
};


// Create an instance of AdyenCheckout using the configuration object.
const checkout = new AdyenCheckout(configuration);

const threeDSConfiguration = {
  challengeWindowSize: '05'
   // Set to any of the following:
   // '02': ['390px', '400px'] -  The default window size
   // '01': ['250px', '400px']
   // '03': ['500px', '600px']
   // '04': ['600px', '400px']
   // '05': ['100%', '100%']
};
// Create an instance of the Component and mount it to the container you created.
//const cardComponent = checkout.create('card').mount('#card-container');

const bcmcComponent = checkout.create('bcmc',configuration).mount('#bcmc-container');

</script>
  </body>
</html>
