<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Miracle's Notice Board</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell">
        <main class="page-card">
            <div class="header-block">
                <h1>Create your account</h1>
                <p>Register as a student or lecturer to access the notice board.</p>
            </div>
            <div id="feedback" class="message"></div>
            <form id="signupForm">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="full_name" placeholder="Enter your full name" required>

                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select role</option>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                </select>

                <label for="identifier" id="identifierLabel">Unique Identifier</label>
                <input type="text" id="identifier" name="identifier" placeholder="Enter your matric number or staff ID" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>

                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm your password" required>

                <button type="submit">Sign Up</button>
            </form>
            <p class="helper">Already have an account? <a href="login.php">Login here</a></p>
        </main>
    </div>

    <script>
        const roleField = document.getElementById('role');
        const identifierLabel = document.getElementById('identifierLabel');
        const identifierField = document.getElementById('identifier');
        const feedback = document.getElementById('feedback');

        roleField.addEventListener('change', () => {
            const role = roleField.value;
            if (role === 'student') {
                identifierLabel.textContent = 'Matric Number';
                identifierField.placeholder = 'Enter your matric number';
            } else if (role === 'lecturer') {
                identifierLabel.textContent = 'Staff ID';
                identifierField.placeholder = 'Enter your staff ID';
            } else {
                identifierLabel.textContent = 'Unique Identifier';
                identifierField.placeholder = 'Enter your matric number or staff ID';
            }
        });

        document.getElementById('signupForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            feedback.style.display = 'none';
            feedback.textContent = '';
            feedback.className = 'message';

            const fullName = document.getElementById('fullName').value.trim();
            const role = roleField.value;
            const identifier = identifierField.value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!fullName || !role || !identifier || !password || !confirmPassword) {
                showMessage('Please fill out every field.', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showMessage('Passwords do not match.', 'error');
                return;
            }

            try {
                const response = await fetch('api/signup.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        full_name: fullName,
                        role,
                        identifier,
                        password
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showMessage(result.message || 'Signup successful. Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1400);
                } else {
                    showMessage(result.message || 'Signup failed. Please try again.', 'error');
                }
            } catch (error) {
                showMessage('An error occurred while sending your request. Please try again.', 'error');
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
