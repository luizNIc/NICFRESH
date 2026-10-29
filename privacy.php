<?php
require 'config/db.php';
require 'includes/functions.php';
$page_title = 'Privacy Policy';
require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <h1>Privacy Policy</h1>
    <div class="prose">
        <p>Last updated: <?= date('F Y') ?></p>

        <h2>What We Collect</h2>
        <p>Customer accounts store your name, email address, phone number, and a securely hashed password. When you place an order, we also collect delivery details and your Mobile Money number to fulfil and communicate with you about that order.</p>

        <h2>How We Use Your Information</h2>
        <p>Order information is used to prepare, deliver, and follow up on your order, and to contact you if there's an issue such as an out-of-stock item. We do not sell or share your personal information with third parties for marketing purposes.</p>

        <h2>Cart Data</h2>
        <p>While you browse, your shopping cart is stored temporarily in your browser session and is cleared once an order is placed or your session ends.</p>

        <h2>Customer Accounts</h2>
        <p>Your account lets you place orders and view your order history. Contact us through the Contact page if you need help with your account or personal details.</p>

        <h2>Website Analytics</h2>
        <p>Analytics is optional and starts only if you choose Accept in the consent notice. Google Analytics collects website usage information such as page views and general device and browser details under Google's privacy terms. Separately, when a customer is signed in and has accepted, we record their account name, email, visited page, and visit time in our database for business analysis. These named visit records are visible to the store administrator and are not sent to Google. We do not identify anonymous visitors or use device fingerprinting. Signed-in customers can withdraw consent from My Account; withdrawing deletes their previously recorded page visits. Anyone can change their browser's choice using Analytics settings in the footer.</p>

        <h2>Data Security</h2>
        <p>Order and account data is stored in a secured database. Administrative access is protected by password authentication, and passwords are never stored in plain text.</p>

        <h2>Your Rights</h2>
        <p>You can request a copy of, or ask us to delete, any personal information we hold about you by contacting us via our <a href="contact.php">Contact page</a>.</p>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
