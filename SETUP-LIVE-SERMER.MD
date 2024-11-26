# KUMI - Interactive Quiz Management System

## Overview
KUMI is a web-based quiz management system that allows teachers to create, manage, and conduct quizzes while students can participate in both live and scheduled quizzes.

## Live Demo
Access the live demo at: [http://169.239.251.102:3341/~nana.amoako/KUMI/view/](http://169.239.251.102:3341/~nana.amoako/KUMI/view/)


## Installation

1. **Clone the Repository**
```bash
git clone https://github.com/nanadotam/kumi_fcln
cd kumi_fcln
```

2. **Database Setup**
```php
<?php
$host = "localhost";
$username = "nana.amoako";
$password = ""; // Password is empty for security reasons
$database = "webtech_fall2024_nana_amoako";
```

## Features

### For Teachers
- Create quizzes with multiple question types
- Set quiz parameters (time limit, attempts, etc.)
- Generate unique quiz codes
- View quiz results and analytics
- Live quiz mode with real-time leaderboard

### For Students
- Join quizzes using quiz codes
- Take quizzes with various question types
- View quiz history and scores
- Participate in live quizzes

## Directory Structure
```
KUMI/
├── actions/         # API endpoints and form processing
├── assets/         # CSS, JS, and media files
├── components/     # Reusable PHP components
├── db/            # Database configuration and schema
├── functions/     # Helper functions
├── utils/         # Utility classes
└── view/          # Main application views
```

## Security Features
- Session-based authentication
- SQL injection prevention using prepared statements
- CSRF protection
- XSS prevention

## Error Handling
The system includes comprehensive error handling and logging:
- Database connection errors
- Authentication failures
- Quiz access violations
- Form validation errors

## Support
For support, please reach out to Nana Amoako at nana.amoako@ashesi.edu.gh