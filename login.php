<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Fixed this line: use user_id, not id
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        }
    }

    $error = "Invalid credentials!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - MovieMania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="auth-container">
    <h2>Login to MovieMania</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" required class="form-control" autocomplete="username" autofocus>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" required class="form-control" autocomplete="current-password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">Show</button>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Login</button>
    </form>
    <a href="register.php" class="btn-link">Don't have an account? Register</a>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-danger text-white">
      <div class="modal-header">
        <h5 class="modal-title">Login Failed</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?= htmlspecialchars($error ?? '') ?>
      </div>
    </div>
  </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}

<?php if (!empty($error)): ?>
const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
errorModal.show();
<?php endif; ?>
</script>
</body>
</html>