# Gmail SMTP Setup for Carrousel de Paris Contact Form

## Quick Setup Instructions

### 1. Gmail Account Preparation

1. **Enable 2-Step Verification** on your Gmail account:
   - Go to [Google Account Security](https://myaccount.google.com/security)
   - Click "2-Step Verification" and follow the setup

2. **Generate an App Password**:
   - In Google Account Security, under "Signing in to Google", click "2-Step Verification"
   - Scroll down and click "App passwords"
   - Select "Mail" and "Other (custom name)"
   - Enter "Carrousel de Paris" as the custom name
   - **Copy the 16-character password** (you'll need this)

### 2. WordPress Admin Configuration (Using WP Mail SMTP Plugin)

1. **Login to WordPress Admin**
2. **Go to Settings > WP Mail SMTP** (plugin menu item)
3. **Fill in the General tab**:
   - **From Email**: `contact@carrouseldeparis.fr`
   - **From Name**: `Carrousel de Paris`
   - **Return Path**: Check this box
4. **Configure the Mailer tab**:
   - **Mailer**: Select **Other SMTP**
   - **SMTP Host**: `smtp.gmail.com`
   - **Encryption**: Select **TLS**
   - **SMTP Port**: `587`
   - **Auto TLS**: Check this box
   - **Authentication**: Turn this **ON**
   - **SMTP Username**: Your full Gmail address (e.g., `your-email@gmail.com`)
   - **SMTP Password**: The 16-character App Password (NOT your regular Gmail password)

5. **Save Settings**
6. **Go to Settings > WP Mail SMTP > Email Test** to verify it's working

### 3. Alternative: Configuration via wp-config (WP Mail SMTP)

For production or if you prefer to use configuration files with WP Mail SMTP:

1. **Edit your wp-config file** (local: `wp-config.local.php`, production: `wp-config.production.php`)
2. **Add these lines** (uncomment and fill in your details):

```php
// WP Mail SMTP Configuration
define('WPMS_ON', true);
define('WPMS_MAIL_FROM', 'contact@carrouseldeparis.fr');
define('WPMS_MAIL_FROM_NAME', 'Carrousel de Paris');
define('WPMS_MAILER', 'smtp');
define('WPMS_SMTP_HOST', 'smtp.gmail.com');
define('WPMS_SMTP_PORT', 587);
define('WPMS_SSL', 'tls');
define('WPMS_SMTP_AUTH', true);
define('WPMS_SMTP_USER', 'your-email@gmail.com');
define('WPMS_SMTP_PASS', 'your-16-char-app-password');
```

### 4. Security Notes

- ✅ **DO use App Passwords** - never use your regular Gmail password
- ✅ **DO keep credentials secure** - don't commit them to version control
- ✅ **DO test the configuration** after setup
- ❌ **DON'T share your App Password** - treat it like a regular password

### 5. Testing

1. **Use the built-in test feature** in Settings > SMTP Settings
2. **Try the contact form** on your website
3. **Check error logs** if emails aren't sending (look in your WordPress debug log)

### 6. Troubleshooting

**Common Issues:**

1. **"Authentication failed"**
   - Make sure you're using the App Password, not your regular password
   - Verify 2-Step Verification is enabled on your Gmail account

2. **"Connection failed"**
   - Check your internet connection
   - Verify Gmail SMTP settings (smtp.gmail.com, port 587, TLS)

3. **Emails not received**
   - Check spam/junk folders
   - Verify the recipient email address is correct
   - Test with different email providers

4. **"From" address issues**
   - Gmail may override the "From" address with your actual Gmail address
   - This is normal Gmail behavior for security

### 7. Email Flow

When someone fills out your contact form:

1. **Main email** is sent to your admin email address (set in WordPress Settings > General)
2. **Confirmation email** is automatically sent to the person who filled out the form
3. **Both emails** are sent via Gmail SMTP using your configured settings

### 8. Production Deployment

When deploying to production:

1. **Copy** `wp-config.production.template.php` to `wp-config.production.php`
2. **Update** the SMTP settings in the production config
3. **Test** the email functionality on your live site
4. **Monitor** error logs for any issues

### 9. Local Development

For local development, the system is configured to skip SMTP (emails may not send or may use local sendmail). To test SMTP locally:

1. **Comment out** `define('CARROUSEL_LOCAL_EMAIL', true);` in `wp-config.local.php`
2. **Add your SMTP credentials** to `wp-config.local.php`
3. **Test** the contact form

---

## Support

If you encounter issues:

1. Check the WordPress error logs
2. Use the test email feature in the admin
3. Verify your Gmail App Password is correct
4. Ensure 2-Step Verification is enabled on Gmail

The contact form will continue to work even if SMTP fails - WordPress will fall back to the default mail system, though emails may be less reliable.
