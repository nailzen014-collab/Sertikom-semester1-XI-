# 002 — Animate public navigation states

- **Status**: TODO
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
- Add a current-page indicator using a pseudo-element. It must move/scale with `transform` and opacity, not animate width or other layout properties. Use `transform-origin: left center` and `transition: transform 200ms var(--ease-in-out), opacity 200ms var(--ease-in-out)`.
- Add press feedback to links and the menu toggle: `transform: scale(0.97)` on `:active`, with `transition: transform 160ms var(--ease-out)`. Restore to `scale(1)` when released.
- Animate the mobile menu enter/exit with opacity and `translateY(-4px)` over **200ms** using `var(--ease-out)`. Start at `opacity: 0` and `scale(0.97)` at most; never use `scale(0)`.
- Keep desktop navigation visible and unaffected by the mobile open/close state.
- Under reduced motion, remove translate/scale movement while preserving color and opacity feedback. Override the global `.01ms !important` duration only for public navigation states, using a **160ms** color/background/opacity transition.
- Use the `--ease-out` and `--ease-in-out` tokens added by plan 001. Execute plan 001 first.

Use this indicator geometry so it does not change the link's layout:

```css
.public-nav-link {
    position: relative;
}

.public-nav-link::after {
    position: absolute;
    right: .7rem;
    bottom: .35rem;
    left: .7rem;
    height: 2px;
    background: var(--park-copper);
    content: "";
    opacity: 0;
    transform: scaleX(0);
    transform-origin: left center;
    transition: transform 200ms var(--ease-in-out), opacity 200ms var(--ease-in-out);
}

.public-nav-link.is-current::after {
    opacity: 1;
    transform: scaleX(1);
}
```

## Repo conventions to follow

- The public header uses one responsive navigation tree and Alpine state `menuOpen` in `resources/views/layouts/public.blade.php:16–29`.
- Navigation classes and breakpoints are in `resources/css/app.css:1430–1508` and `resources/css/app.css:1928–1955`.
- Shared easing tokens live in `:root` in `resources/css/app.css`; plan 001 adds `--ease-out` and `--ease-in-out`.
- Preserve the existing `.is-current` route assignment and `aria-current="page"` on the landing link in `resources/views/layouts/public.blade.php:29`.

## Steps

1. Confirm plan 001 has added `--ease-out` and `--ease-in-out` to `resources/css/app.css`. If either token is missing, stop and report the dependency.
2. In `resources/css/app.css`, add the 150ms color/background transition to `.public-nav-link` and `.public-account-menu > summary`. Add a distinct `:active` transform rule for links and the menu toggle using the specified 160ms transition.
3. Replace the static `.is-current` underline with a positioned pseudo-element. Keep the existing underline color and current-route class, animate only `transform` and `opacity` with the specified 200ms transition, and provide a visible current state without depending on animation.
4. Change `#site-navigation` to use `x-show="menuOpen"` with `x-cloak` and Alpine `x-transition` enter/leave directives. Use `transition ease-out duration-200`, `opacity-0 translate-y-[-4px] scale-95` as enter-start, and `opacity-100 translate-y-0 scale-100` as enter-end; reverse those states on leave. Make the mobile base `.public-nav` display `flex` when shown. In the desktop media query, force `.public-nav` to `display: flex !important; opacity: 1 !important; transform: none !important; visibility: visible !important;` so Alpine's closed inline display and transition state cannot hide desktop navigation. Remove the `:class` display toggle after the `x-show` transition works. Keep closed mobile links out of the tab order through `x-show`.
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
- **Feel check**: In DevTools, replay the active indicator at 10% speed. It should track the selected link without moving surrounding layout; the press feedback should feel immediate and subtle.
- **Done when**: Keyboard, mobile, desktop, hover, pressed, active, and reduced-motion checks all pass, with no route or account-menu regressions.
