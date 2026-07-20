<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Miracle's Notice Board</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell">
        <main class="page-card">
            <div class="header-block">
                <h1>Welcome Back</h1>
                <p>Sign in with your credentials to access the notice board.</p>
            </div>

            <div id="feedback" class="message"></div>

            <form id="loginForm">
                <label for="identifier">Matric Number / ID</label>
                <input type="text" id="identifier" name="identifier" placeholder="Enter your Matric Number / Staff ID / Admin ID" required>

                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select role</option>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="admin">Admin</option>
                </select>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit">Login</button>
            </form>

            <p class="helper">Already have an account? <a href="signup.php">Sign up here</a></p>
        </main>
    </div>

    <script>
        const feedback = document.getElementById('feedback');

        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            feedback.style.display = 'none';
            feedback.textContent = '';
            feedback.className = 'message';

            const data = {
                identifier: document.getElementById('identifier').value.trim(),
                password: document.getElementById('password').value,
                role: document.getElementById('role').value
            };

            if (!data.identifier || !data.password || !data.role) {
                showMessage('Please enter your login identifier, password, and select a role.', 'error');
                return;
            }

            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    window.location.href = `${result.role}/dashboard.php`;
                } else {
                    showMessage(result.message || 'Login failed. Please try again.', 'error');
                }
            } catch (error) {
                showMessage('Unable to reach the server. Please try again.', 'error');
            }
        });

        function showMessage(message, type) {
            feedback.textContent = message;
            feedback.className = `message ${type}`;
            feedback.style.display = 'block';
        }
    </script>
</body>
</html>
