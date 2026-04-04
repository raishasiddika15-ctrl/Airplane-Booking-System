#Names of students
#Raisha Siddika
#Mahmoud Mansour

# Airline Booking System

## Project Overview

This project is a simple Airline Booking System built using PHP, MySQL, HTML, CSS and JavaScript.
The application allows users to view available flights, book seats and mange their bookings.

The main goal of the project was to design a relational database and build a working web application that interactswith database using SQL queries and joins.

## Features

- View available flights
- Book seats on a flight
- Store passenger information
- View all bookings
- Cancel bookings
- Automatic seat availability updates
- SQL JOIN queries to display combined booking information

  ## Database Design
  The system uses the following main tables:
  - **Flights** - stores flight details
  - **Passengers** - stores passenger information
  - **Flight_Seats** -  stores seat numbers and availability
  - **Bookings** - connects passengers to flights and seats
  -  **Payments** - stores payment details for bookings
 
    Relationships between tables are maintained using **foreign keys**

  Example JOIN query used in the project:

  ```sql
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
  ```
 
  ## How to Run the Application
  1. Install XAMPP
  2. Start Apache and MySQL
  3. Place the project folder inside - C:\xampp\htdocs\airline-booking-system
  4. Import the database using MySQL Workbench
  5. Open the application in a browser: http://localhost/airline-booking-system/public/index.php
  
