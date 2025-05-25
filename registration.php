<?php
require_once('config.php');

$registrationSuccess = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone = mysqli_real_escape_string($db, $_POST['phone']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    // Optional: check if email already exists
    $checkEmail = mysqli_query($db, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($checkEmail) > 0) {
        $errorMessage = "Email is already registered!";
    } else {
        $query = "INSERT INTO users (firstname, lastname, email, phone, password) 
                  VALUES ('$firstname', '$lastname', '$email', '$phone', '$password')";
        if (mysqli_query($db, $query)) {
            $registrationSuccess = true;
        } else {
            $errorMessage = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/reg_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="registration-container">
        <h2>Register</h2>
        <p>Fill out the form below</p>
        <hr class="mb-3">

        <form method="POST" action="registration.php">
            <label for="firstname"><b>First Name</b></label>
            <input class="form-control mb-2" type="text" name="firstname" id="firstname" required>

            <label for="lastname"><b>Last Name</b></label>
            <input class="form-control mb-2" type="text" name="lastname" id="lastname" required>

            <label for="email"><b>Email</b></label>
            <input class="form-control mb-2" type="email" name="email" id="email" required>

            <label for="phone"><b>Phone</b></label>
            <input class="form-control mb-2" type="text" name="phone" id="phone" required>

            <label for="password"><b>Password</b></label>
            <input class="form-control mb-3" type="password" name="password" id="password" required>

            <input class="btn btn-primary" type="submit" value="Register">
        </form>

        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-danger mt-3"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php if ($registrationSuccess): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Registration Successful!',
        text: 'You can now log in.',
        confirmButtonText: 'Proceed to Login'
    }).then(() => {
        window.location.href = 'login.php';
    });
</script>
<?php endif; ?>

</body>
</html>
