# Google reCAPTCHA v2 Setup Instructions

## Step 1: Get Your reCAPTCHA Keys

1. Go to [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin/create)
2. Click "Create" to register a new site
3. Fill in the form:
   - **Label**: Enter a name for your site (e.g., "MOIST Alumni Admin")
   - **reCAPTCHA type**: Select "reCAPTCHA v2" → "I'm not a robot" Checkbox
   - **Domains**: Add your domains:
     - For development: `localhost`
     - For production: `yourdomain.com` (replace with your actual domain)
   - **Owners**: Your Google account email
   - Accept the reCAPTCHA Terms of Service
4. Click "Submit"

## Step 2: Configure Your Keys

1. After creating the site, you'll see two keys:
   - **Site Key** (public key)
   - **Secret Key** (private key)

2. Open the file `admin/recaptcha_config.php`

3. Replace the placeholder values:
   ```php
   // Replace YOUR_RECAPTCHA_SITE_KEY_HERE with your actual Site Key
   define('RECAPTCHA_SITE_KEY', 'your_actual_site_key_here');
   
   // Replace YOUR_RECAPTCHA_SECRET_KEY_HERE with your actual Secret Key
   define('RECAPTCHA_SECRET_KEY', 'your_actual_secret_key_here');
   ```

## Step 3: Test the Implementation

1. Navigate to `admin/login.php`
2. You should see:
   - The MOIST background image
   - A professional login form with reCAPTCHA
   - The "I'm not a robot" checkbox

3. Try logging in:
   - Without completing reCAPTCHA: Should show error message
   - With completed reCAPTCHA: Should proceed with normal login validation

## Security Features Implemented

- ✅ **reCAPTCHA v2 Integration**: Prevents automated bot attacks
- ✅ **Backend Verification**: Server-side validation of reCAPTCHA responses
- ✅ **Dynamic Background**: Professional MOIST branding with responsive design
- ✅ **Enhanced UI**: Modern glass morphism effects and animations
- ✅ **Mobile Responsive**: Optimized for all device sizes
- ✅ **Error Handling**: Comprehensive feedback for different failure scenarios

## Troubleshooting

### reCAPTCHA Not Loading
- Check that your Site Key is correct in `recaptcha_config.php`
- Ensure your domain is added to the reCAPTCHA console
- Check browser console for JavaScript errors

### reCAPTCHA Verification Failing
- Verify your Secret Key is correct in `recaptcha_config.php`
- Check server error logs for detailed error messages
- Ensure your server can make outbound HTTPS requests to Google

### Background Image Not Showing
- Verify the image path: `../assets/img/moist12.jpg`
- Check file permissions on the image file
- Ensure the image file exists and is accessible

## Files Modified

- `admin/login.php` - Updated UI and reCAPTCHA integration
- `admin/ajax.php` - Added reCAPTCHA verification to login process
- `admin/recaptcha_config.php` - Configuration file for reCAPTCHA keys

## Next Steps

1. **Get your reCAPTCHA keys** from Google reCAPTCHA console
2. **Update the configuration** in `recaptcha_config.php`
3. **Test the login** functionality
4. **Deploy to production** with your production domain keys

Your admin login is now secure with reCAPTCHA protection and features a professional, dynamic design with the MOIST branding!
