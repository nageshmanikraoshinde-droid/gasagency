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

// Get Customer Details
$user_sql = "SELECT * FROM users WHERE email='$email'";
$user_result = mysqli_query($conn, $user_sql);

if (mysqli_num_rows($user_result) > 0) {
    $user = mysqli_fetch_assoc($user_result);
} else {
    die("Customer not found.");
}

// Get Booking History
$booking_sql = "SELECT * FROM bookings WHERE email='$email' ORDER BY booking_date DESC";
$booking_result = mysqli_query($conn, $booking_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - GasEase</title>

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
            max-width: 1180px;
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

        /* ===== Profile Card ===== */
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 40px var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 48px var(--shadow-hover);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            font-weight: bold;
            flex-shrink: 0;
        }

        .profile-title h2 {
            font-family: var(--font-display);
            font-size: 1.8rem;
            color: var(--navy);
            margin-bottom: 0.25rem;
        }

        .profile-title .badge {
            display: inline-block;
            background: rgba(40, 167, 69, 0.12);
            color: var(--success);
            padding: 0.2rem 0.8rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .profile-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .profile-item .label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--ink-soft);
            font-weight: 600;
        }

        .profile-item .value {
            font-size: 1.05rem;
            color: var(--ink);
            font-weight: 500;
        }

        /* ===== Booking History ===== */
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

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-header h2 {
            font-family: var(--font-display);
            font-size: 1.6rem;
            color: var(--navy);
        }

        .section-header .booking-count {
            background: var(--primary);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* ===== Table Styles ===== */
        .table-wrapper {
            overflow-x: auto;
            margin-bottom: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
            min-width: 700px;
        }

        table thead {
            background: var(--navy);
            color: white;
        }

        table th {
            padding: 1rem 1.2rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        table td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--border);
            color: var(--ink);
        }

        table tbody tr {
            transition: background 0.15s ease;
        }

        table tbody tr:hover {
            background: rgba(13, 110, 253, 0.03);
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.15);
            color: #D4A000;
        }

        .status-confirmed {
            background: rgba(13, 110, 253, 0.12);
            color: var(--primary);
        }

        .status-delivered {
            background: rgba(40, 167, 69, 0.12);
            color: var(--success);
        }

        .status-cancelled {
            background: rgba(220, 53, 69, 0.12);
            color: var(--danger);
        }

        /* Pay Button */
        .pay-btn {
            background: var(--success);
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: background 0.15s ease, transform 0.15s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25);
        }

        .pay-btn:hover {
            background: var(--success-dark);
            transform: translateY(-2px);
        }

        .pay-btn:active {
            transform: translateY(0);
        }

        .paid-badge {
            display: inline-block;
            background: rgba(40, 167, 69, 0.12);
            color: var(--success);
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .paid-badge::before {
            content: '✓ ';
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state .empty-icon {
            font-size: 48px;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-family: var(--font-display);
            font-size: 1.4rem;
            color: var(--navy);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--ink-soft);
            margin-bottom: 1.5rem;
        }

        /* ===== Buttons ===== */
        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 0.7rem 1.8rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 16px rgba(13, 110, 253, 0.25);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(13, 110, 253, 0.35);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
            box-shadow: 0 4px 16px rgba(255, 122, 61, 0.25);
        }

        .btn-secondary:hover {
            background: var(--secondary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 122, 61, 0.35);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
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

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-grid {
                grid-template-columns: 1fr 1fr;
            }

            .booking-card {
                padding: 1.5rem;
            }

            .profile-card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.3rem;
            }

            nav a {
                font-size: 0.85rem;
            }

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-card {
                padding: 1.25rem;
            }

            .booking-card {
                padding: 1.25rem;
            }

            table {
                font-size: 0.85rem;
                min-width: 500px;
            }

            table th,
            table td {
                padding: 0.75rem;
            }

            .btn-group {
                flex-direction: column;
                width: 100%;
            }

            .btn-group .btn {
                width: 100%;
                text-align: center;
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

        .profile-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .booking-card {
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        /* ===== Focus Visibility ===== */
        a:focus-visible,
        button:focus-visible,
        .btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* ===== Scrollbar ===== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
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
            <!-- <div class="brand-icon"></div> -->
            <h1>Gas<span>Ease</span></h1>
        </div>
        <nav>
            <a href="index1.html">Dashboard</a>
            <a href="profile.php" class="active">Profile</a>
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
        <span>My Profile</span>
    </div>

    <!-- ===== Profile Card ===== -->
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
            </div>
            <div class="profile-title">
                <h2><?php echo htmlspecialchars($user['fullname']); ?></h2>
                <span class="badge">● Active Member</span>
            </div>
        </div>

        <div class="profile-grid">
            <div class="profile-item">
                <span class="label">Customer ID</span>
                <span class="value">#<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="profile-item">
                <span class="label">Username</span>
                <span class="value"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="profile-item">
                <span class="label">Email</span>
                <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="profile-item">
                <span class="label">Mobile</span>
                <span class="value"><?php echo htmlspecialchars($user['mobile']); ?></span>
            </div>
        </div>
    </div>

    <!-- ===== Booking History ===== -->
    <div class="booking-card">
        <div class="section-header">
            <h2>📋 Booking History</h2>
            <span class="booking-count">
                <?php echo mysqli_num_rows($booking_result); ?> Bookings
            </span>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Cylinder Type</th>
                        <th>Booking Date</th>
                        <th>Delivery Address</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($booking_result) > 0): ?>
                        <?php while ($booking = mysqli_fetch_assoc($booking_result)): ?>
                            <tr>
                                <td><strong>#<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo htmlspecialchars($booking['cylinder_type']); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($booking['booking_date'])); ?></td>
                                <td><?php echo htmlspecialchars(substr($booking['address'], 0, 30)) . (strlen($booking['address']) > 30 ? '...' : ''); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    $status_text = '';
                                    switch(strtolower($booking['status'])) {
                                        case 'pending':
                                            $status_class = 'status-pending';
                                            $status_text = 'Pending';
                                            break;
                                        case 'confirmed':
                                            $status_class = 'status-confirmed';
                                            $status_text = 'Confirmed';
                                            break;
                                        case 'delivered':
                                            $status_class = 'status-delivered';
                                            $status_text = 'Delivered';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'status-cancelled';
                                            $status_text = 'Cancelled';
                                            break;
                                        default:
                                            $status_class = 'status-pending';
                                            $status_text = htmlspecialchars($booking['status']);
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (strtolower($booking['status']) == 'pending'): ?>
                                        <a href="payment.php?booking_id=<?php echo $booking['id']; ?>">
                                            <button class="pay-btn">Pay Now</button>
                                        </a>
                                    <?php else: ?>
                                        <span class="paid-badge">Paid</span>
                                    <?php endif; ?>
                                </td>
                                <td>



<?php
if(strtolower($booking['status'])!="pending"){
?>

<a href="receipt.php?booking_id=<?php echo $booking['id']; ?>" target="_blank">
    <button style="
        background:#ff5733;
        color:white;
        border:none;
        padding:8px 15px;
        border-radius:20px;
        cursor:pointer;
        font-weight:bold;">
        📄 Download
    </button>
</a>

<?php
}else{
    echo "-";
}
?>

</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <h3>No Bookings Yet</h3>
                                    <p>You haven't made any bookings. Start your first booking now!</p>
                                    <a href="booking.php" class="btn btn-primary">Book a Cylinder</a>
                                </div>
                            </td>
                        </tr>
                        
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="btn-group">
            <a href="index1.html" class="btn btn-primary">← Back to Dashboard</a>
            <a href="booking.php" class="btn btn-secondary">+ New Booking</a>
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
