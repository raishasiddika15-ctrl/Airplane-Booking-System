<?php
require_once __DIR__ . '/../config/db.php';

$booking_id = (int)($_GET['booking_id'] ?? 0);

if ($booking_id <= 0) {
    exit("Invalid booking ID.");
}

try {
    $pdo->beginTransaction();

   
    $stmt = $pdo->prepare("
        SELECT seat_id
        FROM bookings
        WHERE booking_id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        throw new Exception("Booking not found.");
    }

    $seat_id = (int)$booking['seat_id'];

  
    $stmt = $pdo->prepare("
        DELETE FROM payments
        WHERE booking_id = ?
    ");
    $stmt->execute([$booking_id]);

   
    $stmt = $pdo->prepare("
        DELETE FROM bookings
        WHERE booking_id = ?
    ");
    $stmt->execute([$booking_id]);

    
    $stmt = $pdo->prepare("
        UPDATE flight_seats
        SET is_available = 1
        WHERE seat_id = ?
    ");
    $stmt->execute([$seat_id]);

    $pdo->commit();

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cancel Booking</title>
  <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<div class="container">

  <div class="card">
    <div class="section">
      <?php if (isset($error)): ?>
        <h1 class="section-title">Cancellation Error</h1>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
      <?php else: ?>
        <h1 class="section-title">Booking Cancelled</h1>
        <p>Your booking is cancelled successfully and the seat is now available again.</p>
      <?php endif; ?>

      <p>
        <a class="btn" href="my_bookings.php">Back to Bookings</a>
      </p>
    </div>
  </div>

</div>
</body>
</html>
