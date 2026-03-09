<?php
require_once __DIR__ . '/../config/db.php';

$flight_id = (int)($_GET['flight_id'] ?? 0);
if ($flight_id <= 0) exit("Missing flight_id.");

$flightStmt = $pdo->prepare("SELECT * FROM flights WHERE flight_id = ?");
$flightStmt->execute([$flight_id]);
$flight = $flightStmt->fetch();
if (!$flight) exit("Flight not found.");

$seatStmt = $pdo->prepare("
  SELECT seat_id, seat_number, seat_class
  FROM flight_seats
  WHERE flight_id = ? AND is_available = 1
  ORDER BY seat_class, seat_number
");
$seatStmt->execute([$flight_id]);
$seats = $seatStmt->fetchAll();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first = trim($_POST['first_name'] ?? '');
  $last  = trim($_POST['last_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $seat_id = (int)($_POST['seat_id'] ?? 0);

  // Basic validation
  if ($first === '' || $last === '' || $email === '' || $seat_id <= 0) {
    $error = "Please fill all required fields and choose a seat.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format.";
  } else {
    try {
      $pdo->beginTransaction();

      // 1) Find or create passenger
      $findP = $pdo->prepare("SELECT passenger_id FROM passengers WHERE email = ?");
      $findP->execute([$email]);
      $passenger = $findP->fetch();

      if ($passenger) {
        $passenger_id = (int)$passenger['passenger_id'];
      } else {
        $insP = $pdo->prepare("INSERT INTO passengers (first_name, last_name, email, phone) VALUES (?,?,?,?)");
        $insP->execute([$first, $last, $email, $phone]);
        $passenger_id = (int)$pdo->lastInsertId();
      }

      // 2) Check seat availability (lock the row)
      $seatCheck = $pdo->prepare("
        SELECT is_available
        FROM flight_seats
        WHERE seat_id = ? AND flight_id = ?
        FOR UPDATE
      ");
      $seatCheck->execute([$seat_id, $flight_id]);
      $seatRow = $seatCheck->fetch();

      if (!$seatRow || (int)$seatRow['is_available'] !== 1) {
        throw new Exception("Seat is no longer available. Please choose another seat.");
      }

      // 3) Create booking
      $insB = $pdo->prepare("
        INSERT INTO bookings (passenger_id, flight_id, seat_id, booking_status)
        VALUES (?,?,?, 'CONFIRMED')
      ");
      $insB->execute([$passenger_id, $flight_id, $seat_id]);
      $booking_id = (int)$pdo->lastInsertId();

      // 4) Mark seat unavailable
      $updSeat = $pdo->prepare("UPDATE flight_seats SET is_available = 0 WHERE seat_id = ?");
      $updSeat->execute([$seat_id]);

      // 5) Payment record (safe, no card storage)
      $pay = $pdo->prepare("INSERT INTO payments (booking_id, amount, payment_status) VALUES (?, ?, 'PAID')");
      $pay->execute([$booking_id, $flight['base_price']]);

      $pdo->commit();

      header("Location: confirm.php?booking_id={$booking_id}");
      exit;
    } catch (Throwable $e) {
      $pdo->rollBack();
      $error = $e->getMessage();
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Book Flight</title>
  <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
  <h1>Book Flight <?= htmlspecialchars($flight['flight_number']) ?></h1>
  <p><?= htmlspecialchars($flight['origin']) ?> → <?= htmlspecialchars($flight['destination']) ?> |
     Depart: <?= htmlspecialchars($flight['depart_time']) ?> |
     Price: $<?= htmlspecialchars($flight['base_price']) ?></p>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>First Name* <input name="first_name" required></label><br>
    <label>Last Name* <input name="last_name" required></label><br>
    <label>Email* <input name="email" required></label><br>
    <label>Phone <input name="phone"></label><br><br>

    <label>Choose Seat*:
      <select name="seat_id" required>
        <option value="">-- select --</option>
        <?php foreach ($seats as $s): ?>
          <option value="<?= (int)$s['seat_id'] ?>">
            <?= htmlspecialchars($s['seat_number']) ?> (<?= htmlspecialchars($s['seat_class']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <br><br>

    <button type="submit">Confirm Booking</button>
  </form>

  <p><a href="index.php">Back</a></p>
</body>
</html>