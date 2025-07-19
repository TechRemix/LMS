<?php
// Connect to PostgreSQL
$conn = pg_connect("host=dpg-d1tafq6mcj7s73d58avg-a dbname=lms_db_3hx1 user=lms_db_3hx1_user password=ImGmDcnvBzwU1ustoexBQSjvywKJmFsx port=5432");

if (!$conn) {
  die("Connection failed: " . pg_last_error());
}

// Get POST data
$name     = $_POST['name'];
$details  = $_POST['details'];
$category = $_POST['category'];
$quantity = $_POST['quantity'];
$item_code = $_POST['item_code'];
$year     = $_POST['year'];
$location = $_POST['location'];
$status   = $_POST['status'];

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
