<?php
require __DIR__ . '/../vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

// here I'll get the subscription endpoint in the POST parameters
// but in reality, you'll get this information in your database
// because you already stored it (cf. push_subscription.php)
// $subscription = Subscription::create(json_decode(file_get_contents('php://input'), true));

$dbhost = '';
$dbuser = '';
$dbpass = '';
$dbname = '';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

$sql = 'SELECT * FROM user';
         $result = mysqli_query($conn, $sql);

         if (mysqli_num_rows($result) > 0) {
            // https://stackoverflow.com/questions/22527465/fetch-all-results-from-database-using-mysqli
            while($row = mysqli_fetch_all($result, MYSQLI_ASSOC)) { 
                foreach ($row as $message) {
                    echo "Endpoint: " . $message["endpoint"]. "<br>";
                    echo "auth: " . $message["auth"]. "<br>";
                    echo "p256dh: " . $message["p256dh"]. "<br>";
                    echo "contentEncoding: " . $message["contentEncoding"]. "<br>";
                        
                        $notifications = [
                                            [
                                                'subscription' => Subscription::create([
                                                    'endpoint' => $message["endpoint"],
                                                    'publicKey' => $message["p256dh"],
                                                    'authToken' => $message["auth"],
                                                    'contentEncoding' => $message["contentEncoding"]
                                                ]),
                                                'payload' => 'hello !',
                                            ]

                                        ];                                              
                                        // print_r($subscription);
                                           }
                                                                  }
               
            //    var_dump($row);"<br>";
//There was an error about the push notification failing because it needs a valid url endpoint
//The problem was due to the data length or the amount of data to be stored being longer or greater
//than the amount of data that could be stored in the database and or for that specific column.
//Originally, the database column for the endpoint was set to a data length of 100 but after changing it
//to 200, the web push notification worked. After observing the the var_dump,the total data length for
//the endpoint states/says 188, hence I wasn't storing everything & hence the error.

//https://stackoverflow.com/questions/58525667/php-successful-webpush-not-triggering-push-event-listener-in-sw             
                                   
                                                      
         } else {
            echo "0 results";
         }

        if(! $conn ){
        die('Could not connect: ' . mysqli_error());
        }
        echo 'Connected successfully';

$auth = array(
    'VAPID' => array(
        'subject' => 'https://github.com/Minishlink/web-push-php-example/',
        'publicKey' => file_get_contents(__DIR__ . '/../keys/public_key.txt'), // don't forget that your public key also lives in app.js
        'privateKey' => file_get_contents(__DIR__ . '/../keys/private_key.txt'), // in the real world, this would be in a secret file
    ),
);

$webPush = new WebPush($auth);


foreach ($notifications as $notification) {
    $webPush->sendNotification(
        $notification['subscription'],
        "Hello!",
        true
    );
}


// handle eventual errors here, and remove the subscription from your server if it is expired
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();

    if ($report->isSuccess()) {
        echo "[v] Message sent successfully for subscription {$endpoint}.";
    } else {
        echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
    }
}
