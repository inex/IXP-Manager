# Security Policy

## Secure Application Development Policy

Please see: [https://docs.ixpmanager.org/dev/policy/](https://docs.ixpmanager.org/dev/policy/).

## Reporting a Vulnerability

Thank you in advance for responsibly reporting security issues. 

Please contact us by email at [security@ixpmanager.org](mailto:security@ixpmanager.org) to report security issues or discuss security concerns. All security vulnerabilities will be promptly addressed. You can also [submit security issues securely via GitHub here](https://github.com/inex/IXP-Manager/security/advisories/new).

If you need to share sensitive information, you can encrypt your email using [our PGP key](https://www.ixpmanager.org/security-at-ixpmanager.asc), or email and request another channel such as Signal.

**What to expect:**

* We aim to acknowledge receipt of your vulnerability report within 48 hours.
* We will keep you updated as we triage the issue and establish a timeline for the fix and public disclosure.
* While we do not offer a financial bug bounty program, we are delighted to offer public acknowledgement (see below).

### Confidentiality and Acknowledgements

We understand that some organisations do not wish to disclose their use of specific software for security reasons. If you do not want to be named or acknowledged in the release notes addressing the security issue, please let us know, and we will ensure your anonymity. 

Likewise, we are delighted to acknowledge and thank anyone who responsibly reports security issues to us. We usually do this in the release notes and related announcements. Please do let us know the appropriate attribution when you contact us.


## Disclosure and Regulatory Compliance

INEX has a long track record of identifying security issues through internal audits or via third-party reporting, implementing fixes, and publishing advisories with new version releases.

From June 2026, we utilise [GitHub's Security Advisories](https://github.com/inex/IXP-Manager/security/advisories) service to provide clarity and consistency when publishing security disclosures, which also supplies machine-readable vulnerability data to downstream networks.

As an Open-Source Software Steward under the EU Cyber Resilience Act (CRA):

* **Upstream Reporting:** If a reported vulnerability in IXP Manager is confirmed to be actively exploited in the wild, INEX will notify the ENISA Single Reporting Platform and relevant national CSIRTs within the legally mandated timeframes.
* **Infrastructure Security:** Any severe cyber incident that compromises our development or distribution infrastructure (such as version control or build pipelines) will be reported to EU authorities and communicated transparently to our users.
* **Ecosystem Cooperation:** If a reported vulnerability stems from an upstream open-source dependency used by IXP Manager, we will responsibly share relevant information with the maintainers of that project to help secure the wider ecosystem.


## Supported Versions


| Version | Supported          | Details / Until                                                             |
|---------|--------------------|-----------------------------------------------------------------------------|
| 7.x.y   | :white_check_mark: | Current release, full support until min. 6 months after a future v8 release |
| 6.x.y   | :x:                | Security support ended March 1st 2026.                                      |
| 5.x.y   | :x:                |                                                                             |
| 4.x.y   | :x:                |                                                                             |
| < 4.0   | :x:                |                                                                             |

