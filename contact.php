<?php
require 'config/db.php';
require 'includes/functions.php';
$page_title = 'Contact & Support';

$sent = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name'] ?? '');
    $contact_info = trim($_POST['contact'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $contact_info === '' || $message === '') {
        $error = 'Please fill in all fields.';
    } else {
        // Saved to the database so the admin can see it in the
        // admin panel — more reliable than PHP's mail(), which is
        // often disabled or restricted on free hosting.
        $stmt = $pdo->prepare(
            "INSERT INTO contact_messages (name, contact_info, message) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $contact_info, $message]);
        $sent = true;
    }
}

require 'includes/header.php';
?>

<section class="container" style="padding-top:40px;">
    <h1>Contact & Support</h1>
    <p class="prose">Have a question about an order, delivery, or a product? We're happy to help.</p>

    <div style="display:flex; gap:14px; margin:20px 0 40px; flex-wrap:wrap;">
        <a href="https://wa.me/256753946220" target="_blank" class="btn" style="background:#3D7A4E; color:#fff;">💬 Chat with us on WhatsApp</a>
        <a href="tel:0759887596" class="btn btn-secondary">📞 Call 0759887596</a>
    </div>

    <h2>Frequently Asked Questions</h2>
    <div class="prose">
        <div class="faq-item">
            <h4>What areas in Masaka do you deliver to?</h4>
            <p>We currently deliver to Nyendo, Kyabakuza, Ndegeya, Nyabikungu, Katwe, and surrounding areas within Masaka town. See our <a href="delivery.php">Delivery page</a> for fees and timing.</p>
        </div>
        <div class="faq-item">
            <h4>What time do I need to order for same-day delivery?</h4>
            <p>Order before 1:00 PM for same-day evening delivery. Orders placed after the cut-off are delivered the next morning or evening, whichever you choose at checkout.</p>
        </div>
        <div class="faq-item">
            <h4>What happens if an item is out of stock or substituted?</h4>
            <p>If something sells out after you order, we'll call you before delivery to agree on a substitute of equal or lesser value, or remove it and refund that portion.</p>
        </div>
        <div class="faq-item">
            <h4>How do I pay?</h4>
            <p>We accept MTN Mobile Money and Airtel Money at checkout. For this, payment is simulated in test mode.</p>
        </div>
        <div class="faq-item">
            <h4>What if I'm not happy with the produce I received?</h4>
            <p>See our <a href="returns.php">Returns & Refunds policy</a> — report quality issues within 4 hours of delivery.</p>
        </div>
    </div>

    <h2>Send Us a Message</h2>
    <?php if ($sent): ?>
        <div class="alert alert-success">Thanks — your message has been received. We'll get back to you shortly.</div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= h($error) ?></div>
    <?php endif; ?>
    <form method="post" style="max-width:500px;">
        <div class="form-group">
            <label>Your Name</label>
            <input type="text" name="name" value="<?= h($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Phone or Email</label>
            <input type="text" name="contact" value="<?= h($_POST['contact'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea name="message" required><?= h($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" name="send_message" class="btn">Send Message</button>
    </form>
</section>

<?php require 'includes/footer.php'; ?>
