# Quiz Management System

Welcome to the Quiz Management System! This application allows teachers to create, manage, and evaluate quizzes, while students can take quizzes and view their results. The system is designed to be user-friendly and secure, with features like session management and role-based access control.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Features

- **User Authentication**: Secure login and registration for teachers and students.
- **Role-Based Access Control**: Different interfaces and functionalities for teachers and students.
- **Quiz Creation**: Teachers can create quizzes with various question types, including multiple choice, true/false, and short answer.
- **Quiz Management**: Edit, delete, and view quizzes with detailed statistics.
- **Session Management**: Automatic logout after a period of inactivity to enhance security.
- **Responsive Design**: User-friendly interface that adapts to different screen sizes.
- **Leaderboard**: Interactive leaderboard to display top-performing students.

## Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/yourusername/quiz-management-system.git
   cd quiz-management-system
   ```

2. **Set Up the Database**:
   - Import the `database.sql` file into your MySQL database to set up the necessary tables and data.

3. **Configure the Application**:
   - Update the database connection settings in `utils/Database.php` with your database credentials.

4. **Install Dependencies**:
   - Ensure you have PHP and a web server (like Apache or Nginx) installed.
   - Use Composer to install any PHP dependencies if applicable.

5. **Run the Application**:
   - Start your web server and navigate to the project directory in your browser.

## Usage

- **Login**: Access the application by logging in with your credentials. If you don't have an account, register as a teacher or student.
- **Create Quizzes**: Teachers can create new quizzes by navigating to the "Create Quiz" section.
- **Take Quizzes**: Students can take available quizzes and view their results.
- **View Results**: Both teachers and students can view quiz results and statistics.

## Security

- **Password Hashing**: All passwords are securely hashed before storage.
- **Session Timeout**: Users are automatically logged out after 15 minutes of inactivity.
- **Input Validation**: All user inputs are validated and sanitized to prevent SQL injection and XSS attacks.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request with your changes. Ensure your code follows the project's coding standards and includes appropriate tests.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

---

Thank you for using the Quiz Management System! If you have any questions or feedback, please feel free to reach out.
