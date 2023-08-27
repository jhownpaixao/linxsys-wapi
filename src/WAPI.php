<?php

namespace Linxsys\Wapi;

/*******************************************************************************************
 LinxSys WAPI Project
 ____________________________________________________________________________________________
 *
 * Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
 * Iniciado em: 15/10/2022
 * Arquivo: WAPI.php
 * Data da Criação Thu Jul 13 2023
 * Copyright (c) 2023
 *
 *********************************************************************************************/

use Exception;
use Komodo\Logger\Logger;
use Linxsys\Wapi\Errors\ConnectionException;
use Linxsys\Wapi\Errors\SessionCreationException;
use Linxsys\Wapi\Errors\SessionDisconectionException;
use Linxsys\Wapi\Interfaces\HTTP;
use SebastianBergmann\Diff\ConfigurationException;

/**
 * Classe para criação de instâncias do WAPI
 *
 * @package    WAPI
 * @author     Jhonnata Paixão <https://github.com/jhownpaixao>
 * @copyright  Copyright (c) 2023 LinxSys (https://linxsys.com.br/)
 * @license    MIT License
 * @version    1.0
 */
class WAPI
{
    // #Connection Props
    public $host;
    public $token;
    public $session;
    public $qrcode;

    // #Parameters
    /**
     * @var \CurlHandle|bool|resource $request
     */
    private $curl;

    private $logger;


    // *Constructor
    /**
     * @param string $host
     * @param string|null $session
     * @param string|null $token
     * @param Logger|null $logger
     * @return WAPI
     */
    public function __construct($host, $session = null, $token = null, $logger = null)
    {
        $this->host = $host;
        $this->curl = curl_init();
        $this->logger = $logger ? clone $logger : new Logger();
        $this->logger->register('Komodo\\WAPI');
        if ($session && $token) {
            $this->connect($session, $token);
        }
    }

    // #Private Methods
    /**
     * @param string $endpoint
     * @param HTTP $method
     *
     * @return Response|boolean
     */
    public function request($endpoint, $method, $body = [], $type = 'json', $session = true)
    {
        $session = $session ? "/{$this->session}" : '';
        $headers = ["Authorization: Bearer {$this->token}"];

        if ($type == 'json') {
            $headers[] = 'Content-Type: application/json';
            $body = json_encode($body);
        }

        curl_setopt_array($this->curl, array(
           CURLOPT_URL => $this->host . $session . $endpoint,
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => $method->value,
           CURLOPT_HTTPHEADER => $headers,
           CURLOPT_POSTFIELDS => $body
        ));

        $this->logger->trace([$this->host . $session . $endpoint, $method->value, $headers, $body], 'requesting data...');
        $curl_response = curl_exec($this->curl);
        $process = false;
        if (curl_errno($this->curl)) {
            $this->logger->error('Curl error: ' . curl_error($this->curl));
        } else {
            $info = curl_getinfo($this->curl);
            $this->logger->debug("Took {$info['total_time']} seconds to send a request to {$info['url']}");
            $process = new Response(intval($info['http_code']), $curl_response);
        }


        curl_close($this->curl);
        return $process;
    }

    // #Public Methods
    /**
     * @param string $token Token de acesso à sessão
     * @param string $session Nome da sessão
     *
     * @return boolean|WhatsappClient
     */
    public function connect($session, $token)
    {
        $this->session = $session;
        $this->token = $token;
        $request = $this->request('/start-session', HTTP::POST);

        if (!$request) {
            throw new ConnectionException($this->host, 'undefined request', 500, $this->logger);
        };

        switch ($request->status) {
            case 202:
                $this->logger->info("escaneie o QRCode");
                $this->qrcode = $this->status();
                return true;
            case 409:
                $this->logger->info("sessão conectada");
                $connection = $request->body['connection'];
                return new WhatsappClient($this, $connection, $this->logger);
            case 401:
                throw new ConnectionException($this->host, 'invalid access token', $request->status, $this->logger);
            default:
                throw new ConnectionException($this->host, 'unsupported status code', $request->status, $this->logger);
        }
    }

    public function status()
    {
        $request = $this->request('/check-connection-session', HTTP::POST);
        return $request->body['qrcode'];
    }

    /**
     * @return boolean
     */
    public function disconnect()
    {
        $request = $this->request('/disconnect', HTTP::GET);

        switch ($request->status) {
            case 200:
                $this->logger->info($request->status, "Sessão desconectada com sucesso");
                return true;
            case 401:
                throw new SessionDisconectionException('Not authorized', $request->status, $this->logger);
            case 403:
                throw new SessionDisconectionException('Forbidden', $request->status, $this->logger);
            case 404:
                throw new SessionDisconectionException('Session not found', $request->status, $this->logger);
            default:
                throw new SessionDisconectionException('unosupported statuc code', $request->status, $this->logger);
        }
    }

    /**
     * @param string $name
     *
     * @return [type]
     */
    public function create($name, $webhook = null)
    {
        $request = $this->request('/create-session', HTTP::POST, [
           "session" => $name,
           "webhook" => $webhook
        ], false);


        switch ($request->status) {
            case 200:
                $this->logger->info($request->body['error'], 'Sessão criada com sucess');
                return true;
            case 409:
                throw new SessionCreationException('Not authorized', $request->status, $this->logger);
            default:
                throw new SessionCreationException('unsupported status code', $request->status, $this->logger);
        }
    }

    /**
     * @param string $name
     *
     * @return [type]
     */
    public static function createSession($name)
    {
    }
}
