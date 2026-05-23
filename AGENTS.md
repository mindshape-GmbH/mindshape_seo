# AGENTS.md

TYPO3 CMS extension (`mindshape_seo`) — SEO suite: SERP preview, metadata validation, analytics injection, 410 support.

## Stack & constraints

- PHP `>=8.2 <8.6`, TYPO3 `^13.4 || ^14.3` (see `composer.json`, `ext_emconf.php`).
- Node `^24` (see `.nvmrc`, `package.json` engines). Use `nvm use` before building assets.
- No PHPUnit, no linter, no CI config in this repo. Do not assume `composer test` / `composer lint` exist.

## Commands

- Frontend assets (CSS only, despite the "webpack" name):
  - `npm run build:production`
  - `npm run build:development` — watch mode with sourcemaps
  - Entry: `Resources/Private/Assets/scss/backend.scss` → `Resources/Public/StyleSheets/backend.css` (see `Build/webpack.config.babel.js`).
  - JS under `Resources/Public/JavaScript/` is **not** built; it is shipped as-is. Edit those files directly.
- Documentation: `make docs` — runs `ghcr.io/typo3-documentation/render-guides` via Docker against `Documentation/`. The `docker-compose.yml` references an older image and is not the canonical path.

## Layout

- PSR-4 root: `Mindshape\MindshapeSeo\` → `Classes/` (see `composer.json`). Extension key is `mindshape_seo`.
- Bootstrapping wired in `ext_localconf.php`:
  - TypoScript constants/setup imported from `Configuration/TypoScript/`.
  - `render-preProcess` hook → `Hook\RenderPreProcessHook`.
  - Custom FormEngine node `googlePreview` → `Backend\Form\Element\GooglePreviewElement` (registered under key `1594739604`).
- DB schema in `ext_tables.sql` adds `mindshapeseo_*` columns to `pages` plus the `tx_mindshapeseo_domain_model_configuration` table. Update both this file and matching TCA in `Configuration/TCA/` when changing fields.
- Backend module + AJAX routes registered in `Configuration/Backend/Modules.php` and `Configuration/Backend/AjaxRoutes.php`; AJAX handled by `Classes/Handler/AjaxHandler.php`.
- Services / DI in `Configuration/Services.yaml`.

## Conventions

- `.editorconfig` enforces **4-space** indent for PHP, **2-space** for JS/CSS/SCSS/JSON/YAML/TypoScript/HTML/XML/XLF/SQL. Match this when editing.
- Translations live in `Resources/Private/Language/` as XLIFF (`*.xlf`); do not invent ad-hoc keys.
- Frontend SCSS lives under `Resources/Private/Assets/scss/`, **not** `Resources/Private/Scss/`.

## Gotchas

- `.gitignore` ignores `node_modules` and `Documentation-GENERATED-temp` only; built CSS in `Resources/Public/StyleSheets/` is committed — commit rebuilt artifacts when SCSS changes.
- `README.md` is the project README.
- Extension is distributed via both Composer (`mindshape/mindshape-seo`) and TER (`typo3-ter/mindshape_seo` via `replace`); keep version bumps in sync across `composer.json`, `ext_emconf.php`, and `package.json`.
