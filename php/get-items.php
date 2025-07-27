<?php
header("Content-Type: application/json");

// Connect to PostgreSQL
pg_connect("host=db.dfyivadvrlpakujebnqf.supabase.co port=5432 dbname=postgres user=postgres password=WjdiV/BVT2q8g2k");



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
