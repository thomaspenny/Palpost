<?php

session_start();

include "background_fns/connection.php";
include "background_fns/functions.php";

EnsureValidToken();

// Check if user is logged in
$user_data = CheckLogin($con);

$successMsg = "";
$errorMsg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Handle profile picture upload to server
    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
        $userID = $user_data['userID'];
        $fileTmpPath = $_FILES['profilePic']['tmp_name'];
        $fileName = basename($_FILES['profilePic']['name']);
        $fileType = mime_content_type($fileTmpPath);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = __DIR__ . '/uploads/profiles/';
            // Unique file name to avoid collisions
            $newFileName = 'user_' . $userID . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . $newFileName;
            $relativePath = 'uploads/profiles/' . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Update DB with path and type
                $stmtImg = $con->prepare("UPDATE Users SET userImagePath = ?, userImageType = ? WHERE userID = ?");
                $stmtImg->bind_param("ssi", $relativePath, $fileType, $userID);
                if ($stmtImg->execute()) {
                    $successMsg = "Profile picture uploaded";
                    $user_data = CheckLogin($con);
                } else {
                    $errorMsg = "Failed to update profile picture in database.";
                }
                $stmtImg->close();
            } else {
                $errorMsg = "Failed to move uploaded file.";
            }
        } else {
            $errorMsg = "Invalid image type. Only JPG, PNG, and GIF allowed.";
        }
    }

    // Update bio only
    $userBio = isset($_POST['userBio']) ? SanitiseInput($_POST['userBio']) : '';
    $userID = $user_data['userID'];
    $stmt = $con->prepare("UPDATE Users SET userBio = ? WHERE userID = ?");
    $stmt->bind_param("si", $userBio, $userID);

    if ($stmt->execute()) {
        if (!$successMsg) $successMsg = "Settings updated successfully";
        $user_data = CheckLogin($con);
    } else {
        $errorMsg = "Failed to update settings. Please try again.";
    }
    $stmt->close();
}

GenerateToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Settings</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lexend Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light-subtle">
    <!-- Blue banner with Home, search, settings, and logout -->
    <?php include "banner.php"; ?>

    <h1 class="visually-hidden">Settings Page</h1>

    <div class="container d-flex justify-content-center align-items-center settings-container">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card card-shadow">
                <div class="card-body p-4">
                    <h2 class="card-title text-center mb-4">User Settings</h2>

                    <!-- error and success messages -->
                    <?php if ($successMsg): ?>
                        <div class="alert alert-success text-center"><?= htmlspecialchars($successMsg) ?></div>
                    <?php endif; ?>
                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMsg) ?></div>
                    <?php endif; ?>

                    <!-- Profile Picture Upload Section -->
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">
                        <div class="mb-3">
                            <label for="profilePic" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profilePic" name="profilePic" accept="image/*">
                            <div class="mt-2">
                                <img src="<?= htmlspecialchars($user_data['userImagePath']) ?>" alt="Profile Picture" class="profile-picture-preview">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="userBio" class="form-label">Bio</label>
                            <textarea id="userBio" name="userBio" class="form-control" rows="4" maxlength="500"><?= isset($user_data['userBio']) ? stripslashes($user_data['userBio']) : '' ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg btn-lg-settings">Save Settings</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <a href="index.php" class="text-decoration-none">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- include search functionality -->
    <script src="background_fns/search.js"></script>
</body>
</html>