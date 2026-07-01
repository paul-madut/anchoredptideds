# EasyFaithForAll (WordPress plugin)

Runs the **EasyFaithForAll** single-page app on this WordPress site.

The app is a self-contained React bundle that takes over the whole document when it loads, so the plugin runs it inside an **isolated iframe** (embed) or serves it **full-screen** (launch URL / front page). It never touches your theme's markup, so there are no script/style conflicts.

## Install
- **Admin:** Plugins → Add New → Upload Plugin → choose `easyfaithforall.zip` → Install → **Activate**.
- **FTP/SSH:** copy the `easyfaithforall` folder into `wp-content/plugins/`, then activate it.

Activation auto-flushes permalinks, so URLs work right away.

## Three ways to use it
1. **Make it the whole site** — Settings → EasyFaithForAll → tick **"Serve as front page"**. The app now shows at your site root (`/`).
2. **Launch URL** — `https://yoursite.com/effa` serves it full-screen.
3. **Embed on a page** — add the shortcode to any page/post:
   ```
   [effa]
   [effa height="800px"]
   ```
   Default height `85vh`, full width.

> If a URL 404s, go to **Settings → Permalinks** and click **Save Changes** once.

## Updating the app
Replace `easyfaithforall/apps/app.html` with the new bundle (keep the filename). The plugin picks it up automatically.

## Requirements
WordPress 5.0+, JavaScript enabled, modern browser.
