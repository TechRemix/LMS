<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Connect to PostgreSQL
  pg_connect("host=db.dfyivadvrlpakujebnqf.supabase.co port=5432 dbname=postgres user=postgres password=WjdiV/BVT2q8g2k");

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
