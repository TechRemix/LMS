<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Connect to PostgreSQL
  $conn = pg_connect("host=dpg-d1tafq6mcj7s73d58avg-a dbname=lms_db_3hx1 user=lms_db_3hx1_user password=ImGmDcnvBzwU1ustoexBQSjvywKJmFsx port=5432");

  if (!$conn) {
    http_response_code(500);
    echo "Database connection failed";
    exit;
  }

  $id = $_POST['id'];

  $result = pg_query_params($conn, "DELETE FROM items WHERE id = $1", [$id]);

  if ($result) {
    echo "success";
  } else {
    echo "error";
  }

  pg_close($conn);
}
?>
