<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'sales_inventory_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if (isset($_GET['action']) && $_GET['action'] == 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['type'];
            $_SESSION['username'] = $user['username'];

            if ($user['type'] == 1) {
                echo 1; // Admin
            } elseif ($user['type'] == 2) {
                echo 2; // Cashier
            }
        } else {
            echo 0; // Invalid password
        }
    } else {
        echo 0; // User not found
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin | Groceries Sales and Inventory System</title>
  <style>
    body {
      width: 100%;
      height: 100%;
      background: #007bff;
    }

    main#main {
      width: 100%;
      height: 100%;
      background: white;
    }

    #login-right {
      position: absolute;
      right: 0;
      width: 40%;
      height: 100%;
      background: white;
      display: flex;
      align-items: center;
    }
  </style>

  <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
  <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/vendor/venobox/venobox.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <main id="main" class="bg-dark">
    <div id="login-left">
      <div class="logo">
        <span class="fa fa-coins"></span>
      </div>
    </div>
    <div id="login-right">
      <div class="card col-md-8">
        <div class="card-body">
          <form id="login-form">
            <div class="form-group">
              <label for="username" class="control-label">Username</label>
              <input type="text" id="username" name="username" class="form-control">
            </div>
            <div class="form-group">
              <label for="password" class="control-label">Password</label>
              <input type="password" id="password" name="password" class="form-control">
            </div>
            <center><button type="submit" class="btn btn-primary btn-sm btn-block col-md-4">Login</button></center>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $('#login-form').submit(function(e) {
      e.preventDefault();
      const form = $(this);
      form.find('button').attr('disabled', true).text('Logging in...');
      form.find('.alert-danger').remove();

      $.ajax({
        url: 'ajax.php?action=login',
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
          if (response == 1) {
            location.href = 'index.php?page=home'; // Admin dashboard
          } else if (response == 2) {
            location.href = 'cashier.php'; // Cashier dashboard
          } else {
            form.prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
          }
          form.find('button').removeAttr('disabled').text('Login');
        },
        error: function(err) {
          console.error(err);
          form.find('button').removeAttr('disabled').text('Login');
        }
      });
    });
  </script>
  <script src="assets/js/select2.min.js"></script>
</body>

</html>
