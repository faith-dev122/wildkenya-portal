# WildKenya - Virtual Safari Guide \& Kenya Parks Information Portal

> \*\*BIT3208: Advanced Web Design and Development \*\*
> A fully functional multi-tier web application for Kenya wildlife tourism


## Table of Contents

* [Project Overview](#project-overview)
* [System Features](#system-features)
* [User Types \& Roles](#user-types--roles)
* [Pages \& Functionality](#pages--functionality)
* [Tech Stack](#tech-stack)
* [Database Schema](#database-schema)
* [Installation \& Setup](#installation--setup)
* [Login Credentials](#login-credentials)
* [Project Structure](#project-structure)
* [Security Implementation](#security-implementation)
* [CAT Requirements Coverage](#cat-requirements-coverage)
* [Screenshots](#screenshots)

## Project Overview

**WildKenya** is a virtual safari guide and Kenya national parks information portal built as a capstone project for BIT3208 (Advanced Web Design and Development). The system allows tourists to explore Kenya's national parks, discover wildlife species, find and book certified safari guides, make safari bookings, plan multi-park itineraries, and leave reviews all through a single responsive web platform.

The application mirrors real-world tourism platforms like **Kenya Wildlife Service (KWS)**, **Safarilink**, and **Airbnb Experiences**, adapted for the Kenyan safari and wildlife tourism industry.


## System Features

### Public Features (No Login Required)

* Browse all 10 Kenya national parks with descriptions, entry fees, and best seasons
* Search parks by name, county, or description
* Filter parks by region (Rift Valley, Coast, Central, Northern Kenya)
* Browse all 15 Kenya wildlife species with conservation status
* Filter animals by conservation status (Critically Endangered, Endangered, Vulnerable, Near Threatened, Least Concern)
* Search animals by name, scientific name, or habitat
* View detailed park profiles including wildlife found in each park
* View detailed animal profiles including parks where each animal is found
* View visitor reviews and ratings for each park
* Browse registered safari guides

### Authenticated Features (Login Required)

* User registration with role selection (Tourist or Safari Guide)
* Secure login and logout with session management
* Personal dashboard with booking history and profile
* Safari booking system with live cost calculator
* Multi-park trip planner with interactive itinerary builder
* Leave star ratings and reviews on visited parks

### Admin Features (Admin Login Required)

* Admin dashboard with system-wide statistics
* Full CRUD on parks (Create, Read, Update, Delete)
* Full CRUD on animals (Create, Read, Update, Delete)
* View all registered users with roles and join dates
* Monitor booking status overview with progress indicators
* Add or remove featured parks and spotlight animals

## User Types \& Roles

The system has three distinct user roles, each with different access levels:

### Guest (Not Logged In)

A guest is any visitor who has not created an account or logged in.

**What a Guest CAN do:**

* Visit the homepage and view featured parks and wildlife
* Browse the full parks listing page with search and filter
* Browse the full wildlife listing page with conservation filters
* View individual park detail pages (description, fees, wildlife, reviews)
* View individual animal detail pages (profile, conservation status, parks where found)
* Browse the safari guides listing page
* View the login and register pages

**What a Guest CANNOT do:**

* Leave a review on any park
* Make a safari booking
* Access the trip planner
* View a personal dashboard
* Access any admin functionality

### Tourist (Registered \& Logged In)

A tourist is a registered user who has selected the **Tourist** account type during registration.

**What a Tourist CAN do:**

*Everything a Guest can do, plus:*

* **Register** a free account with name, email, phone, and password
* **Login** and maintain a persistent session across pages
* **View personal dashboard** showing:

  * Total bookings count
  * Confirmed bookings count
  * Pending bookings count
  * Full booking history table with park name, date, duration, cost, and status
  * Personal profile (name, email, role, phone)
  * Quick links to parks, wildlife, guides, and booking
* **Make a safari booking** by:

  * Selecting a national park from a dropdown
  * Optionally selecting a safari guide
  * Choosing a booking date (cannot be in the past)
  * Setting duration in days (1–30)
  * Setting group size (1–20 people)
  * Adding special requests (dietary, accessibility, wildlife preferences)
  * Viewing a live cost breakdown that updates as inputs change
  * Submitting the booking (saved as Pending status)
* **Plan a multi-park trip** using the interactive Trip Planner:

  * Search and select multiple parks from a clickable list
  * Watch a day-by-day itinerary build in real time
  * View live summary of total parks, days, and estimated cost
  * Save the trip plan with a title, start date, and notes
* **Leave a review** on any national park:

  * Select a star rating from 1 to 5
  * Write a comment about their experience
  * Review appears immediately on the park detail page
* **Logout** at any time which destroys the session

**What a Tourist CANNOT do:**

* Access the admin panel
* Add, edit, or delete parks or animals
* View or manage other users' bookings
* Change their own booking status

### Safari Guide (Registered \& Logged In)

A safari guide is a registered user who has selected the **Safari Guide** account type during registration.

**What a Safari Guide CAN do:**

*Everything a Tourist can do, plus:*

* Register with the Safari Guide role
* Appear in the guides listing once their profile is set up by admin
* Receive bookings from tourists who select them during booking
* View their bookings and profile on the dashboard

**What a Safari Guide CANNOT do:**

* Access the admin panel
* Modify park or animal records

### Admin (Admin Account)

The admin is a system administrator with full access to all site management features.

**What an Admin CAN do:**

*Everything a Tourist can do, plus:*

* **Access the Admin Panel** at `/wildkenya/admin/`
* **View the Admin Dashboard** showing:

  * Total parks, animals, users, bookings, reviews, and guides
  * Quick action buttons for common tasks
  * Recent users table with name, email, role, and join date
  * Booking status overview with progress bars (Pending, Confirmed, Completed, Cancelled)
* **Manage Parks** (`admin/parks.php`):

  * View all parks in a sortable table
  * See park name, county, region, size, entry fee, and featured status
  * **Add** a new park with full details (name, county, region, description, entry fees for citizen/resident/non-resident, best season, size, featured toggle)
  * **Edit** any existing park's details
  * **Delete** any park (with confirmation popup)
  * Toggle a park as **Featured** to display it on the homepage
  * Click the eye icon to view the park's live public page
* **Manage Animals** (`admin/animals.php`):

  * View all animals in a sortable table
  * See animal name, scientific name, conservation status, and featured status
  * **Add** a new animal with full details (name, scientific name, description, conservation status, habitat, diet, featured toggle)
  * **Edit** any existing animal's details
  * **Delete** any animal (with confirmation popup)
  * Toggle an animal as **Wildlife Spotlight** to feature it on the homepage
  * Click the eye icon to view the animal's live public page
* **Navigate using the dark sidebar** with links to Dashboard, Parks, Animals, Users, Bookings, and View Site
* **Return to the public site** via the View Site button in the admin navbar

**What an Admin CANNOT do:**

* Register through the public registration form (admin accounts are created directly in the database)

## Pages \& Functionality

|Page|URL|Access|Description|
|-|-|-|-|
|Homepage|`/wildkenya/`|Public|Hero, stats bar, featured parks, wildlife spotlight, CTA, footer|
|Parks Listing|`/wildkenya/pages/parks.php`|Public|All 10 parks with search and region filter|
|Park Detail|`/wildkenya/pages/park-detail.php?id=X`|Public|Full park info, wildlife list, reviews, booking sidebar|
|Wildlife Listing|`/wildkenya/pages/animals.php`|Public|All 15 species with conservation status filter|
|Animal Detail|`/wildkenya/pages/animal-detail.php?id=X`|Public|Full animal profile, conservation scale, parks where found|
|Guides|`/wildkenya/pages/guides.php`|Public|Browse safari guides with search and park filter|
|Register|`/wildkenya/pages/register.php`|Guest only|Registration form with JS validation and password strength|
|Login|`/wildkenya/pages/login.php`|Guest only|Secure login with session creation|
|Dashboard|`/wildkenya/pages/dashboard.php`|Logged in|Personal bookings, profile, and quick links|
|Booking|`/wildkenya/pages/booking.php`|Logged in|Safari booking form with live cost calculator|
|Trip Planner|`/wildkenya/pages/trip-planner.php`|Logged in|Interactive multi-park itinerary builder|
|Logout|`/wildkenya/logout.php`|Logged in|Destroys session and redirects to login|
|Admin Dashboard|`/wildkenya/admin/`|Admin only|System stats, recent users, booking overview|
|Admin Parks|`/wildkenya/admin/parks.php`|Admin only|Full CRUD management for parks|
|Admin Animals|`/wildkenya/admin/animals.php`|Admin only|Full CRUD management for animals|

## Tech Stack

### Frontend

|Technology|Purpose|
|-|-|
|HTML5|Semantic page structure|
|CSS3|Custom styling and animations|
|Bootstrap 5|Responsive grid, components, and utilities|
|Bootstrap Icons|Icon library used throughout the UI|
|JavaScript (vanilla)|Form validation, password strength, live cost calculator, trip planner interactivity|

### Backend

|Technology|Purpose|
|-|-|
|PHP 8|Server-side logic, routing, form handling|
|Apache (via XAMPP)|Web server serving PHP pages|

### Database

|Technology|Purpose|
|-|-|
|MySQL|Relational database storing all application data|
|phpMyAdmin|Database management interface|

### Development Tools

|Tool|Purpose|
|-|-|
|XAMPP|Local development environment (Apache + MySQL)|
|VS Code|Code editor|
|Git|Version control|
|GitHub|Remote repository and code backup|

## Database Schema

The application uses **7 database tables** in a MySQL database named `wildkenya`.

### Table: `users`

Stores all registered user accounts.

|Column|Type|Description|
|-|-|-|
|id|INT (PK)|Auto-increment primary key|
|name|VARCHAR(100)|Full name|
|email|VARCHAR(150)|Unique email address|
|password|VARCHAR(255)|bcrypt-hashed password|
|role|ENUM|admin, tourist, or guide|
|phone|VARCHAR(20)|Optional phone number|
|created\_at|TIMESTAMP|Account creation date|

### Table: `parks`

Stores all Kenya national park records.

|Column|Type|Description|
|-|-|-|
|id|INT (PK)|Auto-increment primary key|
|name|VARCHAR(150)|Park name|
|county|VARCHAR(100)|County location|
|region|VARCHAR(100)|Region (Rift Valley, Coast, etc.)|
|description|TEXT|Full park description|
|entry\_fee\_citizen|DECIMAL|Entry fee for Kenyan citizens (KES)|
|entry\_fee\_resident|DECIMAL|Entry fee for residents (KES)|
|entry\_fee\_nonresident|DECIMAL|Entry fee for non-residents (KES)|
|best\_season|VARCHAR(150)|Best time to visit|
|size\_km2|DECIMAL|Park size in km²|
|image|VARCHAR(255)|Image filename|
|featured|TINYINT|1 = show on homepage|
|created\_at|TIMESTAMP|Record creation date|

### Table: `animals`

Stores all wildlife species records.

|Column|Type|Description|
|-|-|-|
|id|INT (PK)|Auto-increment primary key|
|name|VARCHAR(100)|Common name|
|scientific\_name|VARCHAR(150)|Latin/scientific name|
|description|TEXT|Full species description|
|conservation\_status|ENUM|IUCN status (Least Concern → Critically Endangered)|
|habitat|VARCHAR(200)|Natural habitat description|
|diet|VARCHAR(150)|Feeding type and prey|
|image|VARCHAR(255)|Image filename|
|featured|TINYINT|1 = show in wildlife spotlight|
|created\_at|TIMESTAMP|Record creation date|

### Table: `park\_animals`

Junction table linking parks to the animals found in them (many-to-many relationship).

|Column|Type|Description|
|-|-|-|
|park\_id|INT (FK)|References parks.id|
|animal\_id|INT (FK)|References animals.id|

### Table: `guides`

Stores safari guide profiles linked to user accounts.

|Column|Type|Description|
|-|-|-|
|id|INT (PK)|Auto-increment primary key|
|user\_id|INT (FK)|References users.id|
|bio|TEXT|Guide biography|
|languages|VARCHAR(200)|Languages spoken|
|specialization|VARCHAR(200)|Areas of expertise|
|price\_per\_day|DECIMAL|Daily rate (KES)|
|rating|DECIMAL|Average rating (0.00–5.00)|
|years\_experience|INT|Years as a guide|
|certified|TINYINT|1 = certified guide|
|available|TINYINT|1 = currently available|
|created\_at|TIMESTAMP|Profile creation date|

### Table: `bookings`

Stores all safari booking records.

|Column|Type|Description|
|-|-|-|
|id|INT (PK)|Auto-increment primary key|
|tourist\_id|INT (FK)|References users.id (the tourist)|
|guide\_id|INT (FK)|References guides.id (nullable)|
|park\_id|INT (FK)|References parks.id|
|booking\_date|DATE|Date of safari|
|duration\_days|INT|Number of days|
|group\_size|INT|Number of people|
|total\_cost|DECIMAL|Calculated total (KES)|
|status|ENUM|pending, confirmed, cancelled, completed|
|special\_requests|TEXT|Any special requirements|
|created\_at|TIMESTAMP|Booking submission date|

### Table: `reviews`

Stores park reviews submitted by logged-in users.

|Column|Type|Description|
|-|-|-|
|id|INT (PK)|Auto-increment primary key|
|user\_id|INT (FK)|References users.id|
|park\_id|INT (FK)|References parks.id|
|rating|INT|Star rating from 1 to 5|
|comment|TEXT|Written review|
|created\_at|TIMESTAMP|Review submission date|


## ⚙️ Installation \& Setup

### Prerequisites

* Windows, Mac, or Linux computer
* XAMPP installed (download from apachefriends.org)
* Git installed (download from git-scm.com)
* VS Code (download from code.visualstudio.com)

### Step 1 Clone the Repository

```bash
git clone https://github.com/faith-dev122/wildkenya.git
```

Or download the ZIP from GitHub and extract it.

### Step 2 Move to XAMPP

Copy the `wildkenya` folder to:

```
C:\\xampp\\htdocs\\wildkenya\\
```

### Step 3 Start XAMPP

Open XAMPP Control Panel and click **Start** next to:

* Apache
* MySQL

Both should turn green.

### Step 4 Create the Database

1. Open your browser and go to `http://localhost/phpmyadmin`
2. Click **New** in the left sidebar
3. Name the database `wildkenya` and click **Create**
4. Click on `wildkenya` → click the **Import** tab
5. Click **Choose File** → select `wildkenya.sql` from the project root
6. Click **Import**

You should see all 7 tables created with data pre-loaded.

### Step 5 Access the Application

Open your browser and go to:

```
http://localhost/wildkenya/
```

## Login Credentials

### Admin Account

```
Email:    admin@wildkenya.co.ke
Password: \[set by admin  see setup]
Role:     Admin
```

### Test Tourist Account

Register a new account at `http://localhost/wildkenya/pages/register.php` with any email and password (minimum 8 characters).

## Project Structure

```
wildkenya/
│
├── index.php                    # Homepage
├── logout.php                   # Session destroy and redirect
├── wildkenya.sql                # Full database export with seed data
├── README.md                    # This file
│
├── config/
│   └── db.php                   # Database connection configuration
│
├── includes/
│   ├── header.php               # HTML head, Bootstrap CSS, session start
│   ├── nav.php                  # Responsive navigation bar
│   └── footer.php               # Footer, Bootstrap JS, custom JS
│
├── pages/
│   ├── parks.php                # Parks listing with search and filter
│   ├── park-detail.php          # Individual park detail page
│   ├── animals.php              # Wildlife listing with conservation filter
│   ├── animal-detail.php        # Individual animal detail page
│   ├── guides.php               # Safari guides listing
│   ├── booking.php              # Safari booking form
│   ├── trip-planner.php         # Interactive trip planner
│   ├── dashboard.php            # User dashboard (login required)
│   ├── login.php                # Login page
│   └── register.php             # Registration page
│
├── admin/
│   ├── index.php                # Admin dashboard
│   ├── parks.php                # Parks CRUD management
│   └── animals.php              # Animals CRUD management
│
└── assets/
    ├── css/
    │   └── style.css            # Custom styles and CSS variables
    ├── js/
    │   └── main.js              # Form validation, cost calculator, alerts
    └── images/
        ├── parks/               # Park images (park-1.jpg to park-10.jpg)
        └── animals/             # Animal images (animal-1.jpg to animal-15.jpg)
```

## Security Implementation

The application implements multiple layers of security:

### Password Security

* All passwords hashed using **bcrypt** via PHP `password\_hash(PASSWORD\_BCRYPT)`
* Passwords verified using `password\_verify()` — plain text passwords are never stored
* Password strength enforced: minimum 8 characters with client-side strength indicator

### SQL Injection Prevention

* All database queries use **prepared statements** with `$conn->prepare()` and `bind\_param()`
* User input is never concatenated directly into SQL queries

### Cross-Site Scripting (XSS) Prevention

* All output sanitised using `htmlspecialchars()` before rendering to the browser
* User-submitted content (reviews, special requests) is escaped on output

### Session Management

* PHP sessions used for authentication state across pages
* Session stores: user ID, name, email, and role
* `session\_destroy()` called on logout to invalidate the session
* Protected pages redirect unauthenticated users to the login page

### Role-Based Access Control

* Admin panel pages check `$\_SESSION\['user\_role'] === 'admin'` before rendering
* Protected user pages check `isset($\_SESSION\['user\_id'])` before rendering
* Unauthorised access redirects to the login page

### Input Validation

* Server-side validation on all form submissions (registration, login, booking, review)
* Client-side JavaScript validation for instant user feedback
* Email validated using PHP `filter\_var(FILTER\_VALIDATE\_EMAIL)`
* Numeric inputs validated for range (group size 1–20, duration 1–30 days)
* Booking date validated to prevent past date

### Security Requirements

|Requirement|Implementation|
|-|-|
|Prepared statements|All queries use $conn->prepare() + bind\_param()|
|Password hashing|password\_hash(PASSWORD\_BCRYPT) on register, password\_verify() on login|
|Role-based access|Admin panel blocked from non-admins via session role check|
|Session management|$\_SESSION used across all protected pages, logout destroys session|
|Input sanitisation|htmlspecialchars() on all output, filter\_var() on email inputs|

## Data Summary

|Category|Count|
|-|-|
|Kenya National Parks|10|
|Wildlife Species|15|
|Park-Animal Relationships|62|
|Default Admin User|1|

### Parks Included

1. Maasai Mara National Reserve (Narok — Rift Valley)
2. Amboseli National Park (Kajiado — Rift Valley)
3. Tsavo East National Park (Taita-Taveta — Coast)
4. Tsavo West National Park (Taita-Taveta — Coast)
5. Lake Nakuru National Park (Nakuru — Rift Valley)
6. Hell's Gate National Park (Nakuru — Rift Valley)
7. Samburu National Reserve (Samburu — Northern Kenya)
8. Aberdare National Park (Nyandarua — Central)
9. Mount Kenya National Park (Nyeri — Central)
10. Nairobi National Park (Nairobi — Central)

### Wildlife Species Included

African Lion, African Elephant, Black Rhinoceros, African Leopard, Plains Zebra, Wildebeest, Cheetah, Masai Giraffe, Hippopotamus, African Buffalo, Lesser Flamingo, Reticulated Giraffe, Grevy's Zebra, African Wild Dog, Mountain Bongo

## Screenshots

Screenshots are available in the `/screenshots` folder showing:

* Homepage hero and featured parks
* Parks listing with search and filter active
* Park detail page with wildlife list and reviews
* Animal detail page with conservation status scale
* User dashboard with bookings table
* Booking page with live cost calculator
* Trip planner with interactive itinerary
* Admin dashboard with statistics
* Admin parks management table
* Admin add/edit park form

## Author

**Student:** Faith Wanjiku
**GitHub:** github.com/faith-dev122/wildkenya-portal
**Course:** BIT3208 - Advanced Web Design and Development
**Institution:** Mount Kenya University
**Year:** 2026
**Project Type:** Capstone Project

*WildKenya Discover the Wild Heart of Kenya* 

