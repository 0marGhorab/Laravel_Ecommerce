# Email setup

## Why you're not getting emails

By default Laravel is set to **`MAIL_MAILER=log`**. That means:

- **No real email is sent** and you don't need any email account.
- The full email (including the 6-digit verification code) is written to **`storage/logs/laravel.log`**.

So for **local testing** you can:

1. Register with any email (e.g. `test@test.com`).
2. Open **`storage/logs/laravel.log`** and search for `Your verification code` or the 6-digit number.
3. Enter that code on the verification screen.

---

## Option 1: Keep using the log (no account)

Leave `.env` as:

```env
MAIL_MAILER=log
```

Then read the verification code from `storage/logs/laravel.log` whenever you register.

---

## Option 2: Send real emails (you need an account)

To actually receive emails in an inbox, set **`MAIL_MAILER=smtp`** and add SMTP settings in `.env`.

### Testing: Mailtrap (fake inbox, no real delivery)

1. Sign up at [mailtrap.io](https://mailtrap.io) (free).
2. Create an inbox and copy the SMTP credentials (host, port, username, password).
3. In `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

(Use the exact host/port from your Mailtrap inbox.)

Then run the app and register with your real email; the message will appear in Mailtrap’s inbox (or in your real inbox if you use a real SMTP provider).

### Real delivery: Gmail, SendGrid, etc.

- **Gmail:** Use an [App Password](https://support.google.com/accounts/answer/185833), then set `MAIL_MAILER=smtp`, `MAIL_HOST=smtp.gmail.com`, `MAIL_PORT=587`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION=tls`.
- **SendGrid / Mailgun / other:** Get SMTP (or API) credentials from their dashboard and set the same `MAIL_*` variables accordingly.

After changing `.env`, restart your app (e.g. `php artisan config:clear` or restart the server).

---

**Summary:** With the default `log` driver you don’t need an account; use the log file to get the code. To receive emails in an inbox, set `MAIL_MAILER=smtp` and add the right SMTP credentials.
