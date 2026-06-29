# Mosharaf Core — Video System

The video renderer helper (`inc/helper-functions/video-renderer.php`) provides a unified interface for multiple video sources and playback behaviours.

---

## Supported Video Sources

The source type is set explicitly via the `video_source` ACF field — it is **not** auto-detected from a URL.

| `video_source` value | Description | ACF fields used |
|---|---|---|
| `self_host` | WordPress media library file | `video_self_host_file`, `video_self_host_poster` |
| `youtube` | YouTube embed (iframe) | `video_youtube_url` |
| `vimeo` | Vimeo CDN direct MP4 URL | `video_vimeo_url`, `video_vimeo_poster` |
| `cdn` | Any external `.mp4` / `.webm` URL | `video_cdn_url`, `video_cdn_poster` |

> **Vimeo note:** The `vimeo` source type renders a `<video>` element using a **direct MP4 URL** from Vimeo's CDN — not a `vimeo.com` watch URL or Vimeo iframe embed. You need the direct file URL.

---

## Supported Behaviours

Set via the `behavior` arg. Three values:

| Value | Class on container | Description |
|---|---|---|
| `autoplay` | `data-behavior="autoplay"` | Muted, looping background video. Pauses when out of viewport. Custom play/pause + mute controls overlaid. |
| `hover` | `data-behavior="hover"` | Plays on mouseenter, pauses/resets on mouseleave. Muted. No controls. |
| `onclick-popup` | `data-behavior="onclick-popup"` | Shows play overlay. Click opens a modal lightbox. Audio can play in popup. |

---

## ACF Field Structure

Create these sub-fields inside any ACF layout that needs video:

```
video_source          (Select)   — 'self_host' | 'youtube' | 'vimeo' | 'cdn'

// Self-hosted (show when source = self_host)
video_self_host_file  (File)     — WordPress media. Return: Array. (Carries an
                                   upload-spec instruction: 1080p / <3MB / H.264 / no audio.
                                   Posters served at mc-1600.)
video_self_host_poster (Image)   — Poster/thumbnail. Return: Array.

// YouTube (show when source = youtube)
video_youtube_url     (URL)      — Standard YouTube URL (youtube.com/watch or youtu.be)

// Vimeo (show when source = vimeo)
video_vimeo_url       (URL)      — Direct Vimeo CDN MP4 URL (not a vimeo.com watch URL)
video_vimeo_poster    (Image)    — Poster/thumbnail. Return: Array.

// CDN / External (show when source = cdn)
video_cdn_url         (URL)      — Direct .mp4 / .webm URL
video_cdn_poster      (Image)    — Poster/thumbnail. Return: Array.
```

Use ACF conditional logic to show/hide the relevant fields based on `video_source`.

### Playback / behaviour fields

In addition to the source fields above, add the playback fields that drive the
renderer's `$args`. Apply ACF conditional logic on `video_behavior` where noted:

```
video_behavior           (Select)     — 'autoplay' | 'hover' | 'onclick-popup'  (default 'autoplay')
video_autoplay           (True/False) — show when behavior = autoplay
video_autoplay_on_scroll (True/False) — show when behavior = autoplay
video_controls           (True/False) — show when behavior = autoplay (not for YouTube)
video_muted              (True/False)
video_loop               (True/False)
video_popup_autoplay     (True/False) — show when behavior = onclick-popup
video_popup_controls     (True/False) — show when behavior = onclick-popup
```

### ACF field instructions — set this copy verbatim

The `instructions` strings are part of the field design: they tell editors how
each option behaves. Set them exactly as below when creating the video fields so
the admin UI is identical across every project built on this core.

| Field | Label | `instructions` |
|---|---|---|
| `video_self_host_file` | Video File | Upload MP4 or WebM video file. |
| `video_behavior` | Behavior | How the video plays. Autoplay = muted looping background video that pauses when off-screen. Hover = plays while the mouse is over it, resets on leave. Onclick Popup = shows a play button and opens the video in a lightbox (sound allowed). |
| `video_autoplay` | Autoplay | Start playing as soon as the section loads. Browsers force it to be muted. (Autoplay behavior only.) |
| `video_autoplay_on_scroll` | Autoplay On Scroll | Only start when the video scrolls into view (50% visible) and pause when it leaves. (Autoplay behavior only.) |
| `video_controls` | Controls | Show the custom play/pause and mute buttons overlaid on the video. (Autoplay behavior, not available for YouTube.) |
| `video_muted` | Muted | Mute the audio. Note: Autoplay and Hover are always muted by the browser regardless of this setting. |
| `video_loop` | Loop | Automatically restart the video from the beginning when it ends. |
| `video_popup_autoplay` | Popup Autoplay | Start playing automatically when the popup opens. (Onclick Popup behavior only.) |
| `video_popup_controls` | Popup Controls | Show the native video controls inside the popup. (Onclick Popup behavior only.) |

> ⚠️ **Template note (controls leak):** `video_controls` defaults to true and can
> leak into hover/popup behaviours, producing a native `<video controls>`. Pass
> controls only for the autoplay behaviour:
> `'controls' => 'autoplay' === $video_behavior && ! empty( $video['video_controls'] )`.

---

## Helper Usage

```php
// Collect the field group data
$video_data = get_sub_field( 'video' ); // returns the ACF group array

// Render
if ( $video_data && function_exists( 'mosharaf_render_video' ) ) {
    mosharaf_render_video( $video_data, [
        'behavior'        => 'autoplay',
        'autoplay'        => true,
        'muted'           => true,
        'loop'            => true,
        'controls'        => true,
        'container_class' => 'hero-video-wrap',
    ] );
}
```

---

## Args Reference

```php
mosharaf_render_video( $video_field, $args = [] )
```

| Arg | Type | Default | Description |
|---|---|---|---|
| `behavior` | string | `'autoplay'` | `'autoplay'` \| `'hover'` \| `'onclick-popup'` |
| `autoplay` | bool | `true` | Auto-play on load (forced muted by browser policy) |
| `autoplay_on_scroll` | bool | `false` | Play when scrolled into viewport (uses IntersectionObserver) |
| `muted` | bool | `false` | Mute the video. Forced `true` for `autoplay` and `hover` behaviors |
| `loop` | bool | `false` | Loop the video |
| `controls` | bool | `true` | Show custom play/pause + mute buttons (autoplay behavior only) |
| `popup_autoplay` | bool | `true` | Auto-play when popup opens (onclick-popup only) |
| `popup_controls` | bool | `true` | Show controls inside popup (onclick-popup only) |
| `class` | string | `''` | CSS class on the `<video>` element |
| `container_class` | string | `''` | CSS class on the wrapper `<div>` |
| `width` | string | `'100%'` | Video element width |
| `height` | string | `'auto'` | Video element height |
| `echo` | bool | `true` | Echo or return the output |
| `preload` | string\|null | `null` | Explicit `<video preload>` value (`'none'` \| `'metadata'` \| `'auto'`). When `null`, resolved automatically (see below). Self-hosted only. |
| `defer` | bool | `false` | Render **without** the `autoplay` attribute and with `preload="none"`, so the file is **not fetched until JS calls `.play()`** (e.g. offscreen slides started later by a rotator/observer). Self-hosted only. |
| `poster` | string | `''` | Fallback poster URL used only when the video field has no poster of its own. Self-hosted only. |

`playsinline` is always added to all video elements automatically.

**`preload` resolution** (self-hosted) when `preload` is left `null`: `defer` → `none`; else `autoplay_on_scroll` → `metadata`; else the attribute is omitted (browser default).

---

## Performance & Asset Loading

The video CSS and JS are **not** loaded globally. In `functions.php` they are
`wp_register_*`'d (not enqueued), and `mosharaf_render_video()` enqueues them
**at render time** — so any page that renders no `<video>` ships none of them:

- `video-behaviors.css` + `video-behaviors.js` — whenever any video renders
- `video-popup.css` + `video-popup.js` — only for `behavior = 'onclick-popup'`
- `jquery-vimeo-player` — only for `video_source = 'vimeo'`

Because this keys off an **actual rendered `<video>`**, a section whose
`media_type` is `image` (or whose video field is empty) ships no video assets.

### WebM multi-source (convention-based)

When a sibling `.webm` sits next to a self-hosted `.mp4` in uploads (same name),
the renderer emits it as a **first `<source type="video/webm">`** ahead of the
MP4 — no ACF field/DB, purely the file's presence. A video with no `.webm`
sibling just serves the MP4. WebM files are **not auto-generated**: make them
with ffmpeg (`-c:v libvpx-vp9 -crf 34 -b:v 0 -an`), keep only if smaller than the
MP4, and deploy them with the media (they live in `uploads`, outside the repo).

### Posters & deferral

Self-hosted posters are served at the **`mc-1600`** size, not the full original
(LCP path). For multiple autoplay videos on one screen, give all but the first
`defer => true` so only the visible one is fetched/decoded until JS plays the
others.

### Upload spec (admin guardrail)

WordPress does **not** compress video on upload. The `video_self_host_file`
field carries instruction text (via `acf/load_field` at the end of
`inc/helper-functions/video-renderer.php`) telling editors to upload a
web-optimised MP4 (**1080p max, <~3 MB, H.264, no audio**).

---

## Autoplay Compliance Rules

Browsers enforce muted autoplay rules. The helper handles this automatically:

- `behavior = 'autoplay'` → forces `muted = true`
- `behavior = 'hover'` → forces `muted = true`
- `behavior = 'onclick-popup'` → popup can play with audio (no forced mute)
- `playsinline` is always present — required for iOS inline playback

---

## Autoplay on Scroll

When `autoplay_on_scroll => true`, the video uses `IntersectionObserver` to play when 50% of the video is visible, and pause when it leaves the viewport.

- Falls back to a low-power play button overlay if autoplay fails (browser blocks it)
- `preload="metadata"` is added so the poster/first frame loads without buffering the full video

```php
mosharaf_render_video( $video_data, [
    'behavior'           => 'autoplay',
    'autoplay_on_scroll' => true,
    'controls'           => false,
    'loop'               => true,
    'container_class'    => 'product-card-video',
] );
```

---

## Popup (onclick-popup) Behaviour

When `behavior` is `onclick-popup`, the helper:

1. Renders the video element as the trigger
2. Adds `.video-play-overlay` with a play button over it
3. JS (`video-popup.js`) creates a modal and moves a clone of the video inside it
4. Clicking the overlay opens the modal

```php
mosharaf_render_video( $video_data, [
    'behavior'       => 'onclick-popup',
    'popup_autoplay' => true,
    'popup_controls' => true,
    'container_class' => 'video-popup-trigger',
] );
```

---

## Hover Behaviour

Video plays on `mouseenter`, pauses and resets on `mouseleave`. Useful for product cards or team grids. Does not work on touch devices — the video will be static (no poster means blank).

```php
mosharaf_render_video( $video_data, [
    'behavior' => 'hover',
    'loop'     => true,
    'class'    => 'card-video',
] );
```

---

## HTML Structure Produced

```html
<div class="video-container {container_class}" data-behavior="{behavior}">

    <!-- For self_host / vimeo / cdn (poster served at mc-1600 size).
         A deferred slide omits `autoplay` and adds `preload="none"`.
         The webm <source> appears only when a sibling .webm file exists. -->
    <video width="100%" height="auto" poster="...-1600x___.jpg" muted playsinline autoplay loop
           data-behavior="autoplay" data-desired-muted="false">
        <source src="...clip.webm" type="video/webm">
        <source src="...clip.mp4" type="video/mp4">
    </video>

    <!-- For youtube: -->
    <iframe src="https://www.youtube.com/embed/{id}?autoplay=1&mute=1&loop=1&playsinline=1&rel=0"
            frameborder="0" allowfullscreen></iframe>

    <!-- onclick-popup only: play overlay (added by PHP) -->
    <div class="video-play-overlay">
        <button class="video-play-button" aria-label="Play Video">...</button>
    </div>

    <!-- autoplay only: low-power fallback overlay -->
    <div class="video-low-power-overlay">
        <button class="video-play-button low-power-play-btn" aria-label="Play Video">...</button>
    </div>

    <!-- autoplay only (when controls = true, non-YouTube): custom controls -->
    <div class="video-autoplay-controls">
        <button class="video-control-btn video-play-pause-btn">...</button>
        <button class="video-control-btn video-mute-btn">...</button>
    </div>

</div>
```

---

## Control Button Styling

Custom control buttons (`.video-control-btn`) use design tokens from your project CSS:

```css
/* These tokens must be defined in your project's :root {} */
--mc-color-accent  /* button background */
--mc-color-light   /* button border */
```

Override in your project CSS if you need a different color.

---

## YouTube Limitations

YouTube is rendered as an `<iframe>` embed. This means:
- No custom play/pause controls (YouTube provides its own UI)
- `controls` arg is ignored for YouTube
- Hover behavior is not supported for YouTube

---

## Mobile Considerations

- `playsinline` — always present, required for inline autoplay on iOS
- Poster image — essential for mobile where autoplay is often blocked
- Hover behavior — does not trigger on touch devices; provide a poster for static display
- Low-power overlay — shown when `autoplay_on_scroll` autoplay fails (e.g., data-saver mode)
