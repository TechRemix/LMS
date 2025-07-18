<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $mysqli = new mysqli("localhost", "root", "", "lms");

  if ($mysqli->connect_error) {
    http_response_code(500);
    echo "Database connection failed";
    exit;
  }

  $id = $_POST['id'];

  $stmt = $mysqli->prepare("DELETE FROM items WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "error";
  }

  $stmt->close();
  $mysqli->close();
}
?>
