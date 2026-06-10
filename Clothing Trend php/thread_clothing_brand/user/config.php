<?php
require 'stripe-php/init.php';

$publishableKey = "pk_test_51QYliqP8XejZPcCFkmyuhk1BlHMeI5wJimezT10Kp2lUF5S7bDdzRI2nH4h0zxnhUteH4fLAkElQS4ASOrKbTJyJ00BwNwmkkD";

$secretKey = "sk_test_51QYliqP8XejZPcCF34ILgWRjbdG9lImo8AiiKz8ypX1pWBeQfCJZW6yBedgzMXRkAZslYkDWD1Xyi9G30CqFzHz000Yxo3LyUl";

\Stripe\Stripe::setApiKey($secretKey);
$stripe = new \Stripe\StripeClient($secretKey);
