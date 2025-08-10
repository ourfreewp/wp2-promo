# WP2 Lead

WP2 Lead is a lightweight, high-conversion lead-capture campaign plugin for WordPress, built for the WP2 Stack and Blockstudio. It provides a robust, extensible architecture for managing, displaying, and analyzing lead campaigns with full REST API and block editor support.

## Architecture Overview

- **Campaigns** are stored as a Custom Post Type (`wp2_lead_campaign`) with all fields managed by Meta Box.
- **Variants** (A/B test) are managed as Meta Box group fields.
- **Analytics** are stored in a custom table (`wp2_lead_analytics`) using Meta Box Custom Table API.
- **Admin UI** includes a dashboard and analytics chart (Chart.js).
- **Blockstudio** block allows campaign selection and rendering in the editor.
- **Client SDK** (JS) handles campaign lifecycle, targeting, triggers, analytics, and state (Nanostores).
- **WSForm Integration** for conversion tracking.

## Data Model

- **CPT:** `wp2_lead_campaign`
- **Meta Fields:** Variants, position, dismissal duration, linked form, triggers, targeting rules
- **Analytics Table:** `wp2_lead_analytics` (fields: id, campaign_id, variant_id, event_type, event_date, meta)

## Block Editor

- Blockstudio block: `wp2-lead/campaign`
- Attribute: `campaignId` (type: post, postType: wp2_lead_campaign)
- Server render: outputs campaign container or placeholder

## REST API

- `/wp2-lead/v1/campaigns/{id}`: Get campaign data
- `/wp2-lead/v1/analytics`: Record analytics event
- `/wp2-lead/v1/analytics/aggregated`: Get aggregated analytics

## Developer Workflow

- **Build assets:** `npm run build`
- **Test PHP:** `composer test`
- **Test E2E:** `npm run test:e2e`
- **Reset:** `wp2 reset --fresh`
- **Sync:** `wp sdk-explorer sync --type=<type>`

## Testing

- PHPUnit tests in `tests/`:
  - `test-campaigns.php`: CPT registration
  - `test-services.php`: CampaignsService and AnalyticsService
  - `test-rest-api.php`: REST API endpoints, permissions, validation

## Internationalization

- All PHP strings are translatable (`__()`, `_x()`, etc.)


## Settings & Fathom Analytics

- All plugin settings are managed via Meta Box Settings Page (see code for `wp2_lead_settings`).
- To integrate with Fathom Analytics, add your Fathom site ID in the settings page. The plugin will automatically inject the Fathom tracking script and associate conversions with Fathom events.

## Cleanup

- All placeholder and redundant files removed.

---

For full developer instructions, see inline code comments and Meta Box/Blockstudio documentation.
