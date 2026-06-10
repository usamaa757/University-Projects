<?php
require('stripe-php/init.php');

$publishableKey = "pk_test_51QahOTIKPiqNBWwCcyNqrV6UGVMsriKjPiHiGj8GCtm70ES7uag8qB1sNbwZsBKsdnyhTI7U4dyFwh2mJuMixxxb00MJnwFQKL";

$secretKey = "sk_test_51QahOTIKPiqNBWwC3HwiqDnkHqlydnS0rWLiNmAtC1HXBs9aq6CWHH67O5D1a1VEf5Ss5raatk80FqJx8euHrD7700LUXy8nwf";

\Stripe\Stripe::setApiKey($secretKey);
$stripe = new \Stripe\StripeClient($secretKey);