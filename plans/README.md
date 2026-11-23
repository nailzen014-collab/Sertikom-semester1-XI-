# Animation implementation plans

| Number | Title | Severity | Status |
| --- | --- | --- | --- |
| 001 | Animate and autoplay the hero carousel | LOW | TODO |
| 002 | Animate public navigation states | MEDIUM | TODO |

## Recommended execution order

1. Execute 001, then 002. Both plans edit `resources/css/app.css`; run them sequentially to avoid conflicting edits.

## Dependencies

- 001 has no prerequisites.
- 002 depends on the motion tokens added by 001.
