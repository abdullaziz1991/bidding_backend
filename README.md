# 🏷️ Auction Backend - Laravel

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
![Firebase](https://img.shields.io/badge/Firebase-FFCA28?logo=firebase&logoColor=white)

**Backend system for a mobile auction application** built with **Laravel**, handling products, bids, user management, notifications, and admin approvals. Fully integrated with Firebase for push notifications and email-based account management.

---

## 🎯 Project Overview

This backend powers a **Flutter-based auction app**. Key features include:

-   Add, update, delete products
-   Manage user profiles
-   Handle live bids and determine winners
-   Send Firebase notifications to both bidders and product owners when auctions end
-   Send account activation and password reset links via email
-   Admin-only email approval system for newly uploaded products
-   Automated tasks using Laravel Jobs and Cron Jobs for background processing

---

## 🚀 Features

✅ User Authentication (Register, Login, Logout)  
✅ Email Verification & Password Reset via secure links  
✅ Auction Listing – Add, Edit, Delete products  
✅ Real-time Auction Bidding System  
✅ Firebase Push Notifications:

-   Notify auction winner when bidding ends
-   Notify product owner with winner details  
    ✅ Profile Update (image, name, contact info…)  
    ✅ Admin Approval System:
-   Special admin panel for approving new products  
    ✅ Cron Jobs:
-   Auto-delete unused tokens after 2 months  
    ✅ Laravel Scheduled Task:
-   Continuously check ended auctions & trigger Firebase notifications  
    ✅ Organized HTTP folder structure (Select, Update, Delete...)  
    ✅ Role Management (User / Admin)  
    ✅ Secure API with middleware + token validation

---

## 📂 Project Structure

app/
├─ Http/
│ ├─ Controllers/
│ ├─ Requests/
│
├─ Services/
│ ├─ cron jobs & background tasks
│
├─ Models/
├─ Notifications/
├─ Console/
│ ├─ Kernel.php → scheduled auction status checks
routes/
├─ api.php → product, auctions, bidding API
public/
├─ Admin page for product approval
├─ Views for:
│ - Password reset
│ - Email verification
resources/
├─ views/ (Blade templates for reset/verify pages)

---

## 🔐 Authentication

-   Token-based authentication
-   Secure password hashing
-   Email verification using signed URL links
-   Password reset using secure token page hosted on backend

---

## 🔔 Firebase Notifications Workflow

1️⃣ Cron Job checks auctions that ended  
2️⃣ Determine the highest bidder (winner)  
3️⃣ Notify:

-   Winner → seller phone number
-   Seller → winner details  
    4️⃣ Update auction status & finalize transaction

---

## 📡 API Highlights

| Category | Actions                                            |
| -------- | -------------------------------------------------- |
| Products | Add, Edit, Delete, Show all, Show single           |
| Auctions | Live bidding, Winner notifications                 |
| Users    | Profile update, Auth, Verification, Password reset |
| Admin    | Approve new uploaded products                      |

---

## 🛠️ Tech Stack

| Technology                    | Purpose                |
| ----------------------------- | ---------------------- |
| Laravel 11                    | Backend framework      |
| MySQL                         | Database               |
| Firebase Cloud Messaging      | Notifications          |
| Laravel Scheduler & Cron Jobs | Background tasks       |
| REST API                      | Mobile app integration |

---

## ▶️ Installation

```bash
git clone https://github.com/YourRepo/Auction-Backend.git
cd Auction-Backend
composer install
cp .env.example .env
php artisan key:generate

## 🛠️ Environment Variables Example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=auction_db
DB_USERNAME=root
DB_PASSWORD=

FIREBASE_SERVER_KEY=your_server_key_here
MAIL_MAILER=smtp
MAIL_HOST=your_mail_server
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email

## 💡 Future Improvements

Real-time WebSocket bidding

Advanced admin dashboard

Analytics for completed auctions

## 📞 Contact

If you have any questions or would like to collaborate:
Developer: Abdulaziz Hallak
📧 Email: your-email@example.com

🌐 GitHub: https://github.com/your-profile

## ⭐ Contributions

Pull requests are always welcome!
If you like this project, please ⭐ the repository ❤️












## ⚙️ Key Features

-   **Product Management**: Create, edit, delete, and view products
-   **Bidding System**: Automatic calculation of winning bids and notifications to winners and product owners
-   **User Management**: Update profile, reset password, account activation links
-   **Firebase Notifications**: Real-time alerts for auction end, winner notification, etc.
-   **Admin Panel**: Separate functionality for managing approved emails
-   **HTTP Endpoints Structure**:
    -   `/select` → Fetch products or bids
    -   `/update` → Update products or user profiles
    -   `/delete` → Delete products or related resources
    -   `/services` → Cron jobs and background services
-   **Cron Jobs & Laravel Jobs**:
    -   Clear unused tokens older than 2 months
    -   Scan expired auctions to notify winners and product owners

---

## 🔔 Firebase Notifications

Sends push notifications when:

An auction ends

A user wins a bid

Product owners are notified of the winner

Requires Firebase Server Key in .env

---

## 🕒 Scheduled Tasks & Jobs

Clear unused tokens older than 2 months

Auction expiration check: Scan bids table, find ended auctions, notify winner and product owner

Implementation: Laravel Jobs triggered via Cron Jobs

protected function schedule(Schedule $schedule)

class ProcessExpiredBiddings extends Command
{
/\*\*
_ The name and signature of the console command.
_
_ @var string
_/
// protected $signature = 'app:process-expired-biddings';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    /**
     * Execute the console command.
     */


      protected $signature = 'bidding:process';
    protected $description = 'Process expired biddings and notify users';

    public function handle(BiddingService $biddingService)
    {
        $biddingService->processExpiredBiddings();
        $this->info('Expired biddings processed successfully.');
    }

}

## Schedule::command('bidding:process')->everyMinute();

## 📩 Email Notifications

Account activation links

Password reset links

Admin notifications for product approvals

Emails sent via Laravel Mail and configurable SMTP

---

## 🔑 Security & Access

Middleware for user authentication and admin-only routes

Validation for all requests to prevent unauthorized changes

Token-based authentication (Passport / Sanctum recommended)

---

## 📂 Project Structure

app/
├─ Http/
│ ├─ Controllers/
│ │ ├─ Select/ # Fetch data endpoints
│ │ ├─ Update/ # Update product/user endpoints
│ │ ├─ Delete/ # Delete product endpoints
├─ Services/ # Cron jobs and background services
├─ Models/ # Eloquent models
├─ Jobs/ # Laravel jobs for async tasks
routes/
├─ api.php # API routes
config/
```
