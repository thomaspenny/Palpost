<?php

session_start();

include "background_fns/connection.php";
include "background_fns/functions.php";

EnsureValidToken();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Set userRank to 2 for regular users
    $userRank = 2;
    $userName = $_POST['userName'];
    $userEmail = $_POST['userEmail'];
    $userPassword = $_POST['userPassword'];
    $userPasswordConfirm = $_POST['userPasswordConfirm'];

    $Error = "";

    // login validation
    if (!preg_match("/^[\w\-.]+@[\w\-.]+\.[\w\-]+$/", $userEmail)) {
        $Error = "Please enter a valid email";
    } else if (!preg_match("/^[a-zA-Z0-9 ]+$/", $userName)) {
        $Error = "Username can only contain letters, numbers, and spaces";
    } else if (strlen($userPassword) < 8) {
        $Error = "Password must be at least 8 characters long";
    } else if ($userPassword !== $userPasswordConfirm) {
        $Error = "Passwords do not match";
    }
    
    // Only proceed if no errors
    if ($Error == "") {
        // Sanitise input
        $userName = SanitiseInput($userName);
        $userEmail = SanitiseInput($userEmail);

        $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);

        if ($userName !== "" && $userEmail !== "" && $userPassword !== "" && is_numeric($userRank)) {
            // Check if email already exists
            $stmt = $con->prepare("SELECT userID FROM Users WHERE userEmail = ? LIMIT 1");
            $stmt->bind_param("s", $userEmail);
            $stmt->execute();
            $check_result = $stmt->get_result();

            if ($check_result === false) {
                $Error = "Database error: " . mysqli_error($con);
            } else if (mysqli_num_rows($check_result) > 0) {
                $Error = "Email already registered. Please use a different email!";
            } else {
                // Set default image path and type
                $userImagePath = "uploads/profiles/default.png";
                $userImageType = "image/png";

                // Use InsertNewUser function
                $insertSuccess = InsertNewUser($con, $userRank, $userName, $userEmail, $userPassword, $userImagePath, $userImageType);

                // Log the user data for debugging
                error_log(print_r([$userRank, $userName, $userEmail, $userPassword, $userImagePath, $userImageType], true));

                if (!$insertSuccess) {
                    $Error = "Query unsuccessful!";
                } else {
                    header("Location: login.php?msg=success_user");
                    exit();
                }
            }
        } else {
            $Error = "Please enter some valid information";
        }
    }
}

GenerateToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lexend Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center signup-container">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card card-shadow">
                <div class="card-body p-4">
                    <!-- Logo and Title -->
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <img src="Images/logo.png" alt="PalPost Logo" class="signup-palpost-logo me-2">
                        <h1 class="card-title text-center mb-0">Create User Account</h1>
                    </div>

                    <!-- Display error message -->
                    <?php if (!empty($Error)): ?>
                        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($Error) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">
                        <input type="hidden" name="userRank" value="2">
                        <div class="mb-3">
                            <label for="textBoxUserName" class="form-label">Create Username:</label>
                            <input id="textBoxUserName" type="text" name="userName" class="form-control" required autocomplete="username" placeholder="Username:">
                        </div>
                        <div class="mb-3">
                            <label for="textBoxUserEmail" class="form-label">Email:</label>
                            <input id="textBoxUserEmail" type="email" name="userEmail" class="form-control" required autocomplete="email" placeholder="Email:">
                        </div>
                        <div class="mb-3">
                            <label for="textBoxUserPassword" class="form-label">Create Password:</label>
                            <input id="textBoxUserPassword" type="password" name="userPassword" class="form-control" required autocomplete="new-password" placeholder="Create Password:">
                        </div>
                        <div class="mb-3">
                            <label for="textBoxUserPasswordConfirm" class="form-label">Re-enter Password:</label>
                            <input id="textBoxUserPasswordConfirm" type="password" name="userPasswordConfirm" class="form-control" required autocomplete="new-password" placeholder="Re-enter Password:">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg btn-lg-signup">Sign Up</button>
                        </div>
                    </form>

                    <!-- Links for admin signup and returning to login -->
                    <div class="text-center mt-4">
                        <div class="mb-2">
                            <span>Admin User?</span>
                            <a href="admin_signup.php" class="text-decoration-none">Create Admin Account</a>
                        </div>
                        <div>
                            <span>Already have an account? </span>
                            <a href="login.php" class="text-decoration-none">Return to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>