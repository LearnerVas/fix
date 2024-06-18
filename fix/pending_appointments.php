<?php
session_start();
include 'config.php';
include 'return.php';;

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT 
        a.APTM_ID, 
        CONCAT(p.PT_FName, ' ', p.PT_LName) AS Patient, 
        CONCAT(d.D_FName, ' ', d.D_LName) AS Doctor, 
        CONCAT(n.N_FName, ' ', n.N_LName) AS Nurse,
        s.Serv_Title AS Service, 
        a.APTM_Date, 
        a.APTM_Status, 
        u.User_UName AS AppointedBy
            FROM 
             appointments a
            LEFT JOIN 
                patient p ON a.PT_ID = p.PT_ID
            LEFT JOIN 
                doctors d ON a.Doctor_ID = d.Doctor_ID
            LEFT JOIN 
                nurses n ON a.Nurse_ID = n.Nurse_ID
            LEFT JOIN 
                services s ON a.Serv_ID = s.Serv_ID
            LEFT JOIN 
                adminstaff u ON a.User_ID = u.User_ID
            WHERE APTM_Status ='Pending'");

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending Appointments</title>
    <style>
        header{
            background: url(assets/img.jpg) no-repeat center fixed; 
            background-size: cover;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background: url(assets/img.jpg) no-repeat center fixed;
            background-size: cover;
            height: 200px;
            text-align: center;
            color: white;
            padding: 60px 0;
        }
        .content {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .add-appointments, .completed, .cancelled, .pending {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .add-appointments:hover, .completed:hover, .cancelled:hover, .pending:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form action="dashboard.php" method="post">
        <?php include 'return.php'; ?>
    </form>
    <header>
        <h1>Pending Appointments</h1>
    </header>
    <div class="content">
        <table>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Nurse</th>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
                <th>AppointedBy</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['APTM_ID'] ?></td>
                    <td><?= $row['Patient'] ?></td>
                    <td><?= $row['Doctor'] ?></td>
                    <td><?= $row['Nurse'] ?></td>
                    <td><?= $row['Service'] ?></td>
                    <td><?= $row['APTM_Date'] ?></td>
                    <td><?= $row['APTM_Status'] ?></td>
                    <td><?= $row['AppointedBy'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="add_appointment.php" class="add-appointments">Add Appointments</a>
        <a href='completed_appointment.php' class="completed">Completed</a>
        <a href='cancelled_appointments.php' class="cancelled">Cancelled</a>
        <a href='pending_appointments.php' class="pending">Pending</a>
    </div>
    <?php
    // Close connection
    $conn->close();
    ?>
</body>
</html>