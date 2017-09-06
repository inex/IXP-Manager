*PR template - remove this line and edit below*

[BF] Summary of fix - fixes [inex|islandbridgenetworks]/IXP-Manager#x

[NF] New feature summary - closes [inex|islandbridgenetworks]/IXP-Manager#x

*Longer description*
 

In addition to the above, I have:

 - [ ] ensured all relevant template output is escaped to avoid XSS attached with `<?= $t->ee( $data ) ?>` or equivalent.
 - [ ] ensured appropriate checks against user privilege / resources accessed
 - [ ] API calls (particular for add/edit/delete/toggle) are not implemented with GET and use CSRF tokens to avoid CSRF attacks
  
