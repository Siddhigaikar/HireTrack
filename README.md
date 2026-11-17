# HireTrack – Job Portal Management System

## About the Project

HireTrack is a web-based Job Portal Management System developed using PHP and MySQL. It connects job seekers with employers by providing a platform where employers can post job openings and candidates can search and apply for jobs.

The system provides separate functionalities for candidates and employers, making the job application and recruitment process easier to manage.

## Features

### Candidate
- Candidate Registration and Login
- Create and Update Candidate Profile
- Upload Resume
- Browse Available Jobs
- Search and Filter Jobs
- Save Jobs
- Apply for Jobs
- View Applied Jobs
- Track Application Status

### Employer
- Employer Registration and Login
- Create and Update Company Profile
- Post New Job Vacancies
- Edit and Delete Job Posts
- View Posted Jobs
- View Candidate Applications
- Manage Application Status

### Admin / Dashboard
- Dashboard with Statistics
- Manage Users
- View Job Listings
- Monitor Applications

## Technologies Used

- **Frontend:** HTML, CSS, Bootstrap 5, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Server:** XAMPP
- **Version Control:** Git and GitHub

## Screenshots
Registration
<img width="1257" height="752" alt="image" src="https://github.com/user-attachments/assets/884d746d-b1da-4f66-b73a-bd9f9f9d558e" />

Login
<img width="1000" height="584" alt="image" src="https://github.com/user-attachments/assets/5e85e913-06a1-4bb2-9f84-574c8c063ba1" />



## Project Structure

```text
HireTrack/
│
├── db.php
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── candidate/
├── employer/
├── jobs/
├── uploads/
└── README.md
```



## Installation and Setup

1. Download and install XAMPP.
2. Clone this repository:

   ```bash
   git clone https://github.com/Siddhigaikar/HireTrack.git
   ```

3. Move the project folder into the XAMPP `htdocs` directory.
4. Start Apache and MySQL from XAMPP.
5. Create a MySQL database.
6. Import the project database SQL file into MySQL.
7. Update the database connection details in `db.php`.
8. Open the project in your browser:

   ```text
   http://localhost/HireTrack/
   ```

## Database

The project uses MySQL to store and manage:

- User Information
- Candidate Profiles
- Employer Profiles
- Job Listings
- Saved Jobs
- Job Applications

## Future Enhancements

- Email Notifications
- Advanced Job Search Filters
- Candidate Resume Management
- Employer Analytics
- Improved User Interface

## Author

**Siddhi Gaikar**

GitHub: [Siddhigaikar](https://github.com/Siddhigaikar)

LinkedIn: [Siddhi Gaikar](https://linkedin.com/in/siddhi-gaikar-15a064329/)

---

**HireTrack – Connecting Candidates with Opportunities.**
