<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

$event_id = $_GET['id'];

$delete_event_query = "DELETE FROM discount_events WHERE id = ?";
$stmt = $conn->prepare($delete_event_query);
$stmt->bind_param('i', $event_id);
$stmt->execute();

$delete_event_games_query = "DELETE FROM discount_event_games WHERE event_id = ?";
$stmt = $conn->prepare($delete_event_games_query);
$stmt->bind_param('i', $event_id);
$stmt->execute();

header('Location: admin_dashboard.php');
exit();
?>
