<?php
    session_start();
    session_unset();
    session_destroy();
    session_start();
    if (isset($_SESSION["user"])) 
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
    <title>Registration Form</title>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST["submit"])) {
            $fullName = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $repeatPassword = $_POST["repeat_password"];
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $errors = array();

            if (empty($fullName) || empty($email) || empty($password) || empty($repeatPassword)) {
                array_push($errors, "All fields are mandatory.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Invalid email entered.");
            }

            if (strlen($password) < 8) {
                array_push($errors, "Password must contain at least 8 characters.");
            }

            if ($password !== $repeatPassword) {
                array_push($errors, "Passwords do not match.");
            }

            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = mysqli_num_rows($result);

            if ($rowCount > 0) {
                array_push($errors, "Email already exists.");
            }

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You have been successfully registered.</div>";
                } else {
                    die("Something went wrong.");
                }
            }
        }
        ?>
        <form action="registration.php" method="post">
            <h1 class="heading"><b>REGISTRATION FORM</b></h1>
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name">
            </div><br>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="E-Mail">
            </div><br>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div><br>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div><br>
            <div class="form-btn text-center">
                <button type="submit" class="btn btn-primary" value="Submit" name="submit">Submit</button>
            </div>
        </form>
        <div class="text-center">
            <p>Already registered? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
