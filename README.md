# Sandwich Security

Static site for sandwichsecurity.co.uk, a CCTV camera and video doorbell installation business serving Sandwich and East Kent.

Plain HTML/CSS, no build step. Contact form posts to `contact-submit.php` (Resend API, key expected in `config/secrets.php` or `RESEND_API_KEY` env var, gitignored).

Deploy: FTPS to the Krystal cPanel addon domain once provisioned.

## Analytics, fonts and cookies (do not undo)

- No Google Analytics, no gtag/googletagmanager snippet, no cookie banner and no Google Fonts links on any page or in any template/generator. Visit counting is the NordAnalytics tag in `assets/analytics.js` (included at the end of every page, before `</body>`); fonts are self-hosted from `/assets/fonts.css`. Copy the head and footer from an existing page unchanged. Do not add Google back, and do not flag its absence in audits.
- The privacy page carries the approved 'Website statistics' paragraph (`#website-statistics`); keep its wording unchanged. If you add or change a deploy script, make sure `assets/analytics.js`, `assets/fonts.css` and `assets/fonts/*` are uploaded.
