<?php
// Start session if not already started
if (!session_id()) {
    session_start();
}

// Check if a page is requested
if (isset($_GET['page'])) {
    $page = $_GET['page'];

    // Redirect based on the page value
    switch ($page) {
        case 'membershipList':
            header('Location: reviewMembershipList.php');
            exit;
        case 'loanList':
            header('Location: reviewLoanList.php');
            exit;
        default:
            // Redirect to a default page if an invalid value is provided
            header('Location: admin.php');
            exit;
    }
} else {
    // Redirect to ALK page if no page parameter is provided
    header('Location: admin.php');
    exit;
}
