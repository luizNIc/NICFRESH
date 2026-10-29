<?php
require 'config/db.php';
require 'includes/functions.php';
$page_title = 'About Us';
require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <h1>About NIC Fresh Supplies</h1>
    <div class="prose">
        <p>NIC Fresh Supplies is a farm-to-consumer produce store serving households and small restaurants in Masaka. We work directly with a small network of named partner farms around the district, buying produce at fair prices and delivering it to your door — often within a day of harvest.</p>
        <p>Our goal is simple: cut out the layers between farm and table so you get fresher food, farmers get a fairer share, and busy households and restaurant owners get their time back.</p>

        <h2>Our Partner Farms</h2>
        <div class="info-cards">
            <div class="info-card"><h4>Kyabakuza Family Farm</h4><p>Vegetables and avocados grown on family land just outside Masaka town.</p></div>
            <div class="info-card"><h4>Nyendo Growers Co-op</h4><p>A cooperative of smallholder farmers supplying tomatoes, pineapples, and spring onions.</p></div>
            <div class="info-card"><h4>Buwunga Organic Farm</h4><p>Chemical-free leafy greens and herbs.</p></div>
            <div class="info-card"><h4>Kalisizo Highland Farm</h4><p>Matoke and groundnuts from the Kalisizo highlands.</p></div>
            <div class="info-card"><h4>Ndegeya Millers</h4><p>Locally milled maize flour and staples.</p></div>
        </div>

        <h2>Our Value Proposition</h2>
        <p>Same-day or next-day delivery of fresh, traceable produce sourced directly from named partner farms — at fair prices for both customers and growers, without the markups of multi-step market chains.</p>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
