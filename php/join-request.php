<?php
header("Content-Type: application/json");

// Connect to Supabase PostgreSQL
$host = 'aws-0-eu-west-2.pooler.supabase.com';
$db   = 'postgres';
$user = 'postgres.dfyivadvrlpakujebnqf';
$pass = 'WjdiV/BVT2q8g2k';
$port = '5432';

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table with status 'pending' and role 'pending'
    $stmt = $conn->prepare("
        INSERT INTO users (full_name, email, password, job, address, phone, reason, status, role)
        VALUES (:full_name, :email, :password, :job, :address, :phone, :reason, 'pending', 'pending')
    ");

    $stmt->execute([
        ':full_name' => $full_name,
        ':email'     => $email,
        ':password'  => $hashedPassword,
        ':job'       => $job,
        ':address'   => $address,
        ':phone'     => $phone,
        ':reason'    => $reason
    ]);

    echo json_encode(["success" => true, "message" => "Membership request submitted."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}
?>
