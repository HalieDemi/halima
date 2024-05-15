<?php
require_once 'connection-folder/connection.php';

$username = $password = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    // $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $password) {
        $error = "invalid password.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "Username already exists. Please choose a different username.";
            } else {
                // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, role_id) VALUES (:username, :password, 2)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                if ($stmt->execute()) {
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <div class="log">
        <h2>User Registration</h2>
        <?php if (!empty($error)) : ?>
            <p style="color:red;"> <?php echo $error; ?> </p>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Username</label><br>
            <input type="text" id="username" name="username" placeholder="Enter Username" required value="<?php echo htmlspecialchars($username); ?>"><br><br>

            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" required placeholder="Enter Password" class="Login"><br>

            <!-- <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password"> -->

            <input type="submit" value="register">
        </form>

        <a href="index.php" class="sig">Login here!</a>
    </div>
</body>

</html>