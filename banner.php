<!-- Used by index.php, profile.php, settings.php -->

<!-- Banner is used across multiple pages, so it is included here -->
<!-- Specific banner layout is desired for mobile phone viewing, given the limited space -->


<?php
// Search logic
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = [];

if ($searchTerm !== '') {
    $searchTerm = SanitiseInput($searchTerm);
    $stmt = $con->prepare("SELECT userID, userName, userImagePath FROM Users WHERE userName LIKE CONCAT('%', ?, '%') LIMIT 20");
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $stmt->bind_result($id, $name, $img);
    while ($stmt->fetch()) {
        $results[] = [
            'userID' => $id,
            'userName' => $name,
            'userImagePath' => $img
        ];
    }
    $stmt->close();
}
?>


<!-- Blue banner with Home, profile, search, settings, and logout -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid flex-column">

        <!-- Mobile view (PalPost logo/title centered on top row) -->
        <!-- d-lg-none hides this on larger screens -->
        <div class="d-block d-lg-none w-100">
            <!-- Render PalPost logo above the links, given lack of width -->
            <div class="d-flex justify-content-center align-items-center mb-2">
                <a href="index.php" class="desktop-logo-container" title="Home">
                    <img src="Images/banner_logo.png" alt="" class="banner-palpost-logo">
                    <span class="palpost-title-banner">PalPost</span>
                </a>
            </div>

            <!-- Other links -->
            <div class="d-flex justify-content-between align-items-center text-center mb-2 px-2">    
            <!-- Home -->
                <a href="index.php" class="mobile-nav-container" title="Home">
                    <img src="Images/home.png" alt="" class="nav-link-icon">
                    <span class="nav-link-text">Home</span>
                </a>
                <!-- Profile -->
                <a href="profile.php?id=<?= $user_data['userID'] ?>" class="mobile-nav-container" title="Profile">
                    <img src="<?= htmlspecialchars($user_data['userImagePath']) ?>"
                         alt="" class="nav-link-icon" class="nav-link-icon">
                    <span class="nav-link-text">Profile</span>
                </a>
                <!-- Settings -->
                <a href="settings.php" class="mobile-nav-container" title="Settings">
                    <img src="Images/settings.png" alt="" class="nav-link-icon">
                    <span class="nav-link-text">Settings</span>
                </a>
                <!-- Logout -->
                <a href="background_fns/logout.php" class="mobile-nav-container" title="Logout">
                    <img src="Images/logout.png" alt="" class="nav-link-icon">
                    <span class="nav-link-text">Logout</span>
                </a>
            </div>
        </div>

        <!-- Desktop view (Palpost logo sits alongside other page links -->
        <!-- d-none: (hides by default) d-lg-flex: (shows on large screens and up) -->
        <div class="w-100 d-none d-lg-flex justify-content-between align-items-center flex-wrap text-center px-lg-5 px-xl-5">
            
        <!-- Home -->
            <div>
                <a href="index.php" class="desktop-nav-container" title="Home">
                    <img src="Images/home.png" alt="" class="nav-link-icon">
                    <span class="nav-link-text">Home</span>
                </a>
            </div>
            <!-- Profile -->
            <div>
                <a href="profile.php?id=<?= $user_data['userID'] ?>" class="desktop-nav-container" title="Profile">
                    <img src="<?= htmlspecialchars($user_data['userImagePath']) ?>"
                         alt="" class="nav-link-icon" class="nav-link-icon">
                    <span class="nav-link-text">Profile</span>
                </a>
            </div>
            <!-- PalPost logo, in-line with links -->
            <div>
                <a href="index.php" class="desktop-logo-container" title="Home">
                    <img src="Images/banner_logo.png" alt="PalPost Logo" class="banner-palpost-logo">
                    <span class="palpost-title-banner">PalPost</span>
                </a>
            </div>
            <!-- Settings -->
            <div>
                <a href="settings.php" class="desktop-nav-container" title="Settings">
                    <img src="Images/settings.png" alt="" class="nav-link-icon">
                    <span class="nav-link-text">Settings</span>
                </a>
            </div>
            <!-- Logout -->
            <div>
                <a href="background_fns/logout.php" class="desktop-nav-container" title="Logout">
                    <img src="Images/logout.png" alt="" class="nav-link-icon">
                    <span class="nav-link-text">Logout</span>
                </a>
            </div>
        </div>

        <!-- Bottom search bar (same for both) -->
        <div class="search-container w-100 mt-2 d-flex align-items-center justify-content-center">
            <form method="get" action="<?= htmlspecialchars(getCurrentUrl()) ?>" class="w-100" autocomplete="off">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Find your pals" aria-label="Search" required>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
            <!-- Search results dropdown -->
            <?php if (isset($_GET['search']) && trim($_GET['search']) !== ''): ?>
                <?php if (count($results) > 0): ?>
                    <div id="searchResults" class="list-group position-absolute">
                        <?php foreach ($results as $user): ?>
                            <a href="profile.php?id=<?= htmlspecialchars($user['userID']) ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                <img src="<?= htmlspecialchars($user['userImagePath']) ?>" alt="Profile" class="search-result-profile-img">
                                <span><?= htmlspecialchars($user['userName']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hide search results when clicking outside the search container -->
<script>
document.addEventListener('click', function(event) {
    const container = document.querySelector('.search-container');
    const results = document.getElementById('searchResults');
    if (results && !container.contains(event.target)) {
        results.style.display = 'none';
    }
});
</script>