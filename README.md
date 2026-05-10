# TravelMate — Web Edition
Overview
TravelMate is a collaborative web platform for group travel planning and management, built with Symfony 6.4 and PHP 8.2+. It allows users to collectively organize destinations, budgets, activities, and itineraries from any browser.
System Architecture
The platform follows a modular MVC architecture powered by Symfony, with Doctrine ORM for data persistence and Twig for templating, distributed across six functional domains sharing a common MySQL database.

Core Modules
🌍 Module 1: Destination & Accommodation Management
- Destination catalog with multi-criteria search
- Accommodation availability and pricing
- City cost-of-living comparisons via Teleport API

🏄 Module 2: Activity & Category Management
- Hierarchical activity categorization
- Activity recommendations based on destination
- Multi-criteria filtering (price, duration, type)

✈️ Module 3: Travel Planning & Participation
- Trip lifecycle management (planned, ongoing, completed)
- Role-based permissions for trip modifications
- Participant invitation and acceptance workflow

💰 Module 4: Budget & Expense Tracking
- Multi-currency budget allocation with live conversion
- Real-time expense tracking with Chart.js visualizations
- AI-powered budget analysis via OpenRouter (Llama)
- Push notifications via ntfy, PDF/Excel export

🗺️ Module 5: Itinerary & Stop Management
- Chronological activity sequencing
- Location-based stop optimization
- Dynamic itinerary adjustments

👥 Module 6: User Management & Authentication
- Symfony Security with role-based access control (ROLE_USER, ROLE_ADMIN)
- Secure authentication and session management
- Admin backoffice with KPI dashboard and statistics

Technical Architecture
Frontend
- Templating: Twig 3.x with responsive custom styling
- Charts & Interactivity: Chart.js, JavaScript (ES6+)
- Navigation: Symfony routing between functional modules

Backend
- Framework: Symfony 6.4 (PHP 8.2+)
- Database: MySQL 8.0+ with Doctrine ORM
- External APIs: OpenRouter, ntfy, Teleport, Exchange Rate API
- Exports: PDF generation, PhpSpreadsheet for Excel

Prerequisites
- PHP 8.2+
- Composer 2.x
- MySQL 8.0+
- Symfony CLI

Installation
```bash
# Clone repository
git clone https://github.com/MalekFekihEsprit/Esprit-PIDEV-3A9-2526-TravelMate.git

# Install dependencies
composer install

# Configure environment
cp .env .env.local
# Edit .env.local with your DATABASE_URL and API keys

# Set up database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Run application
symfony server:start
```
