# Passport Application System

An internal administrative web system that simulates the end-to-end passport application workflow.

Built with native PHP and MySQL, this project focuses on backend system design, process-driven workflows, and administrative state management, rather than public user interaction.

This system is designed for learning, demonstration, and portfolio purposes, with emphasis on real-world backend architecture.

---

## System Context

This application represents an internal service system used by authorized administrative staff.

Applicants do not interact directly with this system.
All application data is entered, processed, and managed by authenticated admins, reflecting real-world government or enterprise service workflows.

---

## Overview

The system manages a multi-stage passport application lifecycle, including:

- Initial data registration
- Re-registration and document validation
- Queue number assignment
- Processing and payment simulation
- Administrative reporting
- Each stage is handled as a distinct backend process, mirroring real administrative operations.

---

## Core Features

- Admin-only authentication using PHP sessions
- Structured, multi-stage application workflow
- Re-registration & document validation (approve / reject)
- Automatic queue number generation
- Processing and payment simulation
- Administrative dashboard with live status indicators
- PDF report generation
- Clean and functional UI using Bootstrap

---

## Application Flow

1. Applicant registration
2. Re-registration & document validation
3. Queue assignment
4. Processing & payment
5. Administrative reporting

Each stage is clearly separated to reflect real-world administrative systems.

---

## Architecture Overview

The backend is structured with clear separation of concerns:
Request
-> Controller / Handler
-> Business Logic (Service)
-> Data Access (Repository / Query Layer)

This design allows the system to remain portable across backend stacks (e.g. PHP, Python, Node.js) with minimal conceptual changes.

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

assets/   - CSS & JS files  
auth/     - Authentication modules  
config/   - Database configuration  
helpers/  - Helper functions  
layouts/  - Layout templates  
sandbox/  - Experimental scripts

---

## Sandbox

The `sandbox/` directory contains experimental and testing scripts
used during development (e.g. hashing tests, PDF rendering tests).
These files are not part of the main application flow.

---

## Scope & Limitations

✔ Internal administrative system
✔ Admin authentication & access control
✔ Backend-driven workflow management

✖ Public user registration
✖ Applicant self-service portal
✖ Online document submission by applicants

A separate public-facing self-service application can be developed to handle applicant-side interactions.

---

## Purpose

This project is built as an academic and portfolio project to demonstrate:

- Backend system design
- Process-oriented workflows
- Session-based authentication
- Database-driven state handling
- Separation of concerns in backend architecture

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
