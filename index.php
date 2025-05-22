<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'ceklok';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceklok!</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(
                    function(position) {
                        $('#latitude').val(position.coords.latitude);
                        $('#longitude').val(position.coords.longitude);
                    },
                    function(error) {
                        console.error("Error occurred. Error code: " + error.code);
                    },
                   
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
       
    </script>
</head>
<body onload="getLocation()">
<div class="container">
    <h1>Check-In</h1>
    <p>Silakan isi data berikut untuk melakukan check-in.</p>
    <p>Pastikan lokasi Anda sudah aktif.</p>
    <div class='center-align'>
    <form method="post" action="">
        <div>
            <label for="latitude">Latitude:</label>
            <input type="text" id="latitude" name="latitude" required>
        </div>
        <div>
            <label for="longitude">Longitude:</label>
            <input type="text" id="longitude" name="longitude" required>
        </div>
        <div>
            <label for="flags">Flags:</label>
            <select id="flags" name="flags">
                <option value="0" selected>Check In</option>
                <option value="1">Check Out</option>
                <option value="2">Jeda</option>
            </select>
        </div>
        <button type="submit" name="checkin">Check In</button>
    </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin'])) {
        $latitude = $conn->real_escape_string($_POST['latitude']);
        $longitude = $conn->real_escape_string($_POST['longitude']);
        $flags = $conn->real_escape_string($_POST['flags']);
        $user_agent = $conn->real_escape_string($_SERVER['HTTP_USER_AGENT']);

        $sql = "INSERT INTO marking (latitude, longitude, user_agent, flags) VALUES ('$latitude', '$longitude', '$user_agent', '$flags')";
        if ($conn->query($sql) === TRUE) {
            echo "<p>Check-in berhasil!</p>";
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }
    }
    ?>

    <h1>Daftar Check-In</h1>
    <table cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Timestamp</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>User Agent</th>
            <th>Flags</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM marking ORDER BY timestamp DESC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
                echo "<td>" . htmlspecialchars($row['latitude']) . "</td>";
                echo "<td>" . htmlspecialchars($row['longitude']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_agent']) . "</td>";
                echo "<td>" . htmlspecialchars($row['flags']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Belum ada data check-in.</td></tr>";
        }
        ?>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>