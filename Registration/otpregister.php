<?php 
session_start();
include_once("../connections/connection.php");
$con = connection();

// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $con->query($sql);
$rowdesign = $query->fetch_assoc();
// TO DISPLAY THE INFO for design

$success_msg = "";

if(isset($_POST['otp_submit'])){
    $otp = $_POST['number'];
    $email = $_SESSION['email'];

    $otp_query = mysqli_query($con, "SELECT * FROM OTP WHERE `OTP` = '$otp' AND `EMAIL` = '$email' AND `TIME` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)");

    if(mysqli_num_rows($otp_query) > 0) {
        // Insert user data after successful OTP verification
        $fname = $_SESSION['fname'];
        $lname = $_SESSION['lname'];
        $bday = $_SESSION['bday'];
        $gender = $_SESSION['gender'];
        $password = $_SESSION['password'];
        $hashed_password = md5($password);
        $profile_pic = $_SESSION['profile_pic'];
        // $_SESSION['profile_pic'] = $profile_pic; 

        // Set default values for user type and status
        $usertype = "User"; 
        $status = "Pending"; 

        $insert_user = mysqli_query($con, "INSERT INTO STAFF (FIRST_NAME,LAST_NAME,BIRTH_DAY,GENDER,EMAIL, PASSWORD, USERTYPE, STATUS, PROFILE) VALUES ('$fname', '$lname', '$bday', '$gender', '$email', '$hashed_password','$usertype','$status','$profile_pic')");

        if($insert_user) {
            mysqli_query($con, "DELETE FROM OTP WHERE EMAIL = '$email'"); // Delete OTP after use
            session_unset();
            session_destroy();
            
            header("Location: ../index.php");
         
            $otp = "";
        } else {
            // echo "<script>alert('Registration failed! Try again.');</script>";
            echo $con->error;
        }
    } else {
        $invalid = "Your OTP is invalid or expired";
    }
}

// Check if email exists in session before displaying
$email_display = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : "your email";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jego's Review Center - OTP Verification</title>
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
            max-width: 80%;
            max-height: 80%;
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
        .otp-subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #444;
        }
        .form-control {
            height: 50px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 15px;
            text-align: center;
            letter-spacing: 4px;
            font-weight: 600;
        }
        .form-control:focus {
            border-color: #283593;
            box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
        }
        .otp-submit-btn {
            width: 100%;
            padding: 12px;
            background: #283593;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .otp-submit-btn:hover {
            background-color: #1a237e;
        }
        .email-alert {
            background-color: #e8f5e9;
            border-color: #c8e6c9;
            color: #2e7d32;
            border-radius: 5px;
            padding: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        .error-container {
            display: none;
            background: #fee0e0;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            margin-bottom: 15px;
            color: #d32f2f;
            text-align: center;
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
                <h2 class="otp-title">Verify Your Account</h2>
                <p class="otp-subtitle">Enter the Verification Code sent to your email to complete registration.</p>
                
                <form method="post" action="">
                    <div class="email-alert">
                        <span>Verification Code sent to: <strong><?php echo $email_display; ?></strong></span>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Enter 6-digit Verification Code</label>
                        <input type="tel" class="form-control" placeholder="• • • • • •" maxlength="6" name="number" required autofocus>
                        <div class="form-text text-center">The Verification Code is valid for 5 minutes</div>
                    </div>
                    
                    <!-- Error Message Section -->
                    <?php if(isset($invalid)): ?>
                    <div class="error-container" style="display: block;">
                        <?php echo $invalid; ?>
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" name="otp_submit" class="otp-submit-btn">Verify OTP</button>
                    
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>