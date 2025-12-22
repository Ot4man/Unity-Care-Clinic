<?php
session_start();
require "config/database.php";

$error = "";

// If already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Get user from DB
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify password (hashed)
        if (password_verify($password, $user['password'])) {

            // Create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect
            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Wrong password";
        }
    } else {
        $error = "User not found";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | Unity Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-900 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-xl shadow w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Admin Login</h2>

    <?php if ($error): ?>
        <p class="bg-red-100 text-red-700 p-2 mb-4 rounded">
            <?= $error ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input
            type="email"
            name="email"
            placeholder="Email"
            required
            class="w-full border p-2 mb-4 rounded"
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
            class="w-full border p-2 mb-6 rounded"
        >

        <button
            class="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800"
        >
            Login
        </button>
    </form>
</div>

</body>
</html>
