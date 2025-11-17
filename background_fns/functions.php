<?php
// CheckLogin function
function CheckLogin($con)
{
    if (isset($_SESSION['userID'])) {
        $userID = $_SESSION['userID'];
        
        $stmt = $con->prepare("SELECT * FROM Users WHERE userID = ? LIMIT 1");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }
    
    //redirect to login
    header("Location: login.php");
    die;
}

// SanitiseInput function
function SanitiseInput($input)
{
    //remove whitespace from beginning and end of string
    $input = trim($input);
    //add backslashes (can be removed with stripslashes)
    $input = addslashes($input);
    return $input;
}

// InsertNewUser function
function InsertNewUser($con, $userRank, $userName, $userEmail, $userPassword, $userImagePath, $userImageType) {
    // Prepare the SQL statement
    $sqlQuery = $con->prepare("INSERT INTO Users (userRank, userName, userEmail, userPassword, userImagePath, userImageType) VALUES (?, ?, ?, ?, ?, ?)");
    
    if (!$sqlQuery) {
        error_log("Prepare failed: " . $con->error);
        return false;
    }

    // Bind parameters
    $sqlQuery->bind_param("isssss", $userRank, $userName, $userEmail, $userPassword, $userImagePath, $userImageType);

    // Execute the query
    $querySuccessful = $sqlQuery->execute();

    if (!$querySuccessful) {
        error_log("Execute failed: " . $sqlQuery->error);
    }

    // Close the statement
    $sqlQuery->close();

    return $querySuccessful;
}

// Token generation and validation
function GenerateRandomString($length) 
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $max = strlen($characters) - 1;
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $max)];
    }
    return $randomString;
}

// GenerateToken functions
function GenerateToken() 
{
    $_SESSION['token'] = generateRandomString(20);
}

// ValidateToken function
function ValidateToken($formToken) 
{
    if ($formToken === $_SESSION['token']) {
        return true;
    } else {
        return false;
    }
}

// GetRelativeTime function for displaying time in a human-readable format
function getRelativeTime($datetime) {
    // get time elapsed since the post was created
    date_default_timezone_set('Europe/London');
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    // Return a human-readable time difference
    if ($diff < 60) {
        return "just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " min" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } else {
        $weeks = floor($diff / 604800);
        return $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
    }
}

// check if a user has liked a post
function hasUserLiked($con, $postID, $userID) {
    $stmt = $con->prepare("SELECT likeID FROM Likes WHERE postID = ? AND userID = ?");
    $stmt->bind_param("ii", $postID, $userID);
    $stmt->execute();
    $stmt->store_result();
    $liked = $stmt->num_rows > 0;
    $stmt->close();
    return $liked;
}
// get username by userID
function getUserName($con, $userID) {
    $stmt = $con->prepare("SELECT userName FROM Users WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $userName = null;
    $stmt->bind_result($userName);
    $stmt->fetch();
    $stmt->close();
    return $userName ?: "Unknown";
}

// user can delete post if they are the author or an admin
function canDeletePost($user_data, $post) {
    return ($user_data['userRank'] == 1) || ($user_data['userID'] == $post['userID']);
}

// EnsureValidToken function
function EnsureValidToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['token']) || !ValidateToken($_POST['token'])) {
            die('Invalid or missing CSRF token.');
        }
    }
}

// function for getting the cu4rrent url of the page, used in the search bar to make the action= dynamic
function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    // Remove query string for cleaner action (optional)
    $uri = strtok($uri, '?');
    return $protocol . $host . $uri;
}
?>