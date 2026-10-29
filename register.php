<?php
require 'config/db.php';
require 'includes/functions.php';
require 'includes/customer-auth.php';

if (customer_id() > 0) {
    header('Location: account.php');
    exit;
}

$page_title = 'Create Customer Account';
$error = '';
$name = trim($_POST['full_name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');
$return_to = customer_return_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($name === '' || $email === '' || $phone === '' || $password === '') {
        $error = 'Please complete every field.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Your password must be at least 8 characters long.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO customers (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['customer_id'] = (int) $pdo->lastInsertId();
            $_SESSION['customer_name'] = $name;
            unset($_SESSION['analytics_consent'], $_SESSION['analytics_visit_token']);
            if ($return_to === 'index.php') {
                $_SESSION['customer_entry_pending'] = true;
            }
            header('Location: ' . $return_to);
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'An account with that email address already exists. Please log in.';
            } else {
                throw $e;
            }
        }
    }
}

require 'includes/header.php';
?>
<section class="container" style="padding-top:40px; max-width:620px;">
    <h1>Create your customer account</h1>
    <p>Create an account to continue to NIC Fresh Supplies and keep track of your orders.</p>
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
        <input type="hidden" name="return" value="<?= h($return_to) ?>">
        <div class="form-group">
            <label for="full_name">Full name</label>
            <input id="full_name" type="text" name="full_name" maxlength="100" value="<?= h($name) ?>" autocomplete="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" maxlength="150" value="<?= h($email) ?>" autocomplete="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone number</label>
            <input id="phone" type="tel" name="phone" maxlength="20" value="<?= h($phone) ?>" autocomplete="tel" required>
        </div>
        <div class="form-group">
            <label for="password">Password (at least 8 characters)</label>
            <input id="password" type="password" name="password" minlength="8" autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn btn-block">Create Account</button>
    </form>
    <p class="helper-text" style="margin-top:16px;">Already registered? <a href="login.php?return=<?= urlencode($return_to) ?>">Log in</a>.</p>
</section>
<?php require 'includes/footer.php'; ?>
