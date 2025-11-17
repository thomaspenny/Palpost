<?php

// used by index.php and profile.php

// get media on posts
while ($post = $post_query->fetch_assoc()) {
    // Fetch media for this post
    $media_query = $con->prepare("
        SELECT mediaID, mediaType, mediaCaption, mediaPath 
        FROM Media 
        WHERE postID = ?
    ");
    $media_query->bind_param("i", $post['postID']);
    $media_query->execute();
    $media_result = $media_query->get_result();
    $media = [];
    while ($row = $media_result->fetch_assoc()) {
        $media[] = $row;
    }
    $media_query->close();
    $post['media'] = $media;

    // get comments for post (oldest first)
    $comments_query = $con->prepare("
        SELECT Comments.*, Users.userName, Users.userImagePath 
        FROM Comments 
        JOIN Users ON Comments.userID = Users.userID 
        WHERE postID = ? 
        ORDER BY CreatedAt ASC
    ");
    $comments_query->bind_param("i", $post['postID']);
    $comments_query->execute();
    $comments_result = $comments_query->get_result();
    $comments = [];
    while ($row = $comments_result->fetch_assoc()) {
        $comments[] = $row;
    }
    $comments_query->close();
    $post['comments'] = $comments;
    $posts[] = $post;
}

// delete post function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $delete_post_id = $_POST['delete_post_id'];
    // check if user is allowed to delete
    foreach ($posts as $post) {
        if ($post['postID'] == $delete_post_id && canDeletePost($user_data, $post)) {
            // Delete post
            $stmt = $con->prepare("DELETE FROM Posts WHERE postID = ?");
            $stmt->bind_param("i", $delete_post_id);
            $stmt->execute();
            $stmt->close();
            // Refresh page to update posts
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

// Handle like/unlike
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_post_id'])) {
    $like_post_id = $_POST['like_post_id'];
    $user_id = $user_data['userID'];

    // Check if user already liked this post
    $stmt = $con->prepare("SELECT likeID FROM Likes WHERE postID = ? AND userID = ?");
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param("ii", $like_post_id, $user_id);
    $stmt->execute();
    $stmt->store_result();
    $alreadyLiked = $stmt->num_rows > 0;
    $stmt->close();

    if ($alreadyLiked) {
        // Unlike action, remove like from db
        $stmt = $con->prepare("DELETE FROM Likes WHERE postID = ? AND userID = ?");
        $stmt->bind_param("ii", $like_post_id, $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Like action, insert like into db
        $stmt = $con->prepare("INSERT INTO Likes (postID, userID) VALUES (?, ?)");
        $stmt->bind_param("ii", $like_post_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_post_id'])) {
    $comment_post_id = intval($_POST['comment_post_id']);
    $comment_text = SanitiseInput(trim($_POST['comment_text'] ?? '')); // Sanitize input
    $user_id = $user_data['userID'];

    if ($comment_text !== '') {
        $stmt = $con->prepare("INSERT INTO Comments (userID, postID, Text, CreatedAt) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $user_id, $comment_post_id, $comment_text);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle comment deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment_id'], $_POST['token']) && ValidateToken($_POST['token'])) {
    $commentID = intval($_POST['delete_comment_id']);
    // Check if user is allowed to delete (admin or comment author)
    $stmt = $con->prepare("SELECT userID FROM Comments WHERE commentID = ?");
    $stmt->bind_param("i", $commentID);
    $stmt->execute();
    $stmt->bind_result($commentUserID);
    $stmt->fetch();
    $stmt->close();
    if ($user_data['userRank'] == 1 || $user_data['userID'] == $commentUserID) {
        $del = $con->prepare("DELETE FROM Comments WHERE commentID = ?");
        $del->bind_param("i", $commentID);
        $del->execute();
        $del->close();
    }
    // Optionally: redirect to avoid resubmission
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

?>