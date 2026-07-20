<?php
declare(strict_types=1);

require_once '../includes/auth.php';
checkRole('lecturer');
$user = currentUser();
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - Miracle's Notice Board</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-shell">
        <div class="container">
            <header>
                <nav>
                    <span>Role: Lecturer</span>
                    <a href="../logout.php">Logout</a>
                </nav>
            </header>
            <div class="dashboard">
                <!-- <aside class="sidebar">
                    <ul>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">My Notices</a></li>
                        <li><a href="#">Profile</a></li>
                    </ul>
                </aside> -->
                <main class="main-content">
                    <div class="content-card">
                        <h2>Lecturer Dashboard</h2>
                        <section class="recommended">
                            <h3>Recommended for You</h3>
                            <ul class="notices">
                        <?php
                        require_once '../config/db.php';
                        $db = getDbConnection();
                        $stmt = $db->prepare('SELECT id, title, content, category, target_role, created_at FROM notices WHERE target_role = ? OR target_role = ? ORDER BY created_at DESC');
                        $stmt->execute([$role, 'all']);
                        while ($notice = $stmt->fetch()) {
                            echo '<li class="notice">';
                            echo '<h3>' . htmlspecialchars($notice['title']) . '</h3>';
                            echo '<p>' . htmlspecialchars($notice['content']) . '</p>';
                            echo '<p class="meta">Category: ' . htmlspecialchars($notice['category']) . ' | Target: ' . htmlspecialchars($notice['target_role']) . ' | ' . htmlspecialchars($notice['created_at']) . '</p>';
                            echo '</li>';
                        }
                        ?>
                            </ul>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>