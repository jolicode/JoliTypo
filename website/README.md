# JoliTypo demo

The demo runs the JoliTypo code of this repository, not a released version: `composer.json` declares the parent
directory as a path repository, copied into `vendor/` at each `composer install`. So the website deployed from `main`
always ships the fixers of `main`. A new fixer still needs its card in `public/index.html`.

## Installation

```bash
castor website:wasm:build
```

`website:wasm:build` installs the dependencies, then builds PHP as WebAssembly with
[lyrixx/php-wasm-builder](https://github.com/lyrixx/php-wasm-builder) (Docker is needed), with `src/` and `vendor/`
embedded as `/app`, in `public/build/`. Run it again after a change in the library: only the last step of the build
runs again.

`website:wasm:build` also downloads the JoliCode footer from [jolicode/oss-theme](https://github.com/jolicode/oss-theme)
to `public/joli-footer.html` (not committed); run `castor website:footer` alone to refresh it.

Then you can test it with

```bash
castor website:serve
```

## Checks

Once PHP is built, node can check that it boots with its PHP version and its extensions, then run
`tests/page.test.mjs`: the tests of `public/js/php.js`, the module of the page that runs JoliTypo, loaded as the page
does. No browser is needed, and GitHub Actions runs it before deploying the website:

```bash
castor website:wasm:check
```

A headless Chrome can also load the demo, submit the form and check the result (Node 22+ and Chrome or Chromium are
needed, set `CHROME` to point at another binary):

```bash
castor website:smoke
```

See https://jolicode.com/blog/heberger-un-projet-php-sans-serveur-avec-webassembly for more information.
