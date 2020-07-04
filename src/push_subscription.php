<?php
   $dbhost = 'localhost';
   $dbuser = 'shayn';
   $dbpass = 'forceofnature314';
   $dbname = 'subscription';
   $conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

   if(! $conn ){
      die('Could not connect: ' . mysqli_error());
   }
   echo 'Connected successfully';

$subscription = json_decode(file_get_contents('php://input'), true);
$_POST = json_decode(file_get_contents('php://input'), true); //for php 7

if (!isset($subscription['endpoint'])) {
    echo 'Error: not a subscription';
    return;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
    // create a new subscription entry in your database (endpoint is unique)
    //  $endpoint = $subscription['endpoint'];
    // $auth = $subscription['authToken'];
    // $p = $subscription['publicKey'];

$endpoint = $_POST['endpoint'];
$auth = $_POST['authToken'];
$p = $_POST['publicKey'];
$contentEncoding = $_POST['contentEncoding'];

        $sql = "INSERT INTO driver (endpoint,auth, p256dh,contentEncoding) VALUES ('$endpoint','$auth','$p','$contentEncoding')";
      
            if (mysqli_query($conn, $sql)) {
               echo "New record created successfully";
            } else {
               echo "Error: " . $sql . "" . mysqli_error($conn);
            }
            $conn->close();
        break;
    case 'PUT':
        // update the key and token of subscription corresponding to the endpoint
        break;
    case 'DELETE':
        // delete the subscription corresponding to the endpoint
        break;
    default:
        echo "Error: method not handled";
        return;
                 }
