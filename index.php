<!DOCTYPE html>
<html lang="en">  
<head>

<title> RAVE | Arts </title>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" >
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>

<body>

<nav class="navbar navbar-expand-sm bg-light navbar-light justify-content-center fixed-top">
  <a class="nabar-brand ">
    <img class="img-overlay" src="http://rave.digital/img/raveLogo_750x336.png" style="width:100px;">
  </a>  
</nav>

<?php
if(isset($_GET['email'])){
    function getref($len)
  {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $string = '';
      for ($i = 0; $i < $len; $i++) {
          $string .= $characters[mt_rand(0, strlen($characters) - 1)];
      }
      return $string;
  }

  $customer_email = $_GET['email'];
  $amount =10000;  
  $currency = "NGN";
  $txref = getref(10); // ensure you generate unique references per transaction.
  $PBFPubKey = "FLWPUBK-2b22ba4979986f2d658e78d2f4d34015-X"; // get your public key from the dashboard.
  $redirect_url = "https://my-rave-demo.herokuapp.com/index.php";
  $payment_plan = "";// this is only required for recurring payments.

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/hosted/pay",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
      'amount'=>$amount,
      'customer_email'=>$customer_email,
      'currency'=>$currency,
      'txref'=>$txref,
      'PBFPubKey'=>$PBFPubKey,
      'redirect_url'=>$redirect_url,
      'payment_plan'=>$payment_plan
    ]),
    CURLOPT_HTTPHEADER => [
      "content-type: application/json",
      "cache-control: no-cache"
    ],
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  if($err){
    // there was an error contacting the rave API
    die('Curl returned error: ' . $err);
  }

  $transaction = json_decode($response);

  if(!$transaction->data && !$transaction->data->link){
    // there was an error from the API
    print_r('API returned error: ' . $transaction->message);
  }

  // uncomment out this line if you want to redirect the user to the payment page
  //print_r($transaction->data->message);


  // redirect to page so User can pay
  // uncomment this line to allow the user redirect to the payment page
  header('Location: ' . $transaction->data->link);
}
else if(isset($_GET['txref'])){
        $ref = $_GET['txref'];

        $query = array(
            "SECKEY" => "FLWSECK-2a85a9dc41ab584b1bdc32f9623c0518-X",
            "txref" => $ref 
        );

        $data_string = json_encode($query);
                
        $ch = curl_init('https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify');                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($ch);

        $resp = json_decode($response, true);

        $paymentStatus = $resp['data']['status'];
        $chargeResponsecode = $resp['data']['chargecode'];
        $chargeAmount = $resp['data']['amount'];
        $chargeCurrency = $resp['data']['currency'];
        //var_dump($resp);
echo '<div class="jumbotron" style="text-align: center; margin-top:120px;"><br><br><br><br>';
if ($chargeResponsecode == "00" || $chargeResponsecode == "0") {
    # code...
    // TIP: you may still verify the transaction
        // before giving value.
?>
    <div id="successfull" class="">
     <h3> Congratulations! You have sucessfully purchased the item.<br>
      <strong>Your Reference Id is: </strong><span class="badge badge-success"><?=$_GET['txref']?></span></h3>

    </div>
<?php
} else {
?>  
  <div id="unsuccessfull" class="">
      <h3>Sorry! It seems there was an error with your purchase. Please try <a class="" href="https://my-rave-demo.herokuapp.com/index.php">again.</a><br>
      <strong>Your Reference Id is: </strong><span class="badge badge-danger"><?=$_GET['txref']?></span></h3>
  </div>
<?php
}
echo '</div>';
// exit();
}else{
?>
<div class="container col-sm-4" style="margin-top:80px">
  
  <div class="card">
    <h3 class="card-title " style="text-align:center;">Do you want to buy this item? </h3>
     <img class="img-fluid" src="http://4.bp.blogspot.com/-VIPRQsOYI0M/U5UDywBoC7I/AAAAAAAAN7A/INMsxguBLKQ/s1600/Watercolor-Landscape.jpg" alt="watercolor-painting">

 <div class="card body">
        
        <blockquote>The Reverie is a watercolor painting by renowned artist <strong>Temi Adesanya</strong>. It is composed of watercolor on arches paper and is priced at <span class="badge badge-info" style="font-size: 15px;">N10,000</span></blockquote>
      </div>
      <button class="btn btn-success " data-toggle="collapse" data-target="#buy">Proceed</button>
        <div id="buy" class="collapse">
          <form class="form-group" action="">
            <hr>
            <input class="form-control" type="text" name="email" placeholder="E-mail"><br>
            <button class="btn btn-warning" type="submit" name="">Pay with RAVE</button>
          </form>
        </div>
  </div>

</div>
<?php
}
?>
</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
<script type="text/javascript" src="https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
</html>
