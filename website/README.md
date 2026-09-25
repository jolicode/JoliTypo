# JoliTypo demo

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
