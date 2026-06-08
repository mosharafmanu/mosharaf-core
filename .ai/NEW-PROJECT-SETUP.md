# Mosharaf Core — New Project Setup Script

`bin/new-project.sh` renames every starter identifier in a copied theme folder to your project values in a single run.

---

## Workflow

### Step 1 — Copy the starter folder

Copy `mosharaf-core/` to your new project's WordPress themes directory and rename it to your project slug:

```bash
# Example
cp -r /path/to/mosharaf-core /your/wp/wp-content/themes/travelnerds-theme
```

> Do **not** run the script inside the `mosharaf-core` folder itself. Always work on a copy.

### Step 2 — (Recommended) Initialise git

```bash
cd /your/wp/wp-content/themes/travelnerds-theme
git init
git add .
git commit -m "Initial copy from mosharaf-core starter"
```

This gives you a clean rollback point if anything goes wrong.

### Step 3 — Run the bootstrap script

```bash
bash bin/new-project.sh
```

The script walks you through five naming prompts plus a WooCommerce yes/no, shows a preview of all replacements, asks for confirmation, then applies everything in one pass.

---

## What It Prompts For

| Field | Example | Used for |
|---|---|---|
| Theme Name | `Travelnerds Theme` | `style.css` header, README title, display strings |
| Theme Slug | `travelnerds-theme` | Folder convention, enqueue handles, text domain, JSON keys |
| PHP Function Prefix | `travelnerds` | All `mosharaf_` → `travelnerds_` function names |
| CSS Token Prefix | `tn` | All `--mc-` → `--tn-` CSS custom properties and utility classes |
| Text Domain | `travelnerds-theme` | `style.css` Text Domain, `__()` calls, POT file name |
| Include WooCommerce support? | `Y` / `n` | Whether to keep or delete the WooCommerce module (see below) |

Pressing **Enter** accepts the suggested default, which is derived automatically from the Theme Name. The WooCommerce prompt defaults to **Yes** on Enter.

### WooCommerce: keep or remove

Answering **`n`** deletes the entire WooCommerce module as one unit before any renaming runs:

- `inc/woocommerce/` (setup hooks + enqueues)
- `woocommerce/` (template overrides)
- `assets/css/woocommerce/` and `assets/js/woocommerce/` (module CSS/JS)
- `.ai/WOOCOMMERCE.md` (module docs)

No edits to `functions.php` are needed either way — its `require` for the module is wrapped in a `file_exists()` check, so it silently no-ops once the folder is gone. Answering **`y`** (or pressing Enter) leaves everything in place; it stays completely inert until the WooCommerce plugin is actually installed on the project (see `.ai/WOOCOMMERCE.md`).

---

## What the Script Changes

### Global replacements (all .php, .css, .js, .json, .md, .pot files)

| From | To |
|---|---|
| `Mosharaf Core` | Theme Name |
| `mosharaf-core` | Theme Slug |
| `mosharaf_` | `{prefix}_` |
| `--mc-` | `--{css-prefix}-` |
| `bg-mc-` | `bg-{css-prefix}-` |
| `color-mc-` | `color-{css-prefix}-` |
| `'mc-N'` / `"mc-N"` | `'{css-prefix}-N'` (image size slugs) |

### Specific file updates

| File | What changes |
|---|---|
| `style.css` `:root {}` | Token prefix via global `--mc-` replacement |
| `style.css` header | Theme Name, Text Domain (explicit); Theme URI, Author, Author URI, Description cleared for you to fill in |
| `package.json` | `name` via slug replacement; `description` set to Theme Name |
| `README.md` | H1 title, all slug/prefix references |
| `languages/mosharaf-core.pot` | Renamed to `languages/{text-domain}.pot` |
| `.ai/*.md` | All `mosharaf_`, `mosharaf-core`, `--mc-` references |

### What the script does NOT change

- `node_modules/`, `vendor/`, `.git/` — skipped entirely
- `bin/new-project.sh` itself — skipped to avoid self-corruption
- `Theme URI`, `Author`, `Author URI`, `Description` in `style.css` — cleared (blank), you fill in per project
- Google Fonts URL in `functions.php` — you update this manually
- `inc/image-sizes.php` size values — numbers stay, prefix is renamed

---

## Recommended Naming Conventions

Following these conventions keeps multi-project setups clean and avoids collisions.

### Theme Slug

```
client-theme     (preferred)
clientname       (acceptable if short and unique)
client-2025      (acceptable if versioning by year)
```

Rules:
- Lowercase only
- Hyphens for word separation
- No underscores (hyphens are the WordPress convention for slugs)
- Short but descriptive

### PHP Function Prefix

Derived from the slug: replace hyphens with underscores.

```
travelnerds-theme  →  travelnerds_
acme-corp          →  acme_
studio-hq          →  studio_hq_
```

Rules:
- Lowercase only
- Underscores only (no hyphens — PHP identifiers don't allow them)
- Unique per project to avoid collisions with plugins

### CSS Token Prefix

Short abbreviation — 2–4 characters:

```
travelnerds-theme  →  tn
acme-corp          →  ac
studio-hq          →  shq
purple-surgical    →  ps
```

Rules:
- Lowercase only
- No hyphens or underscores
- Short: you type `--tn-color-primary` many times
- Unique per project (avoid common two-letter combos like `wp`, `wc`, `cf`)

### Text Domain

Almost always matches the Theme Slug:

```
travelnerds-theme
acme-corp
studio-hq
```

Only differs from the slug if the project requires a specific localisation key for existing translations.

---

## After the Bootstrap Script

Once the script completes, do these five things before writing any project code:

1. **Fill in `style.css` header** — Theme URI, Author, Author URI, Description
2. **Update Google Fonts URL** in `functions.php` → `wp_enqueue_style('{slug}-fonts', 'URL', ...)`
3. **Set token values** in `style.css` `:root {}` — 7 hex colours, font names, container width
4. **Define image sizes** in `inc/image-sizes.php` based on the project layout grid
5. **Run ACF Sync** in WP Admin → Custom Fields after activating the theme

See `.ai/NEW-PROJECT-CHECKLIST.md` for the complete step-by-step setup guide.

---

## Troubleshooting

**Script says "No style.css found"**
You are not running the script from the theme root. `cd` into the theme folder first, then run `bash bin/new-project.sh`.

**Script says Theme Slug cannot be 'mosharaf-core'**
You are running the script inside the original starter folder. Copy the folder to a new location first.

**Some files still show old identifiers after running**
The script reports a list of files with remaining matches. This usually means a file was added after the starter was created. Run the specific replacement manually:
```bash
perl -pi -e 's/mosharaf-core/your-slug/g' path/to/the/file.php
```

**Accidentally ran the script on the wrong folder**
If you initialised git in Step 2, you can roll back with:
```bash
git checkout .
```
If you didn't, copy the original `mosharaf-core` starter again and start fresh.
