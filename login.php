<?php
require_once('config.php');
session_start();

$loginSuccess = false;
$errorMessage = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    //collecting data what user is giving in login form
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    //checking if user exists in users table
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result = mysqli_query($db, $sql);

    //if any user was found
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);  // fetch the user row
        $_SESSION['id'] = $row['id'];       // store user ID
        $loginSuccess = true;
    } else {
        $errorMessage = "Invalid email or password!";
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="login-container">
        <h2>Login</h2>
        <p>Enter your credentials below</p>
        <hr class="mb-3">

        <form method="POST" action="login.php">
            <label for="email"><b>Email</b></label>
            <input class="form-control mb-3" type="email" name="email" id="email" required>

            <label for="password"><b>Password</b></label>
            <input class="form-control mb-3" type="password" name="password" id="password" required>

            <input class="btn btn-primary" type="submit" value="Login">
        </form>

        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-danger mt-3"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <p class="mt-3">Don't have an account? <a href="registration.php">Register here</a></p>
    </div>
</div>

<?php if ($loginSuccess): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Successful!',
        text: 'Welcome back!',
        confirmButtonText: 'Continue'
    }).then(() => {
        window.location.href = 'nav_bar.php'; 
    });
</script>
<?php endif; ?>

</body>
</html>
