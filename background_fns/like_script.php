<!-- Used in profile.php, index.php for like functionality -->
<script>
// ensure the script runs after the page is loaded
document.addEventListener('DOMContentLoaded', function() {
    // for every button with class 'like-btn', add click event listener
    document.querySelectorAll('.like-btn').forEach(function(btn) {
        // when button is clicked...
        btn.addEventListener('click', function() {
            // data-post-id = $post['postID']
            const postId = this.getAttribute('data-post-id');
            // get the CSRF token from the session
            const token = '<?= htmlspecialchars($_SESSION['token']) ?>';
            const button = this;
            //this goes to the next sibling element, which is the like count element in <span class="like-count">
            const likeCountElement = button.nextElementSibling;

            // fetch API to like/unlike the post
            fetch('background_fns/like_post.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                // sends the post_id and token to like_post.php
                body: `post_id=${encodeURIComponent(postId)}&token=${encodeURIComponent(token)}`
            })
            .then(response => response.json())
            .then(data => {
                // set name in button to 'Unlike' if liked, 'Like' if unliked
                button.textContent = data.liked ? 'Unlike' : 'Like';
                // toggle classes for styling depending on like status
                button.classList.toggle('btn-primary', !data.liked);
                button.classList.toggle('btn-outline-primary', data.liked);
                // update data-liked attribute and like count
                button.setAttribute('data-liked', data.liked ? '1' : '0');
                // update the like count displayed
                likeCountElement.textContent = data.likeCount;
            });
        });
    });
});
</script>