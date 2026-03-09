-- ==========================================
-- Airline Booking System - Schema
-- DB Name: airline_booking_portfolio_db
-- ==========================================

DROP DATABASE IF EXISTS airline_booking_portfolio_db;
CREATE DATABASE airline_booking_portfolio_db;
USE airline_booking_portfolio_db;

-- PASSENGERS
CREATE TABLE passengers (
  passenger_id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name  VARCHAR(50) NOT NULL,
  email      VARCHAR(120) NOT NULL UNIQUE,
  phone      VARCHAR(25),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- FLIGHTS
CREATE TABLE flights (
  flight_id INT AUTO_INCREMENT PRIMARY KEY,
  flight_number VARCHAR(20) NOT NULL UNIQUE,
  origin VARCHAR(50) NOT NULL,
  destination VARCHAR(50) NOT NULL,
  depart_time DATETIME NOT NULL,
  arrive_time DATETIME NOT NULL,
  base_price DECIMAL(8,2) NOT NULL DEFAULT 199.99
);

-- FLIGHT SEATS
CREATE TABLE flight_seats (
  seat_id INT AUTO_INCREMENT PRIMARY KEY,
  flight_id INT NOT NULL,
  seat_number VARCHAR(5) NOT NULL,     -- e.g., 12A
  seat_class ENUM('Economy','Business') NOT NULL DEFAULT 'Economy',
  is_available TINYINT(1) NOT NULL DEFAULT 1,

  CONSTRAINT fk_seats_flight
    FOREIGN KEY (flight_id) REFERENCES flights(flight_id)
    ON DELETE CASCADE,

  CONSTRAINT uq_flight_seat UNIQUE (flight_id, seat_number)
);

-- BOOKINGS
CREATE TABLE bookings (
  booking_id INT AUTO_INCREMENT PRIMARY KEY,
  passenger_id INT NOT NULL,
  flight_id INT NOT NULL,
  seat_id INT NOT NULL,
  booking_status ENUM('CONFIRMED','CANCELLED') NOT NULL DEFAULT 'CONFIRMED',
  booked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_booking_passenger
    FOREIGN KEY (passenger_id) REFERENCES passengers(passenger_id)
    ON DELETE RESTRICT,

  CONSTRAINT fk_booking_flight
    FOREIGN KEY (flight_id) REFERENCES flights(flight_id)
    ON DELETE RESTRICT,

  CONSTRAINT fk_booking_seat
    FOREIGN KEY (seat_id) REFERENCES flight_seats(seat_id)
    ON DELETE RESTRICT,

  CONSTRAINT uq_booking_seat UNIQUE (seat_id)
);

CREATE TABLE payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  amount DECIMAL(8,2) NOT NULL,
  payment_status ENUM('PENDING','PAID','REFUNDED') NOT NULL DEFAULT 'PAID',
  paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_payment_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
    ON DELETE CASCADE
);


CREATE INDEX idx_flights_route_time ON flights(origin, destination, depart_time);