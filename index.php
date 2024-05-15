<?php
require_once 'connection-folder/connection.php';

session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin-dashboard.php");
    exit();
} elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // $hashed_password = md5($password);
    //echo($hashed_password);
    // exit();

    try {
        $stmt = $conn->prepare("SELECT users.id, users.username, users.password, roles.role_name FROM users, roles WHERE users.role_id=roles.id and users.username=:username AND users.password=:password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $users = $user['password'];
        ///var_dump($user);
        //die();
        if ($user) {
            if ($password == $user['password']) {
                //if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role_name'];

                if ($user['role_name'] == 'admin') {
                    header("Location: admin-dashboard.php");
                    exit();
                } elseif ($user['role_name'] == 'user') {
                    header("Location: home.php");
                    exit();
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    } catch (PDOException $e) {
        $error = "Connection failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="log">
        <h2>Login</h2>
        <?php if (isset($error)) : ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Username</label><br>
            <input type="text" id="username" name="username"><br><br>
            <label for="password">Password</label><br>
            <input type="password" id="password" name="password"><br><br>
            <input type="submit" value="Login" class="Login">
        </form>
        <a href="registration.php" class="sig">Sign Up here!</a>
    </div>
</body>

</html>