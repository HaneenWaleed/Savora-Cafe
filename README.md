# 🍽️ Savora Cafeteria

**Savora** is a cafeteria management system built as a graduation project during my **ITI Summer Training** in Backend Development with PHP.

The project was developed to provide a complete experience for both **customers and administrators**, including menu browsing, ordering, favorites, customer preferences, AI-powered recommendations, and a role-based AI chatbot.

---

## 📌 About The Project

Savora was developed as a practical application of the backend concepts and technologies learned during the training.

The system focuses on:

* Managing cafeteria menu items and categories
* Handling customer orders
* Managing customer preferences and favorites
* Providing personalized food recommendations
* Offering an AI chatbot with role-based access
* Providing an admin dashboard for managing the system

The project was developed within a short deadline of approximately **one week**, which made time management, problem-solving, and teamwork important parts of the development process.

---

## ✨ Features

### 👤 Authentication & Roles

* User authentication
* Customer and Admin roles
* Role-based authorization
* Protected admin functionality

### 🍔 Menu Management

* Browse food and beverage items
* Organize items into categories
* Admin CRUD operations for menu items
* Admin category management

### 🛒 Orders

* Create and manage customer orders
* Order items and quantities
* Order management from the admin side
* Customer order history

### ❤️ Favorites

* Add and remove favorite menu items
* Manage customer favorites

### ⚙️ Customer Preferences

Customers can provide preferences such as:

* Food categories
* Food types
* Beverage preferences
* Taste preferences
* Dietary preferences
* Price range
* Spicy level
* Ingredients

These preferences are used to provide more personalized recommendations.

### 🤖 AI Recommendations

Savora provides personalized food recommendations based on the customer's stored preferences and menu data.

### 💬 AI Chatbot

The system includes an AI-powered chatbot with **role-based access**.

The chatbot can interact with the available system data according to the authenticated user's permissions, while restricting access to information that the user is not authorized to view.

### 📊 Admin Dashboard

The admin can:

* Manage users
* Manage menu items
* Manage categories
* Manage orders
* View statistics
* Access customer-related information according to the system permissions

---

## 🛠️ Technologies Used

### Backend

* **PHP**
* **Laravel**
* **MySQL**

### Frontend

* **HTML5**
* **CSS3**
* **JavaScript**
* **Bootstrap**

### AI

* AI API integration for:

  * Recommendations
  * Chatbot

---

## 🏗️ Project Structure

The project follows the Laravel MVC architecture:

```text
Savora/
├── app/
│   ├── Http/
│   ├── Models/
│   └── ...
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   └── api.php
├── .env.example
└── ...
```

---

## 🚀 Getting Started

### Prerequisites

Make sure you have the following installed:

* PHP
* Composer
* MySQL
* Laravel
* Node.js & npm

### Installation

Clone the repository:

```bash
git clone YOUR_REPOSITORY_URL
```

Navigate to the project directory:

```bash
cd Savora
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure your database credentials in `.env`.

Then run the migrations and seeders:

```bash
php artisan migrate --seed
```

Build the frontend assets:

```bash
npm run build
```

Finally, start the Laravel development server:

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

## 🔐 Environment Variables

The project uses environment variables for configuration and sensitive credentials.

Make sure to configure your `.env` file with your database settings and AI API key.

**Never commit your actual `.env` file or API keys to GitHub.**

Example:

```env
APP_NAME=Savora

DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

AI_API_KEY=your_api_key
```

---

## 🎨 Design

Savora uses a warm and natural visual identity built around:

* Cream
* Terracotta
* Green
* Brown

The interface was designed from scratch rather than relying on a ready-made template, with a focus on creating a distinctive cafeteria experience.

---

## 🧠 Challenges & What I Learned

Working on Savora helped me apply backend concepts in a complete project rather than isolated exercises.

Some of the main challenges included:

* Working with a very short deadline
* Building the project without a ready-made template
* Designing and implementing the UI while developing the backend
* Implementing authentication and role-based authorization
* Connecting the application with AI services
* Handling customer preferences and personalized recommendations
* Working as a team under time constraints

One small but memorable challenge was the project logo. The initial AI-generated logo lost quality when resized, so I redesigned it using **Adobe Illustrator** as a vector logo to ensure it remained sharp at different sizes.

---

## 👩‍💻 Team

Developed as a graduation project during **ITI Summer Training**.

**Team Members:**

* Haneen Walid
* [Team Member Name]

**Instructor:**

* [Instructor Name]

---

## 📸 Screenshots

Screenshots and project demonstrations can be added here.

---

## 🔗 Links

**GitHub Repository:**
YOUR_REPOSITORY_URL

**Project Demo:**
YOUR_DEMO_URL

---

## 📄 License

This project was developed for educational and training purposes as part of the ITI Summer Training.
