<?php
require 'config/db.php';
require 'includes/functions.php';
require 'includes/customer-auth.php';

$return_to = customer_return_path();
if (customer_id() > 0) {
    header('Location: ' . $return_to);
    exit;
}

$page_title = 'Customer Login';
$error = '';
$email = strtolower(trim($_POST['email'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, full_name, password_hash FROM customers WHERE email = ?');
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if ($customer && password_verify($password, $customer['password_hash'])) {
        session_regenerate_id(true);
        unset($_SESSION['admin_id'], $_SESSION['admin_username']);
        $_SESSION['customer_id'] = (int) $customer['id'];
        $_SESSION['customer_name'] = $customer['full_name'];
        unset($_SESSION['analytics_consent'], $_SESSION['analytics_visit_token']);
        if ($return_to === 'index.php') {
            $_SESSION['customer_entry_pending'] = true;
        }
        header('Location: ' . $return_to);
        exit;
    }
    $error = 'Email or password is incorrect.';
}

require 'includes/header.php';
?>
<section class="container" style="padding-top:40px; max-width:520px;">
    <h1>Customer Login</h1>
    <p>Log in to continue to NIC Fresh Supplies. New customers can create an account.</p>
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
        <input type="hidden" name="return" value="<?= h($return_to) ?>">
        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" maxlength="150" value="<?= h($email) ?>" autocomplete="email" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-block">Log In</button>
    </form>
    <p class="helper-text" style="margin-top:16px;">New to NIC Fresh? <a href="register.php?return=<?= urlencode($return_to) ?>">Create an account</a>.</p>
</section>
<?php require 'includes/footer.php'; ?>
