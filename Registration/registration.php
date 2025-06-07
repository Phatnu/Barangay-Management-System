<?php 
session_start();
include_once("../connections/connection.php");
$con = connection();

// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $con->query($sql);
$rowdesign = $query->fetch_assoc();
// TO DISPLAY THE INFO for design

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if(isset($_POST['submit']))
{
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $bday = $_POST['bday'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    
    // Always use placeholder.jpg as profile picture
    $profile_pic = '../images/placeholder.jpg'; // Default profile image path that will be used for all users

    $_SESSION['fname'] = $fname;
    $_SESSION['lname'] = $lname;
    $_SESSION['bday'] = $bday;
    $_SESSION['gender'] = $gender;
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $pass;
    $_SESSION['profile_pic'] = $profile_pic; // Store profile picture path in session

    // Check if email already exists
    $check_email = mysqli_query($con, "SELECT * FROM STAFF WHERE EMAIL = '$email'");

    if(mysqli_num_rows($check_email) > 0) {
        $invalid = "Email already exists. Please use a different email.";
    } 
    else 
    {
        $otp = rand(100000, 999999);

        // Store OTP in database with expiration
        $otp_query = mysqli_query($con, "INSERT INTO `OTP`(`EMAIL`, `OTP`, `TIME`) VALUES ('$email', '$otp', NOW())");

        if($otp_query) {
            // Send OTP email
            $message = "<div>
            <p>Hello <strong>" . $fname . "</strong></p>
            <br>
            <p>Your OTP for registration is: <strong>$otp</strong></p>
            </div>";

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'cuetop175@gmail.com';
            $mail->Password = 'uhxx lxwx qjur owou';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;     

            $mail->setFrom('cuetop175@gmail.com', 'Mailer');
            $mail->addAddress($email);  

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = $message;

            if($mail->send()){
                header("location:otpregister.php"); // Redirect to OTP verification page
            } else {
                echo "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jego's Review Center - Registration</title>
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
            overflow-y: auto;
        }
        .registration-form {
            width: 100%;
            max-width: 450px;
        }
        .registration-title {
            color: #283593;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
            text-align: center;
        }
        .registration-subtitle {
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
        }
        .form-control:focus {
            border-color: #283593;
            box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
        }
        .register-btn {
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
        .register-btn:hover {
            background-color: #1a237e;
        }
        .form-footer {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        .form-footer a {
            color: #283593;
            text-decoration: none;
            margin-left: 5px;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        #error-container {
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
            <div class="registration-form" data-aos="fade-up" data-aos-duration="3000">
                <h2 class="registration-title">Create Your Account</h2>
                <p class="registration-subtitle">Fill in the details to register for BRM System.</p>
                
                <form method="post" action="" enctype="multipart/form-data"> 
                    <div class="mb-3">
                        <!-- <label class="form-label">First Name</label> -->
                        <input type="text" name="fname" class="form-control" placeholder="Enter your first name" required>
                    </div>
                    
                    <div class="mb-3">
                        <!-- <label class="form-label">Last Name</label> -->
                        <input type="text" name="lname" class="form-control" placeholder="Enter your last name" required>
                    </div>
                    
                    <div class="mb-3">
                        <!-- <label class="form-label">Birthday</label> -->
                        <input type="date" name="bday" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <!-- <label class="form-label">Gender</label> -->
                        <select name="gender" class="form-control" required>
                            <option value="" disabled selected>Select your gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <!-- <label class="form-label">Email</label> -->
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="mb-3">
                        <!-- <label class="form-label">Password</label> -->
                        <input type="password" name="password" class="form-control" placeholder="Create a password" required>
                    </div>
                    
                    <!-- Error Message Section -->
                    <?php if(isset($invalid)): ?>
                    <div id="error-container" style="display: block;">
                        <?php echo $invalid; ?>
                    </div>
                    <?php endif; ?>
                    <div class="text-center mb-4" style="display: none;">
                        <p class="text-muted small mb-2">Default profile picture</p>
                        <div class="profile-pic-container">
                            <img id="preview-image" src="../images/placeholder.jpg" width="50" height="50" alt="Profile Picture" class="profile-pic">
                        </div>
                        <!-- Hidden input to pass the default profile image -->
                        <input type="hidden" name="profile_pic" value="../images/placeholder.jpg">
                    </div>
                    <button type="submit" name="submit" class="register-btn">Register</button>
                    <div class="form-footer">
                        <span>Already have an account?</span>
                        <a href="../index.php">Sign in</a>
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