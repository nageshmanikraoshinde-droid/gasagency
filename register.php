<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "gasease");

// Check Connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if (isset($_POST['fullname'])) {

    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Check Password
    if ($password != $confirm) {
        echo "<script>
                alert('Password and Confirm Password do not match!');
                window.location='register.html';
              </script>";
        exit();
    }

    // Check Email Already Exists
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email already registered!');
                window.location='register.html';
              </script>";
        exit();
    }

    // Encrypt Password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert Data
    $stmt = $conn->prepare("INSERT INTO users(fullname, username, email, mobile, password) VALUES(?,?,?,?,?)");
    $stmt->bind_param("sssss", $fullname, $username, $email, $mobile, $password);

    if ($stmt->execute()) {
        echo "<script>
                alert('Registration Successful!');
                window.location='login.html';
              </script>";
    } else {
        echo "<script>
                alert('Registration Failed!');
                window.location='register.html';
              </script>";
    }

    $stmt->close();
    $check->close();
}

$conn->close();
?>