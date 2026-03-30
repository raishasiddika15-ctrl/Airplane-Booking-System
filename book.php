<?php
require_once 'db.php';

$flight_id = (int)($_GET['flight_id'] ?? 0);
if ($flight_id <= 0) exit("Missing flight_id.");

$flightStmt = $pdo->prepare("SELECT * FROM flights WHERE flight_id = ?");
$flightStmt->execute([$flight_id]);
$flight = $flightStmt->fetch();
if (!$flight) exit("Flight not found.");

$seatsStmt = $pdo->prepare("
  SELECT seat_id, seat_number, seat_class
  FROM flight_seats
  WHERE flight_id = ? AND is_available = 1
  ORDER BY seat_class, seat_number
");
$seatsStmt->execute([$flight_id]);
$seats = $seatsStmt->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first = trim($_POST['first_name'] ?? '');
  $last  = trim($_POST['last_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $seat_id = (int)($_POST['seat_id'] ?? 0);

  if ($first === '' || $last === '' || $email === '' || $seat_id <= 0) {
    $error = "Please fill all required fields and choose a seat.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format.";
  } else {
    try {
      $pdo->beginTransaction();

      $find = $pdo->prepare("SELECT passenger_id FROM passengers WHERE email = ?");
      $find->execute([$email]);
      $p = $find->fetch();

      if ($p) {
        $passenger_id = (int)$p['passenger_id'];
      } else {
        $ins = $pdo->prepare("INSERT INTO passengers(first_name,last_name,email,phone) VALUES (?,?,?,?)");
        $ins->execute([$first, $last, $email, $phone]);
        $passenger_id = (int)$pdo->lastInsertId();
      }

      $seatCheck = $pdo->prepare("
        SELECT is_available
        FROM flight_seats
        WHERE seat_id = ? AND flight_id = ?
        FOR UPDATE
      ");
      $seatCheck->execute([$seat_id, $flight_id]);
      $seatRow = $seatCheck->fetch();

      if (!$seatRow || (int)$seatRow['is_available'] !== 1) {
        throw new Exception("Seat is no longer available.");
      }

      $book = $pdo->prepare("
        INSERT INTO bookings(passenger_id, flight_id, seat_id, booking_status)
        VALUES (?, ?, ?, 'CONFIRMED')
      ");
      $book->execute([$passenger_id, $flight_id, $seat_id]);
      $booking_id = (int)$pdo->lastInsertId();

      $upd = $pdo->prepare("UPDATE flight_seats SET is_available = 0 WHERE seat_id = ?");
      $upd->execute([$seat_id]);

      $pay = $pdo->prepare("INSERT INTO payments(booking_id, amount, payment_status) VALUES (?, ?, 'PAID')");
      $pay->execute([$booking_id, $flight['base_price']]);

      $pdo->commit();

      header("Location: confirm.php?booking_id=" . $booking_id);
      exit;
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) {
        $pdo->rollBack();
      }
      $error = $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Book Flight</title>
  <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<div class="container">

  <div class="header">
    <div class="brand">
      <div class="logo"></div>
      <div>
        <h1>Book Flight</h1>
        <p><?= htmlspecialchars($flight['flight_number']) ?> · <?= htmlspecialchars($flight['origin']) ?> → <?= htmlspecialchars($flight['destination']) ?></p>
      </div>
    </div>
    <a class="btn" href="index.php">Back to Search</a>
  </div>

  <div class="card">
    <div class="section">
      <h2 class="section-title">Passenger Details <span class="pill">Booking Form</span></h2>

      <?php if ($error): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" class="form">
        <div class="field">
          <label>First Name</label>
          <input type="text" name="first_name" required>
        </div>

        <div class="field">
          <label>Last Name</label>
          <input type="text" name="last_name" required>
        </div>

        <div class="field">
          <label>Email</label>
          <input type="email" name="email" required>
        </div>

        <div class="field">
          <label>Phone</label>
          <input type="text" name="phone">
        </div>

        <div class="field">
          <label>Choose Seat</label>
          <select name="seat_id" required>
            <option value="">-- select a seat --</option>
            <?php foreach ($seats as $s): ?>
              <option value="<?= (int)$s['seat_id'] ?>">
                <?= htmlspecialchars($s['seat_number']) ?> (<?= htmlspecialchars($s['seat_class']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button class="btn" type="submit">Confirm Booking</button>
      </form>
    </div>
  </div>

</div>
</body>
</html>