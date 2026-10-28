# French glossary — IXP Manager

Terminology decisions for the French translation, agreed **before** translating
so the draft is consistent. If you disagree with any of these, change the entry
here first and then apply it everywhere — consistency matters more than any
individual choice.

The draft in the spreadsheet already follows this list.

## Terms deliberately kept in English

French network engineers use these in English. Translating them would make the
interface *harder* to read for the audience, not easier.

| Term | Why |
|---|---|
| peering | Universal in French networking. *Appairage* exists but is not used in practice. |
| peering bilatéral / bilateral peering | "peering" stays, the adjective is French |
| looking glass | Always English, including at French IXPs |
| LoA (Letter of Authority) | Industry standard, used untranslated in French datacentres |
| cross connect | Standard in French colocation contracts |
| switch | *Commutateur* is technically correct but nobody says it |
| VLAN, LAN, LAG, MAC, IXP, AS, ASN, IRRDB, RPKI, BGP, NOC, API, URL, IP, SNMP, 2FA, TOTP, HOTP, QR | Acronyms, unchanged |
| PeeringDB, IXP Manager, Google Authenticator, GitHub | Product and company names |
| sflow, MRTG, smokeping, BIRD | Product names |

## Agreed translations

| English | French | Note |
|---|---|---|
| member | membre | the configurable noun — see below |
| customer | client | the configurable noun — see below |
| route server | serveur de routes | |
| route server client | client du serveur de routes | |
| prefix | préfixe | |
| prefix filtering | filtrage de préfixes | |
| filter | filtre | |
| route | route | |
| traffic | trafic | one *f* in French |
| port | port | |
| patch panel | panneau de brassage | |
| patch panel port | port de panneau de brassage | |
| rack / cabinet | baie | |
| facility | site | |
| document store | espace documentaire | |
| directory | répertoire | |
| file | fichier | |
| upload (verb) | téléverser | |
| download (verb) | télécharger | |
| dashboard | tableau de bord | |
| graph | graphique | |
| settings | paramètres | |
| API key | clé API | |
| application password | mot de passe d'application | |
| password | mot de passe | |
| username | nom d'utilisateur | |
| login (verb) | se connecter | |
| log in / logged in | connecté | |
| logout | déconnexion | |
| session | session | |
| two factor authentication | authentification à deux facteurs | keep "(2FA)" after it |
| MAC address | adresse MAC | |
| contact | contact | |
| notes | notes | |
| welcome email | e-mail de bienvenue | |
| email | e-mail | with the hyphen, per current French usage |

## Tone and conventions

- **Address the user as *vous***, never *tu*. This is a professional tool.
- **Use *vous* forms for instructions**: "Veuillez saisir…", not "Saisis…".
- **Sentence case for headings**, as in English. Do not capitalise every word.
- **Non-breaking spaces** before `:` `;` `!` `?` are correct French typography.
  They are welcome but not required — do not let them block you, and do not
  use them inside a `:placeholder`.
- **Keep the final full stop** if the English has one, drop it if not. Several
  strings are button labels or table headings where it matters.

## Placeholders — the one thing that must not change

Anything of the form `:name` is replaced with a real value at run time.

    'Use the default (:language)'   ->  'Utiliser la langue par défaut (:language)'

Rules:

- Every `:placeholder` in the English **must** appear in the French, spelled
  identically. Adding or dropping one is an error and the import will reject
  the row.
- You **may** move a placeholder to wherever French grammar wants it. That is
  the entire reason they exist: `:Customer Details` becomes
  `Coordonnées du :customer`, with the article in the middle, which the old
  English-concatenated text could not express.
- `:customer` and `:customers` are the configurable member/customer noun. Write
  the article and any agreement around them, in the French.
- Do not translate the placeholder name itself.

## HTML

A few strings contain HTML — `<em>`, `<b>`, `<a href=…>`, `<br>`. Keep the tags
exactly as they are and translate only the text between them.
