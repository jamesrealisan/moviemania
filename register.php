<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        header("Location: login.php");
        exit();
    } else {
        $error = "Username already exists!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Register - MovieMania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet" />
</head>
<body>
<div class="auth-container">
    <h2>Create a MovieMania Account</h2>
    <?php if (isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" required class="form-control" autocomplete="username" autofocus>
        </div>
        <div class="mb-3">
    <label>Password</label>
    <div class="input-group">
        <input type="password" name="password" id="password" required class="form-control">
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">Show</button>
    </div>
</div>
        <button class="btn btn-primary" type="submit">Register</button>
    </form>
    <a href="login.php" class="btn-link">Already have an account? Login</a>
</div>
<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-danger text-white">
      <div class="modal-header">
        <h5 class="modal-title">Registration Failed</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?= $error ?? '' ?>
      </div>
    </div>
  </div>
</div>

<script>
// Toggle password visibility
function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}

// Show error modal if there's an error
<?php if (!empty($error)): ?>
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
<?php endif; ?>
</script>

</body>
</html>