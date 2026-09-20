# TODO

States: `[ ]` pending, `[~]` partial, `[!]` blocked, `[x]` done, `[-]` obsolete.

## Daily round

Filed by `~/p/bin/daily`; one bullet per finding, updated in place while it repeats.

- [x] <!-- daily-tasks:BROKEN --> **BROKEN resolved** (first seen 2026-09-12, verified 2026-09-20): reproduced `node-sass@npm:4.13.1 couldn't be built successfully (exit code 1)` on Node 26.9.0; removed its unused Elixir parent without downgrades. Verification: `yarn install` → `Done with warnings in 0s 524ms`; `yarn run build-frontend` → `2 stylesheets created.` and `Done.`; `yarn why node-sass` → no matches (exit 0).
- [x] Forward fix (2026-09-20): recipe adjusted because Dart Sass was already present, there is no webpack.mix.js or Elixir build, and the actual pipeline is Grunt/Less; removed unused `laravel-elixir` and regenerated `yarn.lock`, retaining upgraded dependencies. Verification: `yarn exec grunt deploy` → `1 file created 333 kB → 155 kB` and `Done.`; `wc -c public/assets/stylesheet/application.css public/assets/stylesheet/frontend.css` → 309189 and 178705 bytes. Generated assets restored after verification to keep this dependency fix scoped; no commits made.

- [ ] Dependency warning (2026-09-20): `yarn install` reports `YN0002: attendize@workspace:. doesn't provide postcss (p351ad8), requested by laravel-mix`; Grunt builds pass. Next step: audit whether Laravel Mix is needed before adding its PostCSS peer.
