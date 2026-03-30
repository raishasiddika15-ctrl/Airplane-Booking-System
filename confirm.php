<?php
require_once 'db.php';

$booking_id = (int)($_GET['booking_id'] ?? 0);
if ($booking_id <= 0) exit("Missing booking_id.");

$sql = "
SELECT
  b.booking_id, b.booking_status, b.booked_at,
  p.first_name, p.last_name, p.email,
  f.flight_number, f.origin, f.destination, f.depart_time,
  s.seat_number,
  pay.amount, pay.payment_status, pay.paid_at
FROM bookings b
JOIN passengers p ON b.passenger_id = p.passenger_id
JOIN flights f ON b.flight_id = f.flight_id
JOIN flight_seats s ON b.seat_id = s.seat_id
JOIN payments pay ON pay.booking_id = b.booking_id
WHERE b.booking_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$booking_id]);
$row = $stmt->fetch();
if (!$row) exit("Booking not found.");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Booking Confirmed</title>
<link rel="stylesheet" href="../assets/styles.css"></head>
<body>
  <h1>Booking Confirmed</h1>
  <p><b>Booking #</b> <?= (int)$row['booking_id'] ?> (<?= htmlspecialchars($row['booking_status']) ?>)</p>
  <p><b>Passenger:</b> <?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?> (<?= htmlspecialchars($row['email']) ?>)</p>
  <p><b>Flight:</b> <?= htmlspecialchars($row['flight_number']) ?> | <?= htmlspecialchars($row['origin']) ?> → <?= htmlspecialchars($row['destination']) ?></p>
  <p><b>Depart:</b> <?= htmlspecialchars($row['depart_time']) ?></p>
  <p><b>Seat:</b> <?= htmlspecialchars($row['seat_number']) ?></p>
  <p><b>Payment:</b> $<?= htmlspecialchars($row['amount']) ?> (<?= htmlspecialchars($row['payment_status']) ?>)</p>

  <p><a href="my_bookings.php">View All Bookings</a></p>
</body>
</html>