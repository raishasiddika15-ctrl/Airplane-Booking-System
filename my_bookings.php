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
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Bookings</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="brand">
        <div class="logo"></div>
        <div>
          <h1>Bookings</h1>
          <p>Manage and review all reservations</p>
        </div>
      </div>
      <a class="btn" href="index.php" style="text-decoration:none;">Back to Search</a>
    </div>

    <div class="card">
      <div class="section">
        <h2 class="section-title">Booking List <span class="pill"><?= count($rows) ?> Records</span></h2>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Passenger</th>
              <th>Email</th>
              <th>Flight</th>
              <th>Route</th>
              <th>Depart</th>
              <th>Seat</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($rows) > 0): ?>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= (int)$r['booking_id'] ?></td>
                  <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                  <td><?= htmlspecialchars($r['email']) ?></td>
                  <td><?= htmlspecialchars($r['flight_number']) ?></td>
                  <td><?= htmlspecialchars($r['origin'] . ' → ' . $r['destination']) ?></td>
                  <td><?= htmlspecialchars($r['depart_time']) ?></td>
                  <td><?= htmlspecialchars($r['seat_number']) ?></td>
                  <td>
                    <?php if ($r['booking_status'] === 'CONFIRMED'): ?>
                      <span class="badge ok">CONFIRMED</span>
                    <?php else: ?>
                      <span class="badge warn">CANCELLED</span>
                    <?php endif; ?>
                  </td>
                  <td class="actions">
                    <?php if ($r['booking_status'] === 'CONFIRMED'): ?>
                      <a href="cancel.php?booking_id=<?= (int)$r['booking_id'] ?>">Cancel</a>
                    <?php else: ?>
                      <span class="badge info">No action</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="empty-state">No bookings found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="footer">Made for CIS project</div>
  </div>
</body>
</html>