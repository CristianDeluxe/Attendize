# TODO

States: `[ ]` pending, `[~]` partial, `[!]` blocked, `[x]` done, `[-]` obsolete.

## Daily round

Filed by `~/p/bin/daily`; one bullet per finding, updated in place while it repeats.

- [ ] <!-- daily-tasks:BROKEN --> **BROKEN** (first seen 2026-09-12, last seen 2026-09-12): dependencies moved forward (14 deps, yarn) with install failing. Fix forward; the round never downgrades. Decisive line: `➤ YN0000: │ node-sass@npm:4.13.1 STDERR Build failed with error code: 1`. Re-run: `bash ~/p/bin/daily/ncu-update-repo.sh ~/p/Attendize /tmp/logs`.
