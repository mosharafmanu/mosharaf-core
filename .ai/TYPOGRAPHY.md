# Mosharaf Core — Content Typography & Single Post

---

## Content Typography — `.entry-content`

The `.entry-content` class scopes all rich-text output from `the_content()`. It is not tied to any specific template — apply it anywhere you render `the_content()` to inherit these styles.

**CSS file:** `assets/css/mosharaf-core-design-style.css` — "CONTENT TYPOGRAPHY" section.

### What it styles

| Element | Behaviour |
|---|---|
| `p` | `margin-bottom: 1.25rem` |
| `p:has(> img)` | `margin-bottom: 1.75rem` — classic editor wraps images in `<p>` |
| `* + h2` | `margin-top: 2.5rem` — adjacent sibling only, so the first heading gets no extra space |
| `* + h3` | `margin-top: 2rem` |
| `* + h4/h5/h6` | `margin-top: 1.5rem` |
| `h2 + h3`, `h3 + h4` etc. | `margin-top: 0.75rem` — tighter when a sub-heading directly follows its parent |
| `ul`, `ol` | `padding-left: 1.5rem`, `margin-bottom: 1.25rem` |
| `blockquote` | Primary left border, subtle bg, italic, cite prefixed with `— ` |
| `:not(pre) > code` | Primary color, subtle bg, monospace |
| `pre` | Dark bg, subtle text, overflow-x auto |
| `pre code` | Inherits pre — resets the inline code overrides |
| `table` | Full-width, bordered, striped `<thead>`, hover rows |
| `hr` | Mid color, 20% opacity |
| `figure` | `margin: 1.75rem 0` |
| `figcaption` | Italic, mid color, centered |
| `img` | `max-width: 100%`, `height: auto`, small border radius |
| `.alignleft` | Float left with right + bottom margin |
| `.alignright` | Float right with left + bottom margin |
| `.aligncenter`, `.alignnone` | Block, auto margins |
| `a:not(.site-btn)` | Primary color — does not override `.site-btn` links |
| `kbd` | Dark bg, monospace, subtle drop-shadow |
| `mark` | Accent highlight |

### Where it is used

- `template-parts/content-post.php` — `<div class="entry-content mt-50 mt-md-60">`
- `template-parts/content-page.php` — `<div class="entry-content">`
- `template-parts/content.php` — fallback, same pattern

Add `.entry-content` to any custom section or template that outputs `the_content()` and you get all of the above for free.

---

## Single Post Template

**Template file:** `template-parts/content-post.php`  
**Loaded by:** `single.php` via `get_template_part('template-parts/content', 'post')`

### HTML structure

```
<article class="single-post">                            ← post_class() adds WP classes
  <div class="post-thumbnail">                           ← full-width, max-height: 34rem (18rem mobile)
    <picture> / <img class="post-thumbnail-image">       ← mosharaf_render_responsive_picture(), LCP
  </div>

  <div class="post-inner layout-padding">                ← max-width: 50rem, margin: auto
    <header class="entry-header pt-50 …">
      <div class="entry-categories mb-20">               ← conditional, hidden if no categories
        <a class="entry-category">Category Name</a>
      </div>
      <h1 class="entry-title">…</h1>
      <div class="entry-meta mt-20">
        <time class="entry-date">…</time>
        <span class="entry-author">By …</span>
      </div>
    </header>

    <div class="entry-content mt-50 mt-md-60">
      <?php the_content(); ?>
    </div>

    <footer class="entry-footer pb-30">                  ← conditional, hidden if no tags
      <div class="post-tags">
        <a class="post-tag">#tag-name</a>
      </div>
    </footer>
  </div>
</article>
```

### Key CSS classes and where they live

**`assets/css/mosharaf-core-starter-style.css` — "Single Post" section:**

| Class | Purpose |
|---|---|
| `.post-inner` | `max-width: 50rem` — constrains post body to a comfortable reading width |
| `.single-post .post-thumbnail` | Full-width, `max-height: 34rem` (`18rem` on mobile) |
| `.single-post .post-thumbnail-image` | `object-fit: cover`, fills the thumbnail container |
| `.entry-header` | `padding-bottom: 2rem`, subtle border-bottom |
| `.entry-title` | `line-height: 1.15`, `margin-bottom: 0` |
| `.entry-categories` | Flex row, `gap: 0.375rem` |
| `.entry-category` | Pill: uppercase, primary color, fills primary on hover |
| `.entry-meta` | Flex row, mid color, date + author |
| `.entry-footer` | Subtle border-top, `padding-top: 1.75rem` |
| `.post-tags` | Flex wrap, `gap: 0.5rem` |
| `.post-tag` | Outline style, `#` prefix via `::before`, turns primary on hover |

### Featured image rendering

The featured image uses `mosharaf_render_responsive_picture()` with `fetchpriority: 'high'` and `lazy: false` — it is always above the fold on a single post. Falls back to `the_post_thumbnail('mc-1200')` if the helper is unavailable.

```php
mosharaf_render_responsive_picture(
    [
        'ID'  => $thumbnail_id,
        'url' => wp_get_attachment_url( $thumbnail_id ),
        'alt' => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ?: get_the_title(),
    ],
    [
        'sizes'         => '100vw',
        'fetchpriority' => 'high',
        'lazy'          => false,
        'class'         => 'post-thumbnail-image',
    ]
);
```

---

## Template Hierarchy for Post Content

```
single.php
  └── get_template_part('template-parts/content', 'post')
        ├── content-post.php   ← loads first (specific match)
        └── content.php        ← fallback if content-post.php is missing
```

**Always edit `content-post.php` for single post changes**, not `content.php`. WordPress resolves `content-post.php` before `content.php` — if both exist, `content-post.php` always wins for post post-types.

---

## Page Content Template

**Template file:** `template-parts/content-page.php`  
**Loaded by:** `page.php`

Static WordPress Pages (About, Contact, etc.) use this template. It respects the `show_page_title` ACF True/False field from `group_page_settings.json` — toggle it per page in WP Admin to show or hide the `<h1>`.

The `.entry-content` class is applied to the content wrapper here too, so all content typography styles apply identically to page content as to post content.
