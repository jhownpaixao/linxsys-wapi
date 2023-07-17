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

use CURLFile;
use Komodo\Logger\Logger;
use Linxsys\Wapi\Interfaces\HTTP;

/**
 * Classe com os metodos básicos utilizados no
 * linxsys-baileys-api
 *
 * @package    WAPI
 * @author     Jhonnata Paixão <https://github.com/jhownpaixao>
 * @copyright  Copyright (c) 2023 LinxSys (https://linxsys.com.br/)
 * @license    MIT License
 * @version    1.0
 */
class WhatsappClient
{
    /**
     * @var WAPI
     */
    public $session;

    /**
     * @var string
     */
    public  readonly string $name;

    /**
     * @var string
     */
    public  readonly string $picture;

    /**
     * @var string
     */
    public  readonly string $phone;

    /**
     * @var string
     */
    public readonly string $uniqKey;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Simulation
     */
    private $simulation;

    /**
     * @param WAPI $session
     * @param array $connection
     * @param null|Logger $logger
     */
    public function __construct($session, $connection, $logger = null)
    {
        $this->session = $session;
        $this->logger = $logger ? clone $logger : new Logger;
        $this->logger->register('Komodo\\WhatsappInterface');

        $this->name = $connection['nome'];
        $this->phone = $connection['numero'];
        $this->picture = $connection['image'];
        $this->uniqKey = $connection['id'];

        $this->simulation = new Simulation();
    }

    // #Public Methods
    /**
     * @param string $to
     * @param string $audio
     * @param boolean $recorded
     * @param boolean|null $simulation
     * 
     * @return boolean
     */
    public function sendAudio($to, $audio, $recorded = false, $simulation = null)
    {
        if (!is_file($audio)) {
            $this->logger->error($audio, 'Não é possivel enviar o audio. Arquivo não encontrado');
            return false;
        };
        $simulation = $simulation ? $this->simulation->getParams() : null;
        $this->logger->trace([$audio, $recorded, $to, $simulation], 'Enviando audio');

        $request = $this->session->request('/message/send-audio', HTTP::POST, [
            "number" => $to,
            "audio" => new CURLFile($audio, 'audio/mpeg'),
            "recorded" => $recorded,
            "simulation" => json_encode($simulation),
        ], 'file');
        return $request ? $request->body['status'] : false;
    }
    /**
     * @param string $to
     * @param string $image
     * @param string $body
     * @param null|boolean $simulation
     * 
     * 
     * @return boolean
     */
    public function sendImage($to, $image, $body = null, $simulation = null)
    {
        if (!is_file($image)) {
            $this->logger->error($image, 'Não é possivel enviar a imagem. Arquivo não encontrado');
            return false;
        };
        $simulation = $simulation ? $this->simulation->getParams() : null;
        $this->logger->trace([$image, $body, $to], 'Enviando imagem');

        $request = $this->session->request('/message/send-image', HTTP::POST, [
            "body" => $body,
            "number" => $to,
            "image" => new CURLFile($image, 'image/jpeg')
        ], 'file');

        return $request ? $request->body['status'] : false;
    }

    /**
     * @param string $to
     * @param string $file
     * @param string|null $body
     * @param string|null $mimetype
     * 
     * @return boolean
     */
    public function sendFile($to, $file, $body = null, $mimetype = null)
    {
        if (!is_file($file)) {
            $this->logger->error($file, 'Não é possivel enviar o arquivo. Arquivo não encontrado');
            return false;
        };

        $this->logger->trace([$file, $body, $to], 'Enviando arquivo');

        $request = $this->session->request('/message/send-file', HTTP::POST, [
            "body" => $body,
            "number" => $to,
            "file" => new CURLFile($file, $mimetype)
        ], 'file');

        return $request ? $request->body['status'] : false;
    }

    /**
     * @param string $to
     * @param string $body
     * @param null|boolean $simulation
     * 
     * @return boolean
     */
    public function sendText($to, $body, $simulation = null)
    {
        $simulation = $simulation ? $this->simulation->getParams() : null;
        $this->logger->trace([$body, $to, $simulation], 'Enviando texto');

        $request = $this->session->request('/message/send-text', HTTP::POST, [
            "number" => $to,
            "body" => $body,
            "simulation" => $simulation
        ]);
        return $request ? $request->body['status'] : false;
    }

    public function validate()
    {
    }
}
