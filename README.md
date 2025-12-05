# Campus Navigator - Complete PHP Implementation

A comprehensive campus navigation system with Google Maps integration, voice navigation, and accessibility features.

## Features

✅ **User Authentication**
- Role-based access (Student, Staff, Visitor)
- Secure password hashing
- Session management

✅ **Interactive Maps**
- Google Maps integration
- Real-time navigation
- Building markers with categories

✅ **Voice Navigation**
- Turn-by-turn voice guidance
- Voice commands
- Screen reader support

✅ **Accessibility**
- Full screen reader compatibility
- High contrast mode
- Voice-based navigation for blind users

✅ **Complete Functionality**
- Search buildings & facilities
- Save favorites/bookmarks
- Navigation history
- Emergency SOS
- Feedback system

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Google Maps API Key
- Modern web browser with geolocation support

## Installation Steps

### 1. Database Setup
```
mysql -u root -p < setup_database.sql
```


### 2. Configure Database Connection

Edit `config/database.php`:
```
private $host = "localhost";
private $db_name = "campus_navigator";
private $username = "root";
private $password = "your_password";

```



### 3. Configure Google Maps API

Edit `config/config.php`:
```
define('GOOGLE_MAPS_API_KEY', 'YOUR_ACTUAL_GOOGLE_MAPS_API_KEY');

```


Get your API key from: https://console.cloud.google.com/

Enable these APIs:
- Maps JavaScript API
- Directions API
- Geocoding API
- Places API

### 4. Set File Permissions

```
chmod -R 755 /path/to/campus-navigator
chmod -R 777 uploads/
```


### 5. Configure Virtual Host (Optional)

Apache (`/etc/apache2/sites-available/campus-navigator.conf`):
```
<VirtualHost *:80>
ServerName campus-navigator.local
DocumentRoot /path/to/campus-navigator
```


```
campus-navigator-tailwind/
├── public/
│   ├── index.php              # Root router → redirects to /app/index.php or login
│   ├── assets/
│   │   ├── images/            # App icons, building thumbnails, placeholders
│   │   └── favicon.ico
│   └── .htaccess              # Optional pretty URLs
│
├── app/
│   ├── config/
│   │   ├── config.php         # APP_URL, session, helpers, constants
│   │   └── database.php       # PDO connection (MySQL)
│   │
│   ├── models/
│   │   ├── User.php
│   │   ├── Department.php
│   │   ├── Lab.php
│   │   └── Route.php          # For storing simple demo routes if needed
│   │
│   ├── includes/
│   │   ├── head.php           # <head> with Tailwind CDN + meta
│   │   ├── header.php         # Top bar, user info (used after login)
│   │   ├── bottom-nav.php     # Bottom navigation (Home, Search, Routes, Profile)
│   │   ├── auth-guard.php     # requireLogin() middleware
│   │   └── components/
│   │       ├── block-map.php  # Custom rectangle-block map component
│   │       └── toast.php      # Small notification / flash component
│   │
│   ├── pages/
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   └── signup.php
│   │   │
│   │   ├── home/
│   │   │   └── index.php      # Main “Home / Map” screen with custom block map
│   │   │
│   │   ├── search/
│   │   │   ├── index.php      # Search screen
│   │   │   └── department.php # Department detail (list of labs/rooms)
│   │   │
│   │   ├── labs/
│   │   │   └── detail.php     # Lab/room detail screen
│   │   │
│   │   ├── routes/
│   │   │   └── demo.php       # Route display between user → department → lab
│   │   │
│   │   ├── profile/
│   │   │   └── settings.php   # Basic profile + app settings
│   │   │
│   │   └── info/
│   │       └── onboarding.php # Simple welcome/onboarding screen
│   │
│   ├── api/
│   │   ├── auth-login.php       # AJAX login (optional, or use form POST)
│   │   ├── auth-signup.php      # AJAX signup (optional)
│   │   ├── search.php           # Return departments/labs by query
│   │   └── route-demo.php       # Return demo route nodes for block map
│   │
│   ├── js/
│   │   ├── app.js             # Global JS: nav state, flash, helpers
│   │   ├── map-blocks.js      # Logic for custom block map & route drawing
│   │   └── search.js          # Live search, select department → lab → route
│   │
│   └── index.php              # Entry after root; redirects based on login
│
├── storage/
│   ├── logs/
│   │   └── app.log
│   └── sessions/              # Optional custom session save path
│
├── sql/
│   └── schema.sql             # Users, departments, labs, (optional) routes
│
└── README.md                  # Setup + Tailwind usage notes

```

Usage
Access the application: http://localhost/campus-navigator or your configured domain

Login/Register: Create an account or use test credentials

Grant Location Permission: Allow the browser to access your location for navigation

Enable Voice Navigation: Go to Settings → Enable Voice Navigation for accessibility features

Search & Navigate: Search for buildings and get turn-by-turn directions

Voice Commands
When voice navigation is enabled, you can use these commands:

"Search" - Opens search page

"Map" - Opens map view

"Favorites" - Opens bookmarks

"Help" - Opens emergency help

"Settings" - Opens settings