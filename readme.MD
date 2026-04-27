# FitCore — Gym Management System v2.0.0

![PHP](https://img.shields.io/badge/PHP-8.1-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange?style=for-the-badge&logo=mysql)
![Tailwind](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge&logo=github)

FitCore is a high-performance, security-focused, full-stack Gym Management System designed with premium aesthetics and a robust architecture. It features a dynamic permission system, real-time AJAX interactions, and a clean backend powered by Stored Procedures.

---

<!-- ## 🌐 Live Demo
- **🔗 URL**: [https://fitcore-gym.com](#) *(Deploy to InfinityFree/Render to activate)*
- **🔐 Demo Login**:
  - **Email**: `admin@test.com`
  - **Password**: `123456` -->

---

<!-- ## 📸 Screenshots

| Dashboard | Users Management |
| :--- | :--- |
| ![Dashboard](screenshots/dashboard.png) | ![Users](screenshots/users.png) |

| Permissions System | Profile Settings |
| :--- | :--- |
| ![Permissions](screenshots/permissions.png) | ![Profile](screenshots/profile.png) | -->

---

## 🚀 Key Features

1.  **Generic CRUD Architecture**: A unified and reusable system for all database operations (Create, Read, Update, Delete), ensuring 100% DRY (Don't Repeat Yourself) koodh.
2.  **Dynamic RBAC Permission System**: Granular Role-Based Access Control where the sidebar and actions adapt automatically based on the user's authority level.
3.  **Real-time AJAX Navigation**: A seamless, Single-Page Application (SPA) feel that allows users to navigate and perform actions without page reloads.
4.  **Multi-Role Dashboards**: Specialized and personalized dashboard views for **Admins**, **Trainers**, and **Members**.
5.  **Secure Authentication**: Enhanced security with BCRYPT password hashing and a secure session management system.
6.  **Google OAuth Integration**: Seamless one-click login functionality using Google accounts for better user experience.
7.  **OTP Password Recovery**: Secure password reset workflow using SMTP-based One-Time Passwords (OTP) sent to user emails.
8.  **Comprehensive Fee Management**: A robust system to track, manage, and export gym membership payments and financial records.
9.  **Automated Notice Board**: Centralized institutional announcements with support for expiration dates (TTL).
10. **Performance Optimization**: Core database logic offloaded to **Stored Procedures (SPs)** for maximum execution speed and security.
11. **Premium UI/UX Design**: A professional, state-of-the-art interface featuring **Glassmorphism**, modern typography, and smooth micro-animations.
12. **Responsive & Bilingual**: Fully optimized for all devices (Mobile/Tablet/Desktop) with built-in support for **Somali** and **English** Translation.

---

---

## 🔌 API Endpoints (Samples)

### User Login
`POST /app/api/Auth/login.php`
- **Body**: `{ email, password }`
- **Response**: `{ status: true, message: "Welcome...", data: { user_id, role } }`

### Fetch Dynamic Menu
`POST /app/api/user_authorities.php`
- **Action**: `get_User_Menus`
- **Response**: Returns a JSON tree of Categories and authorized Links.

---

## 📂 Project Structure

```text
Gym_Managment_System/
├── app/
│   ├── api/                     # Backend API Endpoints
│   │   ├── Auth/                # forget_password.php, login.php, logout.php, singup.php, google_callback.php
│   │   ├── users/               # users.php (User data API)
│   │   ├── category.php         # Category management API
│   │   ├── system_links.php     # Link management API
│   │   ├── system_actions.php   # Action management API
│   │   ├── user_authorities.php # Auth/Permission management API
│   │   ├── profile.php          # User profile update API
│   │   ├── sidebar.php          # Dynamic sidebar API
│   │   └── reports.php          # System reports API
│   ├── config/                  # Configuration & Security
│   │   ├── conn.php             # Database connection
│   │   ├── init.php             # Global app initialization
│   │   ├── security.php         # Security functions & session guards
│   │   └── gym_managment_system (3).sql # Database schema & SPs
│   ├── js/                      # Module-specific JavaScript
│   │   ├── category.js          # AJAX for categories
│   │   ├── system_link.js       # AJAX for links
│   │   ├── system_action.js     # AJAX for actions
│   │   ├── user_authority.js    # AJAX for permissions
│   │   ├── dashboard.js         # AJAX for dashboard stats
│   │   ├── profile.js           # AJAX for profile updates
│   │   └── notice.js            # AJAX for notices
│   ├── reusable/                # Shared Components & Helpers
│   │   ├── header.php           # Global HTML head & navbar
│   │   ├── sidebar.php          # Sidebar HTML structure
│   │   ├── footer.php           # Global footer & scripts
│   │   ├── db_crud_helper.php   # Generic CRUD functions (PHP)
│   │   ├── helper.js            # AJAX wrappers & UI Helpers (Toast/Confirm)
│   │   ├── sidebar.js           # AJAX for menu rendering
│   │   ├── validator.php        # Server-side validation helpers
│   │   ├── response.php         # JSON response standardization
│   │   ├── variables.php        # Shared PHP variables
│   │   └── loader.php           # Global loading spinner
│   └── views/                   # Application Pages
│       ├── Auth/                # login.php, singup.php, forget_password.php, logout.php
│       ├── Users/               # User management views
│       ├── Errors/              # 403.php, 404.php, 500.php
│       ├── category.php         # Category management UI
│       ├── system_link.php      # Link configuration UI
│       ├── system_action.php    # Action configuration UI
│       ├── user_authority.php   # Authority assignment UI
│       ├── profile.php          # Profile settings UI
│       ├── fees.php             # Payments & Fees UI
│       └── notices.php          # Announcement UI
├── tailwind.config.js           # TailwindCSS configuration
├── .env                         # Environment variables (SMTP, DB)
├── list_links.php               # Links listing helper
└── index.php                    # Application entry point (Landing Page)
```

---

## ⚙️ Setup & Installation

### 📋 Requirements
- **PHP**: `8.1` or higher
- **Database**: `MySQL 5.7` or higher
- **Server**: Apache (XAMPP / WampServer)

### 🚀 Installation Steps
1. **Clone**: `git clone https://github.com/mrfiiqane/Gym_Managment_System.git`
2. **Database**: Import `app/config/gym_managment_system (3).sql` into your PHPMyAdmin.
3. **Environment**: Update your `.env` file:
   ```env
   DB_HOST=localhost
   DB_NAME=gym_managment_system
   DB_USER=root
   DB_PASS=

   MAIL_HOST=smtp.gmail.com
   MAIL_USER=your-email@gmail.com
   MAIL_PASS=your-app-password
   ```
4. **Run**: Access via `http://localhost/Gym_Managment_system/`

---

## 💎 Technical Principles
- **Clean Code**: SOLID/DRY principle implementation.
- **Security**: SQL Prepared Statements & CSRF Protection.
- **Scalability**: Add new modules in minutes via the Permission Map.

---

## 🚧 Roadmap
- [ ] Mobile App Integration (Flutter)
- [ ] AI-based Member Attendance Tracking
- [ ] Automated Subscription Billing (Stripe/PayPal)
- [ ] Advanced Financial Analytics Dashboard

---

## 🛡️ Known Issues
- Google OAuth is under testing.
- Shared hosting may require specific SP permissions from the provider.

---

## 🤝 Contributing
1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---
Created with ❤️ by **Mr. Fiiqane**
