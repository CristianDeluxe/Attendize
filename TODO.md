# TODO

States: `[ ]` pending, `[~]` partial, `[!]` blocked, `[x]` done, `[-]` obsolete.

## Daily round

Filed by `~/p/bin/daily`; one bullet per finding, updated in place while it repeats.

- [ ] <!-- daily-tasks:BROKEN --> **BROKEN** (first seen 2026-09-12, last seen 2026-09-12): dependencies moved forward (14 deps, yarn) with install failing. Fix forward; the round never downgrades. Decisive line: `➤ YN0000: │ node-sass@npm:4.13.1 STDERR Build failed with error code: 1`. Re-run: `bash ~/p/bin/daily/ncu-update-repo.sh ~/p/Attendize /tmp/logs`.
- [ ] Forward fix (2026-09-12): `node-sass@4.13.1` cannot compile on Node 26 (the package is dead; `gyp` fails). Replace it with `sass` (dart-sass) in package.json so laravel-mix's sass-loader picks it up, and drop any `node-sass` option in webpack.mix.js; verify with `yarn install` and the production build.
