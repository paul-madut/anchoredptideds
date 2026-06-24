# Connor Landing Template

A WordPress plugin that adds a "Connor 90-Day Landing" page template you can assign to any Page.

## What it does

- Registers a custom page template that renders the Connor 90-day transformation landing page
- Works with **any active WordPress theme** — does not modify or replace your current theme
- The landing renders standalone (bypasses your theme's header/footer) for a clean full-bleed funnel page
- Klaviyo opt-in form is preserved with the original list ID

## Installation

1. Zip the entire `connor-landing-template` folder (it should contain `connor-landing-template.php` and a `templates/` subfolder)
2. In WordPress admin: **Plugins → Add New → Upload Plugin**
3. Upload the zip and click **Install Now**, then **Activate**

## How to use it

1. Create a new Page (or edit an existing one): **Pages → Add New**
2. In the right sidebar, find **Page Attributes** (in the block editor it's under the Page tab)
3. In the **Template** dropdown, select **Connor 90-Day Landing**
4. Publish or update the page
5. Visit the page URL — you'll see the full landing rendering instead of the normal page layout

## How it works

The plugin hooks into `theme_page_templates` to add the option to the dropdown, and `template_include` to load the template file from the plugin folder when that option is selected. Your theme is never modified.

## Notes

- The page title, slug, and URL are still controlled normally through the WordPress page editor — only the visual rendering is taken over by the template
- If you deactivate the plugin, any pages using this template will fall back to your theme's default page template (the page itself isn't deleted)
- The form posts directly to Klaviyo from the browser (same behavior as the original standalone HTML)
