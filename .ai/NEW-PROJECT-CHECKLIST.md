# Mosharaf Core — New Project Setup Checklist

Use this checklist every time you start a new WordPress project from this framework.

---

## Phase A — File Setup

- [ ] Copy `mosharaf-core/` theme folder directly into the new project's `wp-content/themes/`
      — ⚠ it must land flat as `wp-content/themes/<slug>/style.css`, not nested in a
      wrapper folder (e.g. `wp-content/themes/my-project/mosharaf-core/`), or WordPress
      won't recognise it as an activatable theme
- [ ] Rename the folder to the project slug (e.g. `acme-corp`)
- [ ] Remove the copied `.git/` (`rm -rf .git`) before running `git init` — `cp -r` carries
      over the starter's `origin` remote, which would point commits/pushes at the original
      `mosharafmanu/mosharaf-core` repo instead of your project's
- [ ] Run `bash bin/new-project.sh` to rename all prefixes in one pass (see `NEW-PROJECT-SETUP.md`)
- [ ] Update `style.css` header: Theme Name, Author URI, Description

---

## Phase B — WordPress Setup

- [ ] Activate the theme in WP Admin > Appearance > Themes
- [ ] Install and activate **Advanced Custom Fields PRO**
- [ ] Go to Custom Fields > Sync — import all field groups from `acf-json/`
- [ ] Run **Settings > Permalinks > Save** to flush rewrite rules

---

## Phase C — Design Tokens

- [ ] Open `style.css` and update the color values in `:root {}`
- [ ] Update `--mc-font-heading` and `--mc-font-body`
- [ ] Update Google Fonts URL in `functions.php`
- [ ] Review `--mc-container-max` and `--mc-section-padding-y` against the design grid
- [ ] Review button color logic in `.btn-primary`, `.btn-secondary`, `.btn-outline`

---

## Phase D — Image Sizes

- [ ] Open `inc/image-sizes.php`
- [ ] Define the width ladder for this project (based on layout breakpoints and design grid)
- [ ] Define the `size_group` token map and variant map in `inc/helper-functions/responsive-picture.php` for this project
- [ ] After media is uploaded: run **Regenerate Thumbnails**

---

## Phase E — ACF Options Pages

- [ ] Go to WP Admin > Custom Fields > Options Pages
- [ ] Create the options pages this project needs (e.g. Site Settings, Blog Options)
- [ ] Configure the fields on those options pages to match the project's header/footer structure
- [ ] Update helper functions in `inc/helper-functions/site-settings.php` to match

---

## Phase F — Navigation

- [ ] Go to WP Admin > Appearance > Menus
- [ ] Create `Main Menu` and assign to the `mainMenu` location
- [ ] Create `Footer Menu` and assign to the `footerMenu` location

---

## Phase G — Site Settings (ACF Options)

- [ ] Go to WP Admin > Site Settings (or whatever the options page is named)
- [ ] Upload site logo
- [ ] Upload footer logo (or leave blank if same as site logo)
- [ ] Set header CTA button link and label
- [ ] Add social media links
- [ ] Set footer copyright text (supports `{year}` for dynamic year)

---

## Phase H — Build Sections from the Client Design

- [ ] For each section in the client design:
  1. Read the design — identify what fields an editor needs to control
  2. Name the layout (consistent `snake_case` or `kebab-case` for the project)
  3. Create the ACF layout in WP Admin > Custom Fields
  4. Copy `template-parts/sections/example_section.php` and rename it
  5. Build the section HTML using helper functions
  6. Test on a real page in WP Admin

See `ACF-PATTERNS.md` for the full section-building workflow.

---

## Phase I — Cleanup

- [ ] Remove or replace `screenshot.png` with a real theme screenshot
- [ ] Remove `template-parts/sections/example_section.php` when no longer needed as a reference
- [ ] Delete any ACF field groups that are not used by this project
- [ ] Review `inc/helper-functions/site-settings.php` and remove functions not used by this project
