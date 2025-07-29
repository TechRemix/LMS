<?php
header("Content-Type: application/json");

// Connect to PostgreSQL
$conn = pg_connect("host=aws-0-eu-west-2.pooler.supabase.com port=5432 dbname=postgres user=postgres.dfyivadvrlpakujebnqf password=WjdiV/BVT2q8g2k");

if (!$conn) {
  http_response_code(500);
  echo json_encode(["error" => "Database connection failed"]);
  exit;
}

// Fetch all users (excluding deleted if needed)
$query = "SELECT id, full_name, email, address, phone, role, status, created_at FROM users ORDER BY created_at DESC";
$result = pg_query($conn, $query);

$users = [];

while ($row = pg_fetch_assoc($result)) {
  $users[] = $row;
}

echo json_encode($users);
pg_close($conn);
?>
