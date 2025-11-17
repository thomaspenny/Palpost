<?php

// used by like_script.php

session_start();

include "connection.php";
include "functions.php";

// setting header for like_script.php
header('Content-Type: application/json');

// Ensure valid token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['token'])) {
    $user_data = CheckLogin($con);
    $postID = intval($_POST['post_id']);
    $userID = $user_data['userID'];

    // Check if already liked
    $stmt = $con->prepare("SELECT * FROM Likes WHERE postID = ? AND userID = ?");
    $stmt->bind_param("ii", $postID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Unlike: delete form Likes table
        $con->query("DELETE FROM Likes WHERE postID = $postID AND userID = $userID");
        $liked = false;
    } else {
        // Like: insert into Likes table
        $con->query("INSERT INTO Likes (postID, userID) VALUES ($postID, $userID)");
        $liked = true;
    }
    // Get new like count
    $countStmt = $con->prepare("SELECT COUNT(*) FROM Likes WHERE postID = ?");
    $countStmt->bind_param("i", $postID);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    // using concole.log to debug, result is [<count>], so get the value via fetch_row()[0]
    $likeCount = $countResult->fetch_row()[0];
    echo json_encode(['liked' => $liked, 'likeCount' => $likeCount]);
    exit;
}
// default response for invalid request
echo json_encode(['error' => 'Invalid request']);