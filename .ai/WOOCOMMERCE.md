# Mosharaf Core — WooCommerce Integration

WooCommerce support is built into the starter. It is **inactive until WooCommerce is installed** — all theme support declarations, style enqueuing, and hook manipulations are gated on `class_exists('WooCommerce')` or via the WC-specific filter/action system.

The whole integration is one self-contained, removable module:

| Piece | Location |
|---|---|
| Setup (theme support, hooks, enqueues) | `inc/woocommerce/woocommerce-setup.php` — required from `functions.php` via a `file_exists()`-guarded `require` |
| Template overrides | `woocommerce/` |
| CSS | `assets/css/woocommerce/` |
| JS | `assets/js/woocommerce/` |
| Docs | `.ai/WOOCOMMERCE.md` (this file) |

For a non-ecommerce project, delete all five and `functions.php` needs no further edits — the guarded `require` skips itself when the setup file is gone. `bin/new-project.sh` offers to do this for you during bootstrap.

---

## What Ships in the Starter

### inc/woocommerce/woocommerce-setup.php

| What | How |
|---|---|
| Theme support | `woocommerce`, `wc-product-gallery-zoom`, `wc-product-gallery-lightbox`, `wc-product-gallery-slider` |
| Default WC styles removed | `woocommerce_enqueue_styles` returns `[]` |
| Our WC CSS enqueued | `mosharaf-core-woocommerce.css`, after `mosharaf-core-starter-style` |
| Default content wrappers replaced | `woocommerce_output_content_wrapper` / `_end` removed on `init:20`, replaced with `<div class="wc-main layout-padding">` |
| Sidebar removed | `woocommerce_get_sidebar` removed from `woocommerce_sidebar` on `init:20` |

### Template overrides (`woocommerce/`)

| File | Overrides | Purpose |
|---|---|---|
| `archive-product.php` | WC default | Shop/category page — clean HTML, our wrapper, no sidebar |
| `single-product.php` | WC default | Single product page — clean HTML, our wrapper |
| `loop/loop-start.php` | WC default `<ul class="products">` | Uses `<div class="products card-grid columns-{n}">` |
| `loop/loop-end.php` | WC default `</ul>` | Closes the div |
| `content-product.php` | WC default | Product card with `.wc-product-card` HTML structure |

### CSS (`assets/css/woocommerce/mosharaf-core-woocommerce.css`)

Fully replaces WC default styles. Uses only `var(--mc-*)` tokens. Sections:

| Section | Covers |
|---|---|
| Layout | `.wc-main` wrapper, `.wc-archive-title` |
| Notices | `.woocommerce-message` (green), `.woocommerce-error` (red), `.woocommerce-info` (primary) |
| Buttons | All WC `.button` variants mapped to our primary button style |
| Breadcrumb | WC breadcrumb trail |
| Ordering bar | Result count + sort-by select |
| Product card | `.wc-product-card` — image, body, footer, hover effect |
| Sale badge | `.onsale` — accent color pill, top-left of image |
| Star rating | `.star-rating` |
| Price | `.price`, `del` (original), `ins` (sale) |
| Single product | Two-column grid: gallery + summary; quantity + add to cart |
| Product tabs | Description / reviews tab bar |
| Cart | Table + totals side panel |
| Checkout | Two-column: fields left, order review right |
| My account | Sidebar nav + content area |
| Responsive | Collapses all two-column layouts at ≤991px |

---

## Shop Archive Layout & Filters (optional)

A second, **independently removable** layer over the stock shop/category archive: replaces the
default top-to-bottom layout with a hero, a sticky category-filter sidebar, a result-count/ordering
toolbar, and the product grid — all wired through hooks, no AJAX. Filter clicks rewrite the URL with
a `filter_category` query argument and reload; `pre_get_posts` turns that into a `tax_query`.

| Piece | Location |
|---|---|
| Hooks | `inc/woocommerce/shop-archive.php` — required from `woocommerce-setup.php` |
| Templates | `inc/woocommerce/templates/shop-hero.php`, `shop-toolbar.php`, `shop-filters.php` |
| CSS | `assets/css/woocommerce/mosharaf-core-shop-archive.css` |
| JS | `assets/js/woocommerce/mosharaf-core-shop-archive.js` |
| Icons | `assets/svgs/filter-icon.php`, `close-icon.php` |

**To remove** (leaving the stock WooCommerce archive layout, still themed by
`mosharaf-core-woocommerce.css`): delete the require line for `shop-archive.php` in
`woocommerce-setup.php`, the hooks file itself, the three `shop-*.php` templates, and the
`mosharaf-core-shop-archive` CSS/JS pair.

### Layout

```
.shop-page-wrapper.layout-padding
└── .shop-grid                      (CSS grid: header / sidebar / content areas)
    ├── .shop-toolbar               (grid-area: header — filter toggle, result count, ordering)
    ├── aside.shop-sidebar          (grid-area: sidebar — sticky category filter tree)
    └── .shop-content               (grid-area: content — product grid + pagination)
```

`shop-hero.php` (title, optional description, optional image) prints separately, above
`.shop-page-wrapper`, via `woocommerce_before_main_content` priority 12.

### Behaviour

- **Filter sidebar**: parent/child `product_cat` tree as buttons; clicking sets/clears
  `?filter_category=slug,slug` and reloads. Active state mirrors both the URL parameter and
  (on category pages) the queried term.
- **Toggle**: desktop collapses the sidebar via `.shop-grid.filters-hidden`; mobile (≤1199px)
  opens it as a fixed overlay via `.shop-grid.filters-visible` + `body.no-scroll`.
- **Result count**: relocated from `.result-count-wrapper` to `.result-count-wrapper-mobile`
  below ≤1199px by `mosharaf-core-shop-archive.js`.
- **Pagination**: WC's default is removed; `mosharaf_render_pagination()` is used instead, so
  shop pagination matches blog/archive pagination styling.
- **Flexible content**: `mosharaf_load_shop_flexible_content()` prints the `cms` field below the
  grid — from the queried category term if it has its own content, otherwise from the Shop page.

### Note on the product grid

The grid itself is **not** styled by this piece — it uses the theme-wide `.card-grid`/`.columns-N`
system already wired up by `woocommerce/loop/loop-start.php` (see Template overrides above), so
shop columns stay visually consistent with every other card grid in the theme.

### Per-project customisation

- **Hero image source**: main shop page uses the Shop page's featured image; category/tag
  archives use the term's `hero_image` ACF field (define it on the `product_cat`/`product_tag`
  field group), falling back to the term's taxonomy thumbnail.
- **Sidebar width / breakpoints / colours**: all in `mosharaf-core-shop-archive.css`, using only
  `var(--mc-*)` tokens — adjust per the project's design grid.
- **Filter taxonomy**: hardcoded to `product_cat`. To filter by a different taxonomy (brand,
  attribute, etc.), adapt `mosharaf_filter_shop_products_by_category()` and `shop-filters.php`.

---

## Product Card Structure

Defined in `woocommerce/content-product.php`. `wc_product_class()` adds WC's default product classes alongside our own.

```html
<div class="wc-product-card product type-{type} status-publish ...">

    <a class="wc-product-card__image" href="...">
        <span class="onsale">Sale!</span>          <!-- conditional -->
        <img ...>                                  <!-- WC thumbnail -->
    </a>

    <div class="wc-product-card__body">
        <h2 class="woocommerce-loop-product__title">
            <a href="...">Product Name</a>
        </h2>
        <!-- star rating (conditional) -->
        <!-- price with del/ins for sale products -->
    </div>

    <div class="wc-product-card__footer">
        <!-- Add to Cart / View Product button -->
    </div>

</div>
```

**Per-project customisation:** Edit `content-product.php` to add category badges, short description excerpts, custom badges, or hover overlays. Keep `wc_product_class()` on the outer div — plugins rely on those classes.

---

## Per-Project WooCommerce Setup

After activating WooCommerce on a project:

1. **Set product image sizes** in `inc/image-sizes.php`:
   ```php
   // Recommended for 3-column grid at 94.5rem container
   add_image_size( 'mc-product-card', 600, 600, true );
   add_image_size( 'mc-product-single', 900, 900, false );
   ```

2. **Update WC image settings** in WP Admin > WooCommerce > Settings > Products > Images to match the sizes above.

3. **Regenerate thumbnails** after changing image sizes (WP-CLI: `wp media regenerate` or use Regenerate Thumbnails plugin).

4. **Review the color tokens** — the WC CSS inherits everything from `--mc-*`. No hex values should need changing unless the project design diverges from the tokens.

5. **Product card columns** — the column count is set by WC's loop prop. To hardcode 3 columns on all archives, add to `inc/woocommerce/woocommerce-setup.php`:
   ```php
   add_filter( 'loop_shop_columns', fn() => 3 );
   ```

6. **Remove WooCommerce Gutenberg blocks** — already blocked globally via `use_block_editor_for_post_type` filter in `functions.php`.

---

## Extending Per Project

### Cart & Checkout Template Overrides

Cart and checkout templates are **not overridden in the starter** due to their complexity. To override them per project, copy from the WC plugin directory:

```
wp-content/plugins/woocommerce/templates/cart/cart.php
wp-content/plugins/woocommerce/templates/checkout/form-checkout.php
```

→ into your theme:

```
woocommerce/cart/cart.php
woocommerce/checkout/form-checkout.php
```

Check the WC template version comment at the top before overriding — stale overrides break on WC updates.

### My Account Template Overrides

```
wp-content/plugins/woocommerce/templates/myaccount/
→ woocommerce/myaccount/
```

### Adding a Shop Hero Above the Product Grid

Add to `woocommerce/archive-product.php`, before `do_action('woocommerce_before_main_content')`:

```php
// Above the wc-main wrapper — full width
get_template_part( 'template-parts/sections/shop_hero' );
```

Or add a hero inside the wrapper after the archive description hook:
```php
do_action( 'woocommerce_archive_description' );
// Insert your hero HTML or template part here
do_action( 'woocommerce_before_shop_loop' );
```

### Featured Product Spanning Full Row

To make a "featured" product card span all 3 columns (e.g. the first product):

```css
/* In custom.css or your project CSS */
.products.card-grid .product:first-child {
    grid-column: 1 / -1;
}
```

Add logic in `content-product.php` to add a `product--featured` class to the first item, then target that class.

### Variable Product Add-to-Cart

Variable products show a "Select options" button instead of "Add to cart" in the loop — this is WC default behaviour and works with our button styling. No changes needed.

---

## ACF + WooCommerce

If a project needs ACF fields on products, add to `group_flexible_content.json` post types list or create a new field group targeting `product` post type.

The dispatcher (`mosharaf_flexible_content('cms')`) can be called from a section within the single product template if a project needs flexible layouts on product pages. Add it after the WC product data section or in a custom tab.
