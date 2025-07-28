<?php
session_start();

// Database connection (Supabase credentials)
$host = 'your-supabase-host';
$db   = 'your-db-name';
$user = 'your-db-username';
$pass = 'your-db-password';
$port = '5432';

$conn = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get POST data
$email    = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$recaptcha = $_POST['g-recaptcha-response'] ?? '';

// === 1. Validate reCAPTCHA ===
$secretKey = 'your-secret-key';  // from Google reCAPTCHA dashboard
$verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha";
$response = json_decode(file_get_contents($verifyUrl));

if (!$response || !$response->success) {
  http_response_code(403);
  echo "reCAPTCHA verification failed.";
  exit;
}

// === 2. Validate form input ===
if (empty($email) || empty($password)) {
  http_response_code(400);
  echo "Email and password are required.";
  exit;
}

// === 3. Check user in database ===
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
  http_response_code(401);
  echo "User not found.";
  exit;
}

// === 4. Check password and status ===
if (!password_verify($password, $userData['password'])) {
  http_response_code(401);
  echo "Incorrect password.";
  exit;
}

if ($userData['status'] !== 'approved') {
  http_response_code(403);
  echo "Account not approved yet.";
  exit;
}

// === 5. Set session and redirect ===
$_SESSION['user'] = $userData['email'];
$_SESSION['role'] = $userData['role']; // staff or member

if ($userData['role'] === 'staff') {
  echo "redirect:staff-dashboard.html";
} else {
  echo "redirect:member-dashboard.html";
}
?>
