<?php

namespace Linxsys\Wapi;

/*******************************************************************************************
 LinxSys WAPI Project
 ____________________________________________________________________________________________
 *
 * Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
 * Iniciado em: 15/10/2022
 * Arquivo: Response.php
 * Data da Criação Thu Jul 13 2023
 * Copyright (c) 2023
 *
 *********************************************************************************************/

class Response
{

    /**
     * @var int
     */
    public $status;

    /**
     * @var mixed
     */
    public $body;

    public function __construct($status, $body)
    {
        $this->status = $status;
        $this->body = json_decode($body, true);
    }
}
