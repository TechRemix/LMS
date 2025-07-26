<?php
header("Content-Type: application/json");

// Connect to PostgreSQL
pg_connect("host=ep-delicate-frog-abhvcfve-pooler.eu-west-2.aws.neon.tech dbname=neondb user=neondb_owner password=npg_9Cf7QdRgmcPB sslmode=require options='endpoint=ep-delicate-frog-abhvcfve'");


if (!$conn) {
  http_response_code(500);
  echo json_encode(["error" => "Database connection failed"]);
  exit;
}

// Fetch the 5 most recent items
$result = pg_query($conn, "SELECT * FROM items ORDER BY id DESC LIMIT 5");

$items = [];

while ($row = pg_fetch_assoc($result)) {
  // Format last_update to British format
  if (!empty($row['last_update'])) {
    $row['last_update'] = date("d/m/Y", strtotime($row['last_update']));
  } else {
    $row['last_update'] = "";
  }

  $items[] = $row;
}

// Output as JSON
echo json_encode($items);

pg_close($conn);
?>
