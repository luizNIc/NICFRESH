<?php
require 'config/db.php';
require 'includes/functions.php';
$page_title = 'Terms of Service';
require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <h1>Terms of Service</h1>
    <div class="prose">
        <p>Last updated: <?= date('F Y') ?></p>
        <h2>1. About This Site</h2>
        <p>NIC Fresh Supplies is an online produce store serving Masaka, Uganda. By placing an order through this site, you agree to the terms below.</p>

        <h2>2. Orders</h2>
        <p>All orders are subject to product availability. We reserve the right to contact you regarding substitutions or cancel an order if an item cannot be fulfilled, with a full refund of any amount already paid for that item.</p>

        <h2>3. Pricing</h2>
        <p>Prices are listed in Ugandan Shillings (UGX) and may change without notice to reflect seasonal availability. The price shown at checkout is the price you pay for that order.</p>

        <h2>4. Payment</h2>
        <p>Payment is accepted via MTN Mobile Money and Airtel Money. For this coursework deployment, all payment processing is simulated in test mode — no real transaction occurs.</p>

        <h2>5. Delivery</h2>
        <p>Delivery timeframes are estimates based on the window you select at checkout. See our <a href="delivery.php">Delivery page</a> for details.</p>

        <h2>6. Limitation of Liability</h2>
        <p>NIC Fresh Supplies is not liable for delays caused by circumstances outside our control, including weather, road conditions, or farm supply disruptions.</p>

        <h2>7. Contact</h2>
        <p>Questions about these terms can be sent via our <a href="contact.php">Contact page</a>.</p>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
