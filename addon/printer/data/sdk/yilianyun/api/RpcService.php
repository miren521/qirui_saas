<?php

namespace addon\printer\data\sdk\yilianyun\api;

use addon\printer\data\sdk\yilianyun\config\YlyConfig;
use addon\printer\data\sdk\yilianyun\protocol\YlyRpcClient;

class RpcService{

    protected $client;

    public function __construct($token, YlyConfig $config)
    {
        $this->client = new YlyRpcClient($token, $config);
    }

}