<?php
session_start();

// Database Connection
$hostname = "localhost"; 
$username = "root";
$password = "";
$database = "BARANGAY_SYSTEM";



try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $ex) {
    die("Connection Failed: " . $ex->getMessage());
}

// Handle OTP Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $otp = rand(100000, 999999); 
    $sender = "cuetopatrick91@gmail.com";

    // Check if email exists
    $sql = "SELECT * FROM staff WHERE EMAIL = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["email" => $email]);
    $count = $stmt->rowCount();

    if ($count > 0) {
        // Update OTP in the database
        $sql_update = "UPDATE staff SET OTP = :otp WHERE EMAIL = :email";
        $update_stmt = $pdo->prepare($sql_update);
        $update_stmt->execute(["otp" => $otp, "email" => $email]);

        try {
            require './emailAPI.php'; // Include PHPMailer setup

            $mail->setFrom($sender, 'OTP Sender');
            $mail->addAddress($email, 'User');
            $mail->addReplyTo($sender, 'Information');

            $mail->isHTML(true);
            $mail->Subject = 'One Time Password - OTP';
            $mail->Body = 'Hi, here is your one-time password:<br/><b>' . $otp . '</b>';

            if ($mail->send()) {
                $_SESSION['otp_email'] = $email; // Store email for verification
                header("Location: enterotp.php"); // Redirect to OTP entry page
                exit();
            } else {
                $_SESSION['error'] = "Failed to send OTP. Please try again.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Email sending error.";
        }
    } else {
        $_SESSION['error'] = "The email address does not exist in our database.";
    }

    header("Location: requestotp.php");
    exit();
}
// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $pdo->query($sql);
$rowdesign = $query->fetch(PDO::FETCH_ASSOC);  // ✅ Correct for PDO

// TO DISPLAY THE INFO for design
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay System - Request OTP</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        .split-screen {
            display: flex;
            height: 100vh;
        }
        .left-panel {
            flex: 1;
                      background: 
            linear-gradient(180deg, rgba(37, 39, 98, 1) 0%, rgba(37, 39, 98, 0.8) 50%, rgba(37, 39, 98, 0.6) 100%),
            url('../images/bgblue.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            position: relative;
        }
        .left-panel::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23000" fill-opacity="0.05"/></svg>');
            background-size: 10px 10px;
            opacity: 0.3;
        }
        .logo-container {
            margin-bottom: 30px;
        }
        .logo-img {
            width: 120px;
            height: 120px;
  
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .logo-img img {
            max-width: 90%;
            max-height: 90%;
        }
        .brand-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .brand-tagline {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        .welcome-text {
            position: absolute;
            bottom: 50px;
            font-size: 16px;
            line-height: 1.5;
            max-width: 80%;
        }
        .right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            padding: 40px;
        }
        .otp-form {
            width: 100%;
            max-width: 400px;
        }
        .otp-title {
            color: #283593;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }
        .email-input {
            height: 55px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 25px;
            padding: 0 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .email-input:focus {
            border-color: #283593;
            box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
        }
        .send-btn {
            width: 100%;
            padding: 14px;
            background: #283593;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .send-btn:hover {
            background-color: #1a237e;
        }
        .error-message {
            color: #d32f2f;
            text-align: center;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 14px;
            border-left: 4px solid #d32f2f;
        }
        .instructions {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .signup-link a {
            color: #283593;
            text-decoration: none;
            font-weight: 600;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .split-screen {
                flex-direction: column;
            }
            .left-panel {
                height: 40vh;
            }
            .welcome-text {
                display: none;
            }
        }

        @keyframes floatUpDown {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-8px);
  }
}

.floating-logo {
  animation: floatUpDown 3s ease-in-out infinite;
}
    </style>
</head>
<body>
    <div class="split-screen">
        <div class="left-panel">
            <div class="logo-container">
            <div class="logo-img">
                <img src="../admin/barangayimage/<?=$rowdesign['MUNICIPAL_LOGO']?>"
                class="rounded-circle mb-2 shadow floating-logo" 
                style="width: 120px; height: 120px; object-fit: cover; background: white; padding: 5px;">
                </div>
               <h1 class="brand-title">BARANGAY MANAGEMENT<br>SYSTEM</h1>
                <p class="brand-tagline">Efficiently manage your barangay operations</p>
            </div>
            <div class="welcome-text">
               Efficiently manage barangay records, residents, services, and local governance through a centralized and user-friendly system.
            </div>
        </div>
        <div class="right-panel">
            <div class="otp-form" data-aos="fade-up" data-aos-duration="3000">
                <h2 class="otp-title">Request Verification Code</h2>
                
                <form method="post" action="">
                    <input type="text" class="form-control email-input" placeholder="Enter your email address" name="email" required>
                    
                    <button class="send-btn" type="submit">Send Verification Code</button>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-message">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="instructions">
                        <p>A 6-digit verification code will be sent to your email.</p>
                        <p>Please check your inbox or spam folder after submission.</p>
                    </div>
                    
                    <div class="signup-link">
                        Not registered? <a href="../Registration/registration.php">Create an account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>