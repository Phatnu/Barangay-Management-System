<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "BARANGAY_SYSTEM";

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $ex) {
    echo "Connection Failed because: ".$ex;
}

if (isset($_POST["otp"]) && isset($_POST["new_password"]) && isset($_POST["confirm_password"])) {
    $otp = $_POST["otp"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if ($new_password == $confirm_password) {
        $hashed_password = md5($new_password);

        $sql = "UPDATE staff SET PASSWORD = :password, otp = NULL WHERE otp = :otp";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["password" => $hashed_password, "otp" => $otp]);

        if ($stmt->rowCount() > 0) {
            // Password updated successfully, redirect to login page
            header("Location: ../index.php");
            exit();
        } else {
            echo "Invalid OTP or OTP already used.";
        }
    } else {
        echo "Passwords do not match!";
    }
} else {
    echo "All fields are required!";
}
?>
