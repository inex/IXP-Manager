#!/bin/sh

echo '*** NOT MEANT TO BE EXECUTED ***'
echo
echo 'Reference only'
echo 
echo 'See https://github.com/inex/IXP-Manager/wiki/Installation-09-Upgrading-IXP-Manager#wiki-correcting-database-issues--verifying-your-schema for more details'

exit

sudo /etc/init.d/memcached restart
rm ../doctrine/schema/*
# gen XML from ORM Designer
./doctrine2-cli.php orm:generate-entities ../application/
./doctrine2-cli.php orm:generate-proxies
./doctrine2-cli.php orm:generate-repositories ../application/


echo "####   ./doctrine2-cli.php orm:schema-tool:drop --force && ./doctrine2-cli.php orm:schema-tool:create "
echo "####   ./doctrine2-cli.php orm:schema-tool:create "
echo "####   mysqldump --single-transaction --no-create-info --complete-insert --skip-comments --disable-keys -u root inex | mysql -u root  inex2 "

