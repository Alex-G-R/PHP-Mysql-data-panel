<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGOWANIE</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1 class="box main-text">Podaj hasło aby zalogować się do panelu</h1>
    </div>
    <div class="container">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="password" class="password-input" name="f_password" id="i_password" placeholder="Wpisz hasło: ">
            <button type="submit" class="submit-button">OK</button>
        </form>
    </div>
    <?php
        session_start();
        $_SESSION['logged_in'] = false;
        $_SESSION['read_in'] = false;

        $SERVICE_PASSWORD = "admin";
        $READ_ONLY_PASSWORD = "user";

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $ppswd = $_POST['f_password'];
            if(empty($ppswd)){
                header("Location: index.php");
                exit();
            } else if ($ppswd == $READ_ONLY_PASSWORD){
                // Password is correct, set the session variable
                $_SESSION['read_in'] = true;
                header("Location: read.php");
                exit();    
            } else if ($ppswd == $SERVICE_PASSWORD){
                // Password is correct, set the session variable
                $_SESSION['logged_in'] = true;
                header("Location: main.php");
                exit();
            } else if ($ppswd != $SERVICE_PASSWORD){    
                header("Location: index.php");
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        }

    ?>
</body>
</html>