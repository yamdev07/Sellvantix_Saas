<div align="center">

<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo"/>

# 🛒 Sellvantix — Hardware Store Management System

**A professional, production-ready ERP for small and medium hardware stores.**
Track inventory, manage sales, monitor suppliers, and pilot your business in real time.

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docker.com)
[![License](https://img.shields.io/badge/License-MIT-22C55E?style=for-the-badge)](LICENSE)
[![CI](https://github.com/yamdev07/QuincApp/actions/workflows/ci.yml/badge.svg)](https://github.com/yamdev07/QuincApp/actions/workflows/ci.yml)

[🌐 Live Demo](https://yamdev07.alwaysdata.net) · [📖 Documentation](#-installation) · [🐛 Report a Bug](https://github.com/yamdev07/Sellvantix_Saas/issues) · [💡 Request Feature](https://github.com/yamdev07/Sellvantix_Saas/issues)

</div>

---

## 📋 Table of Contents

- [About the Project](#-about-the-project)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Getting Started](#-getting-started)
- [Roles & Permissions](#-roles--permissions)
- [Project Structure](#-project-structure)
- [Roadmap](#-roadmap)
- [Contributing](#-contributing)
- [License](#-license)
- [Author](#-author)

---

## 🚀 About the Project

**Sellvantix** is a complete hardware store management system built for real operational use. It covers the entire business lifecycle from supplier orders and stock management to sales, customer tracking, and business reporting.

Designed for **small and medium hardware stores** that need a reliable, offline-capable solution without the complexity of enterprise software.

> ⚡ Built with Laravel 10, Spatie Permissions, Docker, and GitHub Actions CI/CD.

---

## ✨ Features

### 📦 Product & Inventory Management
- Full product CRUD with categorization
- Purchase price vs. selling price tracking
- Real-time stock level monitoring
- **Automatic low stock alerts**

### 💰 Sales Management
- Fast sales registration with automatic total calculation
- Cash and credit payment support
- Full transaction history and **PDF invoice generation**
- Shift-based sales reports

### 🛒 Purchase & Supplier Management
- Supplier order creation and delivery tracking
- Automatic stock update on delivery confirmation
- Supplier database with full order history

### 👥 Customer Management
- Customer profiles with purchase history
- Debt tracking and payment follow-up
- Customer-specific pricing support

### 📊 Dashboard & Analytics
- Daily sales overview and total revenue
- Low stock alerts and best-selling products
- Recent activity feed and business KPIs

---

## 🧰 Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 10 (PHP 8.x) |
| **Frontend** | Blade · Bootstrap 5 · JavaScript |
| **Database** | MySQL 8.0 |
| **Authentication** | Laravel Auth + Sanctum |
| **Permissions** | Spatie Laravel Permission |
| **Containerization** | Docker + docker-compose |
| **CI/CD** | GitHub Actions |
| **Deployment** | Fly.io |

---

## ⚙️ Getting Started

### Prerequisites

Make sure you have the following installed:

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL / MariaDB
- Docker *(optional but recommended)*

### Installation

**1. Clone the repository**
```bash
git clone https://github.com/yamdev07/Sellvantix_Saas.git
cd Sellvantix_Saas
```

**2. Install dependencies**
```bash
composer install
npm install && npm run build
```

**3. Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

> Edit `.env` and set your database credentials:
> ```env
> DB_DATABASE=sellvantix
> DB_USERNAME=root
> DB_PASSWORD=your_password
> ```

**4. Run migrations & seed**
```bash
php artisan migrate --seed
```

**5. Start the server**
```bash
php artisan serve
```

> 🌐 Application available at **http://127.0.0.1:8000**

### 🐳 Docker Setup *(recommended)*

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --seed
```

---

## 🔐 Roles & Permissions

| Role | Access Level |
|---|---|
| **Admin** | Full access — all modules, settings, and reports |
| **Cashier** | Sales management — register sales, generate invoices |
| **Stock Manager** | Inventory management — products, suppliers, purchases |

### Default credentials *(after seeding)*

```
Email:    admin@example.com
Password: password
```

> ⚠️ Change the default credentials before deploying to production.

---

## 📁 Project Structure

```
Sellvantix_Saas/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Business logic
│   │   └── Middleware/      # Auth & permissions
│   ├── Models/              # Eloquent models
│   └── Services/            # Business services
├── resources/
│   └── views/               # Blade templates
├── routes/
│   └── web.php              # Application routes
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/             # Demo data
├── .github/workflows/       # CI/CD pipelines
├── docker-compose.yml
└── public/
```

---

## 🗺️ Roadmap

- [x] Product & inventory management
- [x] Sales and invoice generation
- [x] Customer & supplier management
- [x] Role-based access control
- [x] Docker containerization
- [x] CI/CD with GitHub Actions
- [ ] Multi-tenant SaaS architecture
- [ ] Subscription system (Free / Premium)
- [ ] POS interface for faster checkout
- [ ] Mobile-responsive dashboard
- [ ] Advanced analytics & reporting

---

## 🤝 Contributing

Contributions are welcome! Here's how to get started:

1. **Fork** the repository
2. **Create** your feature branch: `git checkout -b feature/amazing-feature`
3. **Commit** your changes: `git commit -m 'feat: add amazing feature'`
4. **Push** to your branch: `git push origin feature/amazing-feature`
5. **Open** a Pull Request

Please follow [conventional commits](https://www.conventionalcommits.org/) for your commit messages.

---

## 📜 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

<div align="center">

**Yoann ADIGBONON**
*Full-Stack Developer · SaaS Architecture · Software Security*

[![GitHub](https://img.shields.io/badge/GitHub-yamdev07-181717?style=for-the-badge&logo=github)](https://github.com/yamdev07)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-yoann--adigbonon-0A66C2?style=for-the-badge&logo=linkedin)](https://linkedin.com/in/yoann-adigbonon)
[![Portfolio](https://img.shields.io/badge/Portfolio-yyamd.com-4F46E5?style=for-the-badge&logo=vercel)](https://yyamd.com)

</div>

---

<div align="center">
  <sub>Built with ❤️ in Bénin 🇧🇯</sub>
</div>
