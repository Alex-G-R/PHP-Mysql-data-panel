<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMINISTRATOR</title>
    <link rel="stylesheet" href="stylesTwo.css">
</head>
<body>
    <br>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="container">
            Odejmij od całej klasy:<div class="box"><input type="checkbox" name="cala_klasa" ></div>
        </div>
        <br>
        <div class="container">
            Wyplata:<div class="box"><input type="checkbox" name="wyplacil"></div>
        </div>
        <br>
        <div class="container">
            Wplata:<div class="box"><input type="checkbox" name="wplacil" ></div>
        </div>
        <br>
        <div class="container">
            RESET WPŁAT WYPŁAT I SALDA:<div class="box"><input type="checkbox" name="reset_statystyk" id="stat_reset"></div>
        </div>
        <br>
        <div class="container">
            Suma: <div class="box"><input type="double" name="suma"></div>
        </div>
        <br>
        <div class="container">
            ID: <div class="box"><input type="number" name="p_id"></div>
        </div>
        <br>
        <div class="container">
            WIELE ID: <div class="box"><input type="text" name="wiele_id"> <span id="id_help">tutorial(kliknij)</span></div>
        </div>
        <br><br>
        <div class="contianer">
            <div class="box"><button type="submit">OK</button></div>
        </div>
    </form>

    <?php
        session_start();


        // Check if the session variable is set
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
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


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if the keys exist in the $_POST array
            $wplata_status = isset($_POST["wplacil"]) ? 1 : null;
            $wyplata_status = isset($_POST["wyplacil"]) ? 1 : null;
            $cala_klasa_status = isset($_POST["cala_klasa"]) ? 1 : null;
            $suma = isset($_POST['suma']) ? $_POST['suma'] : null;
            $uczen_id = isset($_POST['p_id']) ? $_POST['p_id'] : null;
            $stat_reset_status = isset($_POST['reset_statystyk']) ? 1 : null;
        
            if ($cala_klasa_status == 1) {
                // Check if $people is defined before using it
                if (isset($people) && $people != 0) {
                    // Calculate individual expense if the entire class is selected
                    $indywidualny_wydatek = $suma / $people;

                    $sql = "UPDATE test SET wydal = wydal + $indywidualny_wydatek";
                    // Execute the query
                    if ($conn->query($sql) === TRUE) {
                        header("Location: main.php");
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                } else {
                    echo "Error: Invalid value for $people.";
                }
            }

            if($cala_klasa_status != 1 && $wplata_status == 1){
                // Check if $people is defined before using it
                if (isset($people) && $people != 0) {
                    $sql = "UPDATE test SET wplacil = wplacil + $suma WHERE id = $uczen_id";
                    // Execute the query
                    if ($conn->query($sql) === TRUE) {
                        header("Location: main.php");
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                } else {
                    echo "Error: Invalid value for $people.";
                }
            }

            if($cala_klasa_status != 1 && $wyplata_status == 1 && $wplata_status != 1 && empty($_POST['wiele_id']) ){
                // Check if $people is defined before using it
                if (isset($people) && $people != 0) {
                    $sql = "UPDATE test SET wydal = wydal + $suma WHERE id = $uczen_id";
                    // Execute the query
                    if ($conn->query($sql) === TRUE) {
                        header("Location: main.php");
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                } else {
                    echo "Error: Invalid value for $people.";
                }
            }

            if($cala_klasa_status != 1 && $wyplata_status != 1 && $wplata_status != 1 && $stat_reset_status == 1){
                // Check if $people is defined before using it
                if (isset($people) && $people != 0) {
                    $sql1 = "UPDATE test SET wydal = 0 WHERE id = $uczen_id";
                    $sql2 = "UPDATE test SET wplacil = 0 WHERE id = $uczen_id";
                    // Execute the query
                    if ($conn->query($sql1) === TRUE) {
                        if ($conn->query($sql2) === TRUE) {
                            header("Location: main.php");
                        } else {
                            echo "Error updating record: " . $conn->error;
                        }
                        header("Location: main.php");
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                } else {
                    echo "Error: Invalid value for $people.";
                }
            }

            if($cala_klasa_status != 1 && $wyplata_status == 1 && $wplata_status != 1 && $stat_reset_status != 1 && !empty($_POST['wiele_id'])){
                $string = $_POST['wiele_id'];
                $id_array = extractNumbers($string);
                $length = count($id_array);

                $suma_inwidywidualna = $suma / $length;

                foreach($id_array as $id_ucznia){
                    $sql = "UPDATE test SET wydal = wydal + $suma_inwidywidualna WHERE id = $id_ucznia";
                    if ($conn->query($sql) === TRUE) {
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                }
                header("Location: main.php");
            }
        }

        
        $conn->close();


        function extractNumbers($inputString) {
            // Split the input string by comma
            $numberStrings = explode(',', $inputString);
        
            // Convert each string to an integer
            $numbers = array_map('intval', $numberStrings);
        
            return $numbers;
        }

    ?>

    <script defer>
        const reset_alert_trigger =document.getElementById("stat_reset");

        reset_alert_trigger.addEventListener("click", () => {
            alert("Zaznaczając opcje RESET należy ODZNACZYĆ wszystko inne i pozostawić pustą sume, nic się nie stanie jeżeli tego nie zrobisz ale lepiej nie ryzykować jakiegoś blędu. Wypełij pole ID ucznia którego WPŁATY WYPŁATY I SALDO ma zostać zmienione na ZERO. Używać tylko w sytuacjach takich jak bląd wpisania, podwójne zaliczenie wpłaty lub wypłaty i inne.")
        });

        const id_pomoc_trigger =document.getElementById("id_help");

        id_pomoc_trigger.addEventListener("click", () => {
            alert("Opcja WIELE ID pozwala ci na rozdzielenie sumy na konkretne ID uczniów, w sume podajesz ile łącznie wydali a w wiele id kolejno id rozdzielone przeninkami (,) np. Suma = 405, Wiele id: 2,3,4,6,15,16,12,1,19,23 - bez żadnych spacji, tylko przecinki. WAŻNE ABY POLE ID BYŁO PUSTE. Zaznaczone zostaje wypłata, podana jest lączna suma którą program podzieli po te osoby po równo i podane są id uczniow po przecinku bez zadnych dodatkowych znakow")
        });
    </script>
</body>
</html>