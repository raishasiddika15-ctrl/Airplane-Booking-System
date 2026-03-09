<?php
require_once __DIR__ . '/../config/db.php';

$origin = trim($_GET['origin'] ?? '');
$destination = trim($_GET['destination'] ?? '');

$sql = "
SELECT
  f.flight_id,
  f.flight_number,
  f.origin,
  f.destination,
  f.depart_time,
  f.arrive_time,
  f.base_price,
  SUM(CASE WHEN s.is_available = 1 THEN 1 ELSE 0 END) AS seats_available
FROM flights f
JOIN flight_seats s ON s.flight_id = f.flight_id
WHERE (:origin_blank = '' OR f.origin = :origin_val)
  AND (:dest_blank = '' OR f.destination = :dest_val)
GROUP BY f.flight_id
ORDER BY f.depart_time
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':origin_blank' => $origin,
  ':origin_val'   => $origin,
  ':dest_blank'   => $destination,
  ':dest_val'     => $destination
]);

$flights = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Airline Booking System</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="brand">
        <div class="logo"></div>
        <div>
          <h1>Airline Booking System</h1>
          <p>Search flights, book seats, and manage bookings</p>
        </div>
      </div>
      <a class="btn" href="my_bookings.php" style="text-decoration:none;">View Bookings</a>
    </div>

    <div class="card">
      <div class="section">
        <h2 class="section-title">Search Flights <span class="pill">Available Routes</span></h2>

        <form method="get" class="form">
          <div class="field">
            <label>Origin</label>
            <input type="text" name="origin" placeholder="e.g. New York" value="<?= htmlspecialchars($origin) ?>">
          </div>

          <div class="field">
            <label>Destination</label>
            <input type="text" name="destination" placeholder="e.g. Miami" value="<?= htmlspecialchars($destination) ?>">
          </div>

          <button class="btn" type="submit">Search Flights</button>
        </form>
      </div>

      <div class="section">
        <h2 class="section-title">Flights <span class="pill"><?= count($flights) ?> Found</span></h2>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Flight</th>
              <th>Route</th>
              <th>Depart</th>
              <th>Price</th>
              <th>Seats</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($flights) > 0): ?>
              <?php foreach ($flights as $f): ?>
                <tr>
                  <td><?= htmlspecialchars($f['flight_number']) ?></td>
                  <td><?= htmlspecialchars($f['origin']) ?> → <?= htmlspecialchars($f['destination']) ?></td>
                  <td><?= htmlspecialchars($f['depart_time']) ?></td>
                  <td>$<?= htmlspecialchars($f['base_price']) ?></td>
                  <td><?= htmlspecialchars($f['seats_available']) ?></td>
                  <td>
                    <a class="btn" href="book.php?flight_id=<?= (int)$f['flight_id'] ?>" style="text-decoration:none;">Book</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="empty-state">No flights found.</td>
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