
<?php

use Linxsys\Wapi\WAPI;
use Linxsys\Wapi\WhatsappInterface;
use PHPUnit\Framework\TestCase;

class WAPITest extends TestCase
{


    public function testWAPI()
    {
        // #WAPI
        $wapi = new WAPI('http://localhost:4000');
        $this->assertInstanceOf(WAPI::class,  $wapi);

        // #WhatsappInterface
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uIjoiamhvbm5hdGEiLCJ1bmlxa2V5IjoiNDgxYjhiZmUtMTdkMi00YmQzLWJiNmEtNzAyMDVhMGY0OTE4IiwiaWF0IjoxNjg5MjcyNTQwfQ.FId3UBRtuGDbNrYirwmco_dQWMGgfr9tRklvySiD0Yk';
        $session = 'jhonnata';
        $whatsapp = $wapi->connect($session, $token);
        $this->assertInstanceOf(WhatsappInterface::class, $whatsapp);

        // #SendText
        $sended = $whatsapp->sendText('41999614101', 'unitTest text');
        $this->assertTrue($sended);

        // #SendImage
        $sended = $whatsapp->sendImage('41999614101', __DIR__ . '\files\image.jpg', 'unitTest image');
        $this->assertTrue($sended);

        // #SendAudio
        $sended = $whatsapp->sendAudio('41999614101', __DIR__ . '\files\audio.mp3');
        $this->assertTrue($sended);

        // #SendFile
        $sended = $whatsapp->sendFile('41999614101', __DIR__ . '\files\audio.mp3', 'unitTest Arquivo');
        $this->assertTrue($sended);
    }
}
