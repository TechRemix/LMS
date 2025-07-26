<?php
// Connect to PostgreSQL
pg_connect("host=ep-delicate-frog-abhvcfve-pooler.eu-west-2.aws.neon.tech dbname=neondb user=neondb_owner password=npg_9Cf7QdRgmcPB sslmode=require options='endpoint=ep-delicate-frog-abhvcfve'");


if (!$conn) {
  http_response_code(500);
  echo "Database connection failed";
  exit;
}

$id         = $_POST['id'];
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

$query = "
  UPDATE items 
  SET name = $1, details = $2, category = $3, quantity = $4, item_code = $5, year = $6, location = $7, status = $8
  WHERE id = $9
";

$result = pg_query_params($conn, $query, [
  $name, $details, $category, $quantity, $item_code, $year, $location, $status, $id
]);

if ($result) {
  echo "success";
} else {
  echo "error";
}

pg_close($conn);
?>
