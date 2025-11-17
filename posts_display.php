<!-- used by index.php and profile.php -->

<!-- Posts Feed -->
<?php foreach ($posts as $post): ?>
    <div class="card mb-4 card-container position-relative">
        <!-- Delete Post Button -->
        <?php if (canDeletePost($user_data, $post)): ?>
            <form method="post" class="delete-post-form" onsubmit="return confirm('Are you sure you want to delete this post?');">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">
                <input type="hidden" name="delete_post_id" value="<?= $post['postID'] ?>">
                <button type="submit" class="btn btn-sm delete-post-btn" title="Delete post">&times;</button>
            </form>
        <?php endif; ?>
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <!-- User Image and Name -->
                <img src="<?= htmlspecialchars($post['userImagePath']) ?>" alt="User" class="user-profile-img">
                <a href="profile.php?id=<?= htmlspecialchars($post['userID']) ?>" class="user-profile-link" title="View user profile">
                    <?= htmlspecialchars(getUserName($con, $post['userID'])) ?>
                </a>
                <!-- Post Creation Time -->
                <span class="post-time"><?= getRelativeTime($post['CreatedAt']) ?></span>
            </div>
            <!-- Post text content -->
            <p class="mb-3"><?= nl2br(htmlspecialchars($post['TextContent'])) ?></p>
            <!-- Image Carousel -->
            <?php if (count($post['media']) > 0): ?>
                <div id="carouselPost<?= $post['postID'] ?>" class="carousel slide mb-3" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($post['media'] as $idx => $media): ?>
                            <div class="carousel-item<?= $idx === 0 ? ' active' : '' ?>">
                                <img src="<?= htmlspecialchars($media['mediaPath']) ?>" class="d-block" alt="Media">
                                <?php if (!empty($media['mediaCaption'])): ?>
                                    <div class="carousel-caption">
                                        <span class="caption-bg"><?= htmlspecialchars($media['mediaCaption']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Navigation Buttons -->
                    <?php if (count($post['media']) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselPost<?= $post['postID'] ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselPost<?= $post['postID'] ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <!-- Like Button -->
                    <button
                        class="btn btn-sm <?= hasUserLiked($con, $post['postID'], $user_data['userID']) ? 'btn-outline-primary' : 'btn-primary' ?> like-btn"
                        data-post-id="<?= $post['postID'] ?>"
                        data-liked="<?= hasUserLiked($con, $post['postID'], $user_data['userID']) ? '1' : '0' ?>"
                    >
                        <?= hasUserLiked($con, $post['postID'], $user_data['userID']) ? 'Unlike' : 'Like' ?>
                    </button>
                    <span class="ms-2 like-count"><?= htmlspecialchars($post['LikeCount'] ?? 0) ?></span>
                </div>
                <span class="text-muted">Comments: <?= htmlspecialchars($post['CommentCount'] ?? 0) ?></span>
            </div>
            <!-- Comments -->
            <div class="mt-3">
                <?php if (count($post['comments']) > 0): ?>
                    <div class="list-group">
                        <?php foreach ($post['comments'] as $comment): ?>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($comment['userImagePath']) ?>" alt="User" class="comment-profile-img">
                                    <a href="profile.php?id=<?= htmlspecialchars($comment['userID']) ?>" class="user-profile-link" title="View user profile">
                                        <?= htmlspecialchars(getUserName($con, $comment['userID'])) ?>
                                    </a>
                                    <span class="comment-time"><?= getRelativeTime($comment['CreatedAt']) ?></span>
                                    <!-- Delete Comment Button -->
                                    <?php if ($user_data['userRank'] == 1 || $user_data['userID'] == $comment['userID']): ?>
                                        <form method="post" class="delete-comment-form">
                                            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">
                                            <input type="hidden" name="delete_comment_id" value="<?= $comment['commentID'] ?>">
                                            <button type="submit" class="btn btn-link btn-sm text-danger" title="Delete comment" onclick="return confirm('Delete this comment?');">&times;</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <p class="comment-text"><?= nl2br(htmlspecialchars($comment['Text'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <!-- New Comment -->
                <form method="post" class="comment-form">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">
                    <input type="hidden" name="comment_post_id" value="<?= $post['postID'] ?>">
                    <label for="comment_text_<?= $post['postID'] ?>" class="visually-hidden">Comment</label>
                    <textarea id="comment_text_<?= $post['postID'] ?>" name="comment_text" class="form-control comment-textarea" rows="1" placeholder="Add a comment..."></textarea>
                    <button type="submit" class="btn btn-primary btn-sm submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>