=== Rental master ===
Contributors: aboutrentals
Tags: rentals, property management, listings, maps, ajax search
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.3.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Rental master helps you publish rental listings with filters, AJAX search, and Google Maps.

== Description ==
Rental master is a property listing plugin focused on rental inventory.

Core features:
- Rental listings custom post type
- Taxonomy filters (location, property type, rental category)
- Fast AJAX search
- Map-based listing view with markers
- Sidebar filters and shortcode-driven frontend rendering
- Admin settings for search, maps, and UI behavior

Available shortcodes:
- `[rental_listings]`
- `[rental_search]`
- `[rental_map]`
- `[rental_sidebar_filters]`

Developer extensibility:
- Custom actions and filters for shortcode attributes, query args, template loading, and rendering hooks

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Rental master -> Map Settings** and configure your Google Maps API key.
4. Add shortcodes to pages where you want listings and search.

== Frequently Asked Questions ==
= Does the plugin support theme template overrides? =
Yes. Copy shortcode templates to your theme under `rental-master/` and customize.

= Is Google Maps required? =
Only for `[rental_map]`. Listing and search shortcodes work without Maps.

= Does it support caching? =
Yes. The plugin caches filter terms, map markers, and AJAX search responses.

== Changelog ==
= 1.3.1 =
- Modernized plugin architecture with class-based modules.
- Added `rental_listing` CPT and rental taxonomies.
- Added modern shortcode system and template loading.
- Added nonce-protected fast AJAX search.
- Added Google Maps listing markers and responsive map panel.
- Added performance optimizations: query tuning, transient caching, lazy loading, and conditional asset loading.
- Added WordPress.org release preparation updates.

== Upgrade Notice ==
= 1.3.1 =
Major modernization release with improved architecture, frontend rendering, search speed, and map integration.
