<?php
require_once __DIR__ . '/../config/db.php';

$booking_id = (int)($_GET['booking_id'] ?? 0);
if ($booking_id <= 0) exit("Missing booking_id.");

try {
  $pdo->beginTransaction();

  // Lock booking row
  $stmt = $pdo->prepare("SELECT seat_id, booking_status FROM bookings WHERE booking_id = ? FOR UPDATE");
  $stmt->execute([$booking_id]);
  $b = $stmt->fetch();
  if (!$b) throw new Exception("Booking not found.");

  if ($b['booking_status'] !== 'CONFIRMED') {
    throw new Exception("Only CONFIRMED bookings can be cancelled.");
  }

  $seat_id = (int)$b['seat_id'];

  // Cancel booking
  $upd = $pdo->prepare("UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = ?");
  $upd->execute([$booking_id]);

  // Free seat
  $free = $pdo->prepare("UPDATE flight_seats SET is_available = 1 WHERE seat_id = ?");
  $free->execute([$seat_id]);

  //mark payment refunded
  $refund = $pdo->prepare("UPDATE payments SET payment_status = 'REFUNDED' WHERE booking_id = ?");
  $refund->execute([$booking_id]);

  $pdo->commit();

  header("Location: my_bookings.php");
  exit;
} catch (Throwable $e) {
  $pdo->rollBack();
  exit("Cancel failed: " . htmlspecialchars($e->getMessage()));
}