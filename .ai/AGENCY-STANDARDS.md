# Agency Build Standards

Standing requirements from the marketing agency we build custom ACF flexible-content themes for.
They apply to **every** project built from this theme for that agency, so they do not need to be
handed over again per project.

Source: the agency's developer handover, received 2026-10-13. The rules below are the parts of
that handover that are not tied to one client. A project's own handover (client URLs, contacts,
page-specific requests) belongs in that project's `.ai/CLIENT-HANDOVER.md`, and wins over this
file where the two disagree.

**Read this before building any section, and check each new section against it.**

---

## 1. Admin (ACF) setup

### 1.1 Label fields and layouts by what they hold, not by the design

The client must be able to tell what a row is from its label alone. Earlier builds had rows named
after the design content, and the client could not tell them apart.

- Name a layout after its structure and purpose: "Image and Text", "Link Cards", "Logo Grid",
  "Newsletter Sign Up". Not after the copy or the page it was first designed for ("Overnight
  Stays Row", "What we Offer").
- Same for field labels: "Heading", "Intro Text", "Button", "Background Image".
- Add short instructions where a field's effect is not obvious (image side, style variant,
  autoplay).

### 1.2 Keep rows compact with the field width setting

The client should not have to scroll and scroll to fill in one row.

- Use ACF's wrapper **width** on fields so short fields sit side by side (for example 50/50 for
  image + image position, 33/33/33 for three toggles).
- Group settings and toggles together in one line rather than one per line.
- Repeaters make a row longer and that is accepted, but keep the sub-fields in columns too and
  use a collapsed repeater row where it helps.

### 1.3 Keep image sizes to an absolute minimum

A new upload must not generate a long list of thumbnail sizes.

- Register as few custom sizes as the design really needs.
- **Reuse the same sizes across sections** on the front end instead of adding a new set per
  section. Before registering a size, check whether an existing one is close enough.
- Remove sizes that no section uses any more.

---

## 2. Front end

### 2.1 Booking links open in a new tab

Where bookings go to an external third-party system, every booking link opens in a new tab
(`target="_blank"` with `rel="noopener"`). Take the booking URLs from the client's existing site.
We do not rebuild or change the booking system.

### 2.2 As few CPTs as possible; content lives in Pages

The client should manage their URLs and content in the **Pages** section of the admin. Add a CPT
only when the content really needs one. On a rebuild, the URL structure stays largely the same as
the existing site wherever it is needed.

### 2.3 Hero heading hierarchy

- The small text above the main hero title is a `<span>`, not a heading.
- The main hero title is the page's `<h1>`.

This protects the heading hierarchy across the site.

### 2.4 Filter rows get a visible scrollbar

When a row of filters or tags no longer fits on smaller screens, it scrolls sideways **with a
visible scrollbar**, so the user can see there is more and reach it easily. Do not hide the
scrollbar and rely on swiping.

### 2.5 Menus must work on large touch screens

Do not decide "desktop or mobile" by screen width alone. Use an input media query so a large
screen with tap input can still open menus and sub-menus.

- Hover-to-open only inside `@media (hover: hover) and (pointer: fine)`.
- For `@media (hover: none), (pointer: coarse)`, menus and sub-menus open on tap/click, at any
  width.

(The agency's wording is "a mobile input media query"; the two queries above are our way of
meeting it.)

### 2.6 Forms: Contact Form 7

Forms are built in Contact Form 7. It is the agency's choice because it is easy to track and
because existing forms often have to be migrated out of other systems.

### 2.7 Video and PageSpeed

- Videos must be the smallest file size possible, so they do not inflate LCP. This has been a
  problem on earlier builds.
- PageSpeed scores need to be as high as possible on **both** mobile and desktop.

See `VIDEO-SYSTEM.md` for posters, lazy loading and reduced-motion handling.

### 2.8 Hover effects and transitions

- Every hover effect follows the designer's design.
- Where the design shows a hover state but no transition (Figma cannot always express one), add
  `transition: all 0.5s;`.

---

## 3. Testing

For checking phones and tablets without BrowserStack, the agency uses the "Mobile simulator -
responsive testing tool" Chrome extension:
https://chromewebstore.google.com/detail/mobile-simulator-responsi/ckejmhbmlajgoklhgbapkiccekfoccmk

---

## 4. Checklist per section

- [ ] Layout and field labels describe the content, not the design copy
- [ ] Fields use widths; the row is as short as it can be
- [ ] No new image size unless no existing one fits
- [ ] Booking links open in a new tab
- [ ] No new CPT unless Pages cannot do it
- [ ] Hero: eyebrow is a `<span>`, title is the only `<h1>`
- [ ] Filters scroll with a visible scrollbar
- [ ] Menus open by tap on large touch screens
- [ ] Forms are Contact Form 7
- [ ] Video as small as possible; PageSpeed checked on mobile and desktop
- [ ] Hover states match the design; `transition: all 0.5s;` where the design has none
