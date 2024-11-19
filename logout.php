<?php
session_start();
if (isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] == true) {
    session_destroy();
    header('Location: customer_dashboard.php');
} else {
    session_destroy();
    header('Location: index.php');
}
?>