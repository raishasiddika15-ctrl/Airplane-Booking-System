USE airline_booking_portfolio_db;

-- Flights
INSERT INTO flights (flight_number, origin, destination, depart_time, arrive_time, base_price) VALUES
('AB101','New York','Miami','2026-03-10 09:00:00','2026-03-10 12:10:00',179.99),
('AB202','New York','Chicago','2026-03-11 14:30:00','2026-03-11 16:10:00',159.99),
('AB303','Boston','Los Angeles','2026-03-12 08:15:00','2026-03-12 11:45:00',299.99),
('AB404','Miami','New York','2026-03-13 17:00:00','2026-03-13 20:05:00',189.99),
('AB505','Chicago','Boston','2026-03-14 10:20:00','2026-03-14 12:35:00',149.99);

-- Flight 1
INSERT INTO flight_seats (flight_id, seat_number, seat_class) VALUES
(1,'1A','Business'),(1,'1B','Business'),(1,'1C','Business'),(1,'1D','Business'),(1,'1E','Business'),
(1,'2A','Economy'), (1,'2B','Economy'), (1,'2C','Economy'), (1,'2D','Economy'), (1,'2E','Economy');

-- Flight 2
INSERT INTO flight_seats (flight_id, seat_number, seat_class) VALUES
(2,'1A','Business'),(2,'1B','Business'),(2,'1C','Business'),(2,'1D','Business'),(2,'1E','Business'),
(2,'2A','Economy'), (2,'2B','Economy'), (2,'2C','Economy'), (2,'2D','Economy'), (2,'2E','Economy');

-- Flight 3
INSERT INTO flight_seats (flight_id, seat_number, seat_class) VALUES
(3,'1A','Business'),(3,'1B','Business'),(3,'1C','Business'),(3,'1D','Business'),(3,'1E','Business'),
(3,'2A','Economy'), (3,'2B','Economy'), (3,'2C','Economy'), (3,'2D','Economy'), (3,'2E','Economy');

-- Flight 4
INSERT INTO flight_seats (flight_id, seat_number, seat_class) VALUES
(4,'1A','Business'),(4,'1B','Business'),(4,'1C','Business'),(4,'1D','Business'),(4,'1E','Business'),
(4,'2A','Economy'), (4,'2B','Economy'), (4,'2C','Economy'), (4,'2D','Economy'), (4,'2E','Economy');

-- Flight 5
INSERT INTO flight_seats (flight_id, seat_number, seat_class) VALUES
(5,'1A','Business'),(5,'1B','Business'),(5,'1C','Business'),(5,'1D','Business'),(5,'1E','Business'),
(5,'2A','Economy'), (5,'2B','Economy'), (5,'2C','Economy'), (5,'2D','Economy'), (5,'2E','Economy');

-- Passengers
INSERT INTO passengers (first_name, last_name, email, phone) VALUES
('Raisha','Siddika','raisha@email.com','111-222-3333'),
('Amina','Khan','amina@email.com','222-333-4444'),
('Jason','Lee','jason@email.com','333-444-5555'),
('Maria','Gomez','maria@email.com','444-555-6666'),
('David','Brown','david@email.com','555-666-7777');

-- One sample booking + payment 
INSERT INTO bookings (passenger_id, flight_id, seat_id, booking_status)
VALUES (1, 1, 1, 'CONFIRMED');

UPDATE flight_seats SET is_available = 0 WHERE seat_id = 1;

INSERT INTO payments (booking_id, amount, payment_status)
VALUES (1, 179.99, 'PAID');