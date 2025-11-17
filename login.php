<?php
session_start();

include "background_fns/connection.php";
include "background_fns/functions.php";

// ensure valid token
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['token']) && ValidateToken($_POST['token'])) {
    // Process the login form submission
    $userEmail = trim($_POST['userEmail']);
    $userPassword = trim($_POST['userPassword']);

    $Error = "";

    if (!empty($userEmail) && !empty($userPassword)) {
        // Read from database using email
        $stmt = $con->prepare("SELECT * FROM Users WHERE userEmail = ? LIMIT 1");
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);

            if (password_verify($_POST['userPassword'], $user_data['userPassword'])) {
                // Store user data in session, proceed with login
                $_SESSION['userID'] = $user_data['userID'];
                // Redirect with login message and username
                header("Location: index.php?msg=login&user=" . urlencode($user_data['userName']));
                die;
            }
        }
        // If we reach here, the email or password is incorrect
        $Error = "Incorrect email or password";
    } else {
        $Error = "Invalid information, please try again";
    }
}

// Generate token
GenerateToken();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lexend Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showMessage(msg) {
            alert(msg);
        }
    </script>
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center login-container">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card card-shadow">
                <div class="card-body p-4">
                    <!-- Academic Notice -->
                    <div class="alert alert-warning text-center mb-4" role="alert">
                        <strong>Notice:</strong> This website is intended for showcasing the portfolio of Thomas Penny via the medium of a custom-made social media webiste. Please do not use real names, emails, or passwords.
                    </div>

                    <!-- PalPost title and logo side by side, centered -->
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <img src="Images/logo.png" alt="PalPost Logo" class="login-palpost-logo">
                        <span class="palpost-title-login">PalPost</span>
                    </div>
                    <h1 class="card-title text-center mb-4">Login to your account</h1>

                    <!-- Display success or error messages -->
                    <?php
                    if (isset($_GET['msg']) && $_GET['msg'] === 'success_user') {
                        echo '<div class="alert alert-success text-center" role="alert">
                            Account created successfully! You can now log in.
                        </div>';
                    }

                    if (isset($_GET['msg']) && $_GET['msg'] === 'success_admin') {
                        echo '<div class="alert alert-success text-center" role="alert">
                            Account created successfully! 
                            With great power, comes greater responsibility... 
                            You can now log in.
                        </div>';
                    }

                    if (!empty($Error)) {
                        echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($Error) . '</div>';
                    }
                    ?>
                    
                    <!-- Login Form -->
                    <form method="post">
                        <div class="mb-3">
                            <label for="textBoxUserEmail" class="form-label">Email:</label>
                            <input id="textBoxUserEmail" type="email" name="userEmail" class="form-control" required placeholder="Email:">
                        </div>
                        
                        <div class="mb-3">
                            <label for="textBoxPassword" class="form-label">Password:</label>
                            <input id="textBoxPassword" type="password" name="userPassword" class="form-control" required placeholder="Password:">
                        </div>
                        
                        <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <span>Don't have an account? </span>
                        <a href="user_signup.php" class="text-decoration-none">Register here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Check if the user has come to the page via logout.php, if so, display a message -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logout'): ?>
        <!-- Popup message informing user they have logged out -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showMessage('You have successfully logged out.');
                // Remove msg from URL so popup only shows once, not on refresh
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('msg');
                    window.history.replaceState({}, document.title, url.pathname + url.search);
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>