# 003 — Add motion feedback to visitor and admin areas

- **Status**: TODO
- **Commit**: 7bbcd7e
- **Severity**: MEDIUM
- **Category**: Purpose & frequency; easing & duration; cohesion
- **Estimated scope**: 1 file, approximately 35–55 changed lines

## Problem

The logged-in visitor and admin layouts are separate, but the shared stylesheet gives most interactions immediate state changes:

- Visitor navigation changes active border/background/color without a transition at `resources/css/app.css:342–350`.
- Visitor buttons and order disclosures change color immediately at `resources/css/app.css:507–518` and `resources/css/app.css:728–774`. The account chevron at line 399 has an isolated `transition: transform .18s ease`, but no shared motion timing.
- Admin navigation changes background/color immediately at `resources/css/app.css:2115–2130`.
- Admin action buttons change background immediately at `resources/css/app.css:2260–2276`; linked quick actions only change border color at line 2576.
- The admin table has no row interaction styling at `resources/css/app.css:2409–2416`.

The corresponding shared layouts are `resources/views/layouts/user-dashboard.blade.php:20–40` and `resources/views/layouts/admin-dashboard.blade.php:25–66`. On visitor screens, order summaries are native `<details>` controls in `resources/views/user/dashboard.blade.php:63–162`; admin's quick actions are real links in `resources/views/admin/dashboard.blade.php:77–81`.

## Target

- Keep all routes, labels, form submission behavior, focus rings, and current active-state classes.
- Add a coherent, quiet interaction layer to both authenticated roles, implemented in `resources/css/app.css` only.
- Use the `--ease-out` token from plan 001 for entering/hovering and the `--ease-in-out` token only for reversible on-screen movement.
- For interactive controls, use **150ms** hover-in, **120ms** hover-out, and **90ms** press feedback. On hover, a solid/primary action steps **1px** up; on press it steps **1px** down and scales to **0.985**. Under reduced motion, keep the color state and remove all transform movement.
- Draw a single **1px** underline **4px** below navigation links on hover/focus over **150ms** in and **120ms** out. Keep active-page indicators static.
- On the visitor account chevron, retain the existing **180-degree** open/close rotation but use `var(--ease-in-out)` and a **180ms** transition. On order-disclosure controls, transition only border/background colors; do not animate the details panel's height.
- On linked cards only, lift **2px** and darken the border with a subtle shadow over **180ms** in and **120ms** out. Apply this to `.admin-quick-links a` and `.user-order-row` only if inspection confirms the latter is an anchor. Never lift `.user-summary-card`, `.user-package-card`, `.admin-metric`, `.admin-panel`, or table rows when they are not links.
- Add a subtle row background-color transition for hovered admin table rows; rows must not move.
- Add a **140ms** border-color/box-shadow transition to focused inputs/selects/textareas; do not animate the focus outline.
- Gate hover motion behind `@media (hover: hover) and (pointer: fine)`. Keep all controls usable by touch and keyboard.
- Respect the existing global reduced-motion rule for unrelated elements. For role elements, remove transforms and preserve the instantly readable current/hover/pressed colors; do not create new load, scroll, count-up, or looping effects.

## Repo conventions to follow

- Both layouts load the same `resources/css/app.css` and `resources/js/app.js`; no new JS or motion dependency is needed.
- Shared role-specific input styling already uses `:where(.site-public, .user-portal, .admin-dashboard)` at `resources/css/app.css:1896`.
- Current user/admin route states are supplied by `.is-active` classes in their Blade layouts; do not replace this logic.
- The CSS already gates responsive layout through `@media` rules. Add the fine-pointer gate only around actual hover movement.

Use the existing token values from plan 001 and these exact timings:

```css
transition: color 150ms var(--ease-out), background-color 150ms var(--ease-out), border-color 150ms var(--ease-out);
transition: transform 90ms var(--ease-out);
```

For linked cards, use the separate **180ms** in / **120ms** out timing. Do not use `transition: all`.

## Steps

1. Confirm plans 001 and its shared motion tokens are implemented. If the tokens are missing, stop and report the dependency.
2. In `resources/css/app.css`, add hover-in/out and pressed feedback to `.user-nav a`, `.admin-sidebar-nav a`, `.admin-mobile-menu nav a`, `.user-primary-action`, `.user-secondary-action`, `.admin-heading-action`, and `.admin-topbar-link`. Preserve existing color and active selectors; use explicit transition properties only.
3. Add the single hover/focus underline for visitor and admin navigation links. Keep `.is-active` and `.is-current` indicators static and visible when animation is disabled.
4. Update the existing `.user-account-menu > summary svg` transition to `transform 180ms var(--ease-in-out)`. Add color-only transitions to `.user-order-summary` and its toggle; do not animate the disclosure content's height or add scroll-triggered reveals.
5. Gate the 2px lift and subtle border/shadow response to linked `.admin-quick-links a` cards and verified linked `.user-order-row` elements. Add only a color transition to admin table row hover; do not move rows or static data cards.
6. Add the specified 140ms focus border/box-shadow transition to the shared controls. Ensure focus rings remain immediate and clearly visible.
7. Add a scoped reduced-motion override for user/admin hover and press targets that removes transform movement but leaves colors and border states legible. Preserve the existing global reduced-motion behavior everywhere else.

## Boundaries

- Do NOT edit `resources/views/layouts/user-dashboard.blade.php`, `resources/views/layouts/admin-dashboard.blade.php`, controllers, routes, or data.
- Do NOT animate static metric cards, charts, table rows, disclosure height, or any section as it scrolls into view.
- Do NOT add parallax, count-up effects, loops, card tilts, glow, loading theater, or autoplay on authenticated pages.
- Do NOT change navigation destinations, form actions, or keyboard/focus behavior.
- Do NOT add dependencies or global `transition: all`.
- If `.user-order-row` is not a link in the current code, skip its card lift rather than making it interactive.
- If the cited selectors have changed when execution starts, stop and report the drift rather than improvising.

## Verification

- **Mechanical**: Run `npm run build` and `php artisan test`; both must pass.
- **Visitor role**: Open the dashboard, bookings, and order-history pages. Verify active navigation stays visible; hover/focus underlines do not move surrounding content; primary and secondary actions show color feedback and subtle press feedback; the account chevron still indicates open/closed state; order disclosures still open and close correctly.
- **Admin role**: Open dashboard, order list, ticket validation, and an edit form. Verify the active sidebar item remains identifiable; linked quick-action cards lift only on a fine pointer; table rows do not move; buttons and text links respond on hover/press; fields preserve visible focus rings.
- **Responsive**: Check at 1440px and 390px. No horizontal overflow; hover-only movement must not fire on touch; keyboard navigation must expose equivalent focus feedback.
- **Reduced motion**: Emulate `prefers-reduced-motion: reduce`; verify all role states remain readable, all transforms are removed, and unrelated global reduced-motion behavior is unchanged.
- **Feel check**: Replay a button press and linked-card hover at 10% speed. The action should move one pixel and return cleanly; a linked quick action lifts only 2px; informational cards and data rows stay still.
- **Done when**: Every interactive visitor/admin control has a clear, restrained state response; static data surfaces remain still; all existing role routes and regression tests pass.
