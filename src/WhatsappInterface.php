<?php

namespace Linxsys\Wapi;

/*******************************************************************************************
 LinxSys WAPI Project
 ____________________________________________________________________________________________
 *
 * Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
 * Iniciado em: 15/10/2022
 * Arquivo: Whatsapp.php
 * Data da Criação Thu Jul 13 2023
 * Copyright (c) 2023
 *
 *********************************************************************************************/

class WhatsappInterface
{
    /**
     * @var WAPI
     */
    public $session;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $picture;

    /**
     * @var string
     */
    public $number;

    /**
     * @var string
     */
    public $token;

    public function __construct($session)
    {
        $this->session = $session;
    }

    // #Public Methods
    public function sendAudio()
    {
    }
    public function sendImage()
    {
    }
    public function sendText($to, $body, $simulation = null)
    {
        $request = $this->session->request('/message/send-text', HTTP::POST, [
            "number" => $to,
            "body" => $body,
            "simulation" => $simulation
        ]);

        var_dump($request);
    }
    public function validate()
    {
    }
}
