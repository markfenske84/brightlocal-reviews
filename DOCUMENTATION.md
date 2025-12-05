# BrightLocal Reviews Plugin - Complete Documentation

**Version:** 1.2.1  
**Author:** Mark Fenske  
**License:** GPL-2.0-or-later

---

## Table of Contents

### Part 1: User Guide
1. [Introduction](#introduction)
2. [What Does This Plugin Do?](#what-does-this-plugin-do)
3. [Requirements](#requirements)
4. [Installation](#installation)
5. [Initial Setup](#initial-setup)
6. [Getting Your Widget ID from BrightLocal](#getting-your-widget-id-from-brightlocal)
7. [Adding Reviews to Your Website](#adding-reviews-to-your-website)
8. [Block Settings & Display Options](#block-settings--display-options)
9. [Customizing Button Appearance](#customizing-button-appearance)
10. [Using the Shortcode](#using-the-shortcode)
11. [Managing Reviews](#managing-reviews)
12. [User FAQ & Troubleshooting](#user-faq--troubleshooting)

### Part 2: Developer Guide
13. [Architecture Overview](#architecture-overview)
14. [File Structure](#file-structure)
15. [Core Components](#core-components)
16. [Development Setup](#development-setup)
17. [Custom Post Type & Taxonomies](#custom-post-type--taxonomies)
18. [Block Development](#block-development)
19. [API Integration](#api-integration)
20. [AJAX Handlers](#ajax-handlers)
21. [Hooks & Filters](#hooks--filters)
22. [Database Schema](#database-schema)
23. [REST API Endpoints](#rest-api-endpoints)
24. [Styling Architecture](#styling-architecture)
25. [JavaScript Architecture](#javascript-architecture)
26. [Auto-Update System](#auto-update-system)
27. [Extending the Plugin](#extending-the-plugin)
28. [Testing & Debugging](#testing--debugging)
29. [Building for Production](#building-for-production)
30. [Contributing Guidelines](#contributing-guidelines)
31. [Additional Resources](#additional-resources)

---

# Part 1: User Guide

## Introduction

The BrightLocal Reviews plugin allows you to display customer reviews from BrightLocal on your WordPress website. It automatically imports reviews from your BrightLocal Showcase Review widgets and displays them in beautiful, customizable layouts.

## What Does This Plugin Do?

This plugin:

- **Imports reviews** from BrightLocal Showcase Review widgets
- **Stores reviews** locally in your WordPress database
- **Displays reviews** on your website in grid, list, or carousel layouts
- **Auto-updates** reviews every hour to keep them current
- **Filters reviews** by location or source labels
- **Customizes appearance** to match your website's design
- **Works everywhere** - use blocks, shortcodes, or widgets

## Requirements

- WordPress 5.9 or later
- PHP 7.4 or later
- An active BrightLocal account with at least one Showcase Review widget
- Block Editor (Gutenberg) enabled (recommended)

## Installation

### Method 1: Upload via WordPress Admin (Recommended)

1. Download the plugin ZIP file from your source
2. Log in to your WordPress admin dashboard
3. Go to **Plugins → Add New**
4. Click **Upload Plugin** at the top
5. Click **Choose File** and select the plugin ZIP file
6. Click **Install Now**
7. After installation completes, click **Activate Plugin**

### Method 2: Manual Installation via FTP

1. Download and unzip the plugin file
2. Connect to your website via FTP
3. Upload the `brightlocal-reviews` folder to `/wp-content/plugins/`
4. Go to **Plugins** in your WordPress admin
5. Find **BrightLocal Reviews** and click **Activate**

### After Activation

Once activated, you'll see a new menu item called **BrightLocal Reviews** with an orange icon in your WordPress admin sidebar.

---

## Initial Setup

### Step 1: Access the Settings Page

1. In your WordPress admin, click **BrightLocal Reviews** in the sidebar
2. This opens the Settings page (Widgets tab)

### Step 2: Add Your First Widget

1. You'll see a table with fields for **Widget ID** and **Label**
2. Enter your BrightLocal Widget ID (see next section for how to get this)
3. Enter a descriptive **Label** (e.g., "Downtown Location", "Google Reviews", "Facebook Reviews")
4. Click **Add Widget** to add more if you have multiple BrightLocal widgets

### Step 3: Save and Import Reviews

1. Click **Save Settings** to save your widget configuration
2. Click **Get Reviews** (or **Update Reviews** if you already have reviews) to import reviews from BrightLocal
3. Wait for the success message confirming how many reviews were imported

---

## Getting Your Widget ID from BrightLocal

Your Widget ID is a 40-character code that identifies your BrightLocal Showcase Review widget.

### How to Find It:

1. **Log in to BrightLocal** at [https://www.brightlocal.com](https://www.brightlocal.com)
2. Navigate to your location
3. Click **Review Widgets** in the left sidebar
4. Click on the **Showcase Reviews** tab
5. Click **Create Widget** or select an existing widget
6. Choose **JSON Feed** as the widget type
7. Copy the **Widget ID** from the URL provided

The Widget ID looks like this: `a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0`

### Need Help Creating a Widget?

BrightLocal provides detailed instructions: [How to create Showcase Review widgets](https://help.brightlocal.com/hc/en-us/articles/360013528499-How-to-create-Showcase-Review-widgets)

### Don't Have a BrightLocal Account?

You can [sign up for a free trial](https://tools.brightlocal.com/seo-tools/admin/sign-up-v2/257/) to get started.

---

## Adding Reviews to Your Website

Once you've imported reviews, you can display them on any page or post using the WordPress Block Editor.

### Using the Block Editor (Gutenberg)

1. **Edit or create a page/post** where you want to display reviews
2. Click the **+** button to add a new block
3. Search for "BrightLocal Reviews"
4. Click the **BrightLocal Reviews** block to insert it
5. Configure the block settings in the right sidebar (see next section)
6. **Update** or **Publish** your page

### Using the Classic Editor

If you're using the Classic Editor:

1. Add a **Shortcode block** or switch to the Text editor
2. Insert the shortcode: `[brightlocal_reviews]`
3. See the [Shortcode section](#using-the-shortcode) for customization options

### Using Widgets (Sidebar/Footer)

1. Go to **Appearance → Widgets**
2. Add a **Shortcode widget** to your desired widget area
3. Enter: `[brightlocal_reviews]`
4. Save the widget

---

## Block Settings & Display Options

When you insert the BrightLocal Reviews block, you can customize how reviews appear using the settings in the right sidebar.

### Display Type

Choose how reviews are laid out:

- **Grid** (default) - Reviews appear in a card-style grid layout (3 columns on desktop)
- **List** - Reviews stack vertically in a single column
- **Carousel** - Reviews appear in a slider with navigation arrows (if enabled)

### Review Label

Filter which reviews to display:

- **All** (default) - Shows reviews from all configured widgets
- **Specific Label** - Shows only reviews from a specific widget label (e.g., only Google reviews, only Downtown location)

This is useful if you have multiple BrightLocal widgets for different locations or platforms.

### Limit Items

Control how many reviews display initially:

- **Disabled** (default) - Shows all reviews
- **Enabled** - Shows a limited number of reviews with a "Load More" button

### Items Per Page

When "Limit Items" is enabled, this controls how many reviews appear initially and how many load when the user clicks "Load More" (default: 3).

### Show/Hide Elements

Toggle individual review elements on or off:

- **Show Author** - Display the reviewer's name
- **Show Date** - Display when the review was published
- **Show Source** - Display the platform icon (Google, Facebook, Yelp, etc.)

### Show Arrows

For list/carousel views, this enables previous/next navigation arrows (default: enabled).

---

## Customizing Button Appearance

You can customize the appearance of the "Read More" and "Load More" buttons to match your website's design.

### Accessing Display Settings

1. Go to **BrightLocal Reviews → Settings**
2. Click the **Display** tab

### Available Options

#### Button Background Color
Choose the background color for buttons (default: blue #0073aa)

#### Button Text Color
Choose the text color for buttons (default: white #ffffff)

#### Button Background Hover Color
Choose the background color when hovering over buttons (default: darker blue #005177)

#### Button Text Hover Color
Choose the text color when hovering over buttons (default: white #ffffff)

#### Button Border Radius
Control the roundness of button corners:
- **TL** (Top Left), **TR** (Top Right), **BL** (Bottom Left), **BR** (Bottom Right)
- Set in pixels (default: 4px for all corners)
- Use the link button to adjust all corners together

#### Button Text Transform
Change text capitalization:
- **None** (default) - Leave text as written
- **Capitalize** - Capitalize First Letter Of Each Word
- **Uppercase** - ALL UPPERCASE
- **Lowercase** - all lowercase

### Saving Changes

Click **Save Display Settings** to apply your changes. Changes take effect immediately on the front-end of your website.

---

## Using the Shortcode

You can embed reviews anywhere using the `[brightlocal_reviews]` shortcode.

### Basic Usage

```
[brightlocal_reviews]
```

This displays reviews with default settings.

### Shortcode Attributes

Customize the shortcode with these attributes:

#### displayType
Display layout (grid, list, or carousel)
```
[brightlocal_reviews displayType="list"]
```

#### reviewLabel
Filter by label (use label name, ID, or "all")
```
[brightlocal_reviews reviewLabel="google"]
```

#### limitItems
Enable item limit (true or false)
```
[brightlocal_reviews limitItems="true"]
```

#### itemsPerPage
Number of items to show per page (number)
```
[brightlocal_reviews itemsPerPage="5"]
```

#### showAuthor
Show author name (true or false)
```
[brightlocal_reviews showAuthor="true"]
```

#### showDate
Show review date (true or false)
```
[brightlocal_reviews showDate="true"]
```

#### showSource
Show source icon (true or false)
```
[brightlocal_reviews showSource="true"]
```

#### showArrows
Show carousel arrows (true or false)
```
[brightlocal_reviews showArrows="true"]
```

### Complete Example

```
[brightlocal_reviews displayType="list" reviewLabel="23" limitItems="true" itemsPerPage="5" showAuthor="true" showDate="false" showSource="true"]
```

This displays reviews as a list, filtered by label ID 23, showing 5 reviews at a time, with author names and source icons but without dates.

---

## Managing Reviews

### Viewing All Reviews

1. Go to **BrightLocal Reviews → Reviews**
2. You'll see all imported reviews in a table
3. Reviews show the author name, source, label, and star rating

### Editing Individual Reviews

1. Click on any review title to edit it
2. You can edit:
   - **Title** (author name)
   - **Content** (review text)
   - **Review Details** (shown in sidebar: rating, date, source)
   - **Review Labels** (categorize the review)
3. Click **Update** to save changes

**Note:** Manually edited reviews won't be overwritten during automatic updates.

### Managing Review Sources

1. Go to **BrightLocal Reviews → Review Sources**
2. View all sources (Google, Facebook, Yelp, etc.)
3. Edit source names or descriptions if needed

### Managing Review Labels

1. Go to **BrightLocal Reviews → Review Labels**
2. View all labels you've created
3. Edit label names or delete unused labels

### Updating Reviews

Reviews automatically update every hour via WordPress Cron. To manually trigger an update:

1. Go to **BrightLocal Reviews → Settings**
2. Click **Update Reviews** button
3. Wait for the success message

### Deleting All Reviews

**⚠️ Warning: This action cannot be undone!**

1. Go to **BrightLocal Reviews → Settings**
2. Scroll to the bottom
3. Click **Delete All Reviews**
4. Confirm the warning dialog

This removes all reviews and associated taxonomy terms from your database.

### Removing All Widgets

1. Go to **BrightLocal Reviews → Settings**
2. Scroll to the bottom
3. Click **Remove All Widgets**
4. Confirm the warning dialog

This removes all widget IDs and clears associated labels and reviews.

---

## User FAQ & Troubleshooting

### Frequently Asked Questions

#### How often do reviews update?

Reviews automatically update every hour using WordPress Cron. You can also manually trigger updates from the Settings page.

#### Do I need to keep BrightLocal active?

Yes, the plugin fetches reviews from BrightLocal's API. Your BrightLocal Showcase Review widgets must remain active to receive updates.

#### Can I display reviews from multiple locations?

Yes! Add multiple widget IDs with different labels, then filter by label when displaying reviews on your site.

#### Can I edit reviews?

Yes, you can edit any review from the Reviews page. Manual edits persist even when reviews are updated from BrightLocal.

#### Do reviews affect my website's SEO?

The plugin outputs schema.org-compliant markup (Review and AggregateRating schemas), which can help search engines understand and display your reviews in search results.

#### What review sources are supported?

The plugin supports all review sources that BrightLocal's Showcase Review widgets support, including:
- Google
- Facebook
- Yelp
- TripAdvisor
- Trustpilot
- HomeAdvisor
- And many more

#### Can I use this plugin without the Block Editor?

Yes! You can use the shortcode `[brightlocal_reviews]` anywhere - in the Classic Editor, text widgets, page builders, or PHP templates.

#### Will reviews display on mobile devices?

Yes, the plugin is fully responsive and adapts to all screen sizes.

#### Can I customize the styling?

Yes! The Display settings allow you to customize button colors and styles. For advanced styling, you can add custom CSS through **Appearance → Customize → Additional CSS**.

### Troubleshooting

#### Reviews aren't importing

**Possible causes:**

1. **Invalid Widget ID** - Ensure your Widget ID is exactly 40 characters (hexadecimal format)
2. **Widget not active** - Verify the widget exists and is active in BrightLocal
3. **No reviews available** - Check that your BrightLocal widget has reviews to import
4. **Server connection issue** - Contact your hosting provider to verify external API connections are allowed

#### Reviews aren't updating automatically

**Possible causes:**

1. **WordPress Cron not running** - Some hosting providers disable WP-Cron. Contact your host or use a server cron job
2. **Manual update works** - If manual updates work but automatic don't, WP-Cron is likely disabled

#### Block doesn't appear in editor

**Possible causes:**

1. **WordPress version** - Ensure you're running WordPress 5.9 or later
2. **Classic Editor plugin active** - The block works in the Block Editor (Gutenberg) only. Use the shortcode if using Classic Editor
3. **Cache issue** - Clear your browser cache and WordPress cache

#### Reviews display but styling looks broken

**Possible causes:**

1. **Theme conflict** - Your theme's CSS may conflict with the plugin styles
2. **Cache plugin** - Clear your cache plugin (if using one)
3. **Browser cache** - Hard refresh your browser (Ctrl+F5 or Cmd+Shift+R)

#### "Load More" button not working

**Possible causes:**

1. **JavaScript conflict** - Another plugin may be causing JavaScript errors. Check browser console (F12)
2. **AJAX not working** - Verify admin-ajax.php is accessible
3. **Cache issue** - Clear cache and try again

#### Getting Started Help

If you're still having trouble:

1. Check the plugin version - ensure you're running the latest version
2. Deactivate other plugins temporarily to check for conflicts
3. Switch to a default WordPress theme temporarily to check for theme conflicts
4. Check your browser's console (F12) for JavaScript errors
5. Contact your website administrator or developer for assistance

---

# Part 2: Developer Guide

## Architecture Overview

The BrightLocal Reviews plugin follows WordPress best practices and uses an object-oriented architecture with three primary class components.

### Component Structure

```
brightlocal-reviews.php (Main Plugin File)
    ├── BL_Reviews_Post_Type (Custom Post Type Management)
    ├── BL_Reviews_Admin (Admin Interface & Settings)
    └── BL_Reviews_Block (Block Registration & Rendering)
```

### Design Patterns

- **Singleton Pattern** - Each class is instantiated once via `plugins_loaded` hook
- **Separation of Concerns** - Admin, post type, and block logic are isolated
- **Server-Side Rendering** - Block rendering happens server-side for SEO and performance
- **Progressive Enhancement** - JavaScript enhances functionality but isn't required for basic display

### WordPress Integration Points

- Custom Post Type (`bl-reviews`)
- Custom Taxonomies (`bl_review_source`, `bl_review_label`)
- Gutenberg Block (`brightlocal-reviews/reviews`)
- Shortcode (`[brightlocal_reviews]`)
- REST API endpoints (via `show_in_rest`)
- WP-Cron (hourly review synchronization)
- AJAX endpoints (load more functionality)

---

## File Structure

```
brightlocal-reviews/
│
├── brightlocal-reviews.php          # Main plugin file, initialization
│
├── includes/                         # PHP classes
│   ├── class-bl-reviews-admin.php   # Admin UI, settings, import logic
│   ├── class-bl-reviews-block.php   # Block registration and rendering
│   └── class-bl-reviews-post-type.php # Custom post type registration
│
├── assets/                           # Static assets
│   ├── brightlocal.png              # Plugin icon
│   ├── css/
│   │   ├── admin.css                # Admin styles
│   │   └── reviews.css              # Legacy frontend styles
│   └── js/
│       └── admin.js                 # Admin JavaScript
│
├── src/                              # Source files for block (pre-build)
│   ├── block.json                   # Block metadata
│   ├── index.js                     # Block editor script
│   ├── render.php                   # Server-side block rendering
│   ├── style.scss                   # Frontend block styles
│   └── view.js                      # Frontend block interactivity
│
├── build/                            # Compiled block assets (generated)
│   ├── block.json
│   ├── index.js                     # Compiled editor script
│   ├── index.asset.php              # Asset dependencies
│   ├── render.php                   # Copied render file
│   ├── style-index.css              # Compiled frontend styles
│   ├── view.js                      # Compiled frontend script
│   └── images/
│
├── plugin-update-checker/            # GitHub auto-update library
│   └── [Yahnis Elsts' Plugin Update Checker]
│
├── package.json                      # npm dependencies and scripts
├── package-lock.json                 # npm lock file
└── README.md                         # GitHub readme
```

---

## Core Components

### 1. Main Plugin File (`brightlocal-reviews.php`)

**Purpose:** Bootstrap the plugin and define constants.

**Key Functions:**

- Plugin metadata (name, version, description, author)
- Constants definition (`BL_REVIEWS_VERSION`, `BL_REVIEWS_PLUGIN_DIR`, `BL_REVIEWS_PLUGIN_URL`)
- Class file includes
- Plugin initialization via `bl_reviews_init()`
- Activation/deactivation hooks
- GitHub auto-update integration
- Global AJAX handlers (`bl_reviews_load_more_ajax`)
- Frontend button styling injection (`bl_reviews_frontend_button_styles`)

**Initialization Flow:**

```php
add_action('plugins_loaded', 'bl_reviews_init');

function bl_reviews_init() {
    new BL_Reviews_Post_Type();
    new BL_Reviews_Admin();
    new BL_Reviews_Block();
}
```

**Activation Hook:**

```php
register_activation_hook(__FILE__, 'bl_reviews_activate');
function bl_reviews_activate() {
    flush_rewrite_rules(); // Regenerate permalinks
}
```

---

### 2. Post Type Class (`class-bl-reviews-post-type.php`)

**Purpose:** Register and manage the `bl-reviews` custom post type.

**Class:** `BL_Reviews_Post_Type`

**Key Responsibilities:**

- Register `bl-reviews` custom post type
- Register meta fields for REST API exposure
- Add custom admin columns (source, label, review score)
- Make columns sortable
- Render meta box with review details
- Disable Gutenberg editor for review posts (uses classic editor)

**Post Type Configuration:**

```php
register_post_type('bl-reviews', array(
    'public'        => true,
    'show_ui'       => true,
    'show_in_menu'  => 'brightlocal-reviews',
    'show_in_rest'  => true,
    'rest_base'     => 'bl-reviews',
    'supports'      => array('title', 'editor'),
    'rewrite'       => array('slug' => 'bl-reviews'),
));
```

**Meta Fields:**

- `_bl_rating` (number) - Star rating (1-5)
- `_bl_date` (string) - Review publication date
- `_bl_source` (string) - Review source (google, facebook, yelp, etc.)
- `_bl_source_id` (string) - External review ID
- `_bl_title` (string) - Optional review title
- `_bl_review_identifier` (string) - Unique hash for duplicate detection

**Admin Columns:**

```php
add_filter('manage_bl-reviews_posts_columns', array($this, 'add_review_score_column'));
```

Adds columns for:
- Source (Google, Facebook, etc.)
- Label (location/category)
- Review Score (star rating visualization)

---

### 3. Admin Class (`class-bl-reviews-admin.php`)

**Purpose:** Handle admin interface, settings, and review import.

**Class:** `BL_Reviews_Admin`

**Key Responsibilities:**

- Register admin menu and submenus
- Register settings (`bl_reviews_widgets`, `bl_reviews_button_settings`)
- Register taxonomies (`bl_review_source`, `bl_review_label`)
- Render settings pages (Widgets tab, Display tab)
- Handle widget validation
- AJAX handlers for:
  - Getting reviews from BrightLocal API
  - Deleting all reviews
  - Removing all widgets
  - Saving widgets
- Enqueue admin scripts and styles

**Settings Structure:**

```php
// Widget settings (array of widget configurations)
bl_reviews_widgets = array(
    array(
        'widget_id' => 'a1b2c3d4e5f6g7h8i9j0...',
        'label'     => 'Google Reviews'
    ),
    // ... more widgets
);

// Display settings (button appearance)
bl_reviews_button_settings = array(
    'bg_color'          => '#0073aa',
    'text_color'        => '#ffffff',
    'bg_color_hover'    => '#005177',
    'text_color_hover'  => '#ffffff',
    'radius_tl'         => 4,
    'radius_tr'         => 4,
    'radius_br'         => 4,
    'radius_bl'         => 4,
    'text_transform'    => 'none',
);
```

**Menu Structure:**

```
BrightLocal Reviews (parent menu)
├── Settings (default page - Widgets tab)
├── Reviews (edit.php?post_type=bl-reviews)
├── Review Sources (taxonomy: bl_review_source)
└── Review Labels (taxonomy: bl_review_label)
```

**Import Logic Flow:**

1. User clicks "Get Reviews" or "Update Reviews"
2. AJAX request to `bl_get_reviews`
3. For each widget:
   - Fetch JSON from BrightLocal API
   - Parse review data
   - Check for existing reviews by unique identifier
   - Insert new reviews or update existing ones
   - Set taxonomies (source, label)
4. Return success message with statistics

---

### 4. Block Class (`class-bl-reviews-block.php`)

**Purpose:** Register Gutenberg block and handle rendering.

**Class:** `BL_Reviews_Block`

**Key Responsibilities:**

- Register the `brightlocal-reviews/reviews` block
- Register and enqueue block assets (JS, CSS)
- Provide `render_block()` method for server-side rendering
- Register shortcode handler (`[brightlocal_reviews]`)
- Localize AJAX data for frontend scripts

**Block Registration:**

```php
register_block_type(
    BL_REVIEWS_PLUGIN_DIR . 'build',
    array(
        'editor_script' => 'brightlocal-reviews-editor',
        'editor_style'  => 'brightlocal-reviews-editor',
        'style'         => 'brightlocal-reviews-style',
        'script'        => 'brightlocal-reviews-view',
    )
);
```

**Rendering Process:**

1. Block attributes passed to `render.php`
2. Query `bl-reviews` posts with optional taxonomy filter
3. Sort by date (newest first)
4. Build HTML output with proper schema.org markup
5. Conditionally show "Load More" button
6. Return HTML string

**Shortcode Handler:**

Maps shortcode attributes to block attributes and reuses `render_block()` method:

```php
add_shortcode('brightlocal_reviews', array($this, 'shortcode_handler'));
```

---

## Development Setup

### Prerequisites

- Node.js 14+ and npm
- PHP 7.4+
- WordPress 5.9+
- Local WordPress development environment (Local, MAMP, or similar)

### Initial Setup

1. **Clone or download the plugin:**

```bash
cd /path/to/wordpress/wp-content/plugins/
git clone https://github.com/markfenske84/brightlocal-reviews.git
cd brightlocal-reviews
```

2. **Install npm dependencies:**

```bash
npm install
```

3. **Install PHP dependencies (optional):**

The Plugin Update Checker library is included. If using Composer:

```bash
composer install
```

4. **Start development mode:**

```bash
npm run start
```

This watches for file changes in `src/` and automatically rebuilds.

### Development Workflow

1. Edit source files in `src/` directory
2. Webpack automatically compiles to `build/`
3. Refresh WordPress admin/frontend to see changes
4. Use browser DevTools for debugging

---

## Custom Post Type & Taxonomies

### Custom Post Type: `bl-reviews`

**Purpose:** Store individual review data.

**Structure:**

- `post_title` - Reviewer name
- `post_content` - Review text/body
- `post_date` - Review publication date (synced from BrightLocal)
- `post_status` - Usually `publish`

**Meta Fields:**

| Meta Key | Type | Description |
|----------|------|-------------|
| `_bl_rating` | number | Star rating (1-5) |
| `_bl_date` | string | Original review date |
| `_bl_source` | string | Platform (google, facebook, etc.) |
| `_bl_source_id` | string | External review ID |
| `_bl_title` | string | Optional review title |
| `_bl_review_identifier` | string | MD5 hash for duplicate detection |

### Taxonomy: `bl_review_source`

**Purpose:** Categorize reviews by platform/source.

**Examples:**
- Google
- Facebook
- Yelp
- TripAdvisor

**Properties:**
- Hierarchical
- Shows in admin UI
- Not shown in REST API

### Taxonomy: `bl_review_label`

**Purpose:** Categorize reviews by location or custom label.

**Examples:**
- Downtown Location
- Uptown Location
- Google Reviews
- All Reviews

**Properties:**
- Hierarchical
- Shows in admin UI
- Shows in REST API (`rest_base: 'bl_review_label'`)

---

## Block Development

### Block Configuration (`src/block.json`)

```json
{
    "apiVersion": 2,
    "name": "brightlocal-reviews/reviews",
    "title": "BrightLocal Reviews",
    "category": "widgets",
    "icon": "star-filled",
    "attributes": {
        "displayType": { "type": "string", "default": "grid" },
        "showAuthor": { "type": "boolean", "default": true },
        "showDate": { "type": "boolean", "default": true },
        "showSource": { "type": "boolean", "default": true },
        "reviewLabel": { "type": "string", "default": "all" },
        "limitItems": { "type": "boolean", "default": false },
        "itemsPerPage": { "type": "number", "default": 3 },
        "showArrows": { "type": "boolean", "default": true }
    },
    "render": "file:./render.php"
}
```

### Editor Script (`src/index.js`)

The editor script handles:
- Block registration
- Inspector controls (settings sidebar)
- Block preview in editor
- Fetching labels from REST API

**Key WordPress Dependencies:**

```javascript
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
```

**Fetching Labels:**

```javascript
useEffect(() => {
    wp.apiFetch({ path: '/wp/v2/bl_review_label' })
        .then((labels) => setLabels(labels));
}, []);
```

### Frontend Script (`src/view.js`)

Handles client-side interactivity:

- "Read More" toggle for truncated reviews
- "Load More" button AJAX pagination
- Carousel/slider navigation (if implemented)

**AJAX Load More Example:**

```javascript
jQuery('.bl-reviews-load-more').on('click', function() {
    const offset = $(this).data('offset');
    const perPage = $(this).data('per-page');
    const label = $(this).data('label');
    
    $.ajax({
        url: blReviews.ajax_url,
        type: 'POST',
        data: {
            action: 'bl_load_more_reviews',
            nonce: blReviews.nonce,
            offset: offset,
            per_page: perPage,
            label: label
        },
        success: function(response) {
            $('.bl-reviews-wrapper').append(response);
            // Update offset for next load
        }
    });
});
```

### Block Rendering (`src/render.php`)

Server-side PHP template that:

1. Queries `bl-reviews` posts
2. Applies filters (label, limit)
3. Sorts by date
4. Outputs HTML with schema.org markup
5. Conditionally renders "Load More" button

**Query Example:**

```php
$args = array(
    'post_type'      => 'bl-reviews',
    'posts_per_page' => $limit_items ? $items_per_page : -1,
    'post_status'    => 'publish'
);

if ($reviewLabel !== 'all') {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'bl_review_label',
            'field'    => is_numeric($reviewLabel) ? 'term_id' : 'slug',
            'terms'    => $reviewLabel,
        )
    );
}

$reviews_query = new WP_Query($args);
```

---

## API Integration

### BrightLocal API

**Endpoint:** `https://www.local-marketing-reports.com/external/showcase-reviews/widgets/{WIDGET_ID}`

**Method:** GET

**Authentication:** None (widget ID acts as authentication)

**Response Format:** JSON

**Example Response:**

```json
{
    "results": [
        {
            "author": "John Doe",
            "reviewBody": "Great service! Highly recommend.",
            "ratingValue": 5,
            "datePublished": "2025-01-15",
            "source": "google",
            "sourceId": "abc123xyz",
            "reviewTitle": "Excellent Experience"
        }
    ]
}
```

### Import Process

**File:** `includes/class-bl-reviews-admin.php`

**Method:** `ajax_get_reviews()`

**Flow:**

1. Validate nonce and permissions
2. Get widget configurations from options
3. Loop through each widget:
   - Construct API URL
   - Fetch JSON via `wp_remote_get()`
   - Decode JSON response
   - Loop through reviews:
     - Generate unique identifier (MD5 hash)
     - Check if review exists
     - Insert or update post
     - Update post meta
     - Set taxonomies
4. Return success/error message

**Duplicate Detection:**

```php
$review_identifier = md5(
    $widget_id . 
    $review['source'] . 
    $review['sourceId'] . 
    $review['author'] . 
    $review['datePublished']
);
```

This ensures the same review isn't imported multiple times.

---

## AJAX Handlers

### 1. Load More Reviews (`bl_load_more_reviews`)

**Purpose:** Fetch additional reviews for pagination.

**Handler:** `bl_reviews_load_more_ajax()` in `brightlocal-reviews.php`

**Parameters:**
- `per_page` (int) - Reviews per page
- `offset` (int) - Current offset
- `label` (string) - Label filter

**Response:** HTML of review items

**Available to:** Logged-in and non-logged-in users

```php
add_action('wp_ajax_bl_load_more_reviews', 'bl_reviews_load_more_ajax');
add_action('wp_ajax_nopriv_bl_load_more_reviews', 'bl_reviews_load_more_ajax');
```

### 2. Get Reviews (`bl_get_reviews`)

**Purpose:** Manually trigger review import.

**Handler:** `ajax_get_reviews()` in `class-bl-reviews-admin.php`

**Parameters:**
- `widgets` (JSON, optional) - Override saved widgets

**Response:** Success message with statistics

**Available to:** Administrators only

### 3. Delete All Reviews (`bl_delete_all_reviews`)

**Purpose:** Delete all review posts and taxonomy terms.

**Handler:** `ajax_delete_all_reviews()` in `class-bl-reviews-admin.php`

**Response:** Success message with count

**Available to:** Administrators only

### 4. Remove All Widgets (`bl_remove_all_widgets`)

**Purpose:** Clear all widget configurations.

**Handler:** `ajax_remove_all_widgets()` in `class-bl-reviews-admin.php`

**Response:** Success message

**Available to:** Administrators only

### 5. Save Widgets (`bl_save_widgets`)

**Purpose:** Save widget configurations via AJAX.

**Handler:** `ajax_save_widgets()` in `class-bl-reviews-admin.php`

**Parameters:**
- `widgets` (JSON) - Widget configurations

**Response:** Validated widgets array

**Available to:** Administrators only

---

## Hooks & Filters

### Actions

| Hook | Description | File |
|------|-------------|------|
| `plugins_loaded` | Initialize plugin classes | `brightlocal-reviews.php` |
| `init` | Register post types, taxonomies, blocks | All classes |
| `admin_menu` | Register admin pages | `class-bl-reviews-admin.php` |
| `admin_init` | Register settings | `class-bl-reviews-admin.php` |
| `admin_enqueue_scripts` | Enqueue admin assets | `class-bl-reviews-admin.php`, `class-bl-reviews-post-type.php` |
| `add_meta_boxes` | Add meta boxes | `class-bl-reviews-post-type.php` |
| `save_post` | Save meta box data | `class-bl-reviews-post-type.php` |
| `wp_head` | Inject frontend button styles | `brightlocal-reviews.php` |
| `admin_head` | Inject admin menu icon styles | `brightlocal-reviews.php` |

### Filters

| Filter | Description | File |
|--------|-------------|------|
| `manage_bl-reviews_posts_columns` | Add custom admin columns | `class-bl-reviews-post-type.php` |
| `manage_edit-bl-reviews_sortable_columns` | Make columns sortable | `class-bl-reviews-post-type.php` |
| `use_block_editor_for_post_type` | Disable Gutenberg for reviews | `class-bl-reviews-post-type.php` |
| `parent_file` | Set active parent menu | `class-bl-reviews-admin.php` |
| `submenu_file` | Set active submenu | `class-bl-reviews-admin.php` |
| `plugin_action_links_{basename}` | Add settings link to plugins page | `class-bl-reviews-admin.php` |
| `posts_clauses` | Modify SQL for taxonomy sorting | `class-bl-reviews-post-type.php` |

### Custom Hooks for Extension

**Action: `bl_reviews_after_import`**

Fired after reviews are imported (not currently implemented, but can be added).

**Example:**

```php
do_action('bl_reviews_after_import', $total_created, $total_updated);
```

**Filter: `bl_reviews_source_icons`**

Filter the source icons array (not currently implemented, but can be added).

**Example:**

```php
$source_icons = apply_filters('bl_reviews_source_icons', $source_icons);
```

---

## Database Schema

### Posts Table (`wp_posts`)

Review posts are stored as custom post type `bl-reviews`:

| Column | Usage |
|--------|-------|
| `ID` | Unique review ID |
| `post_title` | Reviewer name |
| `post_content` | Review text |
| `post_date` | Publication date (synced from BrightLocal) |
| `post_status` | Usually `publish` |
| `post_type` | Always `bl-reviews` |

### Post Meta Table (`wp_postmeta`)

| Meta Key | Value Type | Description |
|----------|------------|-------------|
| `_bl_rating` | integer | Star rating (1-5) |
| `_bl_date` | string | Original review date |
| `_bl_source` | string | Platform (google, facebook, etc.) |
| `_bl_source_id` | string | External review ID |
| `_bl_title` | string | Optional review title |
| `_bl_review_identifier` | string | Unique MD5 hash |

### Term Relationships (`wp_term_relationships`)

Links reviews to taxonomies:

- `bl_review_source` - Review platform
- `bl_review_label` - Custom location/category label

### Options Table (`wp_options`)

| Option Name | Type | Description |
|-------------|------|-------------|
| `bl_reviews_widgets` | array | Widget configurations |
| `bl_reviews_button_settings` | array | Button appearance settings |

---

## REST API Endpoints

The plugin exposes REST API endpoints for programmatic access.

### Get All Reviews

**Endpoint:** `/wp-json/wp/v2/bl-reviews`

**Method:** GET

**Parameters:**
- `per_page` - Reviews per page
- `page` - Page number
- `bl_review_label` - Filter by label ID
- `orderby` - Sort field
- `order` - Sort direction (asc/desc)

**Response:**

```json
[
    {
        "id": 123,
        "title": { "rendered": "John Doe" },
        "content": { "rendered": "<p>Great service!</p>" },
        "meta": {
            "_bl_rating": 5,
            "_bl_date": "2025-01-15",
            "_bl_source": "google"
        },
        "bl_review_label": [1, 3]
    }
]
```

### Get Review Labels

**Endpoint:** `/wp-json/wp/v2/bl_review_label`

**Method:** GET

**Response:**

```json
[
    {
        "id": 1,
        "name": "Google Reviews",
        "slug": "google-reviews",
        "count": 15
    }
]
```

### Authentication

Most endpoints require authentication for write operations. Read operations are publicly accessible.

---

## Styling Architecture

### CSS Organization

**Admin Styles** (`assets/css/admin.css`)
- Settings page styles
- Meta box styles
- Admin column styles
- Color picker styles
- Widget repeater styles

**Frontend Styles** (`src/style.scss` → `build/style-index.css`)
- Block wrapper styles
- Grid/list/carousel layouts
- Review item cards
- Star rating display
- Button styles (base)
- Responsive breakpoints

### CSS Class Naming

**BEM-style naming convention:**

```css
.bl-reviews-wrapper           /* Block container */
.bl-reviews-grid              /* Grid layout modifier */
.bl-reviews-list              /* List layout modifier */
.bl-reviews-carousel          /* Carousel layout modifier */

.bl-review-item               /* Individual review card */
.bl-review-header             /* Card header (rating + meta) */
.bl-review-rating             /* Star rating container */
.bl-review-meta               /* Meta info (date + source) */
.bl-review-author             /* Author name */
.bl-review-content            /* Review text */
.bl-review-content-truncated  /* Truncated text modifier */
.bl-review-read-more          /* Read more button */

.bl-reviews-load-more         /* Load more button */

.star                         /* Individual star */
.star.filled                  /* Filled star modifier */
```

### Responsive Design

**Breakpoints:**

```scss
@media (max-width: 768px) {
    .bl-reviews-grid {
        grid-template-columns: 1fr; // Single column on mobile
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .bl-reviews-grid {
        grid-template-columns: repeat(2, 1fr); // Two columns on tablet
    }
}

@media (min-width: 1025px) {
    .bl-reviews-grid {
        grid-template-columns: repeat(3, 1fr); // Three columns on desktop
    }
}
```

### Dynamic Styles

Button styles are injected dynamically from settings:

```php
function bl_reviews_frontend_button_styles() {
    $settings = get_option('bl_reviews_button_settings');
    echo '<style type="text/css">';
    echo '.bl-review-read-more, .bl-reviews-load-more {';
    echo 'background-color:' . esc_attr($settings['bg_color']) . ';';
    echo 'color:' . esc_attr($settings['text_color']) . ';';
    // ... more styles
    echo '}';
    echo '</style>';
}
add_action('wp_head', 'bl_reviews_frontend_button_styles', 20);
```

---

## JavaScript Architecture

### Admin JavaScript (`assets/js/admin.js`)

**Purpose:** Handle settings page interactivity.

**Features:**
- Widget repeater (add/remove rows)
- Get/Update Reviews button
- Delete All Reviews button
- Remove All Widgets button
- AJAX request handling
- Confirmation dialogs

**Example - Add Widget Row:**

```javascript
jQuery('#add-widget').on('click', function() {
    const index = $('.widget-row').length;
    const newRow = `
        <tr class="widget-row">
            <td><input type="text" name="bl_reviews_widgets[${index}][widget_id]" /></td>
            <td><input type="text" name="bl_reviews_widgets[${index}][label]" /></td>
            <td><button type="button" class="button remove-widget">Remove</button></td>
        </tr>
    `;
    $('#bl-widgets-table tbody').append(newRow);
});
```

### Frontend JavaScript (`src/view.js`)

**Purpose:** Handle block interactivity on the frontend.

**Features:**
- Read More/Less toggle
- Load More AJAX pagination
- Carousel navigation (if implemented)

**Example - Read More Toggle:**

```javascript
document.querySelectorAll('.bl-review-read-more').forEach(button => {
    button.addEventListener('click', function() {
        const content = this.previousElementSibling;
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        
        content.classList.toggle('bl-review-content-truncated');
        this.setAttribute('aria-expanded', !isExpanded);
        this.textContent = isExpanded ? 'Read More' : 'Read Less';
    });
});
```

**Example - Load More:**

```javascript
jQuery('.bl-reviews-load-more').on('click', function() {
    const button = jQuery(this);
    const offset = parseInt(button.data('offset'));
    const perPage = parseInt(button.data('per-page'));
    const label = button.data('label');
    
    button.prop('disabled', true).text('Loading...');
    
    jQuery.ajax({
        url: blReviews.ajax_url,
        type: 'POST',
        data: {
            action: 'bl_load_more_reviews',
            nonce: blReviews.nonce,
            offset: offset,
            per_page: perPage,
            label: label
        },
        success: function(response) {
            if (response) {
                jQuery('.bl-reviews-wrapper').append(response);
                button.data('offset', offset + perPage);
                button.prop('disabled', false).text('Load More');
            } else {
                button.hide(); // No more reviews
            }
        },
        error: function() {
            button.prop('disabled', false).text('Load More');
            alert('Error loading reviews');
        }
    });
});
```

---

## Auto-Update System

### Plugin Update Checker

The plugin uses [Yahnis Elsts' Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) library for automatic updates from GitHub.

**Configuration** (`brightlocal-reviews.php`):

```php
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$bl_reviews_update_checker = PucFactory::buildUpdateChecker(
    'https://github.com/markfenske84/brightlocal-reviews/',
    __FILE__,
    'brightlocal-reviews'
);
$bl_reviews_update_checker->setBranch('main');
$bl_reviews_update_checker->getVcsApi()->enableReleaseAssets();
```

### How It Works

1. Plugin checks GitHub repository for new releases
2. Compares version numbers
3. Shows update notification in WordPress admin
4. Downloads and installs update when user clicks "Update"

### Creating a Release

1. Update version number in `brightlocal-reviews.php` header
2. Update `BL_REVIEWS_VERSION` constant
3. Commit and push changes
4. Create a new release on GitHub with a version tag (e.g., `v1.2.1`)
5. Upload plugin ZIP file as a release asset
6. Plugin installations will detect the update automatically

### Manual Update Check

Force an update check by visiting:
```
/wp-admin/plugins.php?bl_check_updates=1
```

---

## Extending the Plugin

### Add Custom Review Sources

**Filter the source icons array:**

```php
add_filter('bl_reviews_source_icons', 'my_custom_source_icons');

function my_custom_source_icons($icons) {
    $icons['customplatform'] = 'https://example.com/custom-icon.png';
    return $icons;
}
```

*Note: This filter needs to be added to the plugin code first.*

### Add Custom Display Layouts

1. **Add new display type to block attributes** (`src/block.json`):

```json
{
    "displayType": {
        "type": "string",
        "default": "grid",
        "enum": ["grid", "list", "carousel", "masonry"]
    }
}
```

2. **Add layout option to editor** (`src/index.js`):

```javascript
<SelectControl
    label="Display Type"
    value={displayType}
    options={[
        { label: 'Grid', value: 'grid' },
        { label: 'List', value: 'list' },
        { label: 'Carousel', value: 'carousel' },
        { label: 'Masonry', value: 'masonry' }
    ]}
    onChange={(value) => setAttributes({ displayType: value })}
/>
```

3. **Add CSS for new layout** (`src/style.scss`):

```scss
.bl-reviews-masonry {
    column-count: 3;
    column-gap: 20px;
    
    .bl-review-item {
        break-inside: avoid;
        margin-bottom: 20px;
    }
}
```

### Hook into Review Import

Add custom processing after reviews are imported:

```php
add_action('update_option_bl_reviews_widgets', 'my_post_import_processing', 20, 3);

function my_post_import_processing($old_value, $new_value, $option) {
    // Send notification email
    // Update external system
    // Generate report
}
```

### Add Custom Review Meta Fields

1. **Register the meta field:**

```php
register_post_meta('bl-reviews', '_bl_custom_field', array(
    'type'         => 'string',
    'single'       => true,
    'show_in_rest' => true,
));
```

2. **Save during import** (modify `ajax_get_reviews()`):

```php
update_post_meta($post_id, '_bl_custom_field', $custom_value);
```

3. **Display in meta box** (modify `render_meta_box()`):

```php
$custom_field = get_post_meta($post->ID, '_bl_custom_field', true);
echo '<p>' . esc_html($custom_field) . '</p>';
```

---

## Testing & Debugging

### Debug Mode

Enable WordPress debug mode in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Logs will be written to `/wp-content/debug.log`.

### Logging Review Imports

Add debugging to import process:

```php
public function ajax_get_reviews() {
    error_log('BL Reviews: Starting import');
    error_log('Widgets: ' . print_r($widgets, true));
    
    // ... import code ...
    
    error_log('BL Reviews: Imported ' . $total_created . ' reviews');
}
```

### Browser Console

Check JavaScript errors in browser console (F12):

```javascript
console.log('BrightLocal Reviews loaded');
console.log('AJAX URL:', blReviews.ajax_url);
```

### Testing AJAX Endpoints

Use browser DevTools Network tab or tools like Postman:

```bash
POST /wp-admin/admin-ajax.php
Content-Type: application/x-www-form-urlencoded

action=bl_load_more_reviews&nonce=abc123&offset=9&per_page=3&label=all
```

### Testing Block Rendering

Preview block in different contexts:

1. Block Editor (live preview)
2. Frontend (actual rendering)
3. REST API (`/wp-json/wp/v2/bl-reviews`)
4. Shortcode (various attributes)

### Common Issues & Solutions

**Issue:** Reviews not importing

**Debug:**
```php
$response = wp_remote_get($widget_url);
error_log('API Response: ' . print_r($response, true));
```

**Issue:** Block not appearing

**Debug:**
```bash
npm run build
# Check build/index.js exists
# Check console for errors
```

**Issue:** Styles not applying

**Debug:**
```bash
# Clear all caches
# Hard refresh browser (Ctrl+F5)
# Check compiled CSS in build/style-index.css
```

---

## Building for Production

### Build Process

1. **Update version numbers:**
   - `brightlocal-reviews.php` header
   - `BL_REVIEWS_VERSION` constant
   - `package.json` version (optional)

2. **Build assets:**

```bash
npm run build
```

This compiles:
- `src/index.js` → `build/index.js`
- `src/view.js` → `build/view.js`
- `src/style.scss` → `build/style-index.css`
- Copies `src/render.php` → `build/render.php`
- Copies `src/block.json` → `build/block.json`
- Generates `build/index.asset.php` with dependencies

3. **Test thoroughly:**
   - Fresh WordPress install
   - Different themes
   - Different PHP versions
   - Block variations
   - Shortcode variations

4. **Create plugin ZIP:**

```bash
cd wp-content/plugins/
zip -r brightlocal-reviews.zip brightlocal-reviews/ \
    -x "*node_modules/*" \
    -x "*.git/*" \
    -x "*src/*" \
    -x "*.scss" \
    -x "package*.json" \
    -x "*.map"
```

**Or create a distribution script:**

```bash
#!/bin/bash
# build-release.sh

# Build assets
npm run build

# Create temp directory
mkdir -p dist/brightlocal-reviews

# Copy plugin files (excluding dev files)
rsync -av \
    --exclude 'node_modules' \
    --exclude '.git' \
    --exclude 'src' \
    --exclude 'package*.json' \
    --exclude '*.map' \
    --exclude 'dist' \
    ./ dist/brightlocal-reviews/

# Create ZIP
cd dist
zip -r brightlocal-reviews.zip brightlocal-reviews/
cd ..

echo "Release created: dist/brightlocal-reviews.zip"
```

5. **Upload to GitHub:**

```bash
git tag v1.2.1
git push origin v1.2.1
```

6. **Create GitHub Release:**
   - Go to GitHub repository
   - Click "Releases" → "Create a new release"
   - Choose tag `v1.2.1`
   - Upload `brightlocal-reviews.zip` as an asset
   - Write release notes
   - Publish release

---

## Contributing Guidelines

### Code Standards

**PHP:**
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Use tabs for indentation
- Document functions with PHPDoc blocks
- Escape output (`esc_html()`, `esc_attr()`, `esc_url()`)
- Sanitize input (`sanitize_text_field()`, `absint()`)
- Validate and check nonces for forms/AJAX

**JavaScript:**
- Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Use ES6+ syntax
- Document functions with JSDoc blocks
- Use jQuery only when necessary (prefer vanilla JS)

**CSS:**
- Use BEM naming convention
- Mobile-first responsive design
- Avoid `!important` unless absolutely necessary

### Git Workflow

1. **Fork the repository**
2. **Create a feature branch:**

```bash
git checkout -b feature/my-new-feature
```

3. **Make changes and commit:**

```bash
git add .
git commit -m "Add new feature: description"
```

4. **Push to your fork:**

```bash
git push origin feature/my-new-feature
```

5. **Create a Pull Request** on GitHub

### Pull Request Guidelines

- Provide clear description of changes
- Reference any related issues
- Include screenshots for UI changes
- Ensure code is tested
- Update documentation if needed

### Reporting Issues

When reporting bugs, include:

- WordPress version
- PHP version
- Plugin version
- Theme name
- Other active plugins
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots/error messages

---

## Additional Resources

### WordPress Developer Resources

- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [REST API Handbook](https://developer.wordpress.org/rest-api/)
- [Coding Standards](https://developer.wordpress.org/coding-standards/)

### BrightLocal Resources

- [BrightLocal API Documentation](https://www.brightlocal.com/api/)
- [Showcase Review Widgets](https://help.brightlocal.com/hc/en-us/articles/360013528499)

### Tools

- [WordPress CLI](https://wp-cli.org/)
- [Query Monitor Plugin](https://wordpress.org/plugins/query-monitor/)
- [Debug Bar Plugin](https://wordpress.org/plugins/debug-bar/)

---

## Getting More Help

### For Users

**BrightLocal Support:**
- Visit [BrightLocal Help Center](https://help.brightlocal.com)
- Contact BrightLocal support

**WordPress Support:**
- Visit [WordPress Support Forums](https://wordpress.org/support/)
- Consult your hosting provider's support

### For Developers

**Development Questions:**
- GitHub Issues: [https://github.com/markfenske84/brightlocal-reviews/issues](https://github.com/markfenske84/brightlocal-reviews/issues)
- GitHub Repo: [https://github.com/markfenske84/brightlocal-reviews](https://github.com/markfenske84/brightlocal-reviews)

---

## Changelog

### Version 1.2.1
- Added button customization options (colors, border radius, text transform)
- Added Display settings tab
- Improved shortcode documentation
- Fixed admin menu icon sizing

### Version 1.1.6
- Initial public release
- Custom post type for reviews
- Gutenberg block with multiple display modes
- Shortcode support
- Auto-import from BrightLocal
- Taxonomy filters

---

## License

This plugin is licensed under GPL-2.0-or-later, the same license as WordPress itself.

---

## Final Notes

This comprehensive documentation covers everything from basic installation and usage for non-technical users to advanced development topics for developers who want to extend or contribute to the plugin.

**For Users:** Start with Part 1 (sections 1-12) to learn how to install, configure, and use the plugin on your website.

**For Developers:** Part 2 (sections 13-31) provides detailed technical information about the plugin's architecture, development setup, and extension points.

**Happy Building! 🚀**

