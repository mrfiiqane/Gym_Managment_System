# FitCore — Gym Management System v2.0.0

![PHP](https://img.shields.io/badge/PHP-8.1-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange?style=for-the-badge&logo=mysql)
![Tailwind](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge&logo=github)

FitCore is a high-performance, full-stack Gym Management System designed with premium aesthetics and a robust "Generic CRUD" architecture. It features a dynamic permission system, real-time AJAX interactions, and a clean backend powered by Stored Procedures.

---

## 🌐 Live Demo
- **🔗 URL**: [https://fitcore-gym.com](#) *(Deploy to InfinityFree/Render to activate)*
- **🔐 Demo Login**:
  - **Email**: `admin@test.com`
  - **Password**: `123456`

---

## 📸 Screenshots

| Dashboard | Users Management |
| :--- | :--- |
| ![Dashboard](screenshots/dashboard.png) | ![Users](screenshots/users.png) |

| Permissions System | Profile Settings |
| :--- | :--- |
| ![Permissions](screenshots/permissions.png) | ![Profile](screenshots/profile.png) |

---

## 🚀 Key Features

### 1. Dynamic RBAC Permission System
The specialized sidebar adapts automatically to the user's authority level using a hierarchical mapping:
**Category** ➔ **System Links** ➔ **System Actions** ➔ **User Authority**.

### 2. High-Performance Architecture
- **Stored Procedures (SPs)**: Core database logic is offloaded to MySQL for maximum speed and security.
- **Generic CRUD Framework**: A unified PHP/AJAX system that handles operations for all modules, ensuring 100% DRY code.
- **Real-time AJAX**: No page reloads for a seamless SPA-like feel.

### 3. Premium UI/UX
- **Glassmorphism Design**: Modern blurred panels and neural glow backgrounds.
- **Bilingual Support**: Fully localized for **Somali** and **English**.
- **Dark/Light Mode**: Full theme support with persistent preferences.

---

## 🧠 System Flow

1. **Authentication**: User logs in; session is established with role-based tagging.
2. **Permission Fetching**: `user_authorities.php` checks allowed actions for the user.
3. **Menu Rendering**: `sidebar.js` calls the API and dynamically builds the menu.
4. **Action Execution**: Every action (Create/Edit/Delete) is validated against the permission map before hitting the Stored Procedure.

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
│   │   └── gym_managment_system (2).sql # Database schema & SPs
│   ├── js/                      # Module-specific JavaScript
│   │   ├── category.js          # AJAX for categories
│   │   ├── system_link.js       # AJAX for links
│   │   ├── system_action.js     # AJAX for actions
│   │   ├── user_authority.js    # AJAX for permissions
│   │   ├── sidebar.js           # AJAX for menu rendering
│   │   ├── dashboard.js         # AJAX for dashboard stats
│   │   ├── profile.js           # AJAX for profile updates
│   │   └── notice.js            # AJAX for notices
│   ├── reusable/                # Shared Components & Helpers
│   │   ├── header.php           # Global HTML head & navbar
│   │   ├── sidebar.php          # Sidebar HTML structure
│   │   ├── footer.php           # Global footer & scripts
│   │   ├── db_crud_helper.php   # Generic CRUD functions (PHP)
│   │   ├── helper.js            # AJAX wrappers & UI Helpers (Toast/Confirm)
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
2. **Database**: Import `app/config/gym_managment_system (2).sql` into your PHPMyAdmin.
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
