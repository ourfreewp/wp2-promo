# Development Status


**No active placeholders.**
All core plugin files are implemented. Analytics data model and uninstall logic are fully functional. All frontend logic is in `assets/scripts/global-scripts-main.js`.

---

# Copilot Instructions: WP2 Lead Plugin Development
## 🎯 Project Overview

WP2 Lead is a self-hosted WordPress plugin for creating and managing high-conversion lead-capture campaigns. It leverages the Meta Box framework extensively for data management (Custom Tables, Relationships, Settings).  
The project follows a strong Object-Oriented Programming (OOP) paradigm with a clear separation of concerns.
## 📂 Key Files & Their Responsibilities


**src/App/Capabilities.php (Optional/Planned):** Not present. All capability logic is handled by WordPress core and controller permission checks.
**src/Models/Analytics/Registrar.php:** Defines the analytics custom table schema and creation logic. No Meta Box fields are registered for analytics; all analytics data is stored in the custom table via Meta Box Custom Table API.
**src/Client/Core.php:** Not present. All client-side SDK logic is in `assets/scripts/global-scripts-main.js`.
**src/Interfaces/REST/**: Not present. All REST controller logic is in `src/API/`.

**src/App/Capabilities.php:** *Planned.* Not yet implemented. Intended for custom WordPress capabilities.
**src/Models/Analytics/Registrar.php:** *Placeholder.* No Meta Box fields registered yet; future analytics data model.
**src/Client/Core.php:** *Placeholder.* All frontend logic is in `assets/scripts/global-scripts-main.js`.
**src/Interfaces/REST/**: *Placeholder interfaces.* All REST controller logic is in `src/API/`.
---

## REST API Security & Versioning

- **All REST API endpoints** require a valid WordPress nonce, passed in the `X-WP-Nonce` header.
- **Capability checks** are enforced for all write operations using `current_user_can()`.
- **API Versioning:** All endpoints are under the `/wp2-lead/v1/` namespace. Future breaking changes will use `/v2/` and so on for backward compatibility.
- **Error Handling:**
    - Server-side errors are returned as `WP_Error` or `WP_REST_Response` with appropriate status codes.
    - Client-side errors are logged to the browser console; debug mode provides additional output.

---

## ✍️ Coding Standards & Best Practices
# Copilot Instructions: WP2 Lead Plugin Development

This document provides guidelines for an AI assistant (e.g., GitHub Copilot) when contributing to the WP2 Lead WordPress plugin. Adherence to these instructions is critical to maintain code quality, consistency, and architectural integrity.

## 🎯 Project Overview

WP2 Lead is a self-hosted WordPress plugin for creating and managing high-conversion lead-capture campaigns. It leverages the Meta Box framework extensively for data management (Custom Tables, Relationships, Settings).  
The project follows a strong Object-Oriented Programming (OOP) paradigm with a clear separation of concerns.

- **PSR-4 Autoloading:** All PHP classes are namespaced under `WP2Lead\` and autoloaded via `composer.json`.
- **Central Orchestration:** `WP2Lead\App\Core` is the main plugin class, responsible for defining constants, instantiating core components (services, controllers, registrars), and registering primary WordPress hooks.
- **Dependency Injection:** Services are injected into controllers and other classes where appropriate (e.g., `AnalyticsAPIController` receives `AnalyticsService`).
- **Meta Box Integration:** Heavy reliance on Meta Box APIs for:
  - **Custom Tables:** `wp2_lead_analytics` (campaigns are stored as CPTs with Meta Box fields).
  - **Relationships:** `campaign_to_form` (linking campaigns to WSForm).
  - **Settings Pages:** `wp2_lead_settings` option key.
  - **Custom Fields:** Defined via `rwmb_meta_boxes` filter.
- **REST API:** Custom endpoints for campaign data and analytics, secured with nonces and capabilities.
- **Client-Side SDK:** A modular JavaScript SDK (`WP2Lead` class) handles all frontend logic.

## 📂 Key Files & Their Responsibilities

- **wp2-lead.php:** The main plugin entry file.
  - Defines core plugin constants (`WP2_LEAD_FILE`, `WP2_LEAD_VERSION`, etc.).
  - Includes Composer's `autoload.php`.
  - Instantiates `WP2Lead\App\Core`.
  - Registers activation and uninstallation hooks.
  - Registers the top-level admin menu page.
- **composer.json:** Defines Composer dependencies and PSR-4 autoloading rules.
- **src/App/Core.php:** The main plugin orchestrator class.
  - Manages plugin-wide constant definitions (though defined in `wp2-lead.php`, Core uses them).
  - Instantiates all other core plugin classes (e.g., Registrars, Controllers, Dashboard).
  - Registers main WordPress hooks (`init`, `wp_enqueue_scripts`, `mb_relationships_init`).
- **src/App/Capabilities.php (Optional/Planned):** If implemented, defines and manages custom WordPress capabilities.
- **src/Models/Campaigns/Registrar.php:** Registers Meta Box definitions for the `wp2_lead_campaign` Custom Post Type.
  - Defines fields for variants, targeting rules, triggers, dismissal, position, and transition.
  - Integrates the `campaign_to_form` Meta Box Relationship.
  - Does NOT perform direct database operations (delegates to Meta Box Custom Table API).
- **src/Models/Settings/Registrar.php:** Registers the Meta Box Settings Page and its fields (`wp2_lead_settings`).
  - Defines fields for default trigger delay, dismissal duration, API keys, debug mode.
- **src/Models/Analytics/Registrar.php:** A placeholder. Currently, no Meta Box fields are registered for analytics data.
- **src/Services/Analytics/Provider.php:** Handles the business logic for analytics data.
  - Records events (`record_event`) using `MetaBox\CustomTable\API`.
  - Retrieves and aggregates analytics data (`get_analytics_data`, `get_aggregated_analytics`) using direct SQL queries for performance, with all data stored in the custom table.
- **src/Services/Campaigns/Provider.php:** Handles business logic for campaigns.
  - `select_variant()`: Implements weighted random selection for A/B testing.
  - `evaluate_rules()`: Placeholder for server-side rule evaluation (primary evaluation is client-side).
  - `can_view_campaign()`: Checks if a campaign is published for public viewing.
- **src/API/Campaigns/Controller.php:** Manages the `/wp2-lead/v1/campaigns` REST API endpoint.
  - Extends `WP_REST_Controller`.
  - Handles GET requests for campaign data.
  - Fetches data from the `wp2_lead_campaign` CPT and Meta Box fields.
- **src/API/Analytics/Controller.php:** Manages the `/wp2-lead/v1/analytics` REST API endpoints.
  - Extends `WP_REST_Controller`.
  - Handles POST requests for recording events (`record_event`).
  - Handles GET requests for aggregated data (`get_aggregated_data`).
  - Injects and delegates to `AnalyticsService`.
  - Implements permission checks using nonces and WordPress capabilities.
- **src/Admin/Analytics/Dashboard.php:** Manages the admin analytics dashboard page.
  - Registers the submenu page under "WP2 Lead".
  - Enqueues admin-specific CSS (`assets/css/admin-styles.css`) and JS (`assets/js/admin-analytics.js`).
  - Localizes data for the admin JS.
- **src/Client/Core.php:** Not present. All client-side SDK logic is in JavaScript.
- **src/Interfaces/REST/**: Not present. The actual controller logic is in `src/API/`.
- **assets/scripts/global-scripts-main.js:** The main client-side JavaScript SDK.
  - Implements the `WP2Lead` class.
  - Manages client-side state (dismissals) using localStorage.
  - Uses Floating UI for positioning.
  - Contains `TriggerEngine`, `RulesEngine`, and analytics logic.
  - Communicates with REST API endpoints.
- **assets/scripts/admin-analytics.js:** JavaScript for the admin analytics dashboard.
  - Fetches aggregated data from the REST API.
  - Uses Chart.js for data visualization.
- **assets/styles/global-styles-main.scss:** Main SCSS file for frontend campaign styles.
  - Compiled to `assets/css/wp2-lead.css`.
  - Includes styles for campaign positions, close button, and toast notifications.
  - No separate admin SCSS; admin styles are handled in the dashboard or via Chart.js defaults.
- **blocks/campaign/template/block.json:** Gutenberg block metadata.
- **blocks/campaign/template/index.php:** Server-side render callback for the Gutenberg block.
  - Outputs a placeholder div with `data-wp2-lead-campaign-id`.
- **blocks/campaign/template/main-inline.js:** Not present. Block selection is handled via Blockstudio's native post attribute type.
  - No custom JS is needed for campaign selection.
- **tests/**: Contains PHPUnit test files (`test-rest-api.php`, `test-models.php`, `test-services.php`, `test-campaigns.php`, `bootstrap.php`).

## ✍️ Coding Standards & Best Practices


**PHP:**

- **PSR-4 & Namespacing:** Use correct namespaces (`WP2Lead\App`, `WP2Lead\API`, `WP2Lead\Models`, `WP2Lead\Services`, `WP2Lead\Admin`).
- **PHPDoc:** Provide PHPDoc blocks for all classes, properties, methods, and functions. Include `@param`, `@return`, `@throws`, `@since`, and `@package` tags.
- **Type Hinting:** Use strict type hints for method parameters and return types (`int`, `string`, `bool`, `array`, `void`, `?array`, `\WP_Error`, `\WP_REST_Response`, etc.).
- **WordPress Coding Standards:** Adhere to WordPress coding standards (snake_case for functions/methods, PascalCase for classes).
- **Late Escaping:** Escape data as late as possible, right before outputting to HTML (`esc_html_e()`, `esc_attr()`, `wp_kses_post()`).
- **Internationalization (i18n):** Use WordPress i18n functions (`__()`, `_x()`, `esc_html__()`, `esc_attr__()`) for all translatable strings.
- **Meta Box API Usage:** Use `MetaBox\CustomTable\API`, `MB_Relationships_API`, `rwmb_meta_boxes`, `mb_settings_pages` for all data storage and retrieval. Direct `$wpdb` queries are only used for analytics aggregation.
- **Security:** Implement nonces for write operations, use `current_user_can()` with appropriate capabilities, and sanitize/validate all input.


**JavaScript:**

- **ES Modules:** Use `import` and `export` syntax where possible.
- **JSDoc:** Provide JSDoc for classes, methods, and properties.
- **Modern JS:** Use `async/await`, `const`/`let`, arrow functions, and other ES6+ features.
- **Client-Side State:** Use localStorage for managing persistent client-side state (e.g., dismissed campaigns).
- **DOM Manipulation:** Use native DOM APIs (`document.createElement`, `querySelector`, `addEventListener`).
- **Accessibility (A11y):** Implement focus trapping, ARIA attributes, and keyboard navigation.
- **Error Handling:** Use `try-catch` blocks for fetch and other asynchronous operations. Log errors to the console.


**CSS/SCSS:**

- **SCSS:** Use SCSS features (nesting, variables, mixins) in `.scss` files.
- **BEM-like Naming:** Use `wp2-lead-` prefix for all plugin-specific classes to avoid conflicts.
- **Responsiveness:** Design for mobile-first and use media queries for responsive adjustments.
- **Transitions/Animations:** Use CSS transitions for smooth UI changes.

## 🤖 Interaction Guidelines for Copilot

- **Adhere to Existing Files:** NEVER create new files unless explicitly instructed. Always modify the existing files provided in the project structure. Uninstall logic is now in `uninstall.php`.
- **Prioritize Refactoring:** If a new feature can be integrated by refactoring existing code to fit the class-based, namespaced architecture, prioritize that over adding new, standalone functions.
- **Maintain PSR-4:** When adding new classes or refactoring existing procedural code into classes, ensure they are correctly namespaced and follow the `WP2Lead\` root namespace.
- **Use Dependency Injection:** If a class needs to interact with a service (e.g., `AnalyticsService`), ensure that service is passed into its constructor (dependency injection), rather than instantiating it directly within the method or relying on global access.
- **Meta Box First:** For any data storage, custom fields, relationships, or settings, always assume and utilize the Meta Box framework APIs (`MetaBox\CustomTable\API`, `MB_Relationships_API`, `rwmb_meta_boxes`, `mb_settings_pages`). Do not write raw `$wpdb` queries for these purposes unless explicitly necessary for complex reports not covered by Meta Box APIs.
- **Comprehensive Documentation:** When generating or modifying code, always include detailed PHPDoc/JSDoc.
- **Security Mindset:** For any REST API interactions or user input, always consider and implement appropriate nonces, capability checks, sanitization, and escaping.
- **Localization:** Ensure all user-facing strings are wrapped in WordPress internationalization functions.
- **Testing:** When implementing new features or fixing bugs, consider how they can be tested and suggest relevant unit or integration tests in the `tests/` directory.
- **Consistency:** Pay close attention to existing naming conventions, file organization, and code style. If a pattern is already established (e.g., specific variable naming, function prefixes), follow it.

By following these guidelines, the AI assistant will be a highly effective and consistent contributor to the WP2 Lead project.
