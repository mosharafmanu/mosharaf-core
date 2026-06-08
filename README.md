# Mosharaf Core

A clean, modular ACF-based WordPress theme framework for custom client projects.

**Every real section is created per project from the client design.** This theme provides the architecture, dispatcher, and helper patterns — not pre-built sections.

---

## What This Is

Mosharaf Core is a personal WordPress theme framework built for repeatable custom ACF-based projects. It is not a theme you activate and use out of the box. It is a starting point that you configure and build on top of for each client.

It provides:

- **ACF Flexible Content dispatcher** — add an ACF layout in WP Admin, create a matching template file, done
- **Helper function library** — responsive images, buttons, icons, video, site settings, pagination, breadcrumbs
- **Design token system** — semantic CSS custom properties updated per project
- **Example section template** — the canonical starting point for every new section
- **AI documentation** — `.ai/` folder explains how to build new sections from client designs
- **Video system** — multi-source video renderer (self-hosted, YouTube, Vimeo CDN, CDN URL, autoplay, popup, hover)
- **Bootstrap script** — `bin/new-project.sh` renames all prefixes in one interactive pass

---

## Quick Start — New Project

1. Copy this theme folder and rename it to the project slug
2. Run `bash bin/new-project.sh` — it renames all identifiers interactively
3. Update `style.css` header metadata
4. Update Google Fonts URL in `functions.php`
5. Set design token values in `style.css` `:root {}`
6. Define image sizes in `inc/image-sizes.php`
7. Create ACF Options pages directly in the ACF plugin UI
8. Go to WP Admin > Custom Fields > Sync to import field groups
9. Build client-specific sections from the design

See `.ai/NEW-PROJECT-CHECKLIST.md` for the full step-by-step.

---

## Key Conventions

| Convention | Value |
|---|---|
| Function prefix | `mosharaf_` |
| Text domain | `mosharaf-core` |
| CSS token prefix | `--mc-` |
| Image size prefix | `mc-` |
| ACF flexible content field | `cms` |
| Section templates | `template-parts/sections/{layout_name}.php` |

---

## File Structure

```
mosharaf-core/
├── .ai/                         # AI documentation
│   ├── ACF-PATTERNS.md          # How to build sections + all helper function signatures
│   ├── VIDEO-SYSTEM.md          # Video field and helper documentation
│   ├── NEW-PROJECT-CHECKLIST.md # Setup checklist for each project
│   ├── NEW-PROJECT-SETUP.md     # Bootstrap script documentation
│   └── THEME-ARCHITECTURE.md   # How the framework is structured
├── acf-json/                    # ACF field groups (auto-synced)
├── assets/
│   ├── css/                     # Stylesheets
│   ├── js/                      # JavaScript (video system included)
│   └── svgs/                    # SVG icon includes
├── inc/
│   ├── helper-functions/        # Core reusable helpers
│   │   ├── breadcrumb.php
│   │   ├── button-renderer.php
│   │   ├── flexible-content.php # The dispatcher
│   │   ├── icon-renderer.php
│   │   ├── pagination.php
│   │   ├── post-utilities.php
│   │   ├── responsive-picture.php
│   │   ├── site-settings.php    # Project-specific — configure per project
│   │   └── video-renderer.php   # Multi-source video system
│   └── image-sizes.php          # Image sizes — define per project
├── languages/                   # Translation .pot file
├── template-parts/
│   ├── content*.php             # Standard WordPress loop templates
│   └── sections/
│       └── example_section.php  # The pattern template — start every section here
├── functions.php                # Theme bootstrap
├── style.css                    # Theme metadata + :root {} design tokens
├── header.php
├── footer.php
├── page.php
├── single.php
├── index.php
├── archive.php
└── 404.php
```

---

## ACF Field Groups Included

| File | Description |
|---|---|
| `group_flexible_content.json` | Page Builder — `cms` flexible content field, attached to page/post/product |
| `group_page_settings.json` | Page Settings — `show_page_title` toggle, attached to all pages |
| `group_site_settings.json` | Site Settings — logo, header button, social links, footer copyright |
| `group_blog_options.json` | Blog Options — blog archive hero (title, image, video) |
| `ui_options_page_696524653c0cd.json` | ACF Options page definition — creates "Site Settings" top-level admin menu |
| `ui_options_page_blog_options.json` | ACF Options page definition — creates "Blog Options" under Posts menu |

See `.ai/ACF-PATTERNS.md` for full field documentation.

---

## Documentation

All documentation lives in `.ai/`. Read these before starting a new project:

1. `.ai/NEW-PROJECT-CHECKLIST.md` — setup steps
2. `.ai/ACF-PATTERNS.md` — how to build sections + helper function signatures
3. `.ai/VIDEO-SYSTEM.md` — how to use the video renderer
4. `.ai/NEW-PROJECT-SETUP.md` — bootstrap script reference
5. `.ai/THEME-ARCHITECTURE.md` — how the framework is structured
