# 💼 My Lava Employee Management System
 
## 📜 Overview

The **My Lava Employee Management System** is a web application designed to manage employee attendance, leave applications, and approvals. It offers a user-friendly interface with distinct functionalities for both employees and administrators. This system is built using Laravel for the backend, with Bootstrap and jQuery for the frontend.

## 📺 Video Demo
<video src="LINK" controls="controls" style="max-width: 730px;">
</video>


## ✨ Features

### Employee Features

- **Attendance Tracking**: Employees can view and manage their attendance records.
- **Leave Application**: Employees can apply for leave, specifying the type and duration.
- **Password Reset**: Users can reset their passwords if forgotten.

### Admin Features

- **Admin Attendance Management**: Admins can view and manage attendance records for all employees.
- **Leave Approval**: Admins can view and approve leave applications submitted by employees who report to them.
- **Apply Leave for Employees**: Admins can apply leave on behalf of their reporting employees.

---

## 💻 Technologies Used 
<div align="center">
	<code><img width="50" src="https://user-images.githubusercontent.com/25181517/192109061-e138ca71-337c-4019-8d42-4792fdaa7128.png" alt="Postman" title="Postman"/></code>
	<code><img width="50" src="https://user-images.githubusercontent.com/25181517/183898054-b3d693d4-dafb-4808-a509-bab54cf5de34.png" alt="Bootstrap" title="Bootstrap"/></code>
	<code><img width="50" src="https://user-images.githubusercontent.com/25181517/117447155-6a868a00-af3d-11eb-9cfe-245df15c9f3f.png" alt="JavaScript" title="JavaScript"/></code>
	<code><img width="50" src="https://user-images.githubusercontent.com/25181517/183570228-6a040b9f-3ddf-47a2-a201-743121dac664.png" alt="php" title="php"/></code>
	<code><img width="50" src="https://github.com/marwin1991/profile-technology-icons/assets/25181517/afcf1c98-544e-41fb-bf44-edba5e62809a" alt="Laravel" title="Laravel"/></code>
	<code><img width="50" src="https://user-images.githubusercontent.com/25181517/183896128-ec99105a-ec1a-4d85-b08b-1aa1620b2046.png" alt="MySQL" title="MySQL"/></code>
</div>

- **Backend**: Laravel, PHP
- **Frontend**: Bootstrap 5, jQuery, JavaScript
- **Database**: MySQL
- **API Testing**: Postman

## 📥 Installation

1. **Clone the Repository**
   ```sh
   git clone https://github.com/obiwan04kanobi/HR-Management-System.git
   cd HR-Management-System/WebApp
   ```

2. **Install Dependencies**
   ```sh
   composer install
   npm install
   ```

3. **Configure Environment**
   - Copy the `.env.example` to `.env`
   - Update the `.env` file with your database and mail server details.

4. **Run Migrations**
   ```sh
   php artisan migrate
   ```

5. **Run the Application**
   ```sh
   php artisan serve
   ```

## 📋 Usage

### Authentication

- Employees and Admins can log in using their email addresses.
- Passwords are stored securely using encryption.

### Navigation

- **Dashboard**: The landing page for logged-in users.
- **Attendance**: Displays the attendance records for the logged-in employee.
- **Admin Attendance**: Accessible only to admins, shows the attendance records for all employees.
- **Leave Application**: Form for employees to apply for leave.
- **Approve Leave**: Accessible only to admins, allows leave approval for reporting employees.
- **Reset Password**: Allows users to reset their passwords.

### Admin Privileges

- Admins are determined based on the `report_to` attribute in the employees' data.
- Only admins can access the "Admin Attendance" and "Approve Leave" sections.

## Contributing

We welcome contributions from the community. Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch with a descriptive name.
   ```sh
   git checkout -b feature/your-feature-name
   ```
3. Make your changes and commit them with clear messages.
   ```sh
   git commit -m "Add feature X"
   ```
4. Push to your forked repository.
   ```sh
   git push origin feature/your-feature-name
   ```
5. Open a Pull Request and describe your changes in detail.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Acknowledgements

- **[Laravel](https://laravel.com/)** : The PHP framework for web artisans. 
