<?php 
    session_start();
    include('db/connection.php');

    $error = "";

    if (isset($_POST['login-btn'])){
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if(!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 1){
            $row = $result->fetch_assoc();

            if(password_verify($password, $row['password'])){
                $updatestmt = $conn->prepare("Update users SET last_login = NOW() where id =?");
                $updatestmt->bind_param("i", $row['id']);
                $updatestmt->execute();
                 $updatestmt->close();

                if($row['user_type'] === 'admin'){

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                header("Location: index.php");
                exit();
            }
            } else {
                $error = "invalid password";
            }  

        } else {
            $error = "No user found with this email.";
        }
        $stmt->close();
        } else {
            $error = "Fill the required fields.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <?php include 'includes/header.php'; ?>

    <div class="login-container">
        <form method="post" action="login.php">
            <h2>Login</h2>
            <?php
                if (isset($_SESSION['message'])) {
                    echo "<div class='message'>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']);
                }
            ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <input type="submit" name="login-btn" value="Login" class="btn">

            <p class="signup-link">Don’t have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>