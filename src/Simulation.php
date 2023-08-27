<?php

namespace Linxsys\Wapi;

/*******************************************************************************************
  LinxSys WAPI Project
 ____________________________________________________________________________________________
 *
 * Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
 * Iniciado em: 15/10/2022
 * Arquivo: Simulation.php
 * Data da Criação Mon Jul 17 2023
 * Copyright (c) 2023
 *
 *********************************************************************************************/

/**
 * Representa o objeto de simulação enviado na requisição
 * para metodos padrões da linxsys-baileys-api; tais como 'sendText'.
 * Esta classe contém as proprieades comuns para a simulação de envios na api
 *
 * @package    WAPI
 * @author     Jhonnata Paixão <https://github.com/jhownpaixao>
 * @copyright  Copyright (c) 2023 LinxSys (https://linxsys.com.br/)
 * @license    MIT License
 * @version    1.0
 */
class Simulation
{
    /**
     * @var bool
     */
    public $ActivateHumanizedSimulation = true;
    /**
     * @var bool
     */
    public $AudioBasedRecordingSpeed = true;
    /**
     * @var int
     */
    public $TypingSpeed = 300;
    /**
     * @var int
     */
    public $AudioRecordingSpeed = 2000;

    /**
     * @param array|null $params
     */
    public function __construct($params = null)
    {
        $this->ActivateHumanizedSimulation = $params && array_key_exists('ActivateHumanizedSimulation', $params) ? $params['ActivateHumanizedSimulation'] : true;
        $this->AudioBasedRecordingSpeed = $params && array_key_exists('AudioBasedRecordingSpeed', $params) ? $params['AudioBasedRecordingSpeed'] : true;
        $this->AudioRecordingSpeed = $params && array_key_exists('AudioRecordingSpeed', $params) ? $params['AudioRecordingSpeed'] : 2000;
        $this->TypingSpeed = $params && array_key_exists('TypingSpeed', $params) ? $params['TypingSpeed'] : 300;
    }

    // #Public Methods
    /**
     * Ativa a opção de enviar status digitando, gravando e etc...
     *
     * @param boolean $active
     * @return void
     */
    public function setHumanization($active)
    {
        $this->ActivateHumanizedSimulation = $active;
    }

    /**
     * Envia o status 'gravando...' com o tempo determinado pelo tempo total
     * do arquivo de audio anexado
     *
     * @param boolean $active
     * @return void
     */
    public function setAudioBasedRecording($active)
    {
        $this->AudioBasedRecordingSpeed = $active;
    }

    /**
     * Velocidade de digitação por tecla em milisgundos
     *
     * @param int $speed
     * @return void
     */
    public function setTypingSpeed($speed)
    {
        $this->TypingSpeed = $speed;
    }

    /**
     * Envia de forma manual o tempo que o status 'gravando...' será
     * enviado para o cliente
     *
     * @param int $speed
     * @return void
     */
    public function setManulAudioRecordingSpeed($speed)
    {
        $this->AudioRecordingSpeed = $speed;
    }

    /**
     * Retorna a array de keys fornecendo todos os parametros para
     * o envio da propriedade 'simulation' na requisição
     *
     * @return array
     */
    public function getParams()
    {
        return [
            "ActivateHumanizedSimulation" => $this->ActivateHumanizedSimulation,
            "AudioBasedRecordingSpeed" => $this->AudioBasedRecordingSpeed,
            "TypingSpeed" => $this->TypingSpeed,
            "AudioRecordingSpeed" => $this->AudioRecordingSpeed
        ];
    }
}
