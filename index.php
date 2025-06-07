<?php 
session_start();
include_once("connections/connection.php");
$con = connection();

// TO DISPLAY THE INFO for design
$sql = "SELECT * FROM Barangay_Info";
$query = $con->query($sql);
$rowdesign = $query->fetch_assoc();
// TO DISPLAY THE INFO for design

// FOR PHP MAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer-master/src/Exception.php';
require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';

$error_msg = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = md5($_POST['password']);  

    // Check for matching user with correct credentials
    $user_query = mysqli_query($con, "SELECT * FROM STAFF WHERE EMAIL='$email' AND PASSWORD='$pass'");

    if (mysqli_num_rows($user_query) > 0) {
        $result = mysqli_fetch_assoc($user_query);

        // Check if account is Active
        if ($result['STATUS'] === 'Active') {
            $username = $result['FIRST_NAME'];
            $id = $result['STAFFID'];

            // Store staff ID in session
            $_SESSION['STAFFID'] = $id;

            // Generate OTP
            $otp = rand(100000, 999999);
            $message = '<div>
                <p>Hello <strong>' . $username . '</strong></p>
                <br>
                <p>This is your OTP login number: <strong>' . $otp . '</strong></p>
                <p>Please do not share this OTP with anyone. It is for your secure login.</p>
            </div>';

            // Send OTP via PHPMailer
            $mail = new PHPMailer(true);
            $mail->isSMTP(true);
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'cuetop175@gmail.com';
            $mail->Password = 'uhxx lxwx qjur owou';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;     

            $mail->setFrom('cuetop175@gmail.com', 'Mailer');
            $mail->addAddress($email);  

            $mail->isHTML(true);
            $mail->Subject = 'OTP';
            $mail->Body = $message;

            if ($mail->send()) {
                // Save OTP to database
                mysqli_query($con, "INSERT INTO `OTP`(`STAFFID`, `OTP`) VALUES ('$id', '$otp')");
                header("Location: otp.php");
                exit();
            } else {
                $error_msg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error_msg = "Your account is inactive or pending. Please contact the administrator.";
        }
    } else {
        $error_msg = "Username and password is incorrect.";
    }
}


// $error_msg = "";

// if(isset($_POST['login']))
// {
//     $email = $_POST['email'];
//     $pass = md5($_POST['password']);  

//     $user_query = mysqli_query($con,"SELECT * FROM STAFF WHERE EMAIL='$email' AND PASSWORD='$pass'");

//     if(mysqli_num_rows($user_query)>0){
//         $result = mysqli_fetch_assoc($user_query);

//         $username = $result['FIRST_NAME'];
//         $id = $result['STAFFID'];
        
//         // FOR PHP MAILER
//         $_SESSION['STAFFID'] = $result['STAFFID'];
//         $otp = rand(100000, 999999);
//         $message = '<div>
//         <p>Hello <strong>' . $username . '</strong></p>
//         <br>
//         <p>This is your OTP login number: <strong>' . $otp . '</strong></p>
//         <p>Please do not share this OTP with anyone. It is for your secure login.</p>
//         </div>';

//         $mail = new PHPMailer(true);
//         $mail->isSMTP(true);
//         $mail->Host = 'smtp.gmail.com';
//         $mail->SMTPAuth = true;
//         $mail->Username = 'cuetop175@gmail.com';
//         $mail->Password = 'uhxx lxwx qjur owou';
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port = 587;     

//         $mail->setFrom('cuetop175@gmail.com', 'Mailer');
//         $mail->addAddress($email);  

//         $mail->isHTML(true);
//         $mail->Subject = 'OTP';
//         $mail->Body = $message;

//         if($mail->send()){
//             $otp_query = mysqli_query($con,"INSERT INTO `OTP`(`STAFFID`, `OTP`) VALUES ('$id','$otp')");
//             header("location:otp.php");
//         }
//         else{
//             echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
//         }
//     }
//     else{
//         $error_msg = "Username and Password is incorrect";
//     }
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jego's Review Center - Login</title>
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
            url('images/bgblue.jpg');
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
        .login-form {
            width: 100%;
            max-width: 400px;
        }
        .login-title {
            color: #283593;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
            text-align: center;
        }
        .login-subtitle {
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
        .sign-in-btn {
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
        .sign-in-btn:hover {
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
                <img src="./admin/barangayimage/<?=$rowdesign['MUNICIPAL_LOGO']?>"
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
            <div class="login-form" data-aos="fade-up"
     data-aos-duration="3000">
                <h2 class="login-title">Welcome back!</h2>
                <p class="login-subtitle">Enter your email and password to sign in.</p>
                
                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    
                    <div class="mb-3 d-flex justify-content-end">
                        <a href="./Forget_Password/requestotp.php" style="color: #283593; text-decoration: none; font-size: 14px;">Forgot password?</a>
                    </div>
                    
                    <!-- Error Message Section -->
                    <?php if(!empty($error_msg)): ?>
                    <div id="error-container" style="display: block;">
                        <?php echo $error_msg; ?>
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" name="login" class="sign-in-btn">Sign in</button>
                    
                    <div class="form-footer">
                        <span>Doesn't have an account?</span>
                        <a href="Registration/registration.php">Create an account</a>
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