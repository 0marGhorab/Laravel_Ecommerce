# Email setup (verification & transactional)

## Email verification on register

When **email verification** is enabled, new users must enter a 6-digit code sent to their email before their account is created.

1. **Enable in `.env`:**
   ```env
   EMAIL_VERIFICATION_ON_REGISTER=true
   ```

2. **Configure real mail** so the code is actually sent. If you use `MAIL_MAILER=log`, emails are only written to `storage/logs/laravel.log` and are not delivered.

### Option A: Mailtrap (testing)

1. Sign up at [mailtrap.io](https://mailtrap.io) and create an inbox.
2. In Mailtrap, open **SMTP Settings** and copy host, port, username, password.
3. In your `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=live.smtp.mailtrap.io
   MAIL_PORT=587
   MAIL_USERNAME=your_username
   MAIL_PASSWORD=your_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```
4. Run `php artisan config:clear`. New signups will receive the verification email in your Mailtrap inbox.

### Option B: Gmail / production SMTP

Use your provider’s SMTP host, port (often 587 with TLS), and credentials. Set `MAIL_MAILER=smtp` and the same variables as above.

### Development without SMTP (local only)

If `APP_ENV=local` and `MAIL_MAILER=log`, the app shows the 6-digit code on the verification step so you can complete signup without configuring mail. Do **not** rely on this in production.

## Order and status emails

Order confirmation and status-update emails use the same mail config. Configure `MAIL_*` as above so customers receive them.
