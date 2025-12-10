# Observer Mode for WordPress
*A read-only administrative role with full backend visibility and zero write permissions.*

## Overview
Observer Mode introduces a special WordPress role, **Observer Admin**, created for scenarios where users need complete visibility into the WordPress admin area without being able to modify anything.  

This role is ideal for:

- Client or stakeholder review access  
- Demo or presentation environments  
- Internal team members who need visibility but not editing authority  
- Training and education environments  
- Security-sensitive setups where write access must be restricted  

Observer Admin users can open Gutenberg, Elementor, and all admin screens, but **cannot save, delete, update, or create any content**.

---

## Key Features

### Full Backend Visibility
Observer Admin users can view:

- Posts, Pages, and Custom Post Types  
- Gutenberg editor  
- Elementor editor interfaces  
- Templates and theme structures (read-only)  
- Dashboard and all wp-admin menus  
- User lists and profile pages  
- Tools and settings screens (read-only)  

### Complete Write Protection
Observer Admin users **cannot**:

- Save or update posts, pages, or templates  
- Create new content of any type  
- Delete or trash content  
- Upload media or modify Media Library  
- Change or edit site settings  
- Activate, install, update, or delete plugins  
- Switch themes or edit theme options  
- Use Quick Edit or bulk edit actions  
- Access the Customizer  

Any attempt to save content—whether via Classic Editor, Gutenberg, Elementor, REST API, or third-party builders—is stopped.

---

## Technical Safeguards

Observer Mode enforces read-only behavior through:

1. **Capability restrictions** for plugin, theme, and content operations  
2. **Removal of UI entry points** (Add New, Quick Edit, admin bar “New”)  
3. **Save blocking via `wp_insert_post_data`**  
4. **Gutenberg and REST API write blocking**  
5. **Blocking direct access to `post-new.php`**  
6. **Preventing role or privilege escalation**  

These layers ensure the user can browse everything without being able to break anything.

---

## GitHub Auto-Update Support

Observer Mode includes a built-in updater that checks this public GitHub repository for new releases.  

When a new tagged release is published:

- WordPress detects the new version  
- Displays an update notification  
- Installs the update using GitHub’s generated zipball  

No authentication token is required.

---

## Installation

1. Download the latest release ZIP from the **Releases** section.  
2. In WordPress, go to:  
   **Plugins → Add New → Upload Plugin**  
3. Upload the ZIP file and activate the plugin.  
4. Assign a user the **Observer Admin** role under:  
   **Users → Edit User → Role**

Once assigned, the user instantly receives full read-only access.

---

## Compatibility

Observer Mode works smoothly on:

- Small to large WordPress sites  
- WooCommerce  
- Elementor / Elementor Pro  
- Gutenberg / Full Site Editing  
- ACF, CPT plugins, and meta-builders  
- Multisite networks (role assigned per site)  
- Complex environments with caching and security plugins  
- Cloudflare, Nginx, Apache, Redis, and object caching  

Because write-blocking is done at the core hook level, Observer Mode remains stable across almost all configurations.

---

## Use Cases

- Client review access during development  
- Auditor or compliance access  
- Training and onboarding  
- Secure demo sites  
- Agencies needing view-only access for certain team roles  
- Preventing accidental edits by non-technical users  

---

## Changelog

### 1.0.0
- Initial public release  
- Added Observer Admin role with full visibility and zero write access  
- Added UI restrictions (Quick Edit, Add New, admin bar cleanup)  
- Added save-blocking across Classic, Gutenberg, REST, and Elementor  
- Added GitHub auto-update integration  
- Added Observer Mode Dashboard under Settings  

---

## License

MIT License  

You may use, modify, and distribute this plugin freely for personal or commercial purposes.
