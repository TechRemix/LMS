<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Connect to PostgreSQL
  pg_connect("host=ep-delicate-frog-abhvcfve-pooler.eu-west-2.aws.neon.tech dbname=neondb user=neondb_owner password=npg_9Cf7QdRgmcPB sslmode=require options='endpoint=ep-delicate-frog-abhvcfve'");


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
