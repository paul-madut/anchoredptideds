# Anchored Peptides — Coming Soon: Smoke Test

Run through these after any change. ~5 minutes. URLs assume the live site `anchoredpeptides.com`.

## A. Visibility (store stays intact)
1. **Public sees coming-soon** — open the site in a **private/incognito window** (logged out): `https://anchoredpeptides.com/` → you should see the animated "We're dropping anchor soon." page.
2. **Admin sees the store** — in your normal (logged-in) browser, open `https://anchoredpeptides.com/` → you should see the full storefront (hero, products), NOT the coming-soon page.
3. **Admin preview** — logged in, open `https://anchoredpeptides.com/?apcs_preview=1` → forces the coming-soon page so you can preview it without logging out.

## B. Email capture (happy path)
4. In the incognito window, enter a real test email (e.g. `you+test@gmail.com`) → click **Notify me**.
5. Button shows a brief spinner, then the form morphs into the **checkmark + "You're anchored in."** success state.

## C. Storage + Omnisend sync
6. Logged in → **WP Admin → Coming Soon**. Confirm:
   - the **Waitlist (N)** count went up by 1,
   - the new email is in the table with date + source,
   - the **Omnisend** column shows **✓ synced**.
7. **Omnisend** → **Audience / Contacts** → the same email appears, tagged `waitlist`, status *subscribed*. (May take a few seconds.)
8. Click **⬇ Export all as CSV** → a `anchored-peptides-waitlist-YYYY-MM-DD.csv` downloads with email, joined, source, ip.

## D. Edge cases
9. **Invalid email** — enter `notanemail` → submit → inline red message "Please enter a valid email address." (no row added).
10. **Duplicate** — submit the same email twice → second time returns "You're already anchored in." and does NOT create a duplicate row.
11. **Spam/honeypot** — bots that fill the hidden field are silently ignored (nothing stored). Nothing to do manually; just know real submissions are protected.

## E. Toggle on/off (reversibility)
12. WP Admin → Coming Soon → **untick "Enable Coming Soon mode"** → Save. Reload the site in incognito → the **full store is public** again.
13. Re-tick → Save → incognito shows the coming-soon page again.

## F. Polish (optional)
14. Resize the browser / open on a phone → layout stacks cleanly (input + button full width).
15. Enable OS "Reduce motion" → animations fall back to gentle fades (no drifting/anchor-draw).

## Cleanup
- Delete any test rows: WP Admin → **Coming Soon → Waitlist** → hover the row → **Trash**.
- If a test email reached Omnisend, remove it in **Omnisend → Audience**.

## Notes
- The WordPress list is the reliable source of truth; Omnisend is a bonus sync. If the Omnisend column shows `⚠ http 401/…`, the API key is wrong/expired — re-paste it in Coming Soon settings.
- Security: the API key is stored in WordPress. Rotate it in Omnisend anytime if needed, then update it in Coming Soon settings.
