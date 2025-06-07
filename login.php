<?php

include_once("connections/connection.php");
$con = connection();

IF(isset($_POST['submit'])){
    $username = $_POST['email'];
    $password = md5($_POST['pass']);

    $select = "SELECT * FROM STAFF WHERE EMAIL='$username' AND PASSWORD='$password'";
    $result = $con->query($select);
    $row = mysqli_fetch_array($result);

    if ($row["USERTYPE"] == "Admin") {
        echo "Admin";
    } elseif ($row["USERTYPE"] == "User") {
        echo "User";
    } else {
        echo "WALA SA LISTAHAN";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST">
    <input type="text" name="email" placeholder="email">
    <input type="password" name="pass" placeholder="password">
    <button type="submit" name="submit">login</button>
    </form>
</body>
</html>