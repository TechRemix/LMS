<?php
$mysqli = new mysqli("localhost", "root", "", "lms");

if ($mysqli->connect_error) {
  http_response_code(500);
  echo "Database connection failed";
  exit;
}

$id       = $_POST['id'];
$name     = $_POST['name'];
$details  = $_POST['details'];
$category = $_POST['category'];
$quantity = $_POST['quantity'];
$isbn     = $_POST['isbn'];
$year     = $_POST['year'];
$location = $_POST['location'];
$status   = $_POST['status'];

$stmt = $mysqli->prepare("
  UPDATE items 
  SET name = ?, details = ?, category = ?, quantity = ?, isbn = ?, year = ?, location = ?, status = ?
  WHERE id = ?
");

$stmt->bind_param("sssisissi", $name, $details, $category, $quantity, $isbn, $year, $location, $status, $id);

if ($stmt->execute()) {
  echo "success";
} else {
  echo "error";
}

$stmt->close();
$mysqli->close();
?>
