#!/usr/bin/env bash
# =============================================================
#  Mosharaf Core — New Project Bootstrap
#
#  Run from the theme root AFTER copying mosharaf-core into
#  your new project folder and renaming the folder:
#
#    bash bin/new-project.sh
#
#  This script renames all starter identifiers (function prefix,
#  text domain, CSS token prefix, slugs, display name) throughout
#  the entire theme in a single pass.
#
#  ⚠  Files are modified in-place. Commit or copy before running.
# =============================================================

set -eo pipefail

# ── Terminal colours ─────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; DIM='\033[2m'; RESET='\033[0m'

# ── Resolve paths ────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(dirname "$SCRIPT_DIR")"
CHANGED=0        # files modified counter
WARNINGS=0       # warning counter

# ── Helpers ──────────────────────────────────────────────────
step()    { echo -e "\n${BOLD}${CYAN}▸ $1${RESET}"; }
ok()      { echo -e "  ${GREEN}✓${RESET}  $1"; }
skipped() { echo -e "  ${DIM}–  $1${RESET}"; }
warn()    { echo -e "  ${YELLOW}⚠${RESET}  $1"; WARNINGS=$((WARNINGS + 1)); }
die()     { echo -e "\n${RED}ERROR: $1${RESET}\n"; exit 1; }

# ── Input prompt with optional default ───────────────────────
prompt() {
    local label="$1" varname="$2" default="$3" value
    if [[ -n "$default" ]]; then
        read -rp "  ${BOLD}$label${RESET} [${DIM}${default}${RESET}]: " value
        eval "$varname=\"${value:-$default}\""
    else
        while true; do
            read -rp "  ${BOLD}$label${RESET}: " value
            [[ -n "$value" ]] && break
            echo "    Required — please enter a value."
        done
        eval "$varname=\"$value\""
    fi
}

# ── Validate identifier characters ───────────────────────────
validate() {
    local value="$1" pattern="$2" label="$3"
    [[ "$value" =~ $pattern ]] || die "$label '$value' is invalid. $4"
}

# ── Replace a fixed string in-place across a list of files ───
# Uses perl for macOS + Linux compatibility.
# Returns the number of files where a replacement was made.
replace_in_files() {
    local old="$1" new="$2"; shift 2
    local files=("$@") count=0
    for f in "${files[@]}"; do
        if [[ -f "$f" ]] && grep -qF -- "$old" "$f" 2>/dev/null; then
            perl -pi -e "s/\Q${old}\E/${new}/g" "$f" \
                || { warn "perl failed on: $f"; continue; }
            count=$((count + 1))
        fi
    done
    echo "$count"
}

# ── Collect all processable files (excluding system dirs) ────
collect_files() {
    find "$THEME_DIR" \
        -not -path "*/node_modules/*" \
        -not -path "*/vendor/*" \
        -not -path "*/.git/*" \
        -not -path "*/bin/new-project.sh" \
        \( \
            -name "*.php" -o -name "*.css" -o -name "*.js" \
            -o -name "*.json" -o -name "*.md" -o -name "*.pot" \
        \) \
        -type f
}

# =============================================================
#  BANNER
# =============================================================
echo ""
echo -e "${BOLD}  Mosharaf Core — New Project Bootstrap${RESET}"
echo -e "  ${DIM}$(date '+%Y-%m-%d %H:%M')${RESET}"
echo ""
echo -e "  Theme directory: ${CYAN}$THEME_DIR${RESET}"
echo ""

# Guard: must look like a WordPress theme
[[ -f "$THEME_DIR/style.css" ]] || die "No style.css found. Run from the theme root directory."
[[ -f "$THEME_DIR/functions.php" ]] || die "No functions.php found. Run from the theme root directory."

# =============================================================
#  COLLECT INPUT
# =============================================================
step "Project details"
echo "  Fill in the values for your new project."
echo "  Press Enter to accept a suggested default."
echo ""

prompt "Theme Name"         THEME_NAME    ""
prompt "Theme Slug"         THEME_SLUG    "$(echo "$THEME_NAME" | tr '[:upper:]' '[:lower:]' | tr ' ' '-' | tr -dc 'a-z0-9-')"
prompt "PHP Function Prefix" PHP_PREFIX   "$(echo "$THEME_SLUG" | tr '-' '_')"
prompt "CSS Token Prefix"   CSS_PREFIX    "$(echo "$THEME_SLUG" | tr -dc 'a-z0-9' | cut -c1-4)"
prompt "Text Domain"        TEXT_DOMAIN   "$THEME_SLUG"

echo ""
read -rp "  ${BOLD}Include WooCommerce support?${RESET} (Y/n): " WC_ANSWER
[[ "$WC_ANSWER" =~ ^[Nn]$ ]] && INCLUDE_WOOCOMMERCE=0 || INCLUDE_WOOCOMMERCE=1

echo ""

# ── Validate ─────────────────────────────────────────────────
validate "$THEME_SLUG"  '^[a-z][a-z0-9-]+$'  "Theme Slug"         "Use lowercase letters, numbers, and hyphens only."
validate "$PHP_PREFIX"  '^[a-z][a-z0-9_]+$'  "PHP Function Prefix" "Use lowercase letters, numbers, and underscores only."
validate "$CSS_PREFIX"  '^[a-z][a-z0-9]+$'   "CSS Token Prefix"   "Use lowercase letters and numbers only (no hyphens)."
validate "$TEXT_DOMAIN" '^[a-z][a-z0-9-]+$'  "Text Domain"        "Use lowercase letters, numbers, and hyphens only."

# Safety check: refuse to run on the original starter folder
if [[ "$THEME_SLUG" == "mosharaf-core" ]]; then
    die "Theme Slug cannot be 'mosharaf-core'. Copy the folder first, then run this script inside the copy."
fi

# =============================================================
#  PREVIEW + CONFIRM
# =============================================================
echo ""
echo -e "  ${BOLD}Replacements that will be applied:${RESET}"
echo ""
printf "  %-28s →  %s\n" "Mosharaf Core"     "$THEME_NAME"
printf "  %-28s →  %s\n" "mosharaf-core"      "$THEME_SLUG"
printf "  %-28s →  %s\n" "mosharaf_"          "${PHP_PREFIX}_"
printf "  %-28s →  %s\n" "--mc-"              "--${CSS_PREFIX}-"
printf "  %-28s →  %s\n" "bg-mc-  color-mc-"  "bg-${CSS_PREFIX}-  color-${CSS_PREFIX}-"
printf "  %-28s →  %s\n" "'mc-N'  \"mc-N\""  "'${CSS_PREFIX}-N'  \"${CSS_PREFIX}-N\""
printf "  %-28s →  %s\n" "Text Domain"        "$TEXT_DOMAIN"
printf "  %-28s →  %s\n" "POT file rename"    "languages/${TEXT_DOMAIN}.pot"
if [[ "$INCLUDE_WOOCOMMERCE" -eq 0 ]]; then
    printf "  %-28s →  %s\n" "WooCommerce module"  "REMOVED (inc/woocommerce/, woocommerce/, assets/{css,js}/woocommerce/, .ai/WOOCOMMERCE.md)"
else
    printf "  %-28s →  %s\n" "WooCommerce module"  "kept (inactive until the WooCommerce plugin is installed)"
fi
echo ""
echo -e "  ${YELLOW}⚠  Files are modified in-place. Ensure you have a backup.${RESET}"
echo ""
read -rp "  Proceed? (y/N): " CONFIRM
[[ "$CONFIRM" =~ ^[Yy]$ ]] || { echo -e "\n  Aborted."; exit 0; }

# =============================================================
#  WOOCOMMERCE MODULE
# =============================================================
if [[ "$INCLUDE_WOOCOMMERCE" -eq 0 ]]; then
    step "Removing WooCommerce module"
    WC_PATHS=(
        "$THEME_DIR/inc/woocommerce"
        "$THEME_DIR/woocommerce"
        "$THEME_DIR/assets/css/woocommerce"
        "$THEME_DIR/assets/js/woocommerce"
        "$THEME_DIR/.ai/WOOCOMMERCE.md"
    )
    for p in "${WC_PATHS[@]}"; do
        if [[ -e "$p" ]]; then
            rm -rf "$p"
            ok "Removed ${p#"$THEME_DIR"/}"
            CHANGED=$((CHANGED + 1))
        else
            skipped "${p#"$THEME_DIR"/} not found — already absent"
        fi
    done
    ok "functions.php require is file_exists()-guarded — no edit needed"
else
    step "WooCommerce module"
    skipped "Kept as-is — inactive until the WooCommerce plugin is installed (see .ai/WOOCOMMERCE.md)"
fi

# =============================================================
#  GATHER FILES
# =============================================================
step "Collecting files"

# `mapfile` needs bash 4+; macOS ships bash 3.2 (no mapfile, no namerefs),
# so read lines into arrays portably with plain while-read loops instead.
ALL_FILES=()
while IFS= read -r _file; do
    ALL_FILES+=( "$_file" )
done < <(collect_files)
echo "  Found ${#ALL_FILES[@]} files to process"

# Separate CSS files for CSS-specific replacements
CSS_FILES=()
while IFS= read -r _file; do
    CSS_FILES+=( "$_file" )
done < <(printf '%s\n' "${ALL_FILES[@]}" | grep '\.css$')

# PHP-only files for image-size slug replacement
PHP_FILES=()
while IFS= read -r _file; do
    PHP_FILES+=( "$_file" )
done < <(printf '%s\n' "${ALL_FILES[@]}" | grep '\.php$')

# =============================================================
#  REPLACEMENTS
# =============================================================
step "Applying replacements"

# 1. Display name — must come BEFORE slug replacement to avoid
#    double-processing "Mosharaf Core" → "{slug}" chains.
n=$(replace_in_files "Mosharaf Core" "$THEME_NAME" "${ALL_FILES[@]}")
ok "Display name 'Mosharaf Core' → '$THEME_NAME'  ($n files)"
CHANGED=$((CHANGED + n))

# 2. Slug — covers text domain, enqueue handles, URLs, ACF JSON keys
n=$(replace_in_files "mosharaf-core" "$THEME_SLUG" "${ALL_FILES[@]}")
ok "Slug 'mosharaf-core' → '$THEME_SLUG'  ($n files)"
CHANGED=$((CHANGED + n))

# 3. PHP function prefix
n=$(replace_in_files "mosharaf_" "${PHP_PREFIX}_" "${ALL_FILES[@]}")
ok "PHP prefix 'mosharaf_' → '${PHP_PREFIX}_'  ($n files)"
CHANGED=$((CHANGED + n))

# 4. CSS custom property prefix
n=$(replace_in_files "--mc-" "--${CSS_PREFIX}-" "${ALL_FILES[@]}")
ok "CSS var prefix '--mc-' → '--${CSS_PREFIX}-'  ($n files)"
CHANGED=$((CHANGED + n))

# 5. CSS background utility classes
n=$(replace_in_files "bg-mc-" "bg-${CSS_PREFIX}-" "${ALL_FILES[@]}")
ok "CSS class 'bg-mc-' → 'bg-${CSS_PREFIX}-'  ($n files)"
CHANGED=$((CHANGED + n))

# 6. CSS text colour utility classes
n=$(replace_in_files "color-mc-" "color-${CSS_PREFIX}-" "${ALL_FILES[@]}")
ok "CSS class 'color-mc-' → 'color-${CSS_PREFIX}-'  ($n files)"
CHANGED=$((CHANGED + n))

# 7. PHP image-size slug references (single-quoted strings)
n=$(replace_in_files "'mc-" "'${CSS_PREFIX}-" "${PHP_FILES[@]}")
ok "PHP image sizes 'mc-N' → '${CSS_PREFIX}-N'  ($n files)"
CHANGED=$((CHANGED + n))

# 8. PHP image-size slug references (double-quoted strings)
n=$(replace_in_files "\"mc-" "\"${CSS_PREFIX}-" "${PHP_FILES[@]}")
[[ $n -gt 0 ]] && ok "PHP image sizes \"mc-N\" → \"${CSS_PREFIX}-N\"  ($n files)" \
    || skipped "Double-quoted mc- image sizes: none found"
CHANGED=$((CHANGED + n))

# =============================================================
#  STYLE.CSS HEADER — explicit field updates
# =============================================================
step "Updating style.css header"
STYLE_CSS="$THEME_DIR/style.css"

# Text Domain may differ from slug — update explicitly
if [[ "$TEXT_DOMAIN" != "$THEME_SLUG" ]]; then
    perl -pi -e "s/Text Domain: \Q$THEME_SLUG\E/Text Domain: $TEXT_DOMAIN/" "$STYLE_CSS"
    ok "Text Domain set to '$TEXT_DOMAIN' (differs from slug)"
else
    ok "Text Domain already set to '$TEXT_DOMAIN' via slug replacement"
fi

# Clear author/URI fields (starter-specific, user fills in per project)
perl -pi -e 's/^(Theme URI:\s*).*/$1/' "$STYLE_CSS"
perl -pi -e 's/^(Author:\s*).*/$1/' "$STYLE_CSS"
perl -pi -e 's/^(Author URI:\s*).*/$1/' "$STYLE_CSS"
perl -pi -e 's/^(Description:\s*).*/$1/' "$STYLE_CSS"
perl -pi -e 's/^Version:\s*.*/Version: 1.0.0/' "$STYLE_CSS"
ok "Cleared Theme URI, Author, Author URI, Description (fill in per project)"

# =============================================================
#  PACKAGE.JSON
# =============================================================
step "Updating package.json"
PKG="$THEME_DIR/package.json"
if [[ -f "$PKG" ]]; then
    # Name already replaced by slug step; update description
    perl -pi -e "s|\"description\":\s*\"[^\"]*\"|\"description\": \"$THEME_NAME\"|" "$PKG"
    ok "package.json description set to '$THEME_NAME'"
else
    warn "package.json not found — skipped"
fi

# =============================================================
#  README.md
# =============================================================
step "Updating README.md"
README="$THEME_DIR/README.md"
if [[ -f "$README" ]]; then
    # Global replacements already handled mosharaf_ and mosharaf-core.
    # Update the H1 title if it still contains the old name (edge case).
    if grep -q "^# Mosharaf Core\b" "$README" 2>/dev/null; then
        perl -pi -e "s/^# Mosharaf Core\b/# $THEME_NAME/" "$README"
        ok "README.md H1 updated"
    else
        ok "README.md already updated by global replacements"
    fi
else
    skipped "README.md not found"
fi

# =============================================================
#  LANGUAGE POT FILE
# =============================================================
step "Renaming language file"
LANG_DIR="$THEME_DIR/languages"
OLD_POT="$LANG_DIR/mosharaf-core.pot"
NEW_POT="$LANG_DIR/${TEXT_DOMAIN}.pot"

if [[ -f "$OLD_POT" ]]; then
    mv "$OLD_POT" "$NEW_POT"
    ok "Renamed: mosharaf-core.pot → ${TEXT_DOMAIN}.pot"
    CHANGED=$((CHANGED + 1))
elif [[ -f "$NEW_POT" ]]; then
    ok "POT file already named '${TEXT_DOMAIN}.pot' (slug replacement renamed it)"
else
    warn "No POT file found in languages/ — skipped"
fi

# =============================================================
#  FINAL VERIFICATION
# =============================================================
step "Verification"

REMAINING=$(grep -rl "mosharaf-core\|mosharaf_\|Mosharaf Core\|--mc-\|bg-mc-\|color-mc-" \
    "$THEME_DIR" \
    --include="*.php" --include="*.css" --include="*.js" \
    --include="*.json" --include="*.md" \
    2>/dev/null | grep -v "node_modules\|vendor\|\.git\|bin/new-project.sh" \
    | wc -l | tr -d ' ')

if [[ "$REMAINING" -eq 0 ]]; then
    ok "No starter identifiers remain in active files"
else
    warn "$REMAINING file(s) still contain starter identifiers — manual review needed"
    grep -rl "mosharaf-core\|mosharaf_\|Mosharaf Core\|--mc-\|bg-mc-\|color-mc-" \
        "$THEME_DIR" \
        --include="*.php" --include="*.css" --include="*.js" \
        --include="*.json" --include="*.md" \
        2>/dev/null | grep -v "node_modules\|vendor\|\.git\|bin/new-project.sh" \
        | sed "s|$THEME_DIR/||" | while read -r f; do
        echo -e "    ${YELLOW}${f}${RESET}"
    done
fi

# =============================================================
#  SUMMARY
# =============================================================
echo ""
echo -e "  ${BOLD}────────────────────────────────────────────${RESET}"
echo -e "  ${GREEN}${BOLD}Bootstrap complete${RESET}"
echo -e "  ${BOLD}Theme:${RESET}    $THEME_NAME"
echo -e "  ${BOLD}Slug:${RESET}     $THEME_SLUG"
echo -e "  ${BOLD}Prefix:${RESET}   ${PHP_PREFIX}_  /  --${CSS_PREFIX}-"
echo -e "  ${BOLD}Domain:${RESET}   $TEXT_DOMAIN"
echo -e "  ${BOLD}Modified:${RESET} $CHANGED files touched"
if [[ $WARNINGS -gt 0 ]]; then
    echo -e "  ${YELLOW}${BOLD}Warnings:${RESET} $WARNINGS — review items marked ⚠ above"
fi
echo -e "  ${BOLD}────────────────────────────────────────────${RESET}"
echo ""
echo "  Next steps:"
echo "   1. Fill in Theme URI, Author, Author URI, Description in style.css"
echo "   2. Update Google Fonts URL in functions.php"
echo "   3. Set token values in the :root {} block in style.css"
echo "   4. Define image sizes in inc/image-sizes.php"
echo "   5. Configure ACF options pages in inc/acf-options.php"
echo "   6. Run ACF Sync in WP Admin after activating the theme"
echo ""
echo -e "  ${DIM}See .ai/NEW-PROJECT-CHECKLIST.md for the full setup guide.${RESET}"
echo ""
