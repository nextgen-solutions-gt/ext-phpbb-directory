# Changelog

All notable changes to the **phpBB Directory** extension will be documented in this file.

## [2.0.0-RC7] - 2026-02-01

### Security & Validation (CDB Standards)
- **SQL Hardening**: Implemented explicit integer casting `(int)` for all database IDs (`link_id`, `cat_id`, `user_id`) in controllers and core logic to prevent SQL injection.
- **XSS Prevention**: Standardized JavaScript variable escaping in templates using the `|e('js')` Twig filter and proper `U_` prefixing.
- **Database Portability**: Replaced MySQL-specific `TRUNCATE` commands with standard SQL `DELETE` to ensure compatibility with SQLite, PostgreSQL, and Oracle.
- **Safe SQL Building**: Migrated manual `UPDATE` queries to the native `sql_build_array()` method to handle data sanitization automatically.
- **Privacy Compliance**: Removed automated emails to guest users without explicit consent to align with GDPR and anti-spam guidelines.

### Architecture & Refactoring
- **Constructor Optimization**: Refactored all controllers and core classes to follow Symfony best practices, moving heavy logic and language loading out of constructors.
- **Routing System**: Fixed fatal errors by replacing the non-existent `helper->url()` method with the standardized `helper->route()` system.
- **Code Separation**: Decoupled JavaScript logic from PHP controllers; moved `onclick` and `window.open` handlers from backend code to frontend templates.
- **Vendor Cleanup**: Completed the transition from legacy vendor namespaces to `nextgen`, updating routing keys and language file paths.

### Fixed
- **AJAX Protocol Mismatch**: Improved session persistence by adding `credentials: include` to fetch calls, resolving login issues in mixed HTTP/HTTPS environments.
- **Path Resolution**: Fixed incorrect route names in the search and comments controllers that caused 404 errors during pagination.
- **Resource Management**: Added mandatory `$messenger->save()` calls to properly close mail handles and free system memory.

### UI & Icon System
- **Search Layout Optimization**: Refactored `search_results.html` to separate data from presentation, moving thumbnail generation from PHP to Twig.
- **Visual Fixes**: Resolved a syntax error in the search controller that caused malformed HTML tags to appear as plain text ("/>") in search results.
- **Responsive Profiles**: Implemented `min-height` and `overflow` controls on search result profiles to prevent layout breakage with large site captures.

## [2.0.0-RC5] - 2026-01-26

### UI & Icon System
- **Native Integration**: Standardized the icon system to use phpBB's native Font Awesome 4.7 library. This eliminates conflicts with other extensions and removes the need for external CDNs.
- **Responsive Category Headers**: Added CSS media queries to dynamically resize category icons on mobile devices (downscaling to `2em` for better readability).
- **Enhanced Visuals**: Increased category icon size to `fa-4x` in the main directory listing to provide a more modern and prominent look.
- **Color System**: Implemented `CAT_ICON_COLOR` block variable support in the core `categorie.php` to ensure category colors are correctly rendered across all directory listings.

### ACP (Admin Control Panel)
- **Improved Icon Logic**: Updated the icon previewer and JavaScript logic to support Font Awesome 4.7 syntax (standardizing on the `fa` prefix).
- **UX Improvements**: Integrated a modern Color Picker with real-time preview and hexadecimal input synchronization for category icons.
- **Language Updates**: Updated `DIR_CAT_FA_ICON_EXPLAIN` in English with accurate Font Awesome 4.7 examples and official documentation links.

### Fixed
- **Empty Style Attributes**: Resolved an issue where icons would render `style="color: ;"` when no color was defined in the database.
- **Icon Invisibility**: Fixed prefix mismatch bugs where icons failed to render due to modern `fas` or `fab` class usage.
- **Legacy Image Support**: Cleaned up template logic by removing unused `CAT_IMAGE` fallbacks in favor of the new icon system.

---

## [2.0.0-RC4] - 2026-01-25

### Added
- **Local Banner Storage**: Banners are now downloaded and stored locally in `images/directory/banners/` to prevent broken links and improve privacy.
- **Dynamic ACP Dimensions**: Added settings in the Admin Control Panel to define banner width and height globally.
- **Smart Sizing Logic**: Banners now support "0" as a value in ACP settings, allowing for "auto" or original image proportions.
- **AJAX Comments**: Integrated AJAX-based comment system for better performance and UX, replacing legacy pop-up windows.
- **Version Checker**: Added a dynamic version verification system in the ACP that checks against the GitHub repository.

### Fixed
- **PHP Warnings**: Resolved "Undefined variable" and "Null array offset" errors in the ACP statistics controller.
- **Dynamic Paths**: Fixed 404 errors on banners by implementing `{ROOT_PATH}` in templates, ensuring compatibility with subdomains and subdirectories.
- **Download Failures**: Implemented a "Ninja" download system using cURL with custom User-Agents to bypass restrictions (Telegram/Cloudflare).
- **Security Validation**: Added strict image validation (MIME type check) before saving files.

### Changed
- **ACP Performance**: Optimized statistics calculation and added an orphan file cleaner.
- **Updated Dependencies**: Minimum PHP version requirement updated to 7.1.
- **Project Structure**: Cleaned up the controller and core logic for better PSR-4 compliance.

### Security
- Added `index.htm` protection to the local banners directory.
- Implemented `basename()` filtering to prevent directory traversal attacks.