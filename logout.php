<?php
require 'includes/functions.php';

unset($_SESSION['customer_id'], $_SESSION['customer_name']);
unset($_SESSION['analytics_consent'], $_SESSION['analytics_visit_token']);
unset($_SESSION['customer_entry_pending']);
session_regenerate_id(true);
header('Location: index.php');
exit;
