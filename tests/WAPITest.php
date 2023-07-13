
<?php

use Linxsys\Wapi\WAPI;
use PHPUnit\Framework\TestCase;

class WAPITest extends TestCase
{

    public function testInstace()
    {
        $host = './store';
        $token = 'store.json';
        $session = 'store.json';

        //INSTACE
        $whatsapp = new WAPI($host);
        $this->assertInstanceOf(WAPI::class, $whatsapp);

        //
    }
}
