# Translations

IXP Manager's user facing screens can be translated. Admin and superuser-only
screens, the API, and router configuration templates stay in English.

## How it works

Translations are keyed on **the English source string itself**:

```php
<?= __( 'Peering Manager' ) ?>
```

```json
{ "Peering Manager": "Gestionnaire de peering" }
```

There is therefore no `lang/en.json` - English is the source. A string with no
translation renders as its English key, so **a missing or stale translation
degrades to English and never breaks a page**.

Which language a user sees is resolved, in order, from:

1. their own preference - `user.prefs['locale']`, set on their profile page
2. their customer's default - `cust.prefs['locale']`
3. the instance default - `APP_LOCALE` in `.env`

Only locales listed in `config/ixp_fe.php` (`locales`) are offered or honoured.

### Two kinds of file

| File | Contains |
|---|---|
| `lang/<locale>.json` | the application's own strings, keyed on the English |
| `lang/<locale>/{validation,auth,passwords,pagination}.php` | framework strings, keyed on Laravel's rule names |

Both fall back to English per key, so a partial `lang/fr/validation.php` is
safe: rules it does not define show Laravel's English message.

## Commands

```bash
php artisan lang:extract        # what strings exist in the code?
php artisan lang:audit fr       # what is missing, orphaned or broken?
php artisan lang:export fr      # spreadsheet for a translator
php artisan lang:import <file> fr
```

### `lang:extract`

Tokenises every PHP, Foil and Blade file in scope (see `IXP\Utils\Lang\Scope`)
and collects the literal first argument of `__()`, `trans()` and `@lang()`.

Because it tokenises rather than pattern matches, a `__( '...' )` inside a
comment or inside another string is not collected. Keys that are **not** literal
strings cannot be extracted:

```bash
php artisan lang:extract --dynamic
```

lists them. `__( 'Hello ' . $name )` should be rewritten as
`__( 'Hello :name', [ 'name' => $name ] )` so it can be translated.

### `lang:audit`

The upgrade safety net. Exits non-zero when strings are untranslated, so it can
gate CI:

```bash
php artisan lang:audit fr --show-missing --show-orphaned
```

- **untranslated** - in the code, not in the catalogue. Renders in English.
- **orphaned** - in the catalogue, no longer in the code. Usually means
  upstream reworded the English.
- **placeholder mismatch** - the translation lost or invented a `:placeholder`.
  Case is ignored when comparing, because Laravel treats `:Customer` and
  `:customer` as one placeholder and casts the value: a translation may
  legitimately move a capitalised English noun into mid-sentence.
  This is always a bug: at run time the literal text is rendered instead of the
  value. Audit fails on these even with `--placeholders-only`.

### `lang:export` / `lang:import`

`lang:export` writes one row per string, on a worksheet per area of the site.
The translator edits **only the `french` column**; everything else is context.

`.xlsx` needs `phpoffice/phpspreadsheet` (a dev dependency). Use
`--format=csv` if you would rather not have it, or if the translator prefers
CSV.

`--untranslated` exports only what still needs doing - use this for the small
delta after an upstream upgrade rather than resending the whole file.

`--draft` marks every existing translation `review draft` rather than
`approved`. Use it when the catalogue was machine drafted and each row still
needs a human to check it - which is how the shipped French started life.

`lang:import` validates before writing anything:

- a row whose translation breaks a `:placeholder` is rejected, and **the whole
  import aborts** so the catalogue is never left half updated (`--force`
  imports the good rows and skips the bad)
- a row whose English no longer appears in the code is reported and skipped
- `--dry-run` reports without writing
- `--prune` also drops orphaned translations

## After an upstream upgrade

```bash
git merge upstream/v7.5.0
php artisan lang:audit fr --show-missing --show-orphaned
php artisan lang:export fr --untranslated      # small delta for the translator
php artisan lang:import lang/exports/fr-....xlsx fr
```

Because keys are the English text, a **reworded** string upstream shows up as
one new key plus one orphaned key rather than as a modification. So that the
previous work is not lost, `lang:export` puts the translation of the closest
orphaned key in the `notes` column as a starting suggestion.

## Adding a language

1. Add it to `locales` in `config/ixp_fe.php`.
2. `php artisan lang:export <locale>` and have it translated.
3. `php artisan lang:import <file> <locale>`.
4. Optionally add `lang/<locale>/{validation,auth,passwords,pagination}.php`.

Users can then pick it on their profile page, and it can be set as a customer
wide default on the customer edit form.

## The member / customer noun

Each IXP chooses whether its participants are called *members* or *customers*
(`IXP_FE_FRONTEND_CUSTOMER_*`, exposed as `config('ixp_fe.lang.customer')`).
That noun used to be concatenated into English sentences:

```php
ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' Details'
```

which cannot be translated - the translator never sees the whole sentence, and
French needs an article and agreement around the noun that substitution alone
cannot produce.

Use `__c()` instead. It wraps `__()` and supplies the noun as placeholders:

```php
<?= __c( ':Customer Details' ) ?>
<?= __c( 'Associate :Customers' ) ?>
<?= __c( 'Any :customer which has files uploaded will be listed here.' ) ?>
```

| Placeholder | Config key | English default |
|---|---|---|
| `:customer` | `one` | member |
| `:customers` | `many` | members |
| `:customerOwner` | `owner` | member's |
| `:customerOwners` | `owners` | members' |

Laravel derives the capitalised forms, so `:Customer` and `:CUSTOMER` work too.
Pass a second argument for any further placeholders:

```php
__c( ':Customer :name has :n ports', [ 'name' => $c->name, 'n' => $n ] )
```

`lang:extract` understands `__c()` exactly as it does `__()`.

The configured noun is itself translated, so a French user of an installation
configured as `member` sees *membre*, and one configured as `customer` sees
*client*. `lang:extract` therefore emits the configured nouns as translatable
strings even though they are config values rather than `__()` literals.

`lang/fr.json` carries **both** conventions, so the translation works whichever
way an installation is configured. Only one set is ever in use, which is why
`lang:audit` reports four orphaned entries on any given install. That is
expected - do not prune them.

## Translating a string that is not yet wrapped

```diff
-    Peering Manager
+    <?= __( 'Peering Manager' ) ?>
```

Nothing else is needed: English behaviour is unchanged, and the string appears
in the next `lang:export`. For interpolated text, use a placeholder rather than
concatenation so that the translator controls word order:

```php
// wrong - a French translator cannot reorder or agree this
ucfirst( config( 'ixp_fe.lang.customer.one' ) ) . ' Details'

// right
__( ':Customer Details', [ 'Customer' => ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ] )
```
