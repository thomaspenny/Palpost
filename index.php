<?php
session_start();
include "background_fns/connection.php";
include "background_fns/functions.php";

EnsureValidToken();

// Check if user is logged in
$user_data = CheckLogin($con);

// Determine view mode: 'new' or 'top'
$view = isset($_GET['view']) ? $_GET['view'] : 'new';

// Fetch posts and their media
$posts = [];
if ($view === 'top') {
    // both queries will return posts with their like and comment counts

    // Order by like count DESC, then by CreatedAt ASC for ties
    $post_query = $con->query("
        SELECT Posts.*, 
            Users.userImagePath,
            (SELECT COUNT(*) FROM Likes WHERE Likes.postID = Posts.postID) AS LikeCount,
            (SELECT COUNT(*) FROM Comments WHERE Comments.postID = Posts.postID) AS CommentCount
        FROM Posts
        JOIN Users ON Posts.userID = Users.userID
        ORDER BY LikeCount DESC, CreatedAt ASC
    ");
} else {
    // Default: newest first
    $post_query = $con->query("
        SELECT Posts.*,
            Users.userImagePath,
            (SELECT COUNT(*) FROM Likes WHERE Likes.postID = Posts.postID) AS LikeCount,
            (SELECT COUNT(*) FROM Comments WHERE Comments.postID = Posts.postID) AS CommentCount
        FROM Posts
        JOIN Users ON Posts.userID = Users.userID
        ORDER BY CreatedAt DESC
    ");
}

// Include post functions
include "background_fns/post_functions.php";

GenerateToken(); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>PalPost Homepage</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lexend Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light-subtle">
    <!-- Include the banner at the top of the page -->
    <?php include "banner.php";
    
    // Show success message when user creates a post
    if (isset($_GET['msg']) && $_GET['msg'] === 'post_success') {
        echo "<div class='alert alert-success text-center mb-4'>Post created successfully!</div>";
    }
    ?>

    <!-- alert for logging in -->
    <script>
        function showMessage(msg) {
        alert(msg);
    }
    </script>
    
    <!-- rest of page -->
    <div class="container-fluid">
        <div class="row justify-content-center">    
            <!-- Left Sidebar: Welcome Message (2/12 width) -->
            <aside class="col-12 col-md-2 col-lg-2 mb-4 mb-md-0 order-1 d-flex align-items-start justify-content-center">
                <h1 class="h2 text-center fw-bold mt-3 mb-3 welcome-message">Welcome to PalPost!</h1>
            </aside>
            <!-- Main Content (8/12 width) -->
            <main class="col-12 col-md-8 col-lg-8 px-md-4 d-flex flex-column align-items-center order-2">
                <!-- Add New Post Button above posts, aligned with sidebar heading -->
                <div class="w-100 d-flex justify-content-center align-items-start mb-3">
                    <a href="new_post.php" class="btn btn-primary btn-lg share-button">
                        + Share with Pals
                    </a>
                </div>
                <!-- Post view options -->
                <form method="get" class="d-flex align-items-center justify-content-center pt-3 pb-2 mb-3 border-bottom w-100 feed-options-form">
                    <span class="me-2 fw-semibold">Feed view options</span>
                    <select class="form-select w-auto" name="view" onchange="this.form.submit()">
                        <option value="new" <?= (!isset($_GET['view']) || $_GET['view'] === 'new') ? 'selected' : '' ?>>New</option>
                        <option value="top" <?= (isset($_GET['view']) && $_GET['view'] === 'top') ? 'selected' : '' ?>>Top</option>
                    </select>
                </form>
                <!-- Posts Feed -->
                <?php include "posts_display.php"; ?>
            </main>
            <!-- Right Sidebar: Empty (2/12 width) -->
            <aside class="col-12 col-md-2 col-lg-2 mb-4 mb-md-0 order-3 d-flex align-items-start justify-content-center">
                <h5 class="h5 text-center fw-bold mt-3 mb-3 welcome-message">Currently viewing all posts</h5>
            </aside>
        </div>
    </div>
    <!-- include like script as php file-->
    <?php include "background_fns/like_script.php"; ?>

    <!-- get msg from login.php to display welcome message -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'login' && isset($_GET['user'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showMessage('Welcome, <?= htmlspecialchars($_GET['user']) ?>! You have successfully logged in.');
            // Remove msg and user from URL so popup only shows once, not on refresh
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('msg');
                url.searchParams.delete('user');
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>