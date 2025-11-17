<?php

// functions similarly to user_signup.php but for creating an admin account

session_start();

include "background_fns/connection.php";
include "background_fns/functions.php";

// Force HTTPS
ForceHTTPS();

EnsureValidToken();

// Admin creation logic
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Set userRank to 1 for admins
    $userRank = 1;
    $userName = trim($_POST['userName']);
    $userEmail = trim($_POST['userEmail']);
    $userPassword = trim($_POST['userPassword']);
    $userPasswordConfirm = trim($_POST['userPasswordConfirm']);

    // Credentials of existing admin
    $adminEmail = trim($_POST['adminEmail']);
    $adminPassword = trim($_POST['adminPassword']);

    $Error = "";

    // Verify admin credentials
    if (empty($adminEmail) || empty($adminPassword)) {
        $Error = "Admin email and password are required to create a new admin user.";
    } else {
        // Check if admin exists and has rank 1
        $stmt = $con->prepare("SELECT * FROM Users WHERE userEmail = ? AND userRank = 1 LIMIT 1");
        $stmt->bind_param("s", $adminEmail);
        $stmt->execute();
        $admin_result = $stmt->get_result();

        if ($admin_result && mysqli_num_rows($admin_result) > 0) {
            $admin_data = mysqli_fetch_assoc($admin_result);
            if (!password_verify($adminPassword, $admin_data['userPassword'])) {
                $Error = "Admin credentials are incorrect.";
            }
        } else {
            $Error = "Admin credentials are incorrect or user is not an admin.";
        }
    }

    // Only proceed with new user creation if no admin error
    if ($Error == "") {
        // Email validation
        if (!preg_match("/^[\w\-.]+@[\w\-.]+\.[\w\-]+$/", $userEmail)) {
            $Error = "Please enter a valid email";
        } else if (!preg_match("/^[a-zA-Z0-9_]+$/", $userName)) {
            $Error = "Username can only contain letters, numbers, and underscores";
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

            // Set default image path and type
            $userImagePath = "uploads/profiles/default.png";
            $userImageType = "image/png";

            if ($userName !== "" && $userEmail !== "" && $userPassword !== "" && is_numeric($userRank)) {
                // Check if username already exists
                $stmt = $con->prepare("SELECT userID FROM Users WHERE userName = ? LIMIT 1");
                $stmt->bind_param("s", $userName);
                $stmt->execute();
                $check_result = $stmt->get_result();

                if (mysqli_num_rows($check_result) > 0) {
                    $Error = "Username already taken. Please choose a different username.";
                } else {
                    // Use InsertNewUser function
                    $insertSuccess = InsertNewUser($con, $userRank, $userName, $userEmail, $userPassword, $userImagePath, $userImageType);

                    if (!$insertSuccess) {
                        $Error = "Query unsuccessful!";
                    } else {
                        header("Location: login.php?msg=success_admin");
                        exit();
                    }
                }
            } else {
                $Error = "Please enter some valid information";
            }
        }
    }
    // Regenerate token after processing form
    GenerateToken();
}

// get default image to use for new admin
$defaultImagePath = "uploads/profiles/default.png";
$userImage = file_get_contents($defaultImagePath);
$userImageType = mime_content_type($defaultImagePath);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Sign Up Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lexend Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="style.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center signup-container">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card card-shadow">
                <div class="card-body card-body-custom">
                    <!-- Logo and Title -->
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <img src="Images/logo.png" alt="PalPost Logo" class="signup-palpost-logo me-2">
                        <h1 class="card-title text-center mb-0">Create Admin Account</h1>
                    </div>
                    <p class="text-center text-muted mb-4">Requires existing admin authorization</p>

                    <?php
                    // Display error message if any
                    if (!empty($Error)) {
                        echo '<div class="error-message">' . htmlspecialchars($Error) . '</div>';
                    }
                    ?>
                    
                    <form method="post">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">
                        <input type="hidden" name="userRank" value="1">
                        <!-- New Admin Details -->
                        <h5 class="mb-3">New Admin Details</h5>   
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
                        <div class="mb-4">
                            <label for="textBoxUserPasswordConfirm" class="form-label">Re-enter Password:</label>
                            <input id="textBoxUserPasswordConfirm" type="password" name="userPasswordConfirm" class="form-control" required autocomplete="new-password" placeholder="Re-enter Password:">
                        </div>
                        <hr class="my-4">
                        <!-- Existing admin must authorize -->
                        <h5 class="mb-3">Admin Authorization</h5>                    
                        <div class="mb-3">
                            <label for="adminEmail" class="form-label">Admin Email:</label>
                            <input id="adminEmail" type="email" name="adminEmail" class="form-control" required autocomplete="email" placeholder="Admin Email:">
                        </div>
                        <div class="mb-4">
                            <label for="adminPassword" class="form-label">Admin Password:</label>
                            <input id="adminPassword" type="password" name="adminPassword" class="form-control" required autocomplete="current-password" placeholder="Admin Password:">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg btn-lg-custom">Create Admin Account</button>
                        </div>
                    </form>
                    <!-- links for other account types -->
                    <div class="text-center mt-4">
                        <div class="mb-2">
                            <span>Not an Admin?</span>
                            <a href="user_signup.php" class="text-decoration-none">Create User Account</a>
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