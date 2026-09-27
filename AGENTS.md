# AGENTS.md

Laravel 12 (PHP 8.2) web app: **Pengukur Berat Badan Berbasis BMI dan Analisis Kalori**. It is a class project (KKA + PIPAS) and is graded against a written proposal, so the code, the tests, and the UI wording are all traceable to that document.

## What the app does

Two pages, no database:

- `GET /` — form: usia, jenis kelamin, berat badan (kg), tinggi badan (cm), tingkat aktivitas. Name `bmi.form`.
- `POST /hitung` — validates, calculates, stores the result in the session, redirects. Name `bmi.hitung`.
- `GET /hasil` — renders the stored result. Name `bmi.hasil`. Redirects back to `/` if the session holds no result.

`BmiController::form()` calls `session()->forget()` on the result key, so "Hitung Ulang" is just a link to `/` — there is no separate reset route.

## Domain logic (do not change without updating the proposal)

- `app/Services/BmiCalculator.php` — `hitung()` is the whole algorithm: cm→m conversion, BMI, category, Mifflin-St Jeor BMR, energy, ideal weight range. Also exposes `BMI_NORMAL_MIN = 18.5` / `BMI_NORMAL_MAX = 24.9`, which the result view prints.
- `app/Enums/KategoriBmi.php` — BMI bands are **Permenkes No. 2 Tahun 2025** (adult, 18+): `<18.5` Kurang, `18.5–24.9` Normal, `25.0–27.0` Lebih, `>27.0` Obesitas. Thresholds are hardcoded in `KategoriBmi::dari()`.
- `app/Enums/AktivitasFisik.php` — the 5 required activity levels with PAL factors 1.2 / 1.375 / 1.55 / 1.725 / 1.9.
- `app/Enums/JenisKelamin.php` — holds the Mifflin-St Jeor constant (+5 / −161). Do not put that `+`/`−` arithmetic in the view; read `konstantaMifflin()`.
- BMI is rounded to 2 decimals **before** categorising, so the category always matches the number the user sees.
- `app/Data/HasilPerhitungan.php` is a `readonly` DTO that goes into the session — session driver is `database`, so a schema change here is not needed and the app touches no app tables.

## Proposal traceability

- Proposal "Contoh Data Pengujian" (BAB IV.E, 4 rows) → `dataContohPengujian()` in `tests/Unit/BmiCalculatorTest.php`.
- Proposal "Pengujian Aplikasi" (BAB IV.F, 6 scenarios) → `tests/Feature/BmiPerhitunganTest.php`, one test per scenario, each tagged with the scenario number.
- The proposal's mockups use Indonesian decimal commas (`22,04`). All user-facing numbers use `number_format($n, 2, ',', '.')`. **Exception:** inline CSS values need a dot, or the style is invalid — this bit us once already (see the `$posisiCss` variable in `resources/views/bmi/result.blade.php`).
- `resources/views/bmi/form.blade.php` deliberately sets `novalidate` so the server-rendered "Pesan Kesalahan" card is what the user actually sees, matching the proposal's flowchart.

## Tailwind: classes must live in Blade, not PHP

`resources/css/app.css` `@source` list covers `../**/*.blade.php`, `../**/*.js`, `storage/framework/views/*.php` and the pagination vendor views — **it does not include `app/`.** So Tailwind classes written in a PHP enum or controller are purged from the build. Presentation therefore lives in the Blade: `resources/views/bmi/result.blade.php` has a `match` that maps `KategoriBmi` to badge classes. If you ever need Tailwind classes from PHP, add `@source '../../app/**/*.php';` first.

## Frontend assets

`node_modules/` and `public/build/` are now installed and built, so `@vite` resolves. **Rebuild after editing any Blade template** — Tailwind scans Blade for class names at build time, so new classes are silently missing until `npm run build`:

```bash
npm run build     # required after Blade changes; public/build is gitignored
npm run dev       # alternative: writes public/hot and serves assets on the fly
```

Vite entrypoints are fixed to `resources/css/app.css` + `resources/js/app.js` (`vite.config.js:8`); new JS/CSS files must be added to that `input` array. Tailwind v4 is CSS-first (`@import 'tailwindcss'` + `@theme`) and there is deliberately no `tailwind.config.js` or `postcss.config.js` — do not add one.

## Commands

```bash
php artisan test                                 # 34 tests
php artisan test --filter=BmiPerhitunganTest     # the 6 proposal scenarios
vendor/bin/phpunit tests/Unit/BmiCalculatorTest.php
vendor/bin/pint                                  # --test to check only
php artisan serve --port=8123                    # quick manual check
```

No static analysis (no PHPStan/Psalm) and no JS/TS linter or formatter. `composer test` runs `config:clear` first; `composer dev` runs serve + queue:listen + pail + vite concurrently.

## Environment

- XAMPP: `php` is `C:\xampp\php\php.exe` (8.2.12, ZTS). Every `php`/`composer` call prints `Warning: Module "openssl" is already loaded` — harmless noise, not a failure.
- **Not a git repository.** No commits, branches, or diffs unless the user explicitly asks.
- `database/database.sqlite` exists but holds only the three stock migrations (`users`, `cache`, `jobs`) — the app itself writes no tables. `SESSION_DRIVER`, `CACHE_STORE`, and `QUEUE_CONNECTION` are all `database`, so `migrate:fresh` would wipe live sessions and cache.
- `.env` has a generated `APP_KEY`. Do **not** run `php artisan key:generate` or `composer setup` casually: `key:generate` overwrites the key and breaks existing sessions/encrypted data, and `composer setup` also runs `migrate --force`.
- `APP_URL=http://localhost` with no subdirectory, so absolute URLs built outside an HTTP request point at the domain root. Under XAMPP the app is reached at `http://localhost/Aplikasi_Pengukur_Berat_Badan_Berbasis_BMI_dan_Analisis_Kalori/public`.
- `resources/views/welcome.blade.php` is the stock Laravel page and is no longer routed; `/` now renders the BMI form. Delete it only if the user asks.

## Conventions

- Indonesian is the language of the UI, the enum display strings, and the tests. PHP class names stay English (Laravel convention) — do not translate them.
- Pint's Laravel preset; `.editorconfig` mandates 4-space indent, LF, UTF-8, final newline. Match the surrounding code style instead of adding comments or docblocks.
