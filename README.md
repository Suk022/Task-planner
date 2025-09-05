# Task Planner with Email Reminders
A lightweight **PHP-based task management system** with email reminders.  
Tasks are stored in simple files (JSON + text), and users can subscribe to hourly reminder emails with a **two-step verification process**.

## Features
### Task Management
- Add new tasks  
- Mark tasks as completed/uncompleted  
- Delete tasks  
- View all tasks  

### Email Subscription
- Subscribe to task reminders  
- Verify email via link (2-step process)  
- Hourly email reminders for uncompleted tasks  
- Unsubscribe anytime from email link  

---

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

## Requirements

- PHP **7.4+**
- Web server (Apache/XAMPP or similar)
- Papercut SMTP(for local email testing)
- **Windows users:** WSL (for CRON) or Task Scheduler


## Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/Suk022/Task-planner.git
   cd php-assignment
   ```

2. **Set up Papercut SMTP**
   - Download and run Papercut.Service.exe
   - Papercut will start a local SMTP server on port 25
   - Access the Papercut web interface at `http://localhost:5000` to view sent emails

3. **Configure PHP mail settings:**
   - Update your php.ini:
     ```
     [mail function]
     SMTP = localhost
     smtp_port = 25
     sendmail_from = noreply@localhost
     ```
   - Save the file and restart Apache

   Or use .htaccess in src/:
   
    ```
    php_value SMTP localhost
    php_value smtp_port 25
    php_value sendmail_from noreply@localhost
    ```
   - Restart Apache for the changes to take effect.
  
4. Set up CRON job (Linux/WSL)
   - Navigate to the project directory:
     ```bash
     cd /mnt/c/xampp/htdocs/php-assignment
     ```
   - Edit the CRON configuration:
     ```bash
     crontab -e
     ```
   - Add:
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
2. Add and manage tasks:
   - Add via form
   - Toggle complete/incomplete
   - Delete tasks
3. Subscribe with email:
   - Enter email → get verification link
   - Verify via email → reminders enabled
4. Receive hourly reminders for pending tasks
5. Unsubscribe anytime via email link

## Technical Details
### Storage
- Tasks: tasks.txt (JSON format)
- Subscribers: subscribers.txt (plain text list)
- Pending verifications: pending_subscriptions.txt

### Security
- Two-step email verification
- No password storage
- Unsubscribe link included in every email

## Troubleshooting

**Email not sending?**
   - Check PHP mail() config
   - Confirm Papercut is running

**CRON not working?**
   - Verify CRON service is running:
     ```bash
     sudo service cron status
     ```
   - Check CRON logs:
     ```bash
     grep CRON /var/log/syslog
     ```
   - Test manually:
     ```bash
     cd /mnt/c/xampp/htdocs/php-assignment/src && /mnt/c/xampp/php/php.exe cron.php
     ```

**WSL Issues?**
   - Update WSL: wsl --update
   - Start CRON: sudo service cron start

**Page not loading?**
   - If you see:
      ```
      Not Found
      The requested URL was not found on this server.
      Apache/2.4.29 (Ubuntu) Server at localhost Port 80
      ```
   - Run:
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
<img width="1920" height="1080" alt="Screenshot 2025-08-26 200710" src="https://github.com/user-attachments/assets/5500952f-7a1f-4850-a7bb-6ebf239511f4" />

>Confirmation message displayed after submitting a valid email.
<img width="1920" height="1080" alt="Screenshot 2025-08-26 200718" src="https://github.com/user-attachments/assets/40c00e25-8449-4d9d-840a-021b0779c776" />


>Papercut SMTP tool catches emails sent from the local machine.
<img width="1920" height="1080" alt="Screenshot 2025-08-26 200726" src="https://github.com/user-attachments/assets/eaea7344-da1e-4e8d-b092-6c3121180e5f" />


>Verification email to verify email and subscribe to reminder emails.
<img width="1920" height="1080" alt="Screenshot 2025-08-26 200735" src="https://github.com/user-attachments/assets/0eaae7ff-328a-4ff4-a337-075fed881c67" />


>Notification shown after email verification is completed successfully.
<img width="1920" height="1080" alt="Screenshot 2025-08-26 200735" src="https://github.com/user-attachments/assets/e1d0b295-7d1f-44dd-94a3-8f7991b257c7" />


>CRON setup, Email reminders will be sent every hour following successful verification.
<img width="1920" height="1080" alt="Screenshot 2025-08-26 200937" src="https://github.com/user-attachments/assets/fc71d32e-ec54-45cd-93ca-b423432866a3" />


>Receive hourly emails regarding your pending tasks, with an option to unsubscribe.
<img width="1920" height="1080" alt="Screenshot 2025-08-26 201108" src="https://github.com/user-attachments/assets/8c9e3e3a-32b3-43cd-87f2-5e688b7302a3" />

## Deployment Plans

While the current setup works well for local development using XAMPP, WSL, and Papercut SMTP, I am actively exploring ways to simplify and improve the deployment process. The goal is to make it more accessible, portable, and production-ready.

###  Simplified Local Deployment

- **Remove WSL Dependency**  
  Replace the current CRON job setup with **Windows Task Scheduler** to run periodic scripts without needing a Linux environment.

- **Switch to PHPMailer**  
  Use [PHPMailer](https://github.com/PHPMailer/PHPMailer) instead of PHP’s `mail()` function for better configuration, SMTP support, and reliable email delivery.

- **Enable real delivery via Gmail SMTP / SendGrid**

### Web Hosting

- **Deploy on Free Hosting Platforms**  
  Host the project using platforms like:
  - 000webhost
  - InfinityFree
  - Render

- **Use cPanel or Hosting Schedulers**  
  Schedule tasks using cPanel’s built-in CRON tools instead of managing scripts manually.

###  Framework & Storage Upgrade (Planned)

- **Framework Transition**  
  Rebuild the project using a framework like **Laravel** for improved routing, email handling, and task scheduling with Laravel’s Artisan command scheduler.

- **Move to Database Storage**  
  Replace file-based storage with a lightweight database such as **SQLite** or **MySQL** to support better data integrity and scalability.

These enhancements will help reduce software overhead, improve user experience, and prepare the system for real-world deployment scenarios.

This project is still under development, thank you!
