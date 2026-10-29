<?php
require 'config/db.php';
require 'includes/functions.php';
$page_title = 'Delivery Information';
require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <h1>Delivery & Fulfilment</h1>
    <div class="prose">
        <p>Because our produce is perishable, delivery speed and handling matter as much as the order itself. Here's exactly how it works.</p>

        <h2>Delivery Areas & Fees</h2>
        <table class="admin-table" style="max-width:500px;">
            <thead><tr><th>Area</th><th>Delivery Fee</th></tr></thead>
            <tbody>
                <tr><td>Nyendo</td><td><?= money(3000) ?></td></tr>
                <tr><td>Kyabakuza</td><td><?= money(3000) ?></td></tr>
                <tr><td>Ndegeya</td><td><?= money(4000) ?></td></tr>
                <tr><td>Nyabikungu</td><td><?= money(4000) ?></td></tr>
                <tr><td>Katwe (Masaka)</td><td><?= money(3500) ?></td></tr>
                <tr><td>Other areas within Masaka</td><td><?= money(5000) ?></td></tr>
            </tbody>
        </table>

        <h2>Delivery Windows</h2>
        <ul>
            <li><strong>Today evening</strong> — for orders placed before 1:00 PM</li>
            <li><strong>Tomorrow morning</strong> or <strong>Tomorrow evening</strong> — for orders placed after the cut-off</li>
        </ul>

        <h2>How Orders Are Fulfilled</h2>
        <p>Once you place an order, it's sent straight to the relevant partner farm(s) for same-day picking. Produce is collected, quality-checked, and delivered by our riders in insulated crates to keep it fresh in transit.</p>

        <h2>Out-of-Stock & Substitutions</h2>
        <p>Fresh produce availability can change quickly. If an item sells out after you've ordered, we'll call you before delivery to agree on a substitute of equal or lesser value, or remove it and adjust your total.</p>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
