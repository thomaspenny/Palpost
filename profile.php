<?php
session_start();
include "background_fns/connection.php";
include "background_fns/functions.php";

EnsureValidToken();

// Check if user is logged in
$user_data = CheckLogin($con);

// Determine which profile to show
$profileID = isset($_GET['id']) ? intval($_GET['id']) : $user_data['userID'];

// Fetch profile data (add userImagePath)
$stmt = $con->prepare("SELECT userID, userName, userBio, userImagePath FROM Users WHERE userID = ?");
$stmt->bind_param("i", $profileID);
$stmt->execute();
$stmt->bind_result($pid, $pname, $pbio, $pimage);
$stmt->fetch();
$stmt->close();

if (!$pid) {
    // User not found
    $pname = "User Not Found";
    $pbio = "";
    $pimage = "uploads/profiles/default.png";
}

// Fetch posts for this profile based on view mode
$posts = [];
$view = isset($_GET['view']) ? $_GET['view'] : 'new';

if ($view === 'top') {
    $post_query = $con->prepare("
        SELECT Posts.*, 
            Users.userImagePath,
            (SELECT COUNT(*) FROM Likes WHERE Likes.postID = Posts.postID) AS LikeCount,
            (SELECT COUNT(*) FROM Comments WHERE Comments.postID = Posts.postID) AS CommentCount
        FROM Posts
        JOIN Users ON Posts.userID = Users.userID
        WHERE Posts.userID = ?
        ORDER BY LikeCount DESC, CreatedAt ASC
    ");
    $post_query->bind_param("i", $profileID);
} else {
    $post_query = $con->prepare("
        SELECT Posts.*,
            Users.userImagePath,
            (SELECT COUNT(*) FROM Likes WHERE Likes.postID = Posts.postID) AS LikeCount,
            (SELECT COUNT(*) FROM Comments WHERE Comments.postID = Posts.postID) AS CommentCount
        FROM Posts
        JOIN Users ON Posts.userID = Users.userID
        WHERE Posts.userID = ?
        ORDER BY CreatedAt DESC
    ");
    $post_query->bind_param("i", $profileID);
}
$post_query->execute();
$post_query = $post_query->get_result();

// Include post functions
include "background_fns/post_functions.php";

GenerateToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile - <?= htmlspecialchars($pname) ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light-subtle">

    <!-- Hidden page title for accessibility -->
    <h1 class="visually-hidden">Profile Page - <?= htmlspecialchars($pname) ?></h1>
    
    <!-- Include banner -->
    <?php include "banner.php"; ?>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <!-- Left Sidebar: User Info -->
            <aside class="col-12 col-md-3 col-lg-2 mb-4 mb-md-0 d-flex flex-column align-items-center">
                <img 
                    src="<?= htmlspecialchars($pimage) ?>" 
                    alt="Profile Picture" 
                    class="profile-picture">
                <div class="profile-name">
                    <a href="profile.php?id=<?= $pid ?>" class="text-decoration-none text-dark">
                        <?= htmlspecialchars($pname) ?>
                    </a>
                </div>
                <div class="profile-bio">
                    <?= isset($pbio) ? nl2br(htmlspecialchars(stripslashes($pbio))) : '' ?>
                </div>
            </aside>
            <!-- Main Content (centered, no sidebar) -->
            <main class="col-12 col-md-8 col-lg-8 px-md-4 d-flex flex-column align-items-center order-2">
                <!-- Add New Post Button above posts, aligned with sidebar heading -->
                <div class="w-100 d-flex justify-content-center align-items-start mb-3">
                    <a href="new_post.php" class="btn btn-primary btn-lg share-button">
                        + Share with Pals
                    </a>
                </div>
                <!-- Post view dropdown -->
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
            <!-- Right Sidebar -->
            <aside class="col-12 col-md-2 col-lg-2 mb-4 mb-md-0 order-3 d-flex align-items-start justify-content-center">
                <h5 class="h5 text-center fw-bold mt-3 mb-3 right-sidebar-heading">
                    Currently viewing posts by <?= htmlspecialchars($pname) ?>
                </h5>
            </aside>
        </div>
    </div>
    <!-- Include like functionality -->
    <?php include "background_fns/like_script.php"; ?>
</body>
</html>