# DESIGN.md - "Quay & Oak" (sandwichsecurity.co.uk)

Purpose: a quiet, confident, heritage-aware look for a CCTV and doorbell installer working in one of England's best-preserved medieval towns. One stylesheet (`assets/css/styles.css`), self-hosted fonts (`assets/fonts.css`), no external CDNs, only the existing `assets/analytics.js` tag for visit counting.

## Concept
Parchment pages, oak-brown ink, Sandwich brick red, and ochre used only on dark. It should feel like a well-kept deed or a local archive, not a security-product landing page. Photography shows the real texture of the town (timber frames, brick lanes, the Quay) with the technology fitted discreetly. Text always sits on solid colour, never on a photo.

## Tokens (`:root`)
| Token | Value | Use |
|---|---|---|
| `--oak` / `--oak-2` | #231a14 / #33261d | headings, topbar, proof strip, panel, footer |
| `--parchment` / `--paper` | #f1ebdd / #faf6ec | page ground / cards, forms |
| `--ink` / `--ink-soft` | #241b15 / #5a4d43 | body / secondary text |
| `--mist` | #dccfb8 | secondary text on oak |
| `--brick` / `--brick-deep` | #7b2d2a / #5c201e | links, primary button, promise band |
| `--ochre` | #e0b25c | accents on dark only |
| `--wa` / `--wa-deep` | #0a6e3d / #085832 | WhatsApp buttons (white text >= 5:1) |
| `--font-display` | Newsreader (variable, roman + italic) | headings, brand, prices |
| `--font-text` / `--font-mono` | IBM Plex Sans (variable) / IBM Plex Mono 500 | body / labels, eyebrows, crumbs |

Legacy aliases (`--graphite`, `--cyan-bright`, `--blue`, `--stone` ...) map to these because generated pages carry inline styles that reference them.

## Layout signatures (what makes it Sandwich, not the template)
- Home hero: large serif headline on parchment, with the photo set in an arched "doorway" window with a hard brick-red offset and a rotated mono tag. Text left, arch right; stacks text-first on mobile.
- Double-rule header, section eyebrows prefixed with a section mark in mono.
- Services as parchment tiles with an oak top-beam, line icon and a hard offset shadow that turns brick on hover.
- Ledger-style proof strip under the hero (survey credit, 5% offers, emergency visit), brick promise band with outlined cells.
- Inner pages: parchment hero with a hairline rule; optional framed photo band (areas, about, doorbell service).

## Components
`.topbar`, `header.site`, `nav.primary`, `.hero` / `.hero-media` / `.hero-copy` / `.hero.hero-light`, `.proof`, `.btn(-primary|-ghost|.wa-btn-lg)`, `.grid-2/3/4` (+ `.card`, `.card-icon`), `.guarantees` + `.promise`, `.photo-band`, `.stack` + `.photo`, `.panel`, `table.pricing`, `.callout`, `article.guide`, `.crumb`, form styles, `footer.site`.

## Rules
- Contrast measured on rendered pages (all text >= 4.5:1, large text >= 3:1).
- Mobile first; no horizontal scroll at 390px. Tap targets >= 48px on buttons and form fields.
- Icons are inline SVG (stroke, currentColor). No emoji icons.
- Images: always `width`/`height`, descriptive `alt`, `loading="lazy"` except the hero. No faces, no readable text, no number plates.
- No em or en dashes anywhere; plain hyphens.
- No Google Analytics or external font CDN. Keep `assets/analytics.js` before `</body>`.

## Assets
`assets/img/sandwich-quay-dusk.jpg`, `sandwich-doorbell.jpg`, `sandwich-lane.jpg`, `sandwich-install.jpg` (1400px wide, each under 350KB) and `og-default.png` (1200x630, text-free crop). Generated with gpt-image-2.

## Maintenance caveat
The redesign was applied directly to the committed HTML and CSS. If a generator or build script exists in this repo's history, diff it against the committed pages before running it, it may be stale and would overwrite pages with older content.
