// when user changes scroll position, update the scroll position 


window.addEventListener('scroll', function() {
    // get the current scroll position
    let currentScrollPosition = window.scrollY;
    <?php
    $_SESSION['scrollPosition'] = $scrollPosition;
    ?>
    // get current URL
    let currentURL = window.location.href;
    <?php $_SESSION['currentURL'] = $currentURL; ?>
    ?>
});

