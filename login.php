<?php
session_start();

// Database Connection
$conn = new mysqli("localhost", "root", "", "gasease");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if (isset($_POST['email']) && isset($_POST['password'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check Email and Password
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        $_SESSION['id'] = $row['id'];
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['email'] = $row['email'];

        header("Location: index1.html");
        exit();

    } else {

        echo "<script>
                alert('Invalid Email or Password!');
                window.location='login.html';
              </script>";
    }

} else {

    echo "<script>
            alert('Please Enter Email and Password!');
            window.location='login.html';
          </script>";
}

$conn->close();
?>