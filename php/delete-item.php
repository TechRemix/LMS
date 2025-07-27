<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Connect to PostgreSQL
  $conn = pg_connect("host=aws-0-eu-west-2.pooler.supabase.com port=5432 dbname=postgres user=postgres.dfyivadvrlpakujebnqf password=WjdiV/BVT2q8g2k");


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
