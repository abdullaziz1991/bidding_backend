# 🏷️ Auction Backend - Laravel

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
![Firebase](https://img.shields.io/badge/Firebase-FFCA28?logo=firebase&logoColor=white)

**Backend system for a mobile auction application** built with **Laravel**, handling products, bids, user management, notifications, and admin approvals. Fully integrated with Firebase for push notifications and email-based account management.

---

## 🛠️ Tech Stack

| Technology                    | Purpose                |
| ----------------------------- | ---------------------- |
| Laravel 12                    | Backend framework      |
| MySQL                         | Database               |
| Firebase Cloud Messaging      | Notifications          |
| Laravel Scheduler & Cron Jobs | Background tasks       |
| REST API                      | Mobile app integration |

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
-   Auto-delete unused tokens after a months  
    ✅ Laravel Scheduled Task:
-   Continuously check ended auctions & trigger Firebase notifications  
    ✅ Organized and scalable HTTP layer, with Controllers structured into dedicated modules such as Admin, Auth, Delete, Insert, Notifications, Select, Services, and Update, where each directory contains feature-specific controllers to maintain clean separation of responsibilities and improve maintainability.
    ✅ Role Management (User / Admin)  
    ✅ Secure API with middleware + token validation

---

## 🔐 Security & Authentication

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

## 📩 Email Notifications

Account activation links

Password reset links

Admin notifications for product approvals

Emails sent via Laravel Mail and configurable SMTP

---

## 🕒 Scheduled Tasks & Jobs

Clear unused tokens older than 2 months

Auction expiration check: Scan bids table, find ended auctions, notify winner and product owner

Implementation: Laravel Jobs triggered via Cron Jobs

protected function schedule(Schedule $schedule)

```bash
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
```

## 📂 Project Structure

```bash
app/
├─ Http/
│  ├─ Controllers/
│  │  ├─ Admin/
│  │  ├─ Auth/
│  │  ├─ Delete/
│  │  ├─ Insert/
│  │  ├─ Notifications/
│  │  ├─ Select/
│  │  ├─ Services/
│  │  ├─ Update/
│  ├─ Requests/
│
├─ Mail/
│  ├─ password reset & Verify Email
├─ Services/
│  ├─ laravel jobs & background tasks
│
├─ Models/
├─ Console/
│  ├─ Kernel.php → scheduled auction status checks
│
routes/
├─ api.php → product, auctions, bidding API
│
public/
├─ Admin page for product approval
├─ Views for:
│  - Password reset
│  - Email verification
│
resources/
├─ views/ (Blade templates for reset/verify pages)

```

---

## ▶️ Installation

```bash
git clone https://github.com/YourRepo/Auction-Backend.git
cd Auction-Backend
composer install
php artisan key:generate

```

## 🛠️ Environment Variables Example

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biddings_database
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_ENCRYPTION=tls
MAIL_PORT=587
MAIL_USERNAME=abdullaziz.hallak.1991@gmail.com
MAIL_PASSWORD=
MAIL_FROM_ADDRESS="abdullaziz.hallak.1991@gmail.com"
```

## 📞 Contact

If you have any questions or would like to collaborate:

-   Developer: Abdulaziz Hallak

-   📧 Email: abdullaziz.hallak.1991@gmail.com
-   🌐 GitHub: https://github.com/abdullaziz1991/bidding_backend

## ⭐ Contributions

Pull requests are always welcome!
If you like this project, please ⭐ the repository ❤️

---
