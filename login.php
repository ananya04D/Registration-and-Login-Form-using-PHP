<?php
    session_start();
    if(isset($_SESSION["user"])) 
    {
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>Login Form</title>
</head>
<body>
    <div class="container">
        <?php
            if(isset($_POST["login"]))
            {
                $email = $_POST["email"];
                $password = $_POST["password"];
                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                if($user)
                {
                    if(password_verify($password, $user["password"]))
                    {
                        $_SESSION["user"] = "yes";
                        header("Location: index.php");
                        exit();
                    }
                    else
                    {
                        echo "<div class='alert alert-danger'>Password does not match.</div>";
                    }
                }
                else
                {
                    echo "<div class='alert alert-danger'>Email is not registered.</div>";
                }
            }
        ?>
        <form action="login.php" method="post">
        <h1 class="heading"><b>LOGIN</b></h1>
            <div class="form-group">
                <input type="email" placeholder="Enter email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter password" name="password" class="form-control" required>
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
        </form>
        <div>
            <p>Not registered? <a href="registration.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
