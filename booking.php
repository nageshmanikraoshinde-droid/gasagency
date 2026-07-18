<?php
session_start();

// Check Login
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "gasease");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

// Save Booking
if(isset($_POST['book'])){
    $cylinder_type = $_POST['cylinder_type'];
    $address = $_POST['address'];
    $booking_date = date("Y-m-d");
    $status = "Pending";

    $sql = "INSERT INTO bookings(email, cylinder_type, address, booking_date, status)
            VALUES('$email', '$cylinder_type', '$address', '$booking_date', '$status')";

    if(mysqli_query($conn, $sql)){
        echo "<script>
                alert('✅ Gas Booking Successful!');
                window.location='profile.php';
              </script>";
    }else{
        echo "<script>
                alert('❌ Booking Failed! Please try again.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Gas Cylinder - GasEase</title>

    <style>
        /* ===== Design Tokens ===== */
        :root {
            --primary: #0D6EFD;
            --primary-dark: #084298;
            --primary-light: #4B9EFF;
            --secondary: #FF7A3D;
            --secondary-dark: #E85D25;
            --success: #28A745;
            --success-dark: #1E7E34;
            --warning: #FFC107;
            --danger: #DC3545;
            --navy: #0D1B2A;
            --cream: #FBF7F0;
            --ink: #1C2733;
            --ink-soft: #4B5A66;
            --border: #E3DCCF;
            --shadow: rgba(13, 27, 42, 0.10);
            --shadow-hover: rgba(13, 27, 42, 0.16);
            
            --font-display: 'Arial Black', Arial, sans-serif;
            --font-body: 'Segoe UI', Arial, sans-serif;
            --font-mono: 'Courier New', monospace;
        }

        /* ===== Reset & Base ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: var(--cream);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== Header ===== */
        header {
            background: var(--navy);
            color: white;
            padding: 0 6vw;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 20px rgba(13, 27, 42, 0.2);
        }

        .header-inner {
            max-width: 1180px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        header .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        header .brand-icon {
            width: 38px;
            height: 38px;
            background: var(--secondary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            color: white;
        }

        header h1 {
            font-family: var(--font-display);
            font-size: 1.6rem;
            letter-spacing: 0.03em;
            color: white;
        }

        header h1 span {
            color: var(--secondary);
        }

        nav {
            display: flex;
            align-items: center;
            gap: 1.75rem;
        }

        nav a {
            color: rgba(251, 247, 240, 0.75);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.15s ease;
            padding: 0.4rem 0;
            border-bottom: 2px solid transparent;
        }

        nav a:hover {
            color: var(--cream);
            border-bottom-color: var(--secondary);
        }

        nav a.active {
            color: var(--cream);
            border-bottom-color: var(--secondary);
        }

        nav a.btn-logout {
            background: var(--secondary);
            color: var(--navy);
            padding: 0.5rem 1.15rem;
            border-radius: 999px;
            font-weight: 600;
            border-bottom: none;
            transition: background 0.15s ease, color 0.15s ease;
        }

        nav a.btn-logout:hover {
            background: var(--secondary-dark);
            color: white;
            border-bottom: none;
        }

        /* ===== Container ===== */
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 5%;
            flex: 1;
            width: 100%;
        }

        /* ===== Breadcrumb ===== */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--ink-soft);
            margin-bottom: 1.5rem;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .breadcrumb a:hover {
            color: var(--secondary);
        }

        .breadcrumb span {
            color: var(--ink-soft);
        }

        /* ===== Booking Card ===== */
        .booking-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 40px var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 48px var(--shadow-hover);
        }

        .booking-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border);
        }

        .booking-header .icon {
            font-size: 48px;
            display: block;
            margin-bottom: 0.5rem;
        }

        .booking-header h2 {
            font-family: var(--font-display);
            font-size: 2rem;
            color: var(--navy);
            margin-bottom: 0.25rem;
        }

        .booking-header p {
            color: var(--ink-soft);
            font-size: 0.95rem;
        }

        /* ===== Form Styles ===== */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--navy);
            margin-bottom: 0.4rem;
        }

        .form-group label .required {
            color: var(--danger);
            margin-left: 0.2rem;
        }

        .form-group .hint {
            font-size: 0.8rem;
            color: var(--ink-soft);
            margin-top: 0.3rem;
            display: block;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-family: var(--font-body);
            font-size: 0.95rem;
            color: var(--ink);
            background: var(--cream);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.10);
            background: white;
        }

        .form-group input:disabled,
        .form-group input[readonly] {
            background: #E9ECEF;
            color: var(--ink-soft);
            cursor: not-allowed;
            opacity: 0.8;
        }

        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%234B5A66' stroke-width='2' fill='none'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 12px;
            padding-right: 2.5rem;
            cursor: pointer;
        }

        .form-group select option {
            padding: 0.5rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
            max-height: 200px;
        }

        /* ===== Cylinder Type Cards ===== */
        .cylinder-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .cylinder-option {
            position: relative;
        }

        .cylinder-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .cylinder-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 0.5rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            height: 100%;
            min-height: 80px;
            background: var(--cream);
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--ink-soft);
        }

        .cylinder-option label .cylinder-icon {
            font-size: 28px;
            display: block;
            margin-bottom: 0.3rem;
        }

        .cylinder-option label .cylinder-weight {
            font-size: 0.75rem;
            color: var(--ink-soft);
            opacity: 0.7;
        }

        .cylinder-option input[type="radio"]:checked + label {
            border-color: var(--primary);
            background: rgba(13, 110, 253, 0.05);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.10);
            color: var(--primary);
        }

        .cylinder-option input[type="radio"]:checked + label .cylinder-weight {
            color: var(--primary);
            opacity: 1;
        }

        .cylinder-option label:hover {
            border-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.10);
        }

        .cylinder-option input[type="radio"]:focus + label {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* ===== Submit Button ===== */
        .btn-submit {
            width: 100%;
            padding: 0.95rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 999px;
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 8px 24px rgba(13, 110, 253, 0.25);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(13, 110, 253, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit .icon {
            font-size: 20px;
        }

        /* ===== Footer Links ===== */
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s ease;
        }

        .form-footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        /* ===== Info Box ===== */
        .info-box {
            background: rgba(13, 110, 253, 0.05);
            border: 1px solid rgba(13, 110, 253, 0.15);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .info-box .icon {
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .info-box .content {
            font-size: 0.9rem;
            color: var(--ink-soft);
        }

        .info-box .content strong {
            color: var(--navy);
        }

        /* ===== Footer ===== */
        footer {
            background: var(--navy);
            color: white;
            text-align: center;
            padding: 2rem 6vw;
            margin-top: auto;
            border-top: 1px solid rgba(251, 247, 240, 0.08);
        }

        footer p {
            font-family: var(--font-mono);
            font-size: 0.85rem;
            color: rgba(251, 247, 240, 0.5);
            margin: 0;
        }

        footer p span {
            color: var(--secondary);
        }

        /* ===== Responsive ===== */
        @media (max-width: 860px) {
            header .header-inner {
                flex-direction: column;
                gap: 1rem;
                padding: 0.75rem 0;
            }

            nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }

            .cylinder-options {
                grid-template-columns: 1fr 1fr 1fr;
                gap: 0.5rem;
            }

            .cylinder-option label {
                min-height: 70px;
                padding: 0.6rem 0.3rem;
                font-size: 0.8rem;
            }

            .cylinder-option label .cylinder-icon {
                font-size: 24px;
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 0 4%;
            }

            .booking-card {
                padding: 1.5rem;
            }

            .booking-header h2 {
                font-size: 1.6rem;
            }

            .cylinder-options {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .cylinder-option label {
                flex-direction: row;
                min-height: 50px;
                padding: 0.6rem 1rem;
                gap: 0.75rem;
            }

            .cylinder-option label .cylinder-icon {
                font-size: 20px;
                margin-bottom: 0;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.3rem;
            }

            nav a {
                font-size: 0.85rem;
            }

            .booking-card {
                padding: 1.25rem;
            }

            .booking-header .icon {
                font-size: 36px;
            }

            .booking-header h2 {
                font-size: 1.4rem;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 0.9rem;
                padding: 0.75rem;
            }

            .btn-submit {
                font-size: 0.95rem;
                padding: 0.85rem;
            }

            .info-box {
                flex-direction: column;
                gap: 0.3rem;
            }
        }

        /* ===== Animations ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .booking-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .cylinder-option {
            animation: fadeInUp 0.6s ease-out both;
        }

        .cylinder-option:nth-child(1) { animation-delay: 0.1s; }
        .cylinder-option:nth-child(2) { animation-delay: 0.2s; }
        .cylinder-option:nth-child(3) { animation-delay: 0.3s; }

        /* ===== Focus Visibility ===== */
        a:focus-visible,
        button:focus-visible,
        .btn-submit:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* ===== Scrollbar ===== */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--cream);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary-light);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }
    </style>
</head>

<body>

<!-- ===== Header ===== -->
<header>
    <div class="header-inner">
        <div class="brand">
            <!-- <div class="brand-icon">⛽</div> -->
            <h1>Gas<span>Ease</span></h1>
        </div>
        <nav>
            <a href="index1.html">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="booking.php" class="active">Book Cylinder</a>
            <a href="index.html" class="btn-logout">Logout</a>
        </nav>
    </div>
</header>

<!-- ===== Main Content ===== -->
<div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index1.html">Home</a>
        <span>›</span>
        <a href="profile.php">Profile</a>
        <span>›</span>
        <span>Book Cylinder</span>
    </div>

    <!-- ===== Booking Form ===== -->
    <div class="booking-card">
        <div class="booking-header">
            <!-- <span class="icon">🛢️</span> -->
            <h2>Book Gas Cylinder</h2>
            <p>Fill in the details below to book your LPG cylinder</p>
        </div>

        <form method="POST">

            <!-- Email (Readonly) -->
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                <span class="hint">Your registered email address</span>
            </div>

            <!-- Cylinder Type - Radio Cards -->
            <div class="form-group">
                <label>
                    Select Cylinder Type
                    <span class="required">*</span>
                </label>
                <div class="cylinder-options">
                    <div class="cylinder-option">
                        <input type="radio" id="domestic" name="cylinder_type" value="Domestic 14.2 KG" required>
                        <label for="domestic">
                            <span class="cylinder-icon">🏠</span>
                            Domestic 14.2 KG
                            <span class="cylinder-weight">₹ 850</span>
                        </label>
                    </div>
                    <div class="cylinder-option">
                        <input type="radio" id="commercial19" name="cylinder_type" value="Commercial 19 KG">
                        <label for="commercial19">
                            <span class="cylinder-icon">🏢</span>
                            Commercial 19 KG
                            <span class="cylinder-weight">₹ 1,200</span>
                        </label>
                    </div>
                    <div class="cylinder-option">
                        <input type="radio" id="commercial47" name="cylinder_type" value="Commercial 47.5 KG">
                        <label for="commercial47">
                            <span class="cylinder-icon">🏭</span>
                            Commercial 47.5 KG
                            <span class="cylinder-weight">₹ 2,800</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="form-group">
                <label>
                    Delivery Address
                    <span class="required">*</span>
                </label>
                <textarea name="address" required placeholder="Enter your complete delivery address"></textarea>
                <span class="hint">Please provide a complete address for delivery</span>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <span class="icon">ℹ️</span>
                <div class="content">
                    <strong>Delivery Information:</strong> Your cylinder will be delivered within 24-48 hours.
                    Tracking details will be sent to your registered email.
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="book" class="btn-submit">
                <!-- <span class="icon"></span> -->
                Book Gas Cylinder
            </button>

        </form>

        <div class="form-footer">
            <a href="index1.html">← Back to Dashboard</a>
        </div>
    </div>

</div>

<!-- ===== Footer ===== -->
<footer>
    <p>© 2026 <span>GasEase</span> Booking System | All Rights Reserved</p>
</footer>

</body>
</html>

<?php
$conn->close();
?>