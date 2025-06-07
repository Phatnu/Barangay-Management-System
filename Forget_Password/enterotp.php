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

// Store the email used for OTP
$email = isset($_SESSION['otp_email']) ? $_SESSION['otp_email'] : 'N/A';

$sql = "SELECT * FROM Barangay_Info";
$query = $pdo->query($sql);
$rowdesign = $query->fetch(PDO::FETCH_ASSOC);  // ✅ Correct for PDO

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay System - Enter OTP</title>
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
            margin-bottom: 20px;
            text-align: center;
        }
        .email-alert {
            background-color: #e8f5e9;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 25px;
            border-left: 4px solid #4caf50;
            font-size: 14px;
            color: #2e7d32;
            text-align: center;
        }
        .otp-input {
            height: 55px;
            font-size: 18px;
            text-align: center;
            letter-spacing: 4px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .otp-input:focus {
            border-color: #283593;
            box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
        }
        .verify-btn {
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
        .verify-btn:hover {
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
        .otp-instructions {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 20px;
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
            <div class="otp-form">
                <h2 class="otp-title">Verify Your Identity</h2>
                
                <div class="email-alert">
                    We've sent a verification code to<br>
                    <strong><?php echo htmlspecialchars($email); ?></strong>
                </div>
                
                <form action="reset_password.php" method="POST">
                    <input type="text" class="form-control otp-input" placeholder="Enter OTP" maxlength="6" name="otp" required>
                    
                    <button class="verify-btn" type="submit">Verify OTP</button>
                    
                    <?php if(isset($_GET['error']) && $_GET['error'] == 'incorrect'): ?>
                    <div class="error-message">
                        Incorrect OTP. Please try again.
                    </div>
                    <?php elseif(isset($_GET['error']) && $_GET['error'] == 'notset'): ?>
                    <div class="error-message">
                        OTP not set! Please request a new verification code.
                    </div>
                    <?php endif; ?>
                    
                    <div class="otp-instructions">
                        <p>Please check your inbox or spam folder if you don't see the email.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>