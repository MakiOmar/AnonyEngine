# AnonyEngine - WordPress Development Framework

A comprehensive WordPress development framework that provides input fields, forms, metaboxes, options pages, and utilities for building powerful WordPress plugins and themes.

## 🚀 Features

- **📝 Input Fields System** - 30+ customizable input field types with validation and sanitization
- **📋 Forms Builder** - Complete form creation and management system with AJAX support
- **📦 Metaboxes** - Easy custom metabox creation for posts, pages, and custom post types
- **⚙️ Options Pages** - Beautiful and functional options pages with theme settings
- **🛠️ Utilities** - Helper functions and utilities for WordPress development
- **🌐 Internationalization** - Built-in translation support
- **📱 Responsive Design** - Mobile-friendly interface
- **🔌 Extensible** - Easy to extend and customize
- **🛡️ Security** - Built-in security features with nonce verification and input sanitization

## 📦 Installation

### Manual Installation

1. Download the plugin from the repository
2. Extract to your WordPress plugins directory (`wp-content/plugins/`)
3. Activate the plugin through WordPress admin
4. The framework is now available for use in your themes and plugins

### For Theme/Plugin Integration

To use AnonyEngine in your custom themes or plugins:

```php
<?php
// Check if AnonyEngine is active
if ( class_exists( 'ANONY_Validate_Inputs' ) ) {
    // AnonyEngine is available
    // Your code here
}
```

## 🔧 Quick Start

### Using Input Fields

```php
<?php
// Create a text input field
$field = array(
    'id' => 'my_text_field',
    'type' => 'text',
    'title' => 'My Text Field',
    'default' => 'Default value',
    'validate' => 'required|email'
);

// Use in metabox or options page
$input_field = new ANONY_Input_Field( $field, null, 'option' );
```

### Creating Forms

```php
<?php
// Create a form
$form_config = array(
    'id' => 'my_form',
    'title' => 'My Form',
    'fields' => array(
        array(
            'id' => 'name',
            'type' => 'text',
            'title' => 'Full Name',
            'validate' => 'required'
        ),
        array(
            'id' => 'email',
            'type' => 'email',
            'title' => 'Email Address',
            'validate' => 'required|email'
        )
    ),
    'actions' => array(
        'update_post' => array(
            'post_type' => 'my_post_type'
        )
    )
);

$form = new ANONY_Create_Form( $form_config );
```

### Adding Metaboxes

```php
<?php
// Create a metabox
$metabox_config = array(
    'id' => 'my_metabox',
    'title' => 'My Metabox',
    'post_types' => array( 'post', 'page' ),
    'fields' => array(
        array(
            'id' => 'custom_field',
            'type' => 'text',
            'title' => 'Custom Field'
        )
    )
);

$metabox = new ANONY_Meta_Box( $metabox_config );
```

### Creating Options Pages

```php
<?php
// Create an options page
$options_config = array(
    'opt_name' => 'my_theme_options',
    'menu_title' => 'Theme Options',
    'page_title' => 'My Theme Options',
    'fields' => array(
        array(
            'id' => 'logo',
            'type' => 'file-upload',
            'title' => 'Logo'
        ),
        array(
            'id' => 'primary_color',
            'type' => 'color',
            'title' => 'Primary Color'
        )
    )
);

$options = new ANONY_Theme_Settings( $options_config );
```

## 📋 Available Field Types

### Basic Fields
- `text` - Text input field
- `textarea` - Multi-line text area
- `number` - Numeric input
- `email` - Email input with validation
- `password` - Password field
- `url` - URL input with validation
- `tel` - Telephone input

### Selection Fields
- `select` - Dropdown select
- `select2` - Enhanced select with search
- `radio` - Radio buttons
- `radio-img` - Image-based radio buttons
- `checkbox` - Checkbox field
- `switch` - Toggle switch

### Media Fields
- `file-upload` - File upload field
- `gallery` - Image gallery field
- `uploader` - Media uploader

### Special Fields
- `color` - Color picker
- `color-farbtastic` - Farbtastic color picker
- `color-gradient` - Gradient color picker
- `date-time` - Date and time picker
- `sliderbar` - Range slider
- `location` - Location picker with Google Maps
- `font-select` - Font selector

### Layout Fields
- `tabs` - Tabbed interface
- `heading` - Section heading
- `div` - Custom div container
- `group-start` - Start field group
- `group-close` - Close field group

### Multi-Value Fields
- `multi-input` - Multiple input fields
- `multi-text` - Multiple text areas
- `multi-value` - Multiple value fields

## 🔌 Hooks and Filters

### Actions
```php
// Fired when AnonyEngine is loaded
do_action( 'anonyengine_loaded', $anonyengine );

// Fired when input fields are loaded
do_action( 'anonyengine_input_fields_loaded' );

// Fired when forms are loaded
do_action( 'anonyengine_forms_loaded' );
```

### Filters
```php
// Modify field CSS classes
apply_filters( 'anony_input_field_classes', $classes, $field );

// Custom form validation
apply_filters( 'anony_form_validation', $validation_result, $form_data );

// Modify metabox fields
apply_filters( 'anony_metabox_fields', $fields, $metabox_id );
```

## 📁 File Structure

```
anonyengine/
├── anonyengine.php              # Main plugin file
├── config.php                   # Configuration and autoloader
├── anonyengine-options.php      # Options page configuration
├── input-fields/                # Input fields system
│   ├── class-anony-input-field.php
│   ├── class-anony-input-base.php
│   ├── fields/                  # Individual field types
│   ├── assets/                  # CSS/JS assets
│   └── index.php                # Input fields loader
├── forms/                       # Forms system
│   ├── classes/                 # Form classes
│   ├── samples/                 # Example forms
│   └── forms.php                # Forms loader
├── metaboxes/                   # Metaboxes system
│   ├── classes/                 # Metabox classes
│   ├── assets/                  # CSS/JS assets
│   └── metaboxes.php            # Metaboxes loader
├── options/                     # Options system
│   ├── widgets/                 # Option widgets
│   ├── css/                     # Options CSS
│   └── options.php              # Options loader
├── common-classes/              # Common classes
│   ├── class-anony-validate-inputs.php
│   ├── class-anony-fields-scripts.php
│   └── class-anony-elementor-custom-fonts.php
├── functions/                   # Helper functions
│   ├── helpers.php              # General helpers
│   └── ajax.php                 # AJAX handlers
├── helpme/                      # Helper classes
│   ├── helper-classes/          # PHP and WP helpers
│   ├── wp-hooks.php             # WordPress hooks
│   └── helpme.php               # Helper loader
├── utilities/                   # Utility functions
│   ├── utilities.php            # Utility loader
│   └── share-by-email/          # Email sharing utility
├── assets/                      # Framework assets
│   ├── css/                     # Stylesheets
│   ├── js/                      # JavaScript files
│   ├── images/                  # Images and icons
│   └── fonts/                   # Font files
├── languages/                   # Translation files
├── libs/                        # External libraries
├── plugin-update-checker/       # Update checker
├── vendor/                      # Composer dependencies
├── composer.json                # Composer configuration
└── README.md                    # This file
```

## 🛡️ Security Features

### Input Validation & Sanitization
- All user inputs are properly sanitized using WordPress functions
- Built-in validation for emails, URLs, numbers, and more
- File upload validation with type and size restrictions
- Nonce verification for all forms

### Authentication & Authorization
- Capability checks for admin functions
- User permission verification
- Secure file handling

### Output Escaping
- All output is properly escaped
- HTML content sanitization
- URL escaping

## 🚀 Performance Features

- **Efficient Autoloading** - Classes are loaded only when needed
- **Asset Optimization** - CSS and JS are properly versioned
- **File Existence Checks** - Prevents errors from missing files
- **Caching Support** - Built-in caching capabilities

## 🌐 Internationalization

AnonyEngine supports translations:

```php
// Load text domain
load_plugin_textdomain( 'anonyengine', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

// Use in your code
__( 'My Field', 'anonyengine' );
_e( 'My Field', 'anonyengine' );
```

## 🔧 Configuration

### Plugin Constants
```php
// Plugin directory
define( 'ANOE_DIR', plugin_dir_path( __FILE__ ) );

// Plugin URL
define( 'ANOE_URI', plugin_dir_url( __FILE__ ) );

// Plugin version
define( 'ANOE_VERSION', '1.0.0224' );
```

### Autoloader Configuration
The plugin uses a custom autoloader that automatically loads classes based on naming conventions:
- Classes starting with `ANONY_` are automatically loaded
- File names follow WordPress conventions: `class-anony-class-name.php`
- Supports nested directory structures

## 🎨 Styling

AnonyEngine includes responsive CSS styles. You can customize the appearance:

```css
/* Custom input styling */
.anony-input-field {
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 12px;
}

/* Custom form styling */
.anony-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes following WordPress coding standards
4. Test thoroughly
5. Submit a pull request

## 📄 License

This plugin is licensed under the GPL-2.0-or-later License.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Contact: info@makiomar.com
- Website: https://makiomar.com

## 🔄 Changelog

### Version 1.0.0224
- Enhanced security with input validation and sanitization
- Improved performance with optimized autoloading
- Better error handling and debugging
- WordPress coding standards compliance
- Comprehensive documentation updates

### Version 1.0.0
- Initial release
- Complete input fields system with 30+ field types
- Forms builder with validation and AJAX support
- Metaboxes system for posts and custom post types
- Options pages with theme settings
- Utilities and helper functions
- Internationalization support
- Responsive design

## 📚 Examples

The plugin includes sample files and examples in various directories:
- `forms/samples/` - Form examples
- `input-fields/fields/` - Field type examples
- `metaboxes/` - Metabox implementation examples
- `options/` - Options page examples

## 🔍 Troubleshooting

### Common Issues

1. **Classes not loading**: Check if the autoloader is working properly
2. **Fields not displaying**: Verify field configuration and CSS
3. **Validation errors**: Check validation rules and sanitization
4. **AJAX not working**: Verify nonce verification and permissions

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
// In wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

---

**Note**: This plugin is designed to work with WordPress 5.0+ and PHP 7.3+. Always test in a development environment before using in production. 