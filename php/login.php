<?php
session_start();
header("Content-Type: application/json");

// Connect to Supabase PostgreSQL
$conn = pg_connect("host=aws-0-eu-west-2.pooler.supabase.com port=5432 dbname=postgres user=postgres.dfyivadvrlpakujebnqf password=WjdiV/BVT2q8g2k");

if (!$conn) {
  http_response_code(500);
  echo "Database connection failed.";
  exit;
}

// Get POST data
$email     = $_POST['username'] ?? '';
$password  = $_POST['password'] ?? '';
$recaptcha = $_POST['g-recaptcha-response'] ?? '';

// === reCAPTCHA validation ===
$secretKey = 'your-recaptcha-secret-key';
$verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha";
$response = json_decode(file_get_contents($verifyUrl), true);

if (!$response || !$response['success']) {
  http_response_code(403);
  echo "reCAPTCHA verification failed.";
  exit;
}

// Validate input
if (!$email || !$password) {
  http_response_code(400);
  echo "Email and password required.";
  exit;
}

// Look up user
$result = pg_query_params($conn, "SELECT * FROM users WHERE email = $1 LIMIT 1", [$email]);

if (!$result || pg_num_rows($result) === 0) {
  http_response_code(401);
  echo "Invalid credentials.";
  pg_close($conn);
  exit;
}

$user = pg_fetch_assoc($result);

// Verify password and approval
if (!password_verify($password, $user['password'])) {
  http_response_code(401);
  echo "Incorrect password.";
} elseif ($user['status'] !== 'approved') {
  http_response_code(403);
  echo "Account not approved.";
} else {
  $_SESSION['user'] = $user['email'];
  $_SESSION['role'] = $user['role'];

  if ($user['role'] === 'staff') {
    echo "redirect:staff-dashboard.html";
  } else {
    echo "redirect:member-dashboard.html";
  }
}

pg_close($conn);
?>
