<?php
require 'config.php';  // uses $conn for PDO connection

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match.';
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update password
            $update = $conn->prepare("UPDATE users SET password_hash = :password WHERE email = :email");
            $update->execute([
                'password' => $hashedPassword,
                'email' => $email
            ]);

            $message = '✅ Password updated successfully.';
        } else {
            $message = '❌ Email not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    :root { 
      --primary-light: #a4e0dd;
      --primary: #78cac5;
      --primary-dark: #4db8b2;
      --secondary-light: #f2e6b5;
      --secondary: #e7cf9b;
      --secondary-dark: #96833f;
      --light: #EEF9FF;
      --dark: #173836;
    }

    body {
      background-color: var(--light);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
      background-color: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .header-text {
      color: var(--dark);
    }

    .info-text {
      color: var(--secondary-dark);
    }

    /* Button styles from your code */
    .btn {
      font-family: 'Nunito', sans-serif;
      font-weight: 600;
      transition: all 0.4s ease;
      border: 2px solid transparent;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.5);
      transition: left 0.5s ease;
      z-index: -1;
    }

    .btn:hover::before {
      left: 100%;
    }

    .btn-primary {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
      color: #FFFFFF !important;
      box-shadow: 0 4px 20px rgba(108, 117, 125, 0.3);
    }

    .btn-primary:hover {
      background-color: var(--primary-dark) !important;
      color: var(--dark) !important;
      border-color: var(--primary-dark) !important;
      transform: scale(1.05);
    }

    .btn-secondary {
      background-color: var(--secondary) !important;
      border-color: var(--secondary) !important;
      color: #FFFFFF !important;
      box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    .btn-secondary:hover {
      background-color: var(--secondary-dark) !important;
      color: var(--dark) !important;
      border-color: var(--secondary-dark) !important;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="col-md-6 col-lg-5">
      <div class="card">
        <h2 class="text-center header-text mb-3">Reset Your Password</h2>
        <p class="text-center info-text mb-3">Enter your email and a new password</p>

        <?php if ($message): ?>
          <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="newPassword" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirmPassword" class="form-control" required />
          </div>
          <button type="submit" class="btn btn-primary w-100">Update Password</button>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
