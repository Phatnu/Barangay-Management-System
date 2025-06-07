<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    body {
      font-family: Arial, sans-serif;
       background: 
            linear-gradient(180deg, rgba(37, 39, 98, 1) 0%, rgba(37, 39, 98, 0.8) 50%, rgba(37, 39, 98, 0.6) 100%),
            url('../images/bgblue.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background-color: white;
      padding: 30px;
      border-radius: 4px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 350px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      background-color: #f5f5f5;
      box-sizing: border-box;
      font-size: 14px;
    }

    input[type="text"]::placeholder,
    input[type="password"]::placeholder {
      color: #aaa;
    }

    .login-btn {
        width: 100%;
      padding: 12px;
       background: #283593;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      text-transform: uppercase;
      margin-top: 10px;
      &:hover{
        background-color: #2F3136;
        transition: 0.5s;
        color: white;
      }
    }

    .signup-link {
      text-align: center;
      margin-top: 15px;
      font-size: 13px;
      color: #777;
    }

    .signup-link a {
      color: #00b33c;
      text-decoration: none;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

    .message {
      font-size: 13px;
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>
    <?php
    $hostname = "localhost"; // Use this instead of localhost
    $username = "root";
    $password = "";
    $database = "BARANGAY_SYSTEM";
    // This is your correct MySQL port

    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    } catch (PDOException $ex) {
        echo "Connection Failed because: ".$ex;
    }

    if(isset($_POST["otp"])){
      $otp = $_POST["otp"];
  
      $sql = "SELECT * FROM staff WHERE otp = :otp";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(["otp"=>$otp]);
      $count = $stmt->rowCount();
  
      if($count > 0){ ?>
          <div class="login-container">
              <form action="update_password.php" method="POST">
                  <h2 style="text-align: center;">RESET PASSWORD</h2>
                  <input type="hidden" name="otp" value="<?php echo $otp; ?>">
                  <div class="form-group">
                      <input type="password" placeholder="New password" name="new_password" required>
                  </div>
                  <div class="form-group">
                      <input type="password" placeholder="Confirm password" name="confirm_password" required>
                  </div>
                  <button class="login-btn" type="submit">SEND</button>
              </form>
          </div>
      <?php 
      } else {
          // Redirect with error message
          header("Location: enterotp.php?error=incorrect");
          exit(); // Important to prevent further script execution
      }
  } else {
      header("Location: enterotp.php?error=notset");
      exit();
  }
  ?>

</body>
</html>