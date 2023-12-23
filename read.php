<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USER</title>
    <link rel="stylesheet" href="stylesTwo.css">
</head>
<body>

    <?php
        session_start();


        // Check if the session variable is set
        if (!isset($_SESSION['read_in']) || $_SESSION['read_in'] !== true) {
            header("Location: index.php"); // Redirect to the login page
            exit();
        }

        // Connect to the mySQL database
        $SERVER_NAME = "localhost";
        $USERNAME = "root";
        $PASSWORD = "";
        $DATABASE_NAME = "2pro";
        
        // Create connection
        $conn = new mysqli($SERVER_NAME, $USERNAME, $PASSWORD, $DATABASE_NAME);
        
        // Check connection
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM test";
        $result = $conn->query($sql);

        $class_saldo = 0;
        $whole_added = 0;
        $whole_taken = 0;
        $people = 0;

        if ($result->num_rows > 0) {
            // Output table header
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Imie</th><th>Nazwisko</th><th>Saldo</th><th>Wplacil</th><th>Wydal</th></tr>";
        
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                $saldo = $row["wplacil"] - $row["wydal"];

                echo "<tr>";
                echo "<td>".$row["id"]."</td>";
                echo "<td>".$row["imie"]."</td>";
                echo "<td>".$row["nazwisko"]."</td>";
                echo "<td>".$saldo."</td>";
                echo "<td>".$row["wplacil"]."</td>";
                echo "<td>".$row["wydal"]."</td>";
                echo "</tr>";

                $class_saldo += $saldo;
                $whole_added += $row["wplacil"];
                $whole_taken += $row["wydal"];
                $people++;
            }
        
            // Close the table
            echo "</table>";
            echo "<br><br>";
            echo "Saldo klasowe:".$class_saldo;
            echo "<br><br>";
            echo "Łączne wpłaty:".$whole_added;
            echo "<br><br>";
            echo "Łącznie wypłacono:".$whole_taken;
            echo "<br><br>";
        } else {
            echo "0 results. CONTACT ADMINISTRATOR";
        }

        
        $conn->close();

    ?>
</body>
</html>