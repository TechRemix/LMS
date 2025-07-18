<?php
header("Content-Type: application/json");

// Connect to MySQL
$mysqli = new mysqli("localhost", "root", "", "lms");

// Check for DB connection error
if ($mysqli->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Database connection failed"]);
  exit;
}

// Fetch the 5 most recent items
$result = $mysqli->query("SELECT * FROM items ORDER BY id DESC LIMIT 5");

$items = [];

while ($row = $result->fetch_assoc()) {
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

$mysqli->close();
?>
