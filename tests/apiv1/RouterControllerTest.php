<?php

/**
 * PHPUnit test class to test the configuration generation of the RouterController
 * against known good configurations.
 */
class RouterControllerTest extends TestCase
{
    public $vlanids = [ 1, 2 ];
    public $protos  = [ 4, 6 ];

    public function testDummy()
    {
        $this->assertEquals( 0, 0 );
    }


    public function testRouteServerBirdConfigurationGeneration()
    {
        foreach( $this->vlanids as $vlanid )
        {
            foreach( $this->protos as $proto )
            {
                // get the generated configuration
                $conf = file_get_contents(
                    $this->genURL( 'router', 'server-conf',
                        [
                            'target' => 'bird',
                            'vlanid' => $vlanid,
                            'proto'  => $proto,
                            'config' => "ci-rs1-conf-vlanid{$vlanid}-ipv{$proto}"
                        ]
                    )
                );
                $this->assertFalse( $conf === false, "RS Conf generation failed for file_get_contents for VLAN ID {$vlanid} using IPv{$proto}" );

                $knownGoodConf = file_get_contents( base_path() . "/data/travis-ci/known-good/ci-rs1-vlanid{$vlanid}-ipv{$proto}.conf" );
                $this->assertFalse( $knownGoodConf === false, "RS Conf generation - could not load known good file ci-rs1-vlanid{$vlanid}-ipv{$proto}.conf" );

                // clean the configs to remove the comment lines which are irrelevent
                $conf          = preg_replace( "/^#.*$/m", "", $conf          );
                $knownGoodConf = preg_replace( "/^#.*$/m", "", $knownGoodConf );

                $this->assertEquals( $knownGoodConf, $conf, "Known good and generated RS configuration for ci-rs1-vlanid{$vlanid}-ipv{$proto} do not match" );
            }
        }
    }

    private function genURL( $controller, $action, $params )
    {
        $url = IXP_PHPUNIT_API_URL . "/{$controller}/{$action}/key/" . IXP_PHPUNIT_API_KEY;

        if( is_array( $params ) && count( $params ) )
        {
            foreach( $params as $p => $v )
                $url .= "/{$p}/{$v}";
        }

        return $url;
    }
}
