<?php
header("Content-Type: application/json");

// Connect to Supabase PostgreSQL using pg_connect
$conn = pg_connect("host=aws-0-eu-west-2.pooler.supabase.com port=5432 dbname=postgres user=postgres.dfyivadvrlpakujebnqf password=WjdiV/BVT2q8g2k");

if (!$conn) {
  http_response_code(500);
  echo json_encode(["error" => "Database connection failed."]);
  exit;
}

// Get POST data
$full_name = $_POST['full_name'] ?? '';
$email     = $_POST['email'] ?? '';
$password  = $_POST['password'] ?? '';
$job       = $_POST['job'] ?? '';
$address   = $_POST['address'] ?? '';
$phone     = $_POST['phone'] ?? '';
$reason    = $_POST['reason'] ?? '';

// Basic validation
if (!$full_name || !$email || !$password || !$job || !$address || !$phone || !$reason) {
  http_response_code(400);
  echo json_encode(["error" => "All fields are required."]);
  pg_close($conn);
  exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert into users table
$result = pg_query_params($conn, "
  INSERT INTO users (full_name, email, password, job, address, phone, reason, status, role)
  VALUES ($1, $2, $3, $4, $5, $6, $7, 'pending', 'pending')
", [
  $full_name, $email, $hashedPassword, $job, $address, $phone, $reason
]);

if ($result) {
  echo json_encode(["success" => true, "message" => "Membership request submitted."]);
} else {
  http_response_code(500);
  echo json_encode(["error" => "Database error: " . pg_last_error($conn)]);
}

pg_close($conn);
?>
