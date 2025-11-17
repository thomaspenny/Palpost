<?php
session_start();
include "background_fns/connection.php";
include "background_fns/functions.php";

EnsureValidToken();

$user_data = CheckLogin($con);

// Initialize variables
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $textContent = trim($_POST['TextContent'] ?? '');
    $mediaFiles = $_FILES['mediaContent'] ?? null;
    $mediaCaptions = $_POST['mediaCaption'] ?? [];

    if (empty($textContent)) {
        $errors[] = "Post text cannot be empty.";
    }

    // Validate file count
    $fileCount = isset($mediaFiles['name']) ? count(array_filter($mediaFiles['name'])) : 0;
    if ($fileCount > 5) {
        $errors[] = "You can upload up to 5 files only.";
    }

    // Validate file types and sizes
    $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 100 * 1024 * 1024; // 100 MB

    if (empty($errors)) {
        // Insert post into database
        $stmt = $con->prepare("INSERT INTO Posts (userID, TextContent) VALUES (?, ?)");
        $stmt->bind_param("is", $user_data['userID'], $textContent);
        $stmt->execute();
        $postID = $stmt->insert_id;
        $stmt->close();

        // Handle media uploads
        if ($fileCount > 0) {
            $uploadDir = __DIR__ . '/uploads/post_content/';
            for ($i = 0; $i < $fileCount; $i++) {
                if (!empty($mediaFiles['tmp_name'][$i])) {
                    $fileTmp = $mediaFiles['tmp_name'][$i];
                    $fileType = $mediaFiles['type'][$i];
                    $caption = trim($mediaCaptions[$i] ?? '');

                    // Validate file type and size
                    if (!in_array($fileType, $allowedTypes)) {
                        $errors[] = "Only JPG, PNG, and GIF images are allowed.";
                        continue;
                    }
                    if (filesize($fileTmp) > $maxFileSize) {
                        $errors[] = "Each file must be less than 100MB.";
                        continue;
                    }

                    // Generate unique file name
                    $ext = pathinfo($mediaFiles['name'][$i], PATHINFO_EXTENSION);
                    $newFileName = 'post_' . $postID . '_' . time() . '_' . $i . '.' . $ext;
                    $destPath = $uploadDir . $newFileName;
                    $relativePath = 'uploads/post_content/' . $newFileName;

                    if (move_uploaded_file($fileTmp, $destPath)) {
                        // Store path and type in DB
                        $stmt = $con->prepare("INSERT INTO Media (postID, mediaPath, mediaType, mediaCaption) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("isss", $postID, $relativePath, $fileType, $caption);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $errors[] = "Failed to upload file: " . htmlspecialchars($mediaFiles['name'][$i]);
                    }
                }
            }
        }
        $success = true;
        // Redirect to index with success message
        header("Location: index.php?msg=post_success");
        exit;
    }
}

GenerateToken();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>New Post</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>    
    <?php include "banner.php"; ?>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-7 col-xl-6">
                <div class="card card-shadow">
                    <div class="card-body post-form-container">
                        <h1 class="mb-4 text-center">Create a New Post</h1>
                        <!-- Display success message or errors -->
                        <?php if ($success): ?>
                            <div class="alert alert-success text-center">Post created successfully!</div>
                        <?php elseif (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?= implode('<br>', $errors) ?>
                            </div>
                        <?php endif; ?>

                        <!-- New post inputs -->
                        <form method="post" enctype="multipart/form-data" id="postForm">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">
                            
                            <!-- Post Text -->
                            <div class="mb-3">
                                <label for="TextContent" class="form-label">Post Text</label>
                                <textarea class="form-control" id="TextContent" name="TextContent" rows="3" maxlength="300" required><?= htmlspecialchars($_POST['TextContent'] ?? '') ?></textarea>
                                <div class="form-text">Max 300 characters.</div>
                            </div>

                            <!-- File Uploads -->
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <div class="file-upload">
                                    <label for="mediaContent_<?= $i ?>" class="form-label">Upload Image <?= $i + 1 ?></label>
                                    <input class="form-control" type="file" id="mediaContent_<?= $i ?>" name="mediaContent[]" accept="image/*" <?= $i === 0 ? 'required' : '' ?>>
                                    <label for="mediaCaption_<?= $i ?>" class="form-label caption-input">Caption for Image <?= $i + 1 ?></label>
                                    <input class="form-control" type="text" id="mediaCaption_<?= $i ?>" name="mediaCaption[]" placeholder="Enter caption">
                                </div>
                            <?php endfor; ?>

                            <!-- Submit Button -->
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg btn-lg-post">Post</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>