<?php
// Connect to PostgreSQL
$conn = pg_connect("host=dpg-d1tafq6mcj7s73d58avg-a dbname=lms_db_3hx1 user=lms_db_3hx1_user password=ImGmDcnvBzwU1ustoexBQSjvywKJmFsx port=5432");

if (!$conn) {
  die("Connection failed: " . pg_last_error());
}

// Get POST data
$name       = trim($_POST['name']);
$details    = trim($_POST['details']);
$category   = trim($_POST['category']);
$quantity   = trim($_POST['quantity']);
$item_code  = trim($_POST['item_code']);
$year       = trim($_POST['year']);
$location   = trim($_POST['location']);
$status     = trim($_POST['status']);

// ✅ Backend validation
if (
  empty($name) || empty($details) || empty($category) || empty($quantity) ||
  empty($item_code) || empty($year) || empty($location) || empty($status)
) {
  echo "error";
  pg_close($conn);
  exit;
}

if (!is_numeric($quantity) || (int)$quantity < 1 || (int)$quantity > 9999) {
  echo "error";
  pg_close($conn);
  exit;
}

if (!is_numeric($year) || (int)$year < 1000 || (int)$year > 2100) {
  echo "error";
  pg_close($conn);
  exit;
}

if (strlen($item_code) > 30 || strlen($name) > 255 || strlen($details) > 255 || strlen($category) > 255 || strlen($location) > 100) {
  echo "error";
  pg_close($conn);
  exit;
}

// Sanitize and prepare query
$result = pg_query_params($conn, 
  "INSERT INTO items (name, details, category, quantity, item_code, year, location, status) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)", 
  [$name, $details, $category, $quantity, $item_code, $year, $location, $status]
);

if ($result) {
  echo "success";
} else {
  echo "error";
}

pg_close($conn);
?>
