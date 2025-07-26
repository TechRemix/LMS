<?php
// Try to connect using current app settings
$conn = pg_connect("dbname=neondb"); // You can leave it empty if using env vars

if (!$conn) {
    die("❌ Could not connect to database. It may be suspended or misconfigured.");
}

$res = pg_query($conn, "SELECT inet_server_addr(), current_database();");
$row = pg_fetch_row($res);

echo "✅ Connected to: <br>";
echo "🔹 IP/Host: <strong>$row[0]</strong><br>";
echo "🔹 Database: <strong>$row[1]</strong>";

pg_close($conn);
?>
