<?php

namespace Linxsys\Wapi;

use CURLFile;
use Komodo\Logger\Logger;

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
        
    }

    // #Public Methods
    /**
     * @param string $to
     * @param string $audio
     * @param bool $recorded
     * @param array $simulation
     * 
     * @return boolean
     */
    public function sendAudio($to, $audio, $recorded = false, $simulation = [])
    {
        if (!is_file($audio)) {
            $this->logger->error($audio, 'Não é possivel enviar o audio. Arquivo não encontrado');
            return false;
        };

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
     * @param string $body
     * @param null|boolean $simulation
     * 
     * @return boolean
     */
    public function sendImage($to, $image, $body = null)
    {
        if (!is_file($image)) {
            $this->logger->error($image, 'Não é possivel enviar a imagem. Arquivo não encontrado');
            return false;
        };

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
     * @param string $body
     * @param null|boolean $simulation
     * 
     * @return boolean
     */
    public function sendFile($to, $file, $body = null)
    {
        if (!is_file($file)) {
            $this->logger->error($file, 'Não é possivel enviar o arquivo. Arquivo não encontrado');
            return false;
        };

        $this->logger->trace([$file, $body, $to], 'Enviando arquivo');

        $request = $this->session->request('/message/send-file', HTTP::POST, [
            "body" => $body,
            "number" => $to,
            "file" => new CURLFile($file)
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
