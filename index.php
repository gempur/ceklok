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
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(
                    function(position) {
                        $('#latitude').val(position.coords.latitude);
                        $('#longitude').val(position.coords.longitude);
                        $('.info').html("<p>Akses lokasi berhasil.</p>");
                    },
                    function(error) {
                        console.error("Error occurred. Error code: " + error.code);
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                $('.info').html("<p>User denied the request for Geolocation.</p>");
                                break;
                            case error.POSITION_UNAVAILABLE:
                                $('.info').html("<p>Location information is unavailable.</p>");
                                break;
                            case error.TIMEOUT:
                                $('.info').html("<p>The request to get user location timed out.</p>");
                                break;
                            case error.UNKNOWN_ERROR:
                                $('.info').html("<p>An unknown error occurred.</p>");
                                break;
                        }
                    },
                   
                );
            } else {
                $('.info').html("<p>Geolocation is not supported by this browser.</p>");
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
            <div class="input-group mb-3">
                <span class="input-group-text">Latitude</span>
                <input type="text" class="form-control" id="latitude" name="latitude" >
                <span class="input-group-text">Longitude</span>
                <input type="text" class="form-control" id="longitude" name="longitude" >
            </div>
        </div>
       
        <div>
            <label for="flags">Flags:</label>
            <select class="form-control" id="flags" name="flags">
                <option value="CheckIn" selected>Check In</option>
                <option value="CheckOut">Check Out</option>
                <option value="Jeda">Jeda</option>
            </select>
        </div>
        <div class="info"></div>
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