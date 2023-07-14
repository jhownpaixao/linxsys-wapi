<?php

require('../vendor/autoload.php');
/*******************************************************************************************
  LinxSys WAPI Project - example
 ____________________________________________________________________________________________
 *
 * Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
 * Iniciado em: 15/10/2022
 * Arquivo: example.php
 * Data da Criação Thu Jul 13 2023
 * Copyright (c) 2023
 *
 *********************************************************************************************/

use Linxsys\Wapi\WAPI;


$session = 'jhonnata';
$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzZXNzaW9uIjoiamhvbm5hdGEiLCJ1bmlxa2V5IjoiNDgxYjhiZmUtMTdkMi00YmQzLWJiNmEtNzAyMDVhMGY0OTE4IiwiaWF0IjoxNjg5MjcyNTQwfQ.FId3UBRtuGDbNrYirwmco_dQWMGgfr9tRklvySiD0Yk';

$wapi = new WAPI('http://localhost:4000');

$whatsapp = $wapi->connect($session, $token);
if ($whatsapp === true) {
  var_dump($wapi->qrcode);
}

$whatsapp->sendText('41999614101', 'Olá teste');
