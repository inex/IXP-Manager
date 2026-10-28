# French translation — round 2

This is the second pass. It supersedes the first spreadsheet; please work from
`ixp-manager-fr-review-round2.xlsx` and discard the earlier one.

## Where round 1 got to

Of 616 rows, 9 were changed. We have taken 5 of them and they are already in
this file:

| String | Your change |
|---|---|
| `To whom it may concern,` | *À qui de droit,* |
| `CU - Cust User; CA - Cust Admin; SU - Super User.` | dropped *de membre* |
| `:app has a document store…` | same, applied consistently |
| `Customer could not be deleted…` | *bug* rather than *bogue* |
| `user:` | *utilisateur :* |

Three we did not take, and one you were right about. All four carry a note in
the spreadsheet starting `ROUND 1:` explaining why — please read those and tell
us if you still disagree. We would rather change the glossary than have two
words for the same thing.

The one you were right about: you noticed a sentence on the peering manager
page that read as unfinished. It was — it had been split into three pieces in
the code, which no amount of editing in the spreadsheet could have fixed. We
have merged it into a single string, so it is now translatable properly.

## What we need from round 2

**Every row still needs reading.** The `status` column says `review draft` on
all 615 rows because the French is machine generated. Nine rows reviewed out of
616 leaves the other 607 unverified.

If a full pass is more than you have time for, that is fine — tell us, and we
will agree a subset to prioritise rather than have the rest silently marked as
reviewed. The areas that matter most, in order:

1. **Menu, Login and passwords, My profile, Dashboard** (104 rows) — what every
   user sees on every visit.
2. **Message or alert** (106 rows) — the confirmations and errors people act on.
3. **Route server filters** (58 rows) — dense BGP material; the English is hard
   going and the draft is least trustworthy here.

Everything else can follow later.

## Unchanged from round 1

The rules in `HANDOFF.fr.md` still apply: only the `french` column changes,
`:placeholders` must all survive (though you may move them), and HTML tags and
Markdown formatting stay as they are. `GLOSSARY.fr.md` is still the terminology
reference.
