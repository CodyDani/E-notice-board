<?php
declare(strict_types=1);

require_once '../includes/auth.php';
checkRole('admin');
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Miracle's Notice Board</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="app-shell">
        <div class="container">
            <header>
                <nav>
                    <span>Role: Admin</span>
                    <a href="../logout.php">Logout</a>
                </nav>
            </header>
            <div class="dashboard">
                <!-- <aside class="sidebar">
                    <ul>
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Manage Notices</a></li>
                        <li><a href="#">Users</a></li>
                    </ul>
                </aside> -->
                <main class="main-content">
                    <div class="content-card">
                        <h2>Admin Dashboard</h2>
                        <section>
                            <h3>Create Notice</h3>
                            <form id="create-notice-form" action="../api/create_notice.php" method="POST">
                                <div id="message"></div>
                                <label for="title">Title:</label>
                                <input type="text" id="title" name="title" required>

                                <label for="content">Content:</label>
                                <textarea id="content" name="content" required></textarea>

                                <label for="category">Category:</label>
                                <input type="text" id="category" name="category" required>

                                <label for="target_role">Target Role:</label>
                                <select id="target_role" name="target_role" required>
                                    <option value="student">Student</option>
                                    <option value="lecturer">Lecturer</option>
                                    <option value="admin">Admin</option>
                                    <option value="all">All</option>
                                </select>

                                <button type="submit">Create Notice</button>
                            </form>
                        </section>
                    </div>
                    <div class="content-card">
                        <section>
                            <h3>All Notices</h3>
                            <ul class="notices">
                        <?php
                        require_once '../config/db.php';
                        $db = getDbConnection();
                        $stmt = $db->query('SELECT id, title, content, category, target_role, created_at FROM notices ORDER BY created_at DESC');
                        while ($notice = $stmt->fetch()) {
                            echo '<li class="notice" data-id="' . htmlspecialchars((string)$notice['id']) . '">';
                            echo '<h3>' . htmlspecialchars($notice['title']) . '</h3>';
                            echo '<p>' . htmlspecialchars($notice['content']) . '</p>';
                            echo '<p class="meta">Category: ' . htmlspecialchars($notice['category']) . ' | Target: ' . htmlspecialchars($notice['target_role']) . ' | ' . htmlspecialchars($notice['created_at']) . '</p>';
                            echo '<button class="delete-btn" data-id="' . htmlspecialchars((string)$notice['id']) . '">Delete</button>';
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
    <script>
        document.getElementById('create-notice-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../api/create_notice.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('message');
                if (data.success) {
                    messageDiv.innerHTML = '<p style="color: green;">' + data.message + '</p>';
                    this.reset(); // Clear the form
                    // Optionally refresh the notices list
                    setTimeout(() => { location.reload(); }, 5000);
                } else {
                    messageDiv.innerHTML = '<p style="color: red;">' + data.message + '</p>';
                    setTimeout(() => { messageDiv.innerHTML = ''; }, 5000);
                }
            })
            .catch(error => {
                const messageDiv = document.getElementById('message');
                messageDiv.innerHTML = '<p style="color: red;">An error occurred.</p>';
                setTimeout(() => { messageDiv.innerHTML = ''; }, 5000);
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-btn')) {
                const id = e.target.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this notice?')) {
                    fetch('../api/delete_notice.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ notice_id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const messageDiv = document.getElementById('message');
                        if (data.success) {
                            messageDiv.innerHTML = '<p style="color: green;">' + data.message + '</p>';
                            setTimeout(() => { messageDiv.innerHTML = ''; }, 5000);
                            // Remove the notice from the list
                            e.target.closest('li').remove();
                        } else {
                            messageDiv.innerHTML = '<p style="color: red;">' + data.message + '</p>';
                            setTimeout(() => { messageDiv.innerHTML = ''; }, 5000);
                        }
                    })
                    .catch(error => {
                        const messageDiv = document.getElementById('message');
                        messageDiv.innerHTML = '<p style="color: red;">An error occurred while deleting.</p>';
                        setTimeout(() => { messageDiv.innerHTML = ''; }, 5000);
                    });
                }
            }
        });
    </script>
</body>
</html>