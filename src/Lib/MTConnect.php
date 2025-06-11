<?php

namespace Aleedhillon\MetaTraderClient\Lib;

//+------------------------------------------------------------------+
//|                                             MetaTrader 5 Web API |
//|                   Copyright 2000-2021, MetaQuotes Software Corp. |
//|                                        http://www.metaquotes.net |
//+------------------------------------------------------------------+
/**
 * Create connect to MetaTrader 5 server
 */
class MTConnect
{
    //--- The serial number must be within the range 0000-FFFF:
    //--- 0-3FFF (0-16383) — client commands.
    const MAX_CLIENT_COMMAND = 16383;
    //--- socket connect
    private $connection = null;
    //--- ip to mt5 server
    private $ipMt5 = null;
    //--- port o mt5 server
    private $portMt5 = null;
    //--- timeout
    private $timeoutConnection = 5;
    //--- crypto random string
    private $cryptRand = "";
    //--- crypto array
    private $cryptIv = null;
    //---
    private $aesOut = null;
    //---
    private $aesIn = null;
    /**
     * class crypt aes 256
     * @var MT5CryptAes256
     */
    private $cryptOut = null;
    //---
    private $cryptIn = null;
    //--- number of client packet
    private $clientCommand = 0;

    private $isCrypt = false;

    /**
     * Create MetaTrader 5 Web Api class
     *
     * @param  string $ipMt5              host or ip for MetaTrader 5 server
     * @param  int    $portMt5            port to MetaTrader 5 server
     * @param  int    $timeoutConnection  time out of try connection to MetaTrader 5 server
     * @param bool    $isCrypt            - need crypt connection
     *
     * @return MTConnect
     */
    public function __construct($ipMt5, $portMt5, $timeoutConnection, $isCrypt)
    {
        $this->ipMt5 = $ipMt5;
        $this->portMt5 = $portMt5;
        $this->timeoutConnection = $timeoutConnection;
        //-- if need  crypt lets begin
        $this->isCrypt = $isCrypt;
        $this->clientCommand = 0;
    }

    /**
     * Create connection to MT5
     * @return boolean
     */
    private function CreateConnection()
    {
        //--- create socket
        if (!($this->connection = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- try create connection to server
        if (!socket_connect($this->connection, $this->ipMt5, $this->portMt5)) {
            return MTRetCode::MT_RET_ERR_CONNECTION;
        }
        //--- set block connection
        if (!socket_set_block($this->connection)) {
            return MTRetCode::MT_RET_ERR_NETWORK;
        }
        //--- select socket and listen to change in it
        $r = array($this->connection);
        $w = array($this->connection);
        $f = array($this->connection);
        //---
        switch (socket_select($r, $w, $f, $this->timeoutConnection)) {
            case 2:
                return MTRetCode::MT_RET_ERR_CONNECTION;
            case 1:
                break;
            case 0:
                return MTRetCode::MT_RET_ERR_TIMEOUT;
        }
        //--- OK
        return MTRetCode::MT_RET_OK;
    }

    /**
     * Get las terror from socket
     * @return string
     */
    private function GetSocketError()
    {
        if ($this->connection)
            $errorCode = socket_last_error($this->connection);
        else
            $errorCode = socket_last_error();
        //---
        $errorMsg = socket_strerror($errorCode);
        return "code: " . $errorCode . ", " . $errorMsg;
    }

    /**
     * Close connection
     * @return void
     */
    public function Disconnect()
    {
        if ($this->connection) {
            socket_close($this->connection);
        }
    }

    /**
     * Authentication on MetaTrader 5 server
     * @return boolean
     */
    public function Connect()
    {
        //--- connect to server
        return $this->CreateConnection();
    }

    /**
     * Send data to MetaTrader 5 server
     *
     * @param string  $command       - command, for example AUTH_START, AUTH_ANSWER and etc.
     * @param  array $data
     * @param bool    $firstRequest bool is ot first
     *
     * @return bool
     */
    public function Send($command, $data, $firstRequest = false)
    {
        if (!$this->connection) {
            return false;
        }
        //--- number packet
        $this->clientCommand++;
        //--- packet max, than first
        if ($this->clientCommand > self::MAX_CLIENT_COMMAND)
            $this->clientCommand = 1;
        //--- create query
        $q = $command;
        //--- create string for query
        if (!empty($data)) {
            $bodyRequest = '';
            $q .= "|";
            foreach ($data as $param => $value) {
                if ($param == MTProtocolConsts::WEB_PARAM_BODYTEXT) {
                    $bodyRequest = $value;
                } else {
                    $q .= $param . '=' . MTUtils::Quotes($value) . '|';
                }
            }
            $q .= "\r\n";
            //--- add body request
            if (!empty($bodyRequest))
                $q .= $bodyRequest;
        } else
            $q .= "|\r\n";
        //---
        $queryBody = mb_convert_encoding($q, "utf-16le", "utf-8");
        //--- if need we crypt packet, crypt did not for auth_start and auth_start_answer
        if ($command != MTProtocolConsts::WEB_CMD_AUTH_START && $command != MTProtocolConsts::WEB_CMD_AUTH_ANSWER && $this->isCrypt) {
            $queryBody = $this->CryptPacket($queryBody, strlen($queryBody), $lenQuery);
        } else
            $lenQuery = strlen($queryBody);

        //--- send request
        $queryLen = 0;
        if ($firstRequest) {
            $header = sprintf(MTProtocolConsts::WEB_PREFIX_WEBAPI, $lenQuery, $this->clientCommand);
            $query = $header . '0' . $queryBody;
            $queryLen = strlen($header) + 1 + $lenQuery;
        } else {
            $header = sprintf(MTProtocolConsts::WEB_PACKET_FORMAT, $lenQuery, $this->clientCommand);
            $query = $header . '0' . $queryBody;
            $queryLen = strlen($header) + 1 + $lenQuery;
        }
        //--- send data to MetaTrader 5 server
        $sendData = socket_write($this->connection, $query, $queryLen);
        if (!$sendData) {
            return false;
        }
        //---
        return true;
    }

    /**
     * Crypt the packet
     *
     * @param string   $packetBody
     * @param int      $lenPacket
     * @param int      $lenCryptPacket
     *
     * @internal param int $lenPack
     * @return string|null
     */
    private function CryptPacket(string $packetBody, int $lenPacket, int &$lenCryptPacket): ?string
    {
        $result = '';
        if ($this->cryptOut == null) {
            $key = $this->cryptIv[0] . $this->cryptIv[1];

            $this->cryptOut = new MT5CryptAes256(MTUtils::GetFromHex($key), strlen($key) / 2);
            //---
            $this->aesOut = $this->cryptIv[2];
            $this->aesOut = MTUtils::GetFromHex($this->aesOut);
        }

        //--- check aes
        if (empty($this->aesOut)) {
            return null;
        }
        //---
        for ($i = 0, $key = 16; $i < $lenPacket; $i++) {
            if ($key >= 16) {
                //--- get new key for xor
                $this->aesOut = $this->cryptOut->encryptBlock($this->aesOut);
                //---  key index is 0
                $key = 0;
            }
            //--- xor all bytes
            $result .= chr(ord($packetBody[$i]) ^ ord($this->aesOut[$key]));
            $key++;
        }
        $lenCryptPacket = $i;
        //--- return crypt string
        return $result;
    }

    /**
     * @param string|null $packetBody
     * @param int   $lenPacket
     *
     * @return string|null
     */
    private function DeCryptPacket(?string $packetBody, int $lenPacket): ?string
    {
        if ($packetBody == null)
            return null;
        //---
        if ($this->cryptIn == null) {
            $key = $this->cryptIv[0] . $this->cryptIv[1];
            $this->cryptIn = new MT5CryptAes256(MTUtils::GetFromHex($key), strlen($key) / 2);
            //--- create aes in array
            $this->aesIn = $this->cryptIv[3];
            $this->aesIn = MTUtils::GetFromHex($this->aesIn);
        }
        //---
        if (empty($this->aesIn)) {
            return false;
        }
        $outResult = '';
        for ($i = 0, $key = 16; $i < $lenPacket; $i++) {
            if ($key >= 16) {
                //--- get new key for xor
                $this->aesIn = $this->cryptIn->encryptBlock($this->aesIn);
                //---
                $key = 0;
            }
            //--- xor all bytes
            $outResult .= chr(ord($packetBody[$i]) ^ ord($this->aesIn[$key]));
            $key++;
        }
        return $outResult;
    }

    /**
     * Get data from MetaTrader 5 server
     *
     * @param bool $authPacket wait the auth packet
     * @param bool $isBinary
     *
     * @return null|string
     */
    public function Read($authPacket = false, $isBinary = false)
    {
        if (!$this->connection) {
            return null;
        }
        //---
        $result = '';
        //---
        while (true) {
            $data = $this->ReadPacket($header);
            //--- check header of packet
            if ($header == null)
                break;
            //---
            if ($data == null && $header->SizeBody > 0) {
                break;
            }
            //--- if need decrypt packet do it
            if ($this->isCrypt && !$authPacket)
                $data = $this->DeCryptPacket($data, $header->SizeBody);
            //--- check number of packet
            if ($header->NumberPacket != $this->clientCommand) {
                //--- check packet length
                if ($header->SizeBody != 0) {
                    // packet number mismatch
                } else {
                    //--- this is PING packet
                }
                //--- read next packet
                continue;
            }
            //--- get result
            $result .= $data;
            //--- read to end
            if ($header->Flag == 0)
                break;
        }
        //--- decoding data
        if ($isBinary) {
            $pos = strpos($result, "\n");
            $firstLine = substr($result, 0, $pos);
            $result = mb_convert_encoding($firstLine, "utf-8", "utf-16le") . "\r\n" . substr($result, $pos);
        } else
            $result = mb_convert_encoding($result, "utf-8", "utf-16le");
        //--- return result
        return $result;
    }

    /**
     * Read packet
     *
     * @param MTHeaderProtocol $header
     *
     * @return string|null
     */
    private function ReadPacket(&$header): ?string
    {
        $header = null;
        //---
        $countRead = socket_recv($this->connection, $headerData, MTHeaderProtocol::HEADER_LENGTH, MSG_WAITALL);
        //$headerData = socket_read($this->connection, MTHeaderProtocol::HEADER_LENGTH, PHP_BINARY_READ);
        //---
        if ($countRead != MTHeaderProtocol::HEADER_LENGTH) {
            return null;
        }
        //--- get header from request
        if (!($header = MTHeaderProtocol::GetHeader($headerData))) {
            return null;
        }
        //---
        $needLen = $header->SizeBody;
        $readLen = 0;
        $data = '';
        $countPacket = 0;
        while ($readLen < $needLen) {
            $countRead = socket_recv($this->connection, $tempData, $needLen - $readLen, MSG_WAITALL); //socket_read($this->connection, $needLen - $readLen, PHP_BINARY_READ);
            //--- check data
            if ($tempData === false) {
                $errorCode = socket_last_error($this->connection);
                $errorMsg = socket_strerror($errorCode);
                return null;
            }
            //--- try get all data
            $data .= $tempData;
            $readLen += $countRead;
            $countPacket++;
        }
        //--- check length
        if ($readLen != $header->SizeBody) {
            return null;
        }
        //---
        return $data;
    }

    /**
     * Get command answer
     *
     * @param string $answer
     * @param int    $pos
     *
     * @return null|string
     */
    public function GetCommand(&$answer, &$pos)
    {
        $pos = mb_strpos($answer, '|', 0, 'UTF-8');
        if ($pos > 0)
            return mb_substr($answer, 0, $pos);
        //---
        return null;
    }

    /**
     * Get next param
     *
     * @param string $answer  - answer from server
     * @param int    $pos     - position that begin find
     * @param int    $posEnd - position of end parametrs
     *
     * @return array|null
     */
    public function GetNextParam(&$answer, &$pos, &$posEnd)
    {
        if ($posEnd < 0) {
            $posEnd = mb_strpos($answer, "\r\n", 0, 'UTF-8');
            if ($posEnd == false)
                $posEnd = strlen($answer);
        }
        $posCode = mb_strpos($answer, '|', $pos + 1, 'UTF-8');
        //---
        if ($posCode > 0 && $posCode < $posEnd) {
            $paramsStr = mb_substr($answer, $pos + 1, $posCode - $pos - 1);
            $params = explode('=', $paramsStr, 2);
            if (count($params) < 2)
                return null;
            //---
            $pos = $posCode;
            //---
            return array(
                'name' => strtoupper($params[0]),
                'value' => $params[1]
            );
        }
        //---
        return null;
    }

    /**
     * Get json from answer
     *
     * @param string $answer
     * @param int    $pos
     *
     * @return null|string
     */
    public function GetJson(&$answer, &$pos)
    {
        //--- find json by first {
        $posCode = mb_strpos($answer, "\n", $pos, 'UTF-8');
        if ($posCode > 0) {
            $jsonStr = trim(mb_substr($answer, $posCode));
            //---
            $pos = strlen($answer);
            //---
            return $jsonStr;
        }
        //---
        return null;
    }

    /**
     * read binary in answer
     *
     * @param $answer
     *
     * @return null|string
     */
    public function GetBinary(&$answer)
    {
        //--- find binary by first {
        $posCode = strpos($answer, "\n");
        if ($posCode > 0) {
            return substr($answer, $posCode);
        }
        //---
        return null;
    }

    /**
     * Get code from string
     *
     * @param string $retCodeString
     *
     * @return int
     */
    public static function GetRetCode($retCodeString): int
    {
        if (empty($retCodeString))
            return 0;
        $p = explode(" ", $retCodeString, 2);
        //---
        return (int) $p[0];
    }

    /**
     * @param string $cryptRand    hash random string from MT server
     * @param string $password password to connection mt server
     *
     * @return void
     */
    public function SetCryptRand($cryptRand, $password)
    {
        $this->cryptRand = $cryptRand;
        $out = md5(md5(mb_convert_encoding($password, 'utf-16le', 'utf-8'), true) . MTProtocolConsts::WEB_API_WORD);
        //---
        for ($i = 0; $i < 16; $i++) {
            $out = md5(MTUtils::GetFromHex(substr($this->cryptRand, $i * 32, 32)) . MTUtils::GetFromHex($out));
            $this->cryptIv[$i] = $out;
        }
    }
}
