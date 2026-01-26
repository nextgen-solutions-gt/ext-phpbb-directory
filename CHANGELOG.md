# Changelog

All notable changes to the **phpBB Directory** extension will be documented in this file.

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