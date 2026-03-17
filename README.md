# Wine Nutrition QR Code Generator

A WordPress plugin for managing wine product nutrition information with automatically generated QR codes — built for EU wine labelling compliance.

## Overview

This plugin adds a custom post type for wine nutrition data. Each entry stores wine-specific nutritional values and automatically generates a downloadable SVG QR code that links to the product's nutrition page. Designed for use by wineries, wine retailers, and importers who need to comply with EU regulation (EU 2021/2117) requiring nutrition labelling on wine bottles.

## Features

- **Custom post type** (`naehrwerte`) for wine nutrition entries
- **Automatic QR code generation** (SVG) linking to each product's page
- **Smart regeneration** — QR codes are only regenerated when the post URL changes
- **Admin preview & download** — view and download the QR code directly from the post editor
- **Nutritional calculations** — automatically calculates energy (kJ/kcal) and carbohydrate values from raw wine data using EU-standard formulas
- **Frontend shortcode** for embedding nutrition info on any page or post
- **Responsive layout** — two-column on desktop, single-column on mobile
- **German localization** — all admin labels and field names in German

## Requirements

| Requirement | Version |
|---|---|
| WordPress | >= 5.0 |
| PHP | >= 7.4 |
| Advanced Custom Fields Pro | any |
| Composer | (for dependency installation) |

> **Note:** This plugin requires **ACF Pro**. A warning will be shown in the admin if it is not active.

## Installation

1. Clone or download this repository into your `wp-content/plugins/` directory:
   ```bash
   cd wp-content/plugins/
   git clone <repository-url> wine_nutrition-qr-code-generator
   ```

2. Install PHP dependencies via Composer:
   ```bash
   cd wine_nutrition-qr-code-generator
   composer install --no-dev
   ```

3. Activate the plugin in the WordPress admin under **Plugins**.

4. Make sure **Advanced Custom Fields Pro** is installed and active.

## Usage

### Creating a Nutrition Entry

1. Go to **Nährwerte** in the WordPress admin menu.
2. Click **Add New**.
3. Fill in the wine details:
   - **Product image**
   - **Description**
   - **Volume** (default: 750 ml)
   - **Alcohol content** (%vol)
   - **Residual sugar** (g/l)
   - **Total acidity** (g/l)
   - **Glycerin** (g/l)
   - **Ingredients**
4. **Publish** the post. A QR code SVG is generated automatically.

### Downloading the QR Code

After publishing, scroll down to the **QR-Code Vorschau & Download** meta box in the post editor. Click the download button to save the SVG file for use on wine labels.

### Displaying Nutrition Info on the Frontend

Use the shortcode on any page or post:

```
[wine_nutrition_fields]
```

For legacy installations, the alias `[naehrwerte_felder]` is also supported.

To display a specific wine product, create a page using the permalink of a `naehrwerte` post — the template will render automatically for singular posts of that type.

## Nutritional Calculation

Energy values and carbohydrates are calculated per 100 ml using EU-standard formulas:

| Component | Conversion |
|---|---|
| Alcohol (g/100ml) | %vol × 0.789 |
| Residual sugar (g/100ml) | g/l ÷ 10 |
| Total acidity (g/100ml) | g/l ÷ 10 |
| Glycerin (g/100ml) | g/l ÷ 10 |

**Energy:**
```
kJ  = alcohol×29 + sugar×17 + acidity×13 + glycerin×10
kcal = alcohol×6.96 + sugar×4.08 + acidity×3.12 + glycerin×2.4
```

**Carbohydrates:**
```
Carbohydrates = residual sugar (g/100ml) + glycerin (g/100ml)
```

## File Structure

```
wine_nutrition-qr-code-generator/
├── functions.php                            # Plugin entry point
├── composer.json                            # PHP dependencies
├── includes/
│   ├── class-wine-nutrition.php            # Bootstrap / initialization
│   ├── class-wine-nutrition-cpt.php        # Custom post type registration
│   ├── class-wine-nutrition-qr.php         # QR code generation
│   └── class-wine-nutrition-shortcode.php  # Frontend shortcode
├── assets/
│   ├── css/style.css                        # Frontend styles
│   └── images/wine-glass-icon.png
└── vendor/                                  # Composer dependencies
```

## QR Code Storage

Generated QR codes are stored in:
```
wp-content/uploads/wine-nutrition-qrcodes/qr_[post-id]_[post-slug].svg
```

The URL to each file is stored in the post meta key `_qr_code_url`.

## Uninstall

When the plugin is deleted through the WordPress admin, the `wine-nutrition-qrcodes/` upload directory and all generated SVG files are automatically removed.

## License

Proprietary — &copy; more than ads. All rights reserved.
