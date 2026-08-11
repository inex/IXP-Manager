---
name: Bug report
about: Create a detailed report to help us improve

---

<!---
Verify first that your issue/request is not already reported on GitHub.

Always test if the latest release is affected. Bug reports on older releases cannot be accepted and will be closed.

If you can, we would appreciate it if you could also test against the master branch but this is not necessary.

Do not create an issue for requests for help - use the mailing list:
  See: https://www.ixpmanager.org/support

NB: Issues that are requests for help will be closed. Please use the mailing list / request commercial support via https://www.ixpmanager.org/commercial

For issues with documentation, please use the following issue tracker:
  https://github.com/inex/ixp-manager-docs-md/issues

#########################################################################

IXP Manager includes a tool which can generate a GitHub Issue template with the requested system information prepopulated.

Run `php ./validate.php --github-issue` from your IXP-Manager directory, paste its entire output in as the
Github Issue body, and fill in the final few fields describing your issue.

-->


##### ISSUE SUMMARY

**Describe your issue here**

##### OS

<!---
Mention the OS you are running IXP Manager on (including Linux variant if relevant)
-->

##### VERSION

<!--- Paste verbatim the output from either:
 
  - IXP Manager <4.9: “cat library/IXP/Version.php | grep APPLICATION” 
  - IXP Manager >= 4.9: “cat version.php | grep APPLICATION”
   
between quotes below. NB: run this command from IXP Manager's root directory (e.g.
/srv/ixpmanager -->

```

```

##### ENVIRONMENT 

<!--- Paste verbatim the output from the following commands between quotes below 

php -v
dpkg -l | grep php   (or equivalent for your OS - list of php packages installed)

-->

```

```

<!--- You can also use gist.github.com links for larger files -->

##### CONFIGURATION

<!--- Paste the output of the following between quotes below:

(run from IXP Manager's root directory (e.g. /srv/ixpmanager)
grep -Ev '(^#|^\s*$|^APP_KEY|^APP_PREVIOUS_KEYS|^ATLAS_MEASUREMENT_KEY|^AWS_ACCESS_KEY_ID|^AWS_SECRET_ACCESS_KEY|^DB_|^GRAPHER.*SNMPPASSWD|^HELPDESK|^IDENTITY|^IXP_API_RIR_PASSWORD|^IXP_API_PEERING_DB_|^IXP_RIPE_API_KEY|^.*JSONEXPORTSCHEMA_ACCESS_KEY|^.*LAYER2_ADDRESSES_EMAIL|^LOG_SLACK_|^MAIL_|^MAILGUN|^PAPERTRAIL|^POSTMARK|^PEERINGDB_OAUTH_|^REDIS|^STRIPE)' .env

NB: sanity check the output to make sure you are happy you are not leaking any security information!
-->

```

```

<!--- You can also use gist.github.com links for larger files -->

##### STEPS TO REPRODUCE


##### EXPECTED RESULTS
<!--- What did you expect to happen when running the steps above? -->

##### ACTUAL RESULTS
<!--- What actually happened? -->

##### IMPORTANCE
<!-- Please let us know if the issue is affecting you in a production environment -->

##### RELEVANT LOGS

<!--

Copy all relevant logs (here if reasonably sized or to [an online pastebin such as this one](https://pastebin.ibn.ie/) and place the URL you receive from the pastebin into this section. Logs will usually be found under $IXPROOT/storage/logs/.... 

-->
