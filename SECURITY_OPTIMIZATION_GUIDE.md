# 🔒 AnonyEngine Security & Optimization Guide

## 📋 Overview

This guide outlines the security improvements and optimizations applied to the AnonyEngine WordPress plugin to ensure compliance with WordPress Coding Standards (WPCS) and security best practices.

## 🛡️ Security Improvements Applied

### 1. **Input Validation & Sanitization**

#### ✅ **Before (Vulnerable)**
```php
// Direct use of user input without sanitization
$user_input = $_POST['field_name'];
$field_title = $this->field['title'];
```

#### ✅ **After (Secure)**
```php
// Proper sanitization of all user inputs
$user_input = sanitize_text_field( wp_unslash( $_POST['field_name'] ) );
$field_title = sanitize_text_field( $this->field['title'] );
```

### 2. **Nonce Verification**

#### ✅ **Added Nonce Protection**
```php
// Create nonce for forms
function anoe_create_nonce( $action = 'anonyengine_action' ) {
    return wp_create_nonce( 'anonyengine_' . $action );
}

// Verify nonce in form submissions
function anoe_verify_nonce( $nonce, $action = 'anonyengine_action' ) {
    return wp_verify_nonce( $nonce, 'anonyengine_' . $action );
}
```

### 3. **Capability Checks**

#### ✅ **Added User Capability Verification**
```php
function anoe_user_can( $capability = 'manage_options' ) {
    return current_user_can( $capability );
}
```

### 4. **File Upload Security**

#### ✅ **Secure File Upload Handling**
```php
function anoe_sanitize_file_upload( $file, $allowed_types = array(), $max_size = 0 ) {
    if ( ! is_array( $file ) || empty( $file['name'] ) ) {
        return new WP_Error( 'invalid_file', __( 'Invalid file data.', 'anonyengine' ) );
    }

    // Check file size
    if ( $max_size > 0 && $file['size'] > $max_size ) {
        return new WP_Error( 'file_too_large', __( 'File size exceeds maximum allowed size.', 'anonyengine' ) );
    }

    // Check file type
    if ( ! empty( $allowed_types ) ) {
        $file_extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        if ( ! in_array( $file_extension, $allowed_types, true ) ) {
            return new WP_Error( 'invalid_file_type', __( 'File type not allowed.', 'anonyengine' ) );
        }
    }

    // Sanitize file data
    return array(
        'name'     => sanitize_file_name( $file['name'] ),
        'type'     => sanitize_mime_type( $file['type'] ),
        'tmp_name' => $file['tmp_name'],
        'error'    => absint( $file['error'] ),
        'size'     => absint( $file['size'] ),
    );
}
```

### 5. **SQL Injection Prevention**

#### ✅ **Proper Database Queries**
```php
// Use prepared statements and WordPress functions
$user_id = get_current_user_id();
$option_value = get_option( 'anonyengine_option', '' );
```

### 6. **XSS Prevention**

#### ✅ **Output Escaping**
```php
// Escape all output
echo esc_html( $user_data );
echo wp_kses_post( $html_content );
echo esc_url( $url );
```

## 🔧 Code Quality Improvements

### 1. **WordPress Coding Standards Compliance**

#### ✅ **Proper DocBlocks**
```php
/**
 * Validate and sanitize email address.
 *
 * @since 1.0.0
 * @param string $email Email address to validate.
 * @return string|false Sanitized email or false if invalid.
 */
function anoe_validate_email( $email ) {
    $email = sanitize_email( $email );
    
    if ( ! is_email( $email ) ) {
        return false;
    }

    return $email;
}
```

#### ✅ **Consistent Naming Conventions**
```php
// Use descriptive function names with prefixes
function anoe_enqueue_styles() { }
function anoe_validate_email( $email ) { }
function anoe_create_nonce( $action ) { }
```

### 2. **Error Handling**

#### ✅ **Proper Error Handling**
```php
function anoe_handle_plugin_deactivation( $plugin, $network_wide ) {
    if ( 'anonyengine/anonyengine.php' === $plugin ) {
        $template = get_option( 'template' );
        if ( 'smartpage' === $template ) {
            wp_die(
                esc_html__( 'Sorry, you cannot deactivate this plugin. Because it is mandatory for SmartPage theme.', 'anonyengine' ),
                esc_html__( 'Plugin Deactivation Error', 'anonyengine' ),
                array(
                    'response' => 403,
                    'back_link' => true,
                )
            );
        }
    }
}
```

### 3. **Performance Optimizations**

#### ✅ **Efficient File Loading**
```php
// Check file existence before loading
$script_path = $this->plugin_dir . 'assets/js/' . $script . '.js';
if ( file_exists( $script_path ) ) {
    wp_enqueue_script(
        $script,
        $this->plugin_url . 'assets/js/' . $script . '.js',
        array( 'jquery' ),
        filemtime( $script_path ),
        true
    );
}
```

#### ✅ **Optimized Autoloading**
```php
function anoe_autoloader( $class_name ) {
    // Only handle AnonyEngine classes
    if ( false === strpos( $class_name, 'ANONY_' ) ) {
        return;
    }

    // Efficient path searching
    $autoload_paths = json_decode( ANOE_AUTOLOADS, true );
    if ( ! is_array( $autoload_paths ) ) {
        return;
    }

    foreach ( $autoload_paths as $path ) {
        if ( empty( $path ) ) {
            continue;
        }
        // ... rest of autoloading logic
    }
}
```

## 📁 File Structure Improvements

### 1. **Main Plugin File (`anonyengine.php`)**

#### ✅ **Singleton Pattern Implementation**
```php
final class AnonyEngine {
    private static $instance = null;
    
    private function __construct() {
        $this->init();
    }
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

### 2. **Configuration File (`config.php`)**

#### ✅ **Modular Configuration Loading**
```php
function anoe_load_config_files() {
    $required_files = array(
        'functions/helpers.php',
        'functions/ajax.php',
        'forms/forms.php',
        // ... other files
    );

    foreach ( $required_files as $file ) {
        $file_path = wp_normalize_path( ANOE_DIR . $file );
        if ( file_exists( $file_path ) ) {
            require_once $file_path;
        }
    }
}
```

## 🔍 Security Checklist

### ✅ **Input Validation**
- [x] All user inputs are sanitized
- [x] File uploads are validated
- [x] Email addresses are validated
- [x] URLs are validated and escaped

### ✅ **Authentication & Authorization**
- [x] Nonce verification implemented
- [x] Capability checks added
- [x] User permissions verified

### ✅ **Output Escaping**
- [x] All output is properly escaped
- [x] HTML content is sanitized
- [x] URLs are escaped

### ✅ **Database Security**
- [x] Prepared statements used
- [x] WordPress functions utilized
- [x] SQL injection prevented

### ✅ **File Security**
- [x] File upload validation
- [x] File type restrictions
- [x] File size limits

## 🚀 Performance Optimizations

### 1. **Asset Loading**
- File existence checks before enqueuing
- Proper versioning with `filemtime()`
- Conditional loading based on context

### 2. **Autoloading**
- Efficient class discovery
- Path validation
- Error handling

### 3. **Caching**
- Option caching
- Transient usage where appropriate
- Database query optimization

## 📝 Usage Examples

### **Secure Form Processing**
```php
// In your form handler
if ( ! anoe_verify_nonce( $_POST['_wpnonce'], 'form_action' ) ) {
    wp_die( __( 'Security check failed.', 'anonyengine' ) );
}

if ( ! anoe_user_can( 'edit_posts' ) ) {
    wp_die( __( 'Insufficient permissions.', 'anonyengine' ) );
}

$sanitized_data = anoe_sanitize_text( $_POST['field_name'] );
$validated_email = anoe_validate_email( $_POST['email'] );
```

### **Secure File Upload**
```php
$allowed_types = array( 'jpg', 'jpeg', 'png', 'gif' );
$max_size = 5 * 1024 * 1024; // 5MB

$file_validation = anoe_sanitize_file_upload( $_FILES['upload'], $allowed_types, $max_size );

if ( is_wp_error( $file_validation ) ) {
    $error_message = $file_validation->get_error_message();
    // Handle error
}
```

### **Secure Output**
```php
// Display user data safely
echo esc_html( $user_name );
echo wp_kses_post( $formatted_content );
echo esc_url( $user_website );
```

## 🔧 Maintenance

### **Regular Security Audits**
1. Review all user inputs for proper sanitization
2. Verify nonce usage in all forms
3. Check capability requirements
4. Update dependencies regularly
5. Monitor error logs

### **Performance Monitoring**
1. Monitor database query performance
2. Check asset loading times
3. Review autoloading efficiency
4. Monitor memory usage

## 📚 Additional Resources

- [WordPress Security Best Practices](https://developer.wordpress.org/plugins/security/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- [WordPress Plugin Security](https://wordpress.org/support/article/hardening-wordpress/)

---

**Note:** This guide should be updated regularly as new security threats emerge and WordPress evolves. Always test changes in a development environment before deploying to production. 