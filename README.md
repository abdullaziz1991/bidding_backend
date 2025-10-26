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

---

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
