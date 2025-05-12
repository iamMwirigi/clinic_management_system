# Clinic Management System Database

## Description
A MySQL database for managing clinics, including patient records, doctor appointments, prescriptions, and departments.

## Setup
1. Clone this repository.
2. Import the SQL file into MySQL:
   ```bash
   mysql -u [username] -p [database_name] < clinic_db.sql


## ERD


---

### **Key Features**
- **Constraints**: PK, FK, `NOT NULL`, `UNIQUE`, `ENUM`.
- **Relationships**:
  - 1-M: Departments → Doctors, Patients → Appointments.
  - M-M: Doctors ↔ Specialties (via `doctor_specialties`).
- **Scalability**: Easy to add features like billing or lab reports later.

---

### **Submission**
1. Push the `.sql` file and `README.md` to GitHub.
2. Double-check the ERD screenshot is included.

Let me know if you need help refining the ERD or adding more features! 🚀