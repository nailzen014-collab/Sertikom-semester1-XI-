# 001 — Animate and autoplay the hero carousel

- **Status**: DONE
- **Commit**: 7bbcd7e
- **Severity**: LOW
- **Category**: Missed opportunities; accessibility; performance
- **Estimated scope**: 2 files, approximately 60–100 changed lines

## Design decision

The Taste motion guidance for a place normally rejects autoplaying carousels. The user explicitly chose the original request for an approximately 7-second automatic destination change after that conflict was presented. Implement autoplay only for this existing landing-page destination picker, with a persistent pause control and the pause/reduced-motion safeguards below; do not add autoplay or timed slideshows elsewhere.

## Problem

The public landing page updates its hero content immediately. The Alpine component in `resources/views/landing.blade.php:8` exposes `pilih`, `next`, `prev`, and getters for the current item and image, but has no timer or transition state. The hero image at `resources/views/landing.blade.php:54` changes its `src` in place; the title and description at lines 23–24 change immediately as well.

The relevant CSS in `resources/css/app.css:1668` only sizes and crops the image:

```css
.park-hero__photo img {
    display: block;
    width: 100%;
    aspect-ratio: 3 / 2;
    object-fit: cover;
}
```

Changing the image source and copy without a transition feels abrupt. The carousel also does not advance automatically, as requested.

## Target

- Keep all existing destinations, thumbnails, arrow controls, routes, and initial server-rendered content.
- When automatic playback is enabled, advance to the next destination every **7,000ms**.
- Add an explicit, keyboard-operable pause/resume button. Its accessible name must describe the action it will perform, and its pressed state must match whether automatic playback is paused.
- Pause automatic playback while the pointer is over the hero, while `document.visibilityState` is `hidden`, and when the user requests a pause. When keyboard focus enters the hero, stop rotation and keep it stopped after focus leaves until the user explicitly resumes it. Activating the resume control must clear this focus stop even while that control retains focus. Resume after a pointer hover only when the user has not explicitly paused playback and the focus stop is clear.
- Do not start automatic playback when `prefers-reduced-motion: reduce` matches. Manual previous/next/thumbnail selection must continue to work in that mode.
- On a destination change, crossfade the outgoing and incoming hero image and copy using `opacity` and a small `translateY` only. Keep both image layers at the existing `3 / 2` aspect ratio so the hero does not shift. Mark the inactive layer `aria-hidden="true"` and ensure only one copy layer is exposed to assistive technology.
- Use a **180ms** transition with `var(--ease-out)` for the hero copy and image opacity. Under reduced motion, remove the translation but retain a brief **160ms** opacity transition for state comprehension. Disable automatic rotation under reduced motion.
- Add a restrained image hover scale of **1.02** over **300ms** only inside `@media (hover: hover) and (pointer: fine)`. Do not scale the image on touch.
- Do not add a motion library or change destination content, data, or asset paths.

## Repo conventions to follow

- Put shared CSS variables in the existing `:root` block at `resources/css/app.css:5`; it currently holds the `--park-*` design tokens.
- Use Alpine component state and event directives in the existing Blade markup. Alpine is initialized in `resources/js/app.js:1`.
- Use `prefers-reduced-motion` rules. The global reduced-motion rule is at `resources/css/app.css:2023`; preserve its current behavior for unrelated components and add a scoped hero override because it sets all transition durations to `.01ms !important`.
- The hero already uses an accessible `role="group"` for destination choices and `aria-pressed` on each destination button in `resources/views/landing.blade.php:76`.

Add these shared motion tokens to the existing `:root` block:

```css
--ease-out: cubic-bezier(0.23, 1, 0.32, 1);
--ease-in-out: cubic-bezier(0.77, 0, 0.175, 1);
```

Use the target transition values verbatim:

```css
.park-hero__photo img,
.park-hero__copy-layer {
    transition: opacity 180ms var(--ease-out), transform 180ms var(--ease-out);
}

@media (prefers-reduced-motion: reduce) {
    .park-hero .park-hero__photo img,
    .park-hero .park-hero__copy-layer {
        transition-duration: 160ms !important;
        transition-timing-function: var(--ease-out);
        transform: none;
    }
}

@media (hover: hover) and (pointer: fine) {
    .park-hero__photo img {
        transition: opacity 180ms var(--ease-out), transform 300ms var(--ease-out);
    }
}
```

Use layered outgoing/incoming elements rather than changing the sole visible image source before the outgoing frame can fade. Keep the existing source in the initial server-rendered layer so a visitor still sees the hero before Alpine initializes. Ensure rapid manual changes retarget from the current visible state rather than leaving stale timers or stale layers.

## Steps

1. In `resources/css/app.css`, add the two shared easing tokens to the existing `:root` declaration. Add hero-only outgoing/incoming opacity and transform states; use `transform: translateY(6px)` for the hidden copy state and `transform: translateY(0)` for the visible state. Keep all animated properties limited to `opacity` and `transform`.
2. In `resources/views/landing.blade.php`, extend the existing Alpine state with explicit pause state, current/incoming layer state, and timer lifecycle methods. Clear and recreate the timer after manual destination selection. Use a single 7,000ms timeout per advance rather than an interval that can overlap transitions.
3. Pause/resume playback for pointer enter/leave, focus in/out, document visibility changes, explicit pause button state, and reduced-motion preference changes. Remove listeners and clear pending timeouts when the Alpine component is destroyed.
4. Update the hero markup to render an incoming/outgoing image and copy layer with one accessible active layer, and add an explicit pause/resume button near the existing carousel controls. Keep the existing arrows and thumbnail interactions functional.
5. Add the hover-only image scale behind the specified fine-pointer media query and the reduced-motion override. The global `.01ms !important` duration must be overridden only for the hero layers so their opacity feedback remains perceptible while movement is removed. Do not add hover translation to touch devices.

## Boundaries

- Do NOT edit `resources/views/layouts/public.blade.php`, admin/user layouts, authentication views, or destination data/configuration.
- Do NOT change the hero's text, CTA labels, routes, destination order, thumbnails, or image paths.
- Do NOT add autoplay without a visible pause/resume control.
- Do NOT start automatic movement for users requesting reduced motion.
- Do NOT animate layout properties such as width, height, margin, or padding.
- Do NOT add dependencies.
- If the component or selectors differ from the excerpts above when execution starts, stop and report the drift rather than improvising.

## Verification

- **Mechanical**: Run `npm run build` and `php artisan test`; both must pass.
- **Browser**: On `/`, verify the initial destination is visible before Alpine initialization and after page load. Verify the hero advances after approximately seven seconds, and that arrows and thumbnails still select the expected destination.
- **Pause behavior**: Verify the explicit button pauses/resumes playback; pointer hover pauses playback and it resumes after leaving only when the user did not explicitly pause; keyboard focus stops rotation and leaving focus alone does not restart it; activating the resume control restarts it even while focused; switching tabs prevents hidden-tab advances.
- **Accessibility**: Verify the pause button has a changing action label and correct pressed state. Inspect the accessibility tree during a transition; only the visible image/copy layer should be exposed. With `prefers-reduced-motion: reduce`, automatic rotation must not start, manual selection must work, and the copy/image must not translate.
- **Layout/performance**: Verify the hero image remains `3 / 2`, CLS remains at or below `0.1`, no horizontal overflow occurs at 390px and 1440px widths, and no new destination images are eagerly fetched beyond those already used by the thumbnails.
- **Feel check**: In DevTools, replay a manual slide change at 10% speed. The outgoing layer should fade while the incoming layer fades in without a blank flash, visible double-text, or layout jump; hover scale should feel subtle.
- **Done when**: All behaviors above pass on desktop and mobile, reduced-motion prevents autoplay and movement, and the existing destination controls continue to work.
