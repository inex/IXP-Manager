<?php

namespace Database\Migrations;

use D2EM;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170221140755 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vendor ADD bundle_name VARCHAR(255) DEFAULT NULL');
    }


    public function postUp( Schema $schema ) {
        /** @var \Repositories\Vendor $repo */
        $repo = D2EM::getRepository('Entities\Vendor');

        // known bundle names:
        $bundle_names = [
            'Cisco'      => 'Port-channel',
            'Arista'     => 'Port-Channel'
        ];

        foreach( $bundle_names as $vshortname => $bundle ) {
            /** @var \Entities\Vendor $v */
            if( $v = $repo->findOneBy(['shortname' => $vshortname ] ) ) {
                $v->setBundleName($bundle);
                D2EM::flush();
            }
        }
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vendor DROP bundle_name');
    }
}
