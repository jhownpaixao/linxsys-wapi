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

/**
 * Classe de representação do objeto de respostas
 * da requisição para o linxsys-baileys-api
 *
 * @package    WAPI
 * @author     Jhonnata Paixão <https://github.com/jhownpaixao>
 * @copyright  Copyright (c) 2023 LinxSys (https://linxsys.com.br/)
 * @license    MIT License
 * @version    1.0
 */
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
