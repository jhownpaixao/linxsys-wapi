<?php

namespace Linxsys\Wapi;

use Komodo\Logger\Logger;

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
    * @param string $session
    * @param string $token
    * @param Logger $logger
    * @return WAPI
    */
   public function __construct($host, $session = null, $token = null, $logger = null)
   {
      $this->host = $host;
      $this->curl = curl_init();
      $this->logger = $logger ?: new Logger;
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
   public function request($endpoint, $method, $body = [], $session = true)
   {
      $session = $session ? "/{$this->session}" : '';
      curl_setopt_array($this->curl, array(
         CURLOPT_URL => $this->host . $session . $endpoint,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => $method->value,
         CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$this->token}",
            'Content-Type: application/json'
         ],
         CURLOPT_POSTFIELDS => json_encode($body)
      ));

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
    * @return boolean|WhatsappInterface
    */
   public function connect($session, $token)
   {
      $this->session = $session;
      $this->token = $token;
      $request = $this->request('/start-session', HTTP::POST);

      if (!$request) return false;


      switch ($request->status) {
         case 200:
            $this->logger->info("escaneie o QRCode");
            $this->qrcode = $request->body['qr'];
            return true;
         case 409:
            $this->logger->info("sessão conectada");
            return new WhatsappInterface($this);
         case 401:
            $this->logger->error("token inválido ou sem acesso");
            return false;
         default:
            $this->logger->error($request->status, "Ocorreu um erro na tentativa de conexão");
            return false;
      }
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
            $this->logger->error($request->body['error'], 'Erro de autorização na tentativa de desconexão');
            return false;

         case 403:
            $this->logger->error($request->body['error'], 'Erro de autorização na tentativa de desconexão');
            return false;

         case 404:
            $this->logger->error($request->body['error'], 'Erro de sessão inexistente na tentativa de desconexão');
            return false;
         default:
            $this->logger->error($request->status, "Ocorreu um erro na tentativa de desconexão");
            return false;
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
            $this->logger->error($request->body['error'], 'Erro de autorização na tentativa de criação da sessão');
            return false;
         default:
            return false;
      }
   }


   /**
    * @param string $name
    * 
    * @return [type]
    */
   static public function createSession($name)
   {
   }
}

$CurlErroCode = array(
   1 => 'CURLE_UNSUPPORTED_PROTOCOL',
   2 => 'CURLE_FAILED_INIT',
   3 => 'CURLE_URL_MALFORMAT',
   4 => 'CURLE_URL_MALFORMAT_USER',
   5 => 'CURLE_COULDNT_RESOLVE_PROXY',
   6 => 'CURLE_COULDNT_RESOLVE_HOST',
   7 => 'CURLE_COULDNT_CONNECT',
   8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
   9 => 'CURLE_REMOTE_ACCESS_DENIED',
   11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
   13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
   14 => 'CURLE_FTP_WEIRD_227_FORMAT',
   15 => 'CURLE_FTP_CANT_GET_HOST',
   17 => 'CURLE_FTP_COULDNT_SET_TYPE',
   18 => 'CURLE_PARTIAL_FILE',
   19 => 'CURLE_FTP_COULDNT_RETR_FILE',
   21 => 'CURLE_QUOTE_ERROR',
   22 => 'CURLE_HTTP_RETURNED_ERROR',
   23 => 'CURLE_WRITE_ERROR',
   25 => 'CURLE_UPLOAD_FAILED',
   26 => 'CURLE_READ_ERROR',
   27 => 'CURLE_OUT_OF_MEMORY',
   28 => 'CURLE_OPERATION_TIMEDOUT',
   30 => 'CURLE_FTP_PORT_FAILED',
   31 => 'CURLE_FTP_COULDNT_USE_REST',
   33 => 'CURLE_RANGE_ERROR',
   34 => 'CURLE_HTTP_POST_ERROR',
   35 => 'CURLE_SSL_CONNECT_ERROR',
   36 => 'CURLE_BAD_DOWNLOAD_RESUME',
   37 => 'CURLE_FILE_COULDNT_READ_FILE',
   38 => 'CURLE_LDAP_CANNOT_BIND',
   39 => 'CURLE_LDAP_SEARCH_FAILED',
   41 => 'CURLE_FUNCTION_NOT_FOUND',
   42 => 'CURLE_ABORTED_BY_CALLBACK',
   43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
   45 => 'CURLE_INTERFACE_FAILED',
   47 => 'CURLE_TOO_MANY_REDIRECTS',
   48 => 'CURLE_UNKNOWN_TELNET_OPTION',
   49 => 'CURLE_TELNET_OPTION_SYNTAX',
   51 => 'CURLE_PEER_FAILED_VERIFICATION',
   52 => 'CURLE_GOT_NOTHING',
   53 => 'CURLE_SSL_ENGINE_NOTFOUND',
   54 => 'CURLE_SSL_ENGINE_SETFAILED',
   55 => 'CURLE_SEND_ERROR',
   56 => 'CURLE_RECV_ERROR',
   58 => 'CURLE_SSL_CERTPROBLEM',
   59 => 'CURLE_SSL_CIPHER',
   60 => 'CURLE_SSL_CACERT',
   61 => 'CURLE_BAD_CONTENT_ENCODING',
   62 => 'CURLE_LDAP_INVALID_URL',
   63 => 'CURLE_FILESIZE_EXCEEDED',
   64 => 'CURLE_USE_SSL_FAILED',
   65 => 'CURLE_SEND_FAIL_REWIND',
   66 => 'CURLE_SSL_ENGINE_INITFAILED',
   67 => 'CURLE_LOGIN_DENIED',
   68 => 'CURLE_TFTP_NOTFOUND',
   69 => 'CURLE_TFTP_PERM',
   70 => 'CURLE_REMOTE_DISK_FULL',
   71 => 'CURLE_TFTP_ILLEGAL',
   72 => 'CURLE_TFTP_UNKNOWNID',
   73 => 'CURLE_REMOTE_FILE_EXISTS',
   74 => 'CURLE_TFTP_NOSUCHUSER',
   75 => 'CURLE_CONV_FAILED',
   76 => 'CURLE_CONV_REQD',
   77 => 'CURLE_SSL_CACERT_BADFILE',
   78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
   79 => 'CURLE_SSH',
   80 => 'CURLE_SSL_SHUTDOWN_FAILED',
   81 => 'CURLE_AGAIN',
   82 => 'CURLE_SSL_CRL_BADFILE',
   83 => 'CURLE_SSL_ISSUER_ERROR',
   84 => 'CURLE_FTP_PRET_FAILED',
   85 => 'CURLE_FTP_PRET_FAILED',
   86 => 'CURLE_RTSP_CSEQ_ERROR',
   87 => 'CURLE_RTSP_SESSION_ERROR',
   88 => 'CURLE_FTP_BAD_FILE_LIST',
   89 => 'CURLE_CHUNK_FAILED'
);
