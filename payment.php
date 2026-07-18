<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost","root","","gasease");

if($conn->connect_error){
    die("Connection Failed : ".$conn->connect_error);
}

$email = $_SESSION['email'];

if(!isset($_GET['booking_id'])){
    die("Invalid Booking.");
}

$booking_id = $_GET['booking_id'];

// Get Booking Details
$sql = "SELECT * FROM bookings WHERE id='$booking_id' AND email='$email'";
$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){
    die("Booking Not Found.");
}

$booking = mysqli_fetch_assoc($result);

$cylinder = $booking['cylinder_type'];

// Auto Amount
if($cylinder=="Domestic 14.2 KG"){
    $amount = 1100;
}
elseif($cylinder=="Commercial 19 KG"){
    $amount = 2200;
}
elseif($cylinder=="Commercial 47.5 KG"){
    $amount = 5200;
}
else{
    $amount = 1000;
}

// Payment
if(isset($_POST['pay'])){

    $method = $_POST['payment_method'];
    $date = date("Y-m-d H:i:s");

    $pay = "INSERT INTO payments
    (booking_id,email,cylinder_type,amount,payment_method,payment_date,status)
    VALUES
    ('$booking_id','$email','$cylinder','$amount','$method','$date','Success')";

    if(mysqli_query($conn,$pay)){

        mysqli_query($conn,"UPDATE bookings SET status='Paid' WHERE id='$booking_id'");

        echo "<script>
        alert('✅ Payment Successful!');
        window.location='profile.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gas Payment - GasEase</title>

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
            max-width: 560px;
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

        /* ===== Payment Card ===== */
        .payment-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 40px var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .payment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 48px var(--shadow-hover);
        }

        .payment-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border);
        }

        .payment-header .icon {
            font-size: 56px;
            display: block;
            margin-bottom: 0.5rem;
        }

        .payment-header h2 {
            font-family: var(--font-display);
            font-size: 2rem;
            color: var(--navy);
            margin-bottom: 0.25rem;
        }

        .payment-header p {
            color: var(--ink-soft);
            font-size: 0.95rem;
        }

        /* ===== Order Summary ===== */
        .order-summary {
            background: var(--cream);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
        }

        .order-summary .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .order-summary .summary-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .order-summary .summary-row .label {
            color: var(--ink-soft);
            font-size: 0.9rem;
        }

        .order-summary .summary-row .value {
            font-weight: 600;
            color: var(--navy);
            font-size: 0.95rem;
        }

        .order-summary .summary-row.total {
            padding-top: 0.75rem;
            margin-top: 0.5rem;
            border-top: 2px solid var(--border);
        }

        .order-summary .summary-row.total .label {
            font-weight: 700;
            font-size: 1rem;
            color: var(--navy);
        }

        .order-summary .summary-row.total .value {
            font-size: 1.2rem;
            color: var(--primary);
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

        .form-group input,
        .form-group select {
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
        .form-group select:focus {
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

        /* ===== Payment Methods ===== */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .payment-method {
            position: relative;
        }

        .payment-method input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .payment-method label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 0.5rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            background: var(--cream);
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--ink-soft);
        }

        .payment-method label .method-icon {
            font-size: 22px;
        }

        .payment-method input[type="radio"]:checked + label {
            border-color: var(--success);
            background: rgba(40, 167, 69, 0.05);
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.10);
            color: var(--success);
        }

        .payment-method label:hover {
            border-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.10);
        }

        .payment-method input[type="radio"]:focus + label {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* ===== Submit Button ===== */
        .btn-pay {
            width: 100%;
            padding: 0.95rem;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 999px;
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 8px 24px rgba(40, 167, 69, 0.25);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-pay:hover {
            background: var(--success-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(40, 167, 69, 0.35);
        }

        .btn-pay:active {
            transform: translateY(0);
        }

        .btn-pay .icon {
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

        /* ===== Security Badge ===== */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 0.75rem;
            background: rgba(40, 167, 69, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(40, 167, 69, 0.10);
            color: var(--ink-soft);
            font-size: 0.85rem;
        }

        .security-badge .icon {
            font-size: 18px;
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
        }

        @media (max-width: 600px) {
            .container {
                padding: 0 4%;
            }

            .payment-card {
                padding: 1.5rem;
            }

            .payment-header h2 {
                font-size: 1.6rem;
            }

            .payment-header .icon {
                font-size: 44px;
            }

            .payment-methods {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .payment-method label {
                font-size: 0.8rem;
                padding: 0.6rem 0.3rem;
            }

            .payment-method label .method-icon {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.3rem;
            }

            nav a {
                font-size: 0.85rem;
            }

            .payment-card {
                padding: 1.25rem;
            }

            .payment-header .icon {
                font-size: 36px;
            }

            .payment-header h2 {
                font-size: 1.4rem;
            }

            .order-summary {
                padding: 1rem;
            }

            .form-group input,
            .form-group select {
                font-size: 0.9rem;
                padding: 0.75rem;
            }

            .btn-pay {
                font-size: 0.95rem;
                padding: 0.85rem;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }

            .security-badge {
                font-size: 0.75rem;
                padding: 0.6rem;
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

        .payment-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .payment-method {
            animation: fadeInUp 0.6s ease-out both;
        }

        .payment-method:nth-child(1) { animation-delay: 0.1s; }
        .payment-method:nth-child(2) { animation-delay: 0.15s; }
        .payment-method:nth-child(3) { animation-delay: 0.2s; }
        .payment-method:nth-child(4) { animation-delay: 0.25s; }

        /* ===== Focus Visibility ===== */
        a:focus-visible,
        button:focus-visible,
        .btn-pay:focus-visible,
        input:focus-visible,
        select:focus-visible {
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
            <a href="booking.php">Book Cylinder</a>
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
        <span>Payment</span>
    </div>

    <!-- ===== Payment Card ===== -->
    <div class="payment-card">
        <div class="payment-header">
            <span class="icon">💳</span>
            <h2>Secure Payment</h2>
            <p>Complete your payment to confirm your booking</p>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <div class="summary-row">
                <span class="label">Booking ID</span>
                <span class="value">#<?php echo str_pad($booking_id, 4, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="summary-row">
                <span class="label">Cylinder Type</span>
                <span class="value"><?php echo htmlspecialchars($cylinder); ?></span>
            </div>
            <div class="summary-row">
                <span class="label">Email</span>
                <span class="value"><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="summary-row total">
                <span class="label">Total Amount</span>
                <span class="value">₹ <?php echo number_format($amount, 2); ?></span>
            </div>
        </div>

        <form method="POST">

            <!-- Email (Readonly) -->
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>

            <!-- Gas Cylinder (Readonly) -->
            <div class="form-group">
                <label>Gas Cylinder</label>
                <input type="text" value="<?php echo htmlspecialchars($cylinder); ?>" readonly>
            </div>

            <!-- Amount (Readonly) -->
            <div class="form-group">
                <label>Amount Payable</label>
                <input type="text" value="₹ <?php echo number_format($amount, 2); ?>" readonly style="font-weight:700;color:var(--primary);font-size:1.1rem;">
            </div>

            <!-- Payment Method -->
            <div class="form-group">
                <label>
                    Select Payment Method
                    <span class="required">*</span>
                </label>
                <div class="payment-methods">
                    <div class="payment-method">
                        <input type="radio" id="upi" name="payment_method" value="UPI" required>
                        <label for="upi">
                            <span class="method-icon">📱</span>
                            UPI
                        </label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="credit" name="payment_method" value="Credit Card">
                        <label for="credit">
                            <span class="method-icon">💳</span>
                            Credit Card
                        </label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="debit" name="payment_method" value="Debit Card">
                        <label for="debit">
                            <span class="method-icon">🏦</span>
                            Debit Card
                        </label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="netbanking" name="payment_method" value="Net Banking">
                        <label for="netbanking">
                            <span class="method-icon">🌐</span>
                            Net Banking
                        </label>
                    </div>
                </div>
            </div>

            <!-- Security Badge -->
            <div class="security-badge">
                <span class="icon">🔒</span>
                <span>Your payment is secure. All transactions are encrypted.</span>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="pay" class="btn-pay">
                <span class="icon">🔒</span>
                Pay ₹ <?php echo number_format($amount, 2); ?>
            </button>

        </form>

        <div class="form-footer">
            <a href="profile.php">← Back to Profile</a>
        </div>
    </div>

</div>

<!-- ===== Footer ===== -->
<footer>
    <p>© 2026 <span>GasEase</span> Booking System | All Rights Reserved</p>
</footer>

</body>
</html>