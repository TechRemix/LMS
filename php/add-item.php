<?php
$mysqli = new mysqli("localhost", "root", "", "lms");

if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

$name     = $_POST['name'];
$details  = $_POST['details'];
$category = $_POST['category'];
$quantity = $_POST['quantity'];
$isbn     = $_POST['isbn'];
$year     = $_POST['year'];
$location = $_POST['location'];
$status   = $_POST['status'];

$stmt = $mysqli->prepare("INSERT INTO items (name, details, category, quantity, isbn, year, location, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssisiss", $name, $details, $category, $quantity, $isbn, $year, $location, $status);

if ($stmt->execute()) {
  echo "success";
} else {
  echo "error";
}

$stmt->close();
$mysqli->close();
?>
