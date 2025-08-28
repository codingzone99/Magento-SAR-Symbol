# Saudi Riyal Currency Symbol Extension for Magento 2

This Magento 2 extension replaces the standard Saudi Riyal (SAR) currency symbols with a custom font symbol throughout your store.

## Features

- ✅ Custom Saudi Riyal font symbol using Unicode character `&#xE900;`
- ✅ Automatic symbol replacement on all price displays
- ✅ Support for product pages, category pages, cart, and checkout
- ✅ Dynamic content support (AJAX updates)
- ✅ Responsive design compatibility
- ✅ Print-friendly styling
- ✅ Easy installation and configuration
- ✅ Enhanced period removal for clean display
- ✅ Tested on V2.4.7, V2.4.8


## Installation

### Manual Installation

1. Copy the `zone99/SARSymbol` directory to your Magento 2 installation:
   ```
   app/code/zone99/SARSymbol/
   ```

2. Enable the module:
   ```bash
   php bin/magento module:enable zone99_SARSymbol
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento setup:static-content:deploy
   php bin/magento cache:flush
   ```

### Composer Installation

Add to your `composer.json`:
```json
{
    "require": {
        "zone99/sar-symbol": "^1.0"
    }
}
```

Then run:
```bash
composer install
php bin/magento module:enable zone99_SARSymbol
php bin/magento setup:upgrade
```

## Configuration

1. Set your store currency to SAR (Saudi Riyal)
2. The extension will automatically detect SAR currency and apply custom symbols
3. Clear cache after installation

## How It Works

The extension includes:

### Custom Font Files
- `saudi-riyal.woff` - Web font format
- `saudi-riyal.ttf` - TrueType font format

### Symbol Replacement
Replaces these standard SAR symbols:
- `﷼` (Unicode SAR symbol)
- `SAR` (Text abbreviation)
- `ر.س` (Arabic abbreviation)

With custom symbol: `&#xE900;` using the saudi_riyal font

### Enhanced Period Removal
The extension now includes advanced cleanup to remove unwanted periods that may appear after currency symbols, ensuring clean display across all contexts.

### Coverage Areas
- Product detail pages
- Category/listing pages
- Shopping cart
- Checkout process
- Mini cart
- Price totals
- Admin panel (where applicable)

## Usage Example

```html
<!-- Standard usage -->
<span style="font-family: 'saudi_riyal'">&#xE900;</span>

<!-- With CSS class -->
<span class="saudi-riyal-symbol">&#xE900;</span>
```

## File Structure

```
zone99/SARSymbol/
├── Block/
│   └── Currency.php
├── Helper/
│   └── Data.php
├── etc/
│   └── module.xml
├── view/frontend/
│   ├── layout/
│   │   └── default.xml
│   ├── templates/
│   │   └── currency/
│   │       └── converter.phtml
│   ├── web/
│   │   ├── css/
│   │   │   └── saudi-riyal.css
│   │   ├── fonts/
│   │   │   ├── saudi-riyal.woff
│   │   │   └── saudi-riyal.ttf
│   │   └── js/
│   │       └── currency-converter.js
│   └── requirejs-config.js
├── registration.php
├── composer.json
└── README.md
```

## Module Information

- **Module Name:** zone99_SARSymbol
- **Package Name:** zone99/sar-symbol
- **Namespace:** zone99\SARSymbol
- **Version:** 1.0.0
- **Author:** zone99
- **License:** MIT

## Browser Support

- Chrome 30+
- Firefox 25+
- Safari 7+
- Edge 12+
- Internet Explorer 11+

## Troubleshooting

### Font Not Loading
1. Check that font files are properly deployed to:
   ```
   pub/static/frontend/[theme]/[locale]/zone99_SARSymbol/fonts/
   ```
2. Clear browser cache
3. Verify CSS is loading correctly

### Symbols Not Replacing
1. Ensure SAR is set as current currency
2. Clear Magento cache:
   ```bash
   php bin/magento cache:flush
   ```
3. Check JavaScript console for errors
4. Verify module is enabled:
   ```bash
   php bin/magento module:status zone99_SARSymbol
   ```


### Performance Issues
1. Enable CSS/JS minification
2. Use a CDN for static assets
3. Enable browser caching



### Customization
To modify the symbol or add new replacements:

1. Edit `Helper/Data.php` constants
2. Update CSS selectors in `saudi-riyal.css`
3. Modify JavaScript in `currency-converter.js`

### Extending the Module
The module uses a clean architecture that can be easily extended:

- **Helper Class:** `zone99\SARSymbol\Helper\Data`
- **Block Class:** `zone99\SARSymbol\Block\Currency`
- **JavaScript Module:** `zone99_SARSymbol/js/currency-converter`

## Technical Details

### CSS Loading
```xml
<css src="zone99_SARSymbol::css/saudi-riyal.css"/>
```

### Template Usage
```xml
<block class="zone99\SARSymbol\Block\Currency" 
       template="zone99_SARSymbol::currency/converter.phtml" />
```

### Helper Usage
```php
$helper = $this->helper('zone99\SARSymbol\Helper\Data');
```

## License

MIT License - see LICENSE file for details

## Support

For support and questions:
- Email: contact@99.zone
- Module: zone99/sar-symbol

## Changelog

### Version 1.0.0
- Initial release
- Custom font symbol support
- Automatic symbol replacement
- Enhanced period removal functionality
- Responsive design
- Multi-page coverage
- Clean code architecture 
