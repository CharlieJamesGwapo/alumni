<?php
/**
 * Google reCAPTCHA Configuration
 * 
 * To get your reCAPTCHA keys:
 * 1. Go to https://www.google.com/recaptcha/admin/create
 * 2. Register a new site with reCAPTCHA v2 "I'm not a robot" Checkbox
 * 3. Add your domain (localhost for development)
 * 4. Copy the Site Key and Secret Key below
 */

// reCAPTCHA Site Key (public key - used in frontend)
define('RECAPTCHA_SITE_KEY', '6LdHKcIrAAAAAJJDkkpwXEkRuAFrkBCneVTc94wx');

// reCAPTCHA Secret Key (private key - used in backend)
define('RECAPTCHA_SECRET_KEY', '6LdHKcIrAAAAAHzbCse6RDTn4EBXmMzkkcGMmD7E');

/**
 * Function to verify reCAPTCHA response
 * @param string $response The reCAPTCHA response token
 * @return bool True if verification successful, false otherwise
 */
function verify_recaptcha($response) {
    if (empty($response)) {
        return false;
    }
    
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = array(
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    );
    
    $recaptcha_options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        )
    );
    
    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    
    if ($recaptcha_result === false) {
        return false;
    }
    
    $recaptcha_json = json_decode($recaptcha_result, true);
    
    return isset($recaptcha_json['success']) && $recaptcha_json['success'] === true;
}
?>
