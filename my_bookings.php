<?php
require_once __DIR__ . '/../config/db.php';

$sql = "
SELECT
  b.booking_id,
  p.first_name, p.last_name, p.email,
  f.flight_number, f.origin, f.destination, f.depart_time,
  s.seat_number,
  b.booking_status,
  b.booked_at
FROM bookings b
JOIN passengers p ON b.passenger_id = p.passenger_id
JOIN flights f ON b.flight_id = f.flight_id
JOIN flight_seats s ON b.seat_id = s.seat_id
ORDER BY b.booked_at DESC

";
$rows = $pdo->query($sql)->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Bookings</title>
<link rel="stylesheet" href="../assets/styles.css"></head>
<body>
  <h1>Bookings</h1>
  <table border="1" cellpadding="6">
    <tr>
      <th>ID</th><th>Passenger</th><th>Email</th><th>Flight</th><th>Route</th><th>Depart</th><th>Seat</th><th>Status</th><th>Action</th>
    </tr>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= (int)$r['booking_id'] ?></td>
        <td><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['flight_number']) ?></td>
        <td><?= htmlspecialchars($r['origin'].' → '.$r['destination']) ?></td>
        <td><?= htmlspecialchars($r['depart_time']) ?></td>
        <td><?= htmlspecialchars($r['seat_number']) ?></td>
        <td><?= htmlspecialchars($r['booking_status']) ?></td>
        <td>
      <?php if ($r['booking_status'] === 'CONFIRMED'): ?>



<p><a class="btn" href="cancel.php?booking_id=<?= (int)$r['booking_id'] ?>">Cancel</a></p>
  
<?php else: ?>
 
<?php endif; ?>


        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <p><a href="index.php">Back to Search</a></p>
</body>
</html>
