# Changelog

All notable changes to the **phpBB Directory** extension will be documented in this file.

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