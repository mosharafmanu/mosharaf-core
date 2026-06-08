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
video_self_host_file  (File)     — WordPress media. Return: Array.
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

`playsinline` is always added to all video elements automatically.

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

    <!-- For self_host / vimeo / cdn: -->
    <video width="100%" height="auto" poster="..." muted playsinline autoplay loop
           data-behavior="autoplay" data-desired-muted="false">
        <source src="..." type="video/mp4">
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
