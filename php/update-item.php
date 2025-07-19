<?php
// Connect to PostgreSQL
$conn = pg_connect("host=dpg-d1tafq6mcj7s73d58avg-a dbname=lms_db_3hx1 user=lms_db_3hx1_user password=ImGmDcnvBzwU1ustoexBQSjvywKJmFsx port=5432");

if (!$conn) {
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

$query = "
  UPDATE items 
  SET name = $1, details = $2, category = $3, quantity = $4, isbn = $5, year = $6, location = $7, status = $8
  WHERE id = $9
";

$result = pg_query_params($conn, $query, [
  $name, $details, $category, $quantity, $isbn, $year, $location, $status, $id
]);

if ($result) {
  echo "success";
} else {
  echo "error";
}

pg_close($conn);
?>
