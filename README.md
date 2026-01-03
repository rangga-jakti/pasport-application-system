# Passport Application System

A web-based administrative system that simulates the complete passport
application workflow.

Built with PHP and MySQL, this project focuses on backend logic, data flow,
queue management, and process tracking through a structured dashboard interface.

The system represents a real-world service workflow, designed for learning,
demonstration, and portfolio purposes.

---

## Overview

This application manages a multi-stage passport submission process,
starting from initial registration, re-registration & document validation,
processing, and administrative reporting.

It emphasizes clean backend structure, clear process separation,
and maintainable, readable code.

---

## Core Features

- Secure authentication using PHP sessions
- Structured passport application workflow
- Re-registration & document validation (approved / rejected)
- Automatic queue number assignment
- Processing and payment simulation
- Administrative dashboard with live status indicators
- PDF report generation
- Clean UI using Bootstrap

---

## Application Flow

1. Applicant registration
2. Re-registration & document validation
3. Queue assignment
4. Processing & payment
5. Administrative reporting

Each stage is clearly separated to reflect real-world administrative systems.

---

## Technology Stack

- PHP (Native)
- MySQL
- Bootstrap 5
- DomPDF
- Apache (XAMPP)

---

## Installation

1. Clone the repository
2. Place the project inside `htdocs` (XAMPP)
3. Create MySQL database: `uas_paspor`
4. Configure database connection in `config/database.php`
5. Run Apache & MySQL
6. Open browser: http://localhost/passport-application-system

---

## Project Structure

assets/ - CSS & JS files  
auth/ - Authentication modules  
config/ - Database configuration  
helpers/ - Helper functions  
layouts/ - Layout templates  
sandbox/ - Experimental scripts

---

## Sandbox

The `sandbox/` directory contains experimental and testing scripts
used during development (e.g. hashing tests, PDF rendering tests).
These files are not part of the main application flow.

---

## Purpose

This project is built as an academic and portfolio project to demonstrate:

- Backend system design
- Database-driven workflows
- Session-based authentication
- CRUD operations
- State-based process handling

---

## Disclaimer

This project is an academic simulation.  
All data used in this system is dummy data and does not represent real individuals or services.

---

## Author

**Mirangga Jakti**  
Aspiring Software Engineer with a focus on backend development and system design.

- GitHub: https://github.com/rangga-jakti
- LinkedIn: https://www.linkedin.com/in/mirangga-jakti-8b0a69334/
