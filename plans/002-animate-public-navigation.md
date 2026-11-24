# 002 — Animate public navigation states

- **Status**: DONE
- **Commit**: 7bbcd7e
- **Severity**: MEDIUM
- **Category**: Purpose & frequency; easing & duration; accessibility
- **Estimated scope**: 2 files, approximately 25–45 changed lines

## Problem

The public navigation in `resources/views/layouts/public.blade.php:28` has an `.is-current` class for the current route, but its styles are static. In `resources/css/app.css:1466`, links and account summaries have no transition declaration; `:hover` changes the background immediately, and `.is-current` at line 1486 uses a static underline. The mobile menu switches directly between `display: none` and `display: flex` at lines 1446–1463.

Current navigation styles:

```css
.public-nav-link:hover,
.public-account-menu > summary:hover,
.public-account-menu[open] > summary {
    background: var(--park-sage);
    color: #173c32;
}

.public-nav-link.is-current {
    font-weight: 700;
    text-decoration: underline;
    text-decoration-color: var(--park-copper);
    text-underline-offset: .4rem;
}
```

The frequent navigation feedback currently has no pressed state or animated active indicator.

## Target

- Keep all existing links, route logic, account menu behavior, responsive breakpoints, and navigation labels.
- Animate hover color/background with `transition: color 150ms ease, background-color 150ms ease`.
- Keep the current-page indicator as a static **2px** bar under the `aria-current="page"` link. Animate the hover underline only: a **1px** line **4px** below the word, drawing left-to-right with `transform` over **150ms** in and **120ms** out.
- Add press feedback to links and the menu toggle: deepen the surface color and apply `transform: scale(0.985)` with a **90ms** transition on `:active`; restore when released. Do not animate the focus ring.
- Animate the mobile menu enter over **200ms** and close over **120ms**, using opacity and a **6px** drop with `var(--ease-out)`. Do not scale the menu.
- Keep desktop navigation visible and unaffected by the mobile open/close state.
- Under reduced motion, remove translate/scale movement while preserving color and opacity feedback. Override the global `.01ms !important` duration only for public navigation states, using a **160ms** color/background/opacity transition.
- Use the `--ease-out` and `--ease-in-out` tokens added by plan 001. Execute plan 001 first.

Use this hover underline geometry so it does not change the link's layout. Keep the current-page bar static:

```css
.public-nav-link {
    position: relative;
}

.public-nav-link::after {
    position: absolute;
    right: .7rem;
    bottom: .35rem;
    left: .7rem;
    height: 1px;
    bottom: 4px;
    background: var(--park-copper);
    content: "";
    opacity: 0;
    transform: scaleX(0);
    transform-origin: left center;
    transition: transform 150ms var(--ease-out), opacity 150ms var(--ease-out);
}

.public-nav-link:hover::after,
.public-nav-link:focus-visible::after {
    opacity: 1;
    transform: scaleX(1);
}

.public-nav-link[aria-current="page"] {
    border-bottom: 2px solid var(--park-copper);
}
```

## Repo conventions to follow

- The public header uses one responsive navigation tree and Alpine state `menuOpen` in `resources/views/layouts/public.blade.php:16–29`.
- Navigation classes and breakpoints are in `resources/css/app.css:1430–1508` and `resources/css/app.css:1928–1955`.
- Shared easing tokens live in `:root` in `resources/css/app.css`; plan 001 adds `--ease-out` and `--ease-in-out`.
- Preserve the existing `.is-current` route assignment and `aria-current="page"` on the landing link in `resources/views/layouts/public.blade.php:29`.

## Steps

1. Confirm plan 001 has added `--ease-out` and `--ease-in-out` to `resources/css/app.css`. If either token is missing, stop and report the dependency.
2. In `resources/css/app.css`, add the 150ms in / 120ms out color/background transition to `.public-nav-link` and `.public-account-menu > summary`. Add the specified 90ms pressed transform and deeper surface color to links and the menu toggle.
3. Keep the current page marker static. Add the single 1px hover/focus underline under nav words using the target transform-only transition; do not add a gliding indicator or second underline style.
4. Change `#site-navigation` to use `x-show="menuOpen"` with `x-cloak` and Alpine `x-transition` enter/leave directives. Use `transition ease-out duration-200` on enter and `transition ease-out duration-120` on leave, with `opacity-0 translate-y-[-6px]` as the hidden state and `opacity-100 translate-y-0` as the shown state. Make the mobile base `.public-nav` display `flex` when shown. In the desktop media query, force `.public-nav` to `display: flex !important; opacity: 1 !important; transform: none !important; visibility: visible !important;` so Alpine's closed inline display and transition state cannot hide desktop navigation. Remove the `:class` display toggle after the `x-show` transition works. Keep closed mobile links out of the tab order through `x-show`.
5. Add a reduced-motion override that removes menu translation and press scaling but keeps color and opacity state feedback. The global rule at `resources/css/app.css:2023` forces every duration to `.01ms !important`; add a scoped override for `.public-header #site-navigation`, `.public-header .public-nav-link`, `.public-header #site-navigation .public-nav-link::after`, and `.public-header .public-menu-toggle` with `transition-duration: 160ms !important` and `transition-timing-function: var(--ease-out)`. Do not add hover movement for coarse pointers.

## Boundaries

- Do NOT edit route definitions, route-active logic, account-menu behavior, or any admin/user navigation.
- Do NOT change labels, URLs, markup hierarchy, or authentication branches except for the minimum attributes/directives needed to animate the existing mobile panel.
- Do NOT use `transition: all`, animate layout properties, or add a motion library.
- Do NOT add new animation tokens; use the two tokens from plan 001.
- If the navigation markup or selectors differ from the cited excerpts when execution starts, stop and report the drift rather than improvising.

## Verification

- **Mechanical**: Run `npm run build`; inspect the diff for changes limited to `resources/css/app.css` and `resources/views/layouts/public.blade.php`.
- **Browser**: On `/`, verify the current route has a visible indicator, hover feedback is smooth on a fine pointer, and press feedback returns to normal after release. Verify current state is visible with CSS animations disabled.
- **Mobile**: At 390px wide, open and close the menu by pointer and keyboard. Confirm it animates without horizontal overflow, closed links cannot receive focus, `aria-expanded` stays correct, and a narrow viewport can scroll the menu.
- **Desktop**: At 1440px wide, verify all navigation items remain visible before and after toggling the mobile state through DevTools or Alpine.
- **Reduced motion**: Emulate `prefers-reduced-motion: reduce`; verify menu movement and press scaling are removed while opacity/color feedback lasts 160ms. Check computed style to ensure the scoped override beats the global `.01ms !important` rule without changing unrelated components.
- **Feel check**: In DevTools, replay the hover underline at 10% speed. It should draw under the hovered/focused word without moving layout; the current-page bar must remain static; press feedback should feel immediate and subtle.
- **Done when**: Keyboard, mobile, desktop, hover, pressed, active, and reduced-motion checks all pass, with no route or account-menu regressions.
