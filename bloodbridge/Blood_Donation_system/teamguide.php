*/ For a page that anyone logged in can access:

        require_once "../include/auth.php";

        requireLogin();

    For a donor-only page:
        require_once "../include/auth.php";

        requireRole(["donor"]);

    For a hospital-only page:
        require_once "../include/auth.php";

        requireRole(["hospital"]);

    For a admin-only page:
        require_once "../include/auth.php";

        requireRole(["admin"]);
        