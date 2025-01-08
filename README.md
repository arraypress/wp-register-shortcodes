# WordPress Shortcode Registration Library

A comprehensive PHP library for registering and managing WordPress shortcodes programmatically. This library provides a robust solution for creating and managing WordPress shortcodes with automatic prefixing and attribute management.

## Features

- 🚀 Simple shortcode registration and management
- 🔄 Automatic prefix handling for plugin isolation
- 📝 Default attribute management
- 🛠️ Simple utility functions for quick implementation
- ✅ Comprehensive error handling
- 🔍 Debug logging support

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher

## Installation

You can install the package via composer:

```bash
composer require arraypress/wp-register-shortcodes
```

## Basic Usage

Here's a simple example of registering shortcodes:

```php
// Define shortcode callbacks
function display_user_profile($atts) {
    $defaults = shortcode_atts([
        'user_id' => get_current_user_id(),
        'show_avatar' => 'yes'
    ], $atts);
    
    // Shortcode logic here...
    return 'User profile output';
}

// Define your shortcodes
$shortcodes = [
    'profile' => [
        'callback' => 'display_user_profile',
        'attributes' => [
            'user_id' => '1',
            'show_avatar' => 'yes'
        ],
        'description' => 'Display user profile information'
    ],
    'button' => [
        'callback' => 'display_custom_button',
        'attributes' => [
            'text' => 'Click Me',
            'url' => '',
            'style' => 'default'
        ]
    ]
];

// Register shortcodes with a prefix
register_shortcodes($shortcodes, 'my_plugin');

// The shortcodes would be available as [my_plugin_profile] and [my_plugin_button]
```

## Using the Class Directly

For more advanced usage, you can use the class directly:

```php
use ArrayPress\WP\Register\Shortcodes;

$shortcodes = Shortcodes::instance();

// Add multiple shortcodes
$shortcodes->add_shortcodes([
    'gallery' => [
        'callback' => 'custom_gallery_handler',
        'attributes' => ['columns' => '3']
    ]
]);

// Add single shortcode
$shortcodes->add_shortcode('button', [
    'callback' => 'custom_button_handler',
    'attributes' => ['size' => 'large']
]);

// Install shortcodes
$shortcodes->install();
```

## Configuration Options

Each shortcode can be configured with:

| Option | Type | Description |
|--------|------|-------------|
| callback | callable | Function to handle the shortcode (required) |
| attributes | array | Default attributes for the shortcode |
| description | string | Description of the shortcode functionality |

## Utility Functions

Global helper functions for easy access:

```php
// Register shortcodes
register_shortcodes($shortcodes, 'prefix');

// Unregister shortcodes
unregister_shortcodes($shortcodes, 'prefix');
```

## Advanced Example

Here's an example showing more advanced usage:

```php
class MyPlugin {
    public function init() {
        // Define shortcodes
        $shortcodes = [
            'team_member' => [
                'callback' => [$this, 'render_team_member'],
                'attributes' => [
                    'name' => '',
                    'position' => '',
                    'image' => '',
                    'social' => ''
                ],
                'description' => 'Display team member profile'
            ],
            'pricing_table' => [
                'callback' => [$this, 'render_pricing_table'],
                'attributes' => [
                    'plan' => 'basic',
                    'currency' => 'USD',
                    'show_features' => 'yes'
                ],
                'description' => 'Display pricing table'
            ]
        ];
        
        // Register with plugin prefix
        register_shortcodes($shortcodes, 'myplugin');
    }
    
    public function render_team_member($atts, $content = null) {
        $attributes = shortcode_atts([
            'name' => '',
            'position' => '',
            'image' => '',
            'social' => ''
        ], $atts);
        
        // Render team member HTML...
        return $output;
    }
    
    public function render_pricing_table($atts, $content = null) {
        $attributes = shortcode_atts([
            'plan' => 'basic',
            'currency' => 'USD',
            'show_features' => 'yes'
        ], $atts);
        
        // Render pricing table HTML...
        return $output;
    }
}
```

## Debug Mode

Debug logging is enabled when WP_DEBUG is true:

```php
// Logs will include:
// - Shortcode registration
// - Invalid configurations
// - Errors
// - Removal of shortcodes
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is licensed under the GPL2+ License. See the LICENSE file for details.

## Support

For support, please use the [issue tracker](https://github.com/arraypress/wp-register-shortcodes/issues).