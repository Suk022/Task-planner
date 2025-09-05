# Task Planner with Email Reminders

A PHP-based task management system with email reminders, built using file-based storage and PHP's native mail() function.

## Features

- Task Management
  - Add new tasks
  - Mark tasks as completed/uncompleted
  - Delete tasks
  - View all tasks

- Email Subscription System
  - Subscribe to task reminders
  - Email verification process
  - Unsubscribe from reminders
  - Hourly email reminders for uncompleted tasks

## Project Structure

```
src/
├── mail/
│   └── config.php       # Email configuration
├── cron.log             # CRON job log file, for debugging
├── cron.php             # CRON job script for sending reminders
├── functions.php        # Core functionality and helper functions
├── index.php            # Main application interface
├── pending_subscriptions.txt  # Pending email verifications
├── setup_cron.sh             # Script to set up the CRON job
├── subscribers.txt      # Verified subscriber list
├── tasks.txt            # Task storage
├── unsubscribe.php      # Email unsubscription handler
└── verify.php          # Email verification handler
```

## Setup Instructions

### System Requirements

- PHP 7.4 or higher
- Web server (Apache or similar web server)
- Papercut SMTP (for email testing)
- For Windows users: WSL (Windows Subsystem for Linux)

### Installation

1. Clone the repository to your web server directory:
   ```bash
   git clone <repository-url>
   cd php-assignment
   ```

2. Set up Papercut SMTP for email testing:
   - Download Papercut SMTP from https://github.com/ChangemakerStudios/Papercut-SMTP/releases
   - Extract and run Papercut.Service.exe
   - Papercut will start a local SMTP server on port 25
   - Access the Papercut web interface at http://localhost:5000 to view sent emails

3. Configure PHP mail settings:
   - Open your php.ini file (usually in XAMPP's php directory)
   - Find the [mail function] section
   - Set the following values:
     ```
     [mail function]
     SMTP = localhost
     smtp_port = 25
     sendmail_from = noreply@localhost
     ```
   - Save the file and restart Apache

   OR

  - create a .htaccess file in src/ directory:
  - add this content in the file:
    ```
    php_value SMTP localhost
    php_value smtp_port 25
    php_value sendmail_from noreply@localh
    ```
  - Restart Apache for the changes to take effect.
  
4. For Windows users, install WSL:
   - Open PowerShell as Administrator and run:
     ```powershell
     wsl --install
     ```
   - Restart your computer
   - After restart, WSL will complete the installation
   - Set up a username and password when prompted
   - Update WSL packages:
     ```bash
     sudo apt update && sudo apt upgrade
     ```

5. Set up CRON job:
   - Open WSL terminal
   - Navigate to the project directory:
     ```bash
     cd /mnt/c/xampp/htdocs/php-assignment
     ```
   - Edit the CRON configuration:
     ```bash
     crontab -e
     ```
   - Add the following line to run reminders hourly:
     ```
     0 * * * * cd /mnt/c/xampp/htdocs/php-assignment/src && /mnt/c/xampp/php/php.exe cron.php
     ```
   - Save and exit (Ctrl+X, then Y, then Enter)
   - Verify CRON job is set:
     ```bash
     crontab -l
     ```
     
## Usage

1. Access the application at `http://localhost/php-assignment/src/`
2. Add tasks using the "Add New Task" form
3. Manage tasks:
   - Check/uncheck to mark as completed
   - Click "Delete" to remove tasks
4. Subscribe to email reminders:
   - Enter your email in the subscription form
   - Verify your email through the link sent
   - Receive reminders for pending tasks
5. Unsubscribe to stop receiving email reminders:
   - Click on the link available in reminder emails to unsubscribe

## Technical Details

### Data Storage

- Tasks are stored in JSON format
- Email lists are stored in plain text
- Configuration is stored in PHP files

### Security Features
- Users only verify their email ownership
- No user passwords are stored
- Unsubscribe option in every email

## Troubleshooting

1. Email not sending:
   - Verify PHP's mail() function is working correctly
   - Check email configuration
   - Ensure CRON job is running

2. CRON job not running:
   - Verify CRON service is running:
     ```bash
     sudo service cron status
     ```
   - Check CRON logs:
     ```bash
     grep CRON /var/log/syslog
     ```
   - Test CRON script manually:
     ```bash
     cd /mnt/c/xampp/htdocs/php-assignment/src && /mnt/c/xampp/php/php.exe cron.php
     ```

3. WSL Issues:
   - If WSL installation fails, try:
     ```powershell
     wsl --update
     ```
   - If CRON service isn't running:
     ```bash
     sudo service cron start
     ```
   - To check WSL version:
     ```powershell
     wsl --list --verbose
     ```

  4. Project webpage loading error
   - if you get following error while accessing 'http://localhost/php-assignment/src/index.php'
      ```
      Not Found
      The requested URL was not found on this server.
      Apache/2.4.29 (Ubuntu) Server at localhost Port 80
      ```
   - Run this command in WSL
      ```
      sudo service apache2 stop
      ```

## Important Note on Email Delivery

This project uses **Papercut SMTP** as a local SMTP testing tool. As a result:

- Emails are **not sent to real inboxes**.
- All outgoing messages (verification links, task reminders, etc.) are **intercepted** and viewable at `http://localhost:5000`.

### Why Use Papercut SMTP?

Papercut ensures that PHP’s `mail()` function works correctly in a development environment without:

- Sending actual emails over the internet
- Risking misuse of real email services or triggering spam filters
- Requiring complex setup for secure, production-grade email delivery

### Why Emails Aren’t Sent to Real Inboxes

This project uses PHP’s native `mail()` function, which:

- Lacks authentication and encryption support
- Depends on a local or relay SMTP server (like Papercut)

For this local demo, Papercut provides a safe and effective solution to test email-related features.

### Screenshots/Visuals
>The primary webpage contains features related to tasks and includes a field for email.
<img width="800" height="980" alt="Screenshot 2025-08-26 200710" src="https://github.com/user-attachments/assets/5500952f-7a1f-4850-a7bb-6ebf239511f4" />

>Confirmation message displayed after submitting a valid email.
<img width="800" height="980" alt="Screenshot 2025-08-26 200718" src="https://github.com/user-attachments/assets/40c00e25-8449-4d9d-840a-021b0779c776" />


>Papercut SMTP tool catches emails sent from the local machine.
<img width="800" height="980" alt="Screenshot 2025-08-26 200726" src="https://github.com/user-attachments/assets/eaea7344-da1e-4e8d-b092-6c3121180e5f" />


>Verification email to verify email and subscribe to reminder emails.
![4](https://github.com/user-attachments/assets/b802d74c-3bea-46ae-b38f-fd2f0dcb8ae8)

>Notification shown after email verification is completed successfully.
<img width="800" height="980" alt="Screenshot 2025-08-26 200735" src="https://github.com/user-attachments/assets/e1d0b295-7d1f-44dd-94a3-8f7991b257c7" />


>CRON setup, Email reminders will be sent every hour following successful verification.
<img width="800" height="980" alt="Screenshot 2025-08-26 200937" src="https://github.com/user-attachments/assets/fc71d32e-ec54-45cd-93ca-b423432866a3" />


>Receive hourly emails regarding your pending tasks, with an option to unsubscribe.
<img width="800" height="980" alt="Screenshot 2025-08-26 201108" src="https://github.com/user-attachments/assets/8c9e3e3a-32b3-43cd-87f2-5e688b7302a3" />

## Deployment Plans

While the current setup works well for local development using XAMPP, WSL, and Papercut SMTP, I am actively exploring ways to simplify and improve the deployment process. The goal is to make it more accessible, portable, and production-ready.

###  Simplified Local Deployment

- **Remove WSL Dependency**  
  Replace the current CRON job setup with **Windows Task Scheduler** to run periodic scripts without needing a Linux environment.

- **Switch to PHPMailer**  
  Use [PHPMailer](https://github.com/PHPMailer/PHPMailer) instead of PHP’s `mail()` function for better configuration, SMTP support, and reliable email delivery.

- **Use Gmail SMTP or SendGrid**  
  Integrate Gmail SMTP or a third-party service like SendGrid to allow real email delivery without the need for Papercut SMTP.

---

### Web Deployment Plans

- **Deploy on Free Hosting Platforms**  
  Host the project using platforms like:
  - [000webhost](https://www.000webhost.com/)
  - [InfinityFree](https://infinityfree.net/)
  - [Render](https://render.com/)

- **Use cPanel or Hosting Schedulers**  
  Schedule tasks using cPanel’s built-in CRON tools instead of managing scripts manually.

- **Frontend-Backend Separation (Optional)**  
  Decouple the frontend (hosted on GitHub Pages or Netlify) and the backend (hosted on Render or similar) for greater flexibility.

---

###  Framework & Storage Upgrade (Planned)

- **Framework Transition**  
  Rebuild the project using a framework like **Laravel** for improved routing, email handling, and task scheduling with Laravel’s Artisan command scheduler.

- **Move to Database Storage**  
  Replace file-based storage with a lightweight database such as **SQLite** or **MySQL** to support better data integrity and scalability.

---

These enhancements will help reduce software overhead, improve user experience, and prepare the system for real-world deployment scenarios.


***THANK YOU :)***
