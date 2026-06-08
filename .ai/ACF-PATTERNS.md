# Mosharaf Core — ACF Patterns & Helper Functions

Mosharaf Core is a clean ACF-based WordPress theme framework. Every section is created per project from the client design.

**There is no section library.** You build each section from scratch using the dispatcher pattern, the helper functions, and the example template as your starting point.

---

## How the System Works

```
WP Admin (editor adds layouts to a page)
        ↓
ACF Flexible Content field named "cms"
        ↓
Dispatcher: inc/helper-functions/flexible-content.php
        ↓
Loads: template-parts/sections/{layout_name}.php
        ↓
Frontend output
```

The dispatcher is called once in any template:
```php
mosharaf_flexible_content('cms');
// or with a specific post ID:
mosharaf_flexible_content('cms', $post_id);
```

**Nothing else is needed.** Add a layout in ACF, create the matching template file, done.

---

## Naming Rules

The layout name in ACF and the template filename **must match exactly** — character for character.

| ACF layout name | Template file |
|---|---|
| `hero_section` | `template-parts/sections/hero_section.php` |
| `testimonials_grid` | `template-parts/sections/testimonials_grid.php` |
| `media-content-fifty` | `template-parts/sections/media-content-fifty.php` |

Rules:
- ACF layout name and template filename must always match exactly
- Use either underscores (`_`) or hyphens (`-`) — pick one style per project and stick to it
- No spaces, no uppercase
- Describe what the section *is* (✅ `team_grid`, ❌ `about_page_team`)
- 2–3 words maximum

---

## Workflow: Client Design → Live Section

### Step 1 — Read the design, identify fields

Look at the design. Ask: *"What does an editor need to control?"*

| What you see | ACF field type |
|---|---|
| Heading text | Text or Textarea (Textarea if you need line breaks) |
| Body paragraph | WYSIWYG or Textarea |
| CTA button | Link |
| Image | Image (return: Array) |
| Video | see `VIDEO-SYSTEM.md` |
| List of repeating items | Repeater |
| Icon | Image |
| Toggle on/off | True/False |
| Colour choice | Select (value → CSS class) |
| Form embed | Textarea (shortcode) |

### Step 2 — Name the layout and fields

Choose a name using your chosen delimiter style (snake_case or kebab-case). Choose `snake_case` or `kebab-case` field names consistently. Write them down before touching the code.

### Step 3 — Create the ACF layout in WP Admin

1. WP Admin > Custom Fields > Flexible Content field group
2. Click **Add Layout**
3. Name it exactly as the layout name from Step 2
4. Add the sub-fields
5. **Save** — ACF auto-syncs to `acf-json/group_flexible_content.json`

### Step 4 — Create the template file

Copy `template-parts/sections/example_section.php` and rename it. Follow the structure:
1. Collect all fields with `get_sub_field()` at the top
2. Early bail if nothing to render
3. Build CSS classes as an array
4. Output HTML using helpers

### Step 5 — Test

Add the layout to a page in WP Admin, fill in content, view the frontend.

---

## The Pattern Template

`template-parts/sections/example_section.php` is the canonical starting point. **Always start from this file.** It demonstrates:

1. Collecting all fields at the top before any HTML output
2. Early bail to prevent empty `<section>` tags
3. CSS class array construction
4. Conditional rendering with `if` guards
5. Helper calls wrapped in `function_exists()` guards
6. Responsive image rendering
7. Button rendering

---

## Field Safety Checks

```php
// ✅ Check truthiness before rendering
if ( $title ) {
    echo esc_html( $title );
}

// ✅ Check array fields before looping
if ( $items && is_array( $items ) ) {
    foreach ( $items as $item ) { ... }
}

// ✅ Guard helper calls
if ( $button && function_exists( 'mosharaf_render_button' ) ) {
    mosharaf_render_button( $button );
}

// ✅ Bail early
if ( ! $title && ! $items ) {
    return;
}
```

ACF returns `null` for empty fields — check truthiness, not `=== null`.

---

## Helper Function Reference

All helpers live in `inc/helper-functions/`. Always wrap calls in `function_exists()`.

---

### 1. Flexible Content Dispatcher
**File:** `inc/helper-functions/flexible-content.php`

```php
mosharaf_flexible_content( string $field_name = 'cms', int|null $post_id = null )
```

```php
mosharaf_get_first_flexible_layout() : string|false
mosharaf_get_last_flexible_layout()  : string|false
mosharaf_has_hero_first_section()    : bool
```

Global variables available inside section templates during render:
```php
$GLOBALS['mosharaf_previous_layout']   // name of previous layout (string)
$GLOBALS['mosharaf_section_index']     // 0-based position on the page (int)
$GLOBALS['mosharaf_last_layout']       // name of last layout on the page (string)
```

Use for spacing/padding logic:
```php
if ( 0 === $GLOBALS['mosharaf_section_index'] ) {
    $section_classes[] = 'no-top-padding';
}
```

---

### 2. Responsive Picture / Image
**File:** `inc/helper-functions/responsive-picture.php`

```php
mosharaf_render_responsive_picture( array $image, array $args = [] )
```

**`$image`** — ACF Image field value (return format: Array). Contains `ID`, `url`, `alt`, `sizes`.

**`$args`:**
| Key | Type | Default | Description |
|---|---|---|---|
| `size_group` | string | `null` | Predefined token group — define your token map in `image-sizes.php` per project |
| `mobile_size_group` | string | `null` | Different token group below the mobile breakpoint |
| `mobile_media_query` | string | `'(max-width: 767px)'` | Breakpoint for mobile source swap |
| `lazy` | bool | `true` | `false` for LCP / above-the-fold images |
| `fetchpriority` | string | `'auto'` | `'high'` for LCP images only |
| `class` | string | `''` | CSS class on `<img>` |
| `alt` | string | ACF alt | Override alt text |
| `sizes` | string | `'(max-width: 768px) 100vw, ...'` | HTML `sizes` attribute |
| `echo` | bool | `true` | Echo or return |

**Image size slugs and the `size_group` token map are project-specific.** Define them in `inc/image-sizes.php` for each project. The token map maps group names to registered size slugs, which in turn expand into full srcset ladders.

```php
// Above-the-fold / LCP image
mosharaf_render_responsive_picture( $image, [
    'size_group'     => 'hero',
    'lazy'           => false,
    'fetchpriority'  => 'high',
    'class'          => 'hero-image',
] );

// Lazy-loaded image with mobile swap
mosharaf_render_responsive_picture( $image, [
    'size_group'        => 'media-content',
    'mobile_size_group' => 'card',
    'class'             => 'section-image',
] );
```

---

### 3. Button Renderer
**File:** `inc/helper-functions/button-renderer.php`

```php
mosharaf_render_button( array $button_link, array $args = [] )
```

**`$button_link`** — ACF Link field value (array with `url`, `title`, `target`).

**`$args`:**
| Key | Type | Default | Description |
|---|---|---|---|
| `style` | string | `'btn-primary'` | CSS class added to `.site-btn`. Define per project. |
| `show_icon` | bool | `true` | Show SVG arrow icon |
| `class` | string | `''` | Extra CSS classes |
| `echo` | bool | `true` | Echo or return |

```php
mosharaf_render_buttons( array $buttons, array $args = [] )
```

Renders a repeater of buttons. Each item should have `button_link` and optionally `button_style`.

| Key | Type | Default | Description |
|---|---|---|---|
| `wrapper_class` | string | `'btns'` | CSS class on wrapper `<div>` |
| `default_style` | string | `'btn-primary'` | Fallback style if item has no `button_style` |
| `show_icon` | bool | `true` | Show arrow icon on buttons |
| `echo` | bool | `true` | Echo or return |

---

### 4. Icon Renderer
**File:** `inc/helper-functions/icon-renderer.php`

```php
mosharaf_render_icon( array|int $icon, array $args = [] )
```

Automatically renders SVG inline if the file is an SVG. Falls back to `<img>` for raster formats.

| Key | Type | Default | Description |
|---|---|---|---|
| `class` | string | `''` | CSS class on the SVG or `<img>` |
| `aria_label` | string | `''` | Accessibility label |
| `inline` | bool | `true` | Render SVG inline (enables CSS styling) |

---

### 5. Video Renderer
**File:** `inc/helper-functions/video-renderer.php`

```php
mosharaf_render_video( string|array $video_field, array $args = [] )
```

See `VIDEO-SYSTEM.md` for full documentation, ACF field structure, and examples.

---

### 6. Site Settings
**File:** `inc/helper-functions/site-settings.php`

**Project-specific** — these functions read from ACF Options page fields. ACF Options pages are created and configured directly in the ACF plugin UI (not via code). Add, remove, or modify these functions to match what your project's header/footer actually needs.

```php
// Header — logo
mosharaf_get_site_logo()           : array|false
mosharaf_render_site_logo()        : void

// Header — CTA button
mosharaf_get_header_button()       : array|false
mosharaf_render_header_button()    : void

// Footer — logo (falls back to site_logo if footer_logo not set)
mosharaf_get_footer_logo()         : array|false
mosharaf_render_footer_logo()      : void

// Footer — tagline (short description below logo, not rendered by default)
mosharaf_get_footer_tagline()      : string|false
mosharaf_render_footer_tagline()   : void

// Footer — social icons (repeater: social_icon + social_link)
mosharaf_get_social_medias()       : array|false
mosharaf_render_social_medias()    : void

// Footer — copyright text (supports {year} placeholder)
mosharaf_get_footer_copyright()    : string|false
mosharaf_render_footer_copyright() : void
```

#### Footer menu helper

```php
mosharaf_render_footer_menu( array $args = [] ) : void
```

| Key | Default | Description |
|---|---|---|
| `location` | `''` | Nav menu location slug. Silently exits if location has no menu assigned. |
| `show_title` | `true` | Show the menu name as a heading. Pass `false` for a flat link list. |
| `container_class` | `'footer-menu-column'` | Wrapper `<div>` class |
| `title_class` | `'footer-menu-title'` | Heading `<p>` class |
| `menu_class` | `'footer-menu-list'` | `<ul>` class |
| `echo` | `true` | Echo or return |

**Starter footer default** (`footer.php`) renders one menu with no title:

```php
mosharaf_render_footer_menu( [ 'location' => 'footerMenu', 'show_title' => false ] );
```

**Per-project** — register additional locations in `functions.php` and call the helper once per location. Example for a three-column footer:

```php
// functions.php
register_nav_menus( [
    'mainMenu'    => 'Main Menu',
    'footerMenu'  => 'Footer Menu 1',
    'footerMenu2' => 'Footer Menu 2',
    'footerMenu3' => 'Footer Menu 3',
] );

// footer.php
mosharaf_render_footer_menu( [ 'location' => 'footerMenu',  'show_title' => true ] );
mosharaf_render_footer_menu( [ 'location' => 'footerMenu2', 'show_title' => true ] );
mosharaf_render_footer_menu( [ 'location' => 'footerMenu3', 'show_title' => true ] );
```

#### Starter footer structure

```
footer-top   (background: --mc-color-primary)
  logo                              footer menu (flat, no title)

footer-bottom  (background: --mc-color-secondary)
  copyright text                    social icons
```

All four elements are conditional — if the ACF Options field is empty or the menu location has no menu assigned, that element simply does not render.

---

### 7. Breadcrumb
**File:** `inc/helper-functions/breadcrumb.php`

```php
mosharaf_breadcrumb( bool $layout_padding = false, string $margin_top = 'mt-30', string $margin_bottom = '' )
```

Automatically skips on the front page. Handles posts, pages, archives, categories, tags, taxonomies, search, and 404.

---

### 8. Pagination
**File:** `inc/helper-functions/pagination.php`

```php
mosharaf_render_pagination( WP_Query $query = null )
```

Renders numbered pagination. Defaults to global `$wp_query`.

---

### 9. Post Utilities
**File:** `inc/helper-functions/post-utilities.php`

Open the file to see the full list of post-level helpers.

---

## Background Colour Pattern

Connect an ACF select field to a CSS utility class. Define the choices based on your project's design tokens.

```php
// In your section template:
$bg = get_sub_field( 'background_color' ) ?: 'light';
$section_classes = [ 'my-section', 'bg-mc-' . $bg ];
```

```
// ACF Select field choices (define per project):
light   → White / Light background
subtle  → Light grey background
primary → Brand primary colour background
dark    → Dark background
```

```css
/* CSS (in your project's main CSS): */
.bg-mc-light   { background: var(--mc-color-light); }
.bg-mc-subtle  { background: var(--mc-color-subtle); }
.bg-mc-primary { background: var(--mc-color-primary); }
.bg-mc-dark    { background: var(--mc-color-dark); }
```

---

## Global Grid Utilities

Grid layout classes are defined globally in `assets/css/mosharaf-core-theme-style.css`. **Never redefine grid columns inside a section's own CSS.** Use these classes directly in section templates.

### Base grid class

`.card-grid` sets up the grid container. Always pair it with a `.columns-*` class:

```html
<div class="card-grid columns-3">
    <!-- card items -->
</div>
```

`.card-grid` provides: `display: grid`, `gap: 1.25rem`, `align-items: stretch`.

### Column classes and responsive behaviour

| Class | Desktop | ≤991px | ≤767px |
|---|---|---|---|
| `.columns-2` | 2 columns | 2 columns | 1 column |
| `.columns-3` | 3 columns | 3 columns | 1 column |
| `.columns-4` | 4 columns | 3 columns | 1 column |
| `.columns-5` | 5 columns | 3 columns | 1 column |

### Usage in a section template

```php
<section class="my-section layout-padding">
    <div class="card-grid columns-3">
        <?php foreach ( $items as $item ) : ?>
            <div class="my-card">
                <!-- card content -->
            </div>
        <?php endforeach; ?>
    </div>
</section>
```

### Post card grids (index.php / archive.php)

Use `mosharaf_render_post_card()` inside a `.card-grid.columns-3`. Pass `fetchpriority => 'high'` for the first card (above the fold) and `variant => 'featured'` to mark it for project-specific featured styling:

```php
<div class="blog-grid card-grid columns-3">
    <?php
    $i = 0;
    while ( have_posts() ) :
        the_post();
        $i++;
        mosharaf_render_post_card( null, [
            'variant'       => 1 === $i ? 'featured' : 'default',
            'fetchpriority' => 1 === $i ? 'high' : 'auto',
        ] );
    endwhile;
    ?>
</div>
```

### Spanning a card across all columns (featured card)

To make `.post-card--featured` span the full row, add this to your project CSS:

```css
.blog-grid .post-card--featured {
    grid-column: 1 / -1;
}
```

This is intentionally left out of the starter — column span design varies per project.

---

## ACF Field Groups in This Starter

Six JSON files ship in `acf-json/`. Import them via **WP Admin > Custom Fields > Sync** after activating the theme.

---

### group_flexible_content.json — Page Builder

**Attached to:** page, post, product

The core field group. Contains the `cms` flexible content field that drives the dispatcher. Ships with one starter layout — `example_section` — as a reference. Delete or replace it as you build your project sections.

---

### group_page_settings.json — Page Settings

**Attached to:** all pages

| Field | Type | Purpose |
|---|---|---|
| `show_page_title` | True/False | Show or hide the H1 title in the fallback content template |

Used by `template-parts/content-page.php` when a page has no flexible content layouts.

---

### group_site_settings.json — Site Settings

**Attached to:** Site Settings options page (menu slug: `site-settings`)

| Field | Type | Helper |
|---|---|---|
| `site_logo` | Image | `mosharaf_get_site_logo()` / `mosharaf_render_site_logo()` |
| `header_button` | Link | `mosharaf_get_header_button()` / `mosharaf_render_header_button()` |
| `header_button_mobile_phone` | Text | `mosharaf_get_header_button_mobile_phone()` |
| `footer_logo` | Image | `mosharaf_get_footer_logo()` / `mosharaf_render_footer_logo()` |
| `social_medias` | Repeater (social_icon + social_link) | `mosharaf_get_social_medias()` / `mosharaf_render_social_medias()` |
| `footer_copyright` | Text | `mosharaf_get_footer_copyright()` / `mosharaf_render_footer_copyright()` |

The options page is created by `ui_options_page_696524653c0cd.json` (top-level WP Admin menu item).

> **Per-project:** Add or remove fields to match your header/footer structure. Update `inc/helper-functions/site-settings.php` to match.

---

### group_blog_options.json — Blog Options

**Attached to:** Blog Options options page (menu slug: `blog-options`)

| Field | Type | Purpose |
|---|---|---|
| `blog_hero_title` | Textarea | Blog archive hero heading |
| `blog_hero_description` | Textarea | Blog archive hero body text |
| `blog_hero_media_type` | Button Group | `image` or `video` |
| `blog_hero_image` | Image | Hero image when media type is image |
| `blog_hero_video` | Group | Full video system fields — see `VIDEO-SYSTEM.md` |

The options page is created by `ui_options_page_blog_options.json` (appears under Posts in WP Admin).

> **Per-project:** Remove this group if your project has no blog archive hero.

---

### ui_options_page_696524653c0cd.json — Site Settings Options Page

Defines the **Site Settings** options page in the ACF plugin.

| Property | Value |
|---|---|
| Page title | Site Settings |
| Menu slug | `site-settings` |
| Position | Top-level WP Admin menu |

Without this file synced, the `site-settings` options page won't exist and all `get_field('...', 'options')` calls for site settings will return nothing.

---

### ui_options_page_blog_options.json — Blog Options Page

Defines the **Blog Options** options page.

| Property | Value |
|---|---|
| Page title | Blog Options |
| Menu slug | `blog-options` |
| Position | Under Posts (`edit.php`) |

> **Per-project:** Remove this if your project doesn't need a blog options page.

---

## ACF JSON Sync

- Field groups auto-save to `acf-json/` on every WP Admin save
- Always commit `acf-json/` to version control
- Run Sync in WP Admin when deploying to a new environment
- Never edit `acf-json/*.json` files directly
