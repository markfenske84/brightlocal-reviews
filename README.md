# BrightLocal Reviews

Display customer feedback collected by the BrightLocal **Showcase Review** widget anywhere on your WordPress site.

---

## Table of contents
1.  Features
2.  Requirements
3.  Installation
4.  Configuration – connecting your BrightLocal widget(s)
5.  Displaying reviews in the Block Editor
6.  Updating the reviews that are stored locally
7.  Development / contributing
8.  License

---

## 1.  Features
• Imports reviews for one or more BrightLocal *Showcase Review* widgets and stores them as a custom post type (`bl-reviews`).  
• Provides a **BrightLocal Reviews** block for the WordPress Block Editor (Gutenberg).  
• Grid, list, or slider presentation with star rating, author, date and source-favicon.  
• Filter the output by a *Label* that you define for every widget.  
• Optional "Load more" / infinite-scroll that fetches additional reviews over AJAX.  
• Reviews can be edited like regular posts, assigned labels, and are available through the WP REST API.

## 2.  Requirements
• WordPress 5.9 or later (Block Editor enabled).  
• PHP 7.4 or later.

## 3.  Installation
1.  Download the latest release ZIP from the [GitHub releases page](https://github.com/markfenske84/brightlocal-reviews/releases) **or** clone the repository into `wp-content/plugins/brightlocal-reviews`.
2.  In the WordPress admin go to **Plugins → Installed Plugins** and activate **BrightLocal Reviews**.
3.  (Developers) run `composer install` to pull in the update-checker dependency and `npm install && npm run build` to compile the block assets.

## 4.  Configuration – connecting your BrightLocal widget(s)
1.  In the dashboard open **BrightLocal Reviews → Settings**.
2.  Click **Add Row** and enter:
    • **Widget ID** – the 40-character hexadecimal ID that appears in the BrightLocal *Showcase Review* embed code.  
    • **Label** – any human-readable name that helps you identify this widget (e.g. *Google*, *Facebook*, *Downtown Location*…).  This label is also what you will select inside the block.
3.  Press **Save & Import**.  The plugin contacts BrightLocal's API, downloads all current reviews for every widget you listed and saves them as `bl-reviews` posts.
4.  A WP-Cron task subsequently checks for new reviews every hour and keeps everything in sync.

> **Tip:** You can manage *Review Sources* and *Review Labels* from the same menu if you need to tidy-up taxonomy terms later.

## 5.  Displaying reviews in the Block Editor
Add the **BrightLocal Reviews** block to any page, post or template.

Block settings (right-hand sidebar):

| Setting | Description |
|---------|-------------|
| **Display type** | `grid` (default) or `list`. |
| **Label** | Choose one of the labels you created in the settings screen or **All**. |
| **Limit items / Items per page** | Show only the N most recent reviews and append a *Load more* button that fetches the next batch asynchronously. |
| **Show author / date / source** | Toggle individual metadata elements. |
| **Show arrows** | When enabled in *list* mode the block becomes a basic slider/carousel controlled by previous/next arrows. |

The front-end markup is schema.org compliant (`Review` + `AggregateRating`) and inherits its typography & colours from your theme.

### Rendering in PHP templates
If you are building a classic-theme template you can still use the block:

```php
// Render the block with its default attributes.
echo do_blocks( '<!-- wp:brightlocal-reviews/reviews /-->' );

// …or override attributes in JSON:
echo do_blocks( '<!-- wp:brightlocal-reviews/reviews {"reviewLabel":"google","limitItems":true,"itemsPerPage":5} /-->' );
```

## 6.  Updating the reviews that are stored locally
Reviews are refreshed automatically every hour (WP-Cron). You may also trigger a manual re-import from **BrightLocal Reviews → Settings**.

## 7.  Development / contributing
1.  `composer install` – installs the [Yahnis Elsts Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker).  
2.  `npm install` – pulls down the JavaScript toolchain.  
3.  `npm run start` – watches files and rebuilds the block during development.  
4.  `npm run build` – produces a production build.

Pull requests are welcome! Please open an issue first to discuss what you would like to change.

## 8.  License
GPL-2.0-or-later – the same license that ships with WordPress itself. 