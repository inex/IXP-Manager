# French translation — brief for the reviewer

Thank you for taking this on. This document plus `GLOSSARY.fr.md` and the
spreadsheet are everything you need.

## What you are being asked to do

The spreadsheet contains **616 strings** (about 4,300 words) from the
member-facing parts of IXP Manager.

**Every row is already filled in with a machine-generated draft.** Your job is
to *review and correct* it, not to translate from scratch. The `status` column
says `review draft` for exactly this reason.

Please read every row. The draft is a starting point produced without seeing
the software running, and it will be wrong in places — particularly for
networking terminology, and for short strings where the English is ambiguous
out of context (`Order` could be a noun or a verb; `Current` could describe a
value or a state).

## The spreadsheet

One worksheet per area of the site, so you can work through it a screen at a
time. Columns:

| Column | |
|---|---|
| `key` | Internal identifier. **Do not edit.** |
| `english` | The original text. **Do not edit.** |
| **`french`** | **The only column you change.** |
| `context` | Which part of the site the string is on |
| `screen` | The source file, if you want to ask us where something appears |
| `placeholders` | Any `:name` tokens this string must keep — see below |
| `notes` | Anything we thought you should know about this string |
| `status` | `review draft` — change to `approved` as you go, if it helps you track progress. We ignore this column on import. |

## The three things that must not change

**1. Placeholders.** Anything like `:name`, `:customer`, `:site` is replaced
with a real value when the page is shown.

    'Use the default (:language)'  ->  'Utiliser la langue par défaut (:language)'

Every placeholder in the English must appear in the French, spelled the same.
You *may* move it to wherever French grammar wants it — that is exactly why
they exist. `:Customer Details` becoming `Coordonnées du :customer`, with the
article in the middle, is the whole point.

Capitalisation of the placeholder name does not matter: `:Customer` and
`:customer` are the same thing.

Our import tool rejects any row that adds or drops a placeholder, so a mistake
here cannot reach the software — but it does mean we would have to come back
to you.

**2. HTML.** Some strings contain `<em>`, `<b>`, `<br>` or `<a href="...">`.
Keep the tags exactly; translate only the words between them.

**3. Markdown in emails.** `**bold**`, `*italic*`, `[link](url)` and leading
`*` for bullets are formatting. Keep them.

## Terminology

`GLOSSARY.fr.md` lists the terms we have already decided, including which ones
stay in English (*peering*, *looking glass*, *cross connect*, *LoA* and the
acronyms). The draft follows it.

**If you disagree with a glossary entry, tell us rather than diverging.** We
would rather change it once, everywhere, than end up with two words for the
same thing.

## What we know is weak in the draft

Please look hardest at these:

- **Route server filters** (58 strings) — dense BGP material. The English is
  itself hard going.
- **Two-factor authentication** (several long paragraphs) — nearly identical
  wording repeated three times with small differences. They should end up
  consistent with each other.
- **Short table headings and buttons** — `Order`, `Current`, `Live:`, `Clear`,
  `Flags`, `RL`. These were translated with limited context; if one looks odd,
  the `screen` column says where it appears and we can send you a screenshot.
- **Anything ending in a colon** — several are form labels where French
  typography wants a non-breaking space before the colon.

## Sending it back

Return the same file with the `french` column edited. We import it
automatically, so please do not add, remove or reorder rows or columns, and do
not rename the worksheets.

If a row is genuinely untranslatable, or you need to see it in context, leave a
comment in the `notes` column and we will follow up.
