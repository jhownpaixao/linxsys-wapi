<?php
namespace Linxsys\Wapi\Errors;

use Exception;
use Komodo\Logger\Logger;

/*
|-----------------------------------------------------------------------------
| Linxsys - PHP CRM
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: ConnectionException.php
| Data da Criação Sun Aug 27 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

final class SessionDisconectionException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(string $message = '', int $requestCodeStatus = 500, Logger $logger = null, \Throwable $previous = null)
    {
        $message = "Unable to disconnect session. Error: $message";
        if ($logger) {
            $logger->error($message, $requestCodeStatus);
        }

        parent::__construct($message, $requestCodeStatus, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
