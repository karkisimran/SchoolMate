<?php
session_start();
require 'database.php'; // DB connection ($conn)

$errors = [
    'proof' => '',
    'password' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 1 data from session
    $school_name = $_SESSION['school_name'] ?? '';
    $school_type = $_SESSION['school_type'] ?? '';
    $email = $_SESSION['email'] ?? '';
    $address = $_SESSION['address'] ?? '';

    // Password validation
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password)) {
        $errors['password'] = "Please enter a password.";
    } elseif ($password !== $confirm_password) {
        $errors['password'] = "Passwords do not match.";
    }

    // File validation
    $proof_file = null;
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] == 0) {
        if ($_FILES['proof']['size'] > 1024 * 1024) {
            $errors['proof'] = "File must be less than 1 MB.";
        } else {
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $proof_file = uniqid() . "_" . basename($_FILES['proof']['name']);
            $target_file = $upload_dir . $proof_file;

            if (!move_uploaded_file($_FILES['proof']['tmp_name'], $target_file)) {
                $errors['proof'] = "Failed to upload file.";
            }
        }
    } else {
        $errors['proof'] = "Please upload a proof document.";
    }

    // Save to DB if no errors
    if (empty($errors['password']) && empty($errors['proof'])) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO registered_schools (school_name, school_type, email, address, proof, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $school_name, $school_type, $email, $address, $proof_file, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['school_id'] = $stmt->insert_id; // store logged-in school id
            header("Location: school_dashboard.php"); // redirect to dashboard
            exit;
        } else {
            $errors['password'] = "Database error: " . $stmt->error;
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Your School - Step 2</title>
  <link rel="stylesheet" href="school_register.css">
</head>
<body>
  <!-- Header -->
  <div class="header">
      <img src="images/logo.jpg" alt="logo" class="logo">
      <span class="brand">SchoolMate</span>
  </div>

  <!-- Background + Form -->
  <div class="form-container">
      <h2>Register Your School</h2>
      <p class="subtitle">Please complete the form to create your school account</p>

      <!-- Show errors -->
      <?php if (!empty($errors)): ?>
          <div class="error-box">
              <?php foreach ($errors as $error): ?>
                  <p><?php echo htmlspecialchars($error); ?></p>
              <?php endforeach; ?>
          </div>
      <?php endif; ?>

      <!-- Form -->
        <form method="post" enctype="multipart/form-data" onsubmit="return validatePasswords();">
            <!-- File Upload -->
            <label>Upload Proof Document</label>
            <input type="file" name="proof" accept="image/*,application/pdf" required>

           <label>Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Enter Password" required>
                <img src="images/show.png" class="toggle-icon" onclick="togglePassword('password', this)" alt="Toggle Password">
            </div>

            <label>Confirm Password</label>
            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <img src="images/show.png" class="toggle-icon" onclick="togglePassword('confirm_password', this)" alt="Toggle Password">
            </div>


            <button type="submit" class="btn">Sign Up</button>
        </form>

  </div>

 <script>
function validatePasswords() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;

    if (password !== confirm) {
        alert("Passwords do not match!");
        return false; // prevent form submission
    }
    return true; // allow form submission
}

function togglePassword(id, el) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
        // Optional: change icon when showing password
        el.src = "images/hide.png"; // your "hide" icon
    } else {
        input.type = "password";
        el.src = "images/show.png"; // your "show" icon
    }
}
</script>

</body>
</html>