<?php
require 'root.php'
require 'vendor/autoload.php'; // Make sure to include the Stripe PHP library

\Stripe\Stripe::setApiKey('your-secret-key-here'); // Replace with your own Stripe secret key

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['stripeToken'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $amount = $_POST['amount'];

    try {
        $charge = \Stripe\Charge::create([
            'amount' => $amount * 100, // Amount in cents
            'currency' => 'usd',
            'description' => 'Donation from ' . $name,
            'source' => $token,
            'receipt_email' => $email,
        ]);

        // Handle post-charge actions (e.g., save to database, send thank you email)

        echo 'Success! Thank you for your donation.';
    } catch (\Stripe\Exception\CardException $e) {
        // Handle error
        echo 'Error: ' . $e->getMessage();
    }
}
?>
