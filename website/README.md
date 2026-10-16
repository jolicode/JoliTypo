# JoliTypo demo

The demo runs the JoliTypo code of this repository, not a released version: `composer.json` declares the parent
directory as a path repository, copied into `vendor/` at each `composer install`. So the website deployed from `main`
always ships the fixers of `main`. A new fixer still needs its card in `public/index.html`.

## Installation

```bash
castor website:install
castor website:wasm:export --build --pack
```

`website:wasm:export` also downloads the JoliCode footer from [jolicode/oss-theme](https://github.com/jolicode/oss-theme)
to `public/joli-footer.html` (not committed); run `castor website:footer` alone to refresh it.

Then you can test it with

```bash
castor website:serve
```

## Smoke test

Once the wasm build is exported, a headless Chrome can load the demo, submit the form and check the result
(Node 22+ and Chrome or Chromium are needed, set `CHROME` to point at another binary):

```bash
castor website:smoke
```

See https://jolicode.com/blog/heberger-un-projet-php-sans-serveur-avec-webassembly for more information.
