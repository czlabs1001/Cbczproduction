<?php

namespace Beycan\SolanaWeb3;

use Exception;

final class Connection
{
    private $chainIds = [
        "devnet" => 103,
        "testnet" => 102,
        "mainnet-beta" => 101,
    ];
    
    private $clusters = [
        "mainnet-beta" => [
            "node" => "mainnet",
            "name" => "Mainnet",
            "host" => "https://weathered-sleek-choice.solana-mainnet.discover.quiknode.pro/479c28744528bc7ff0a7f944474f75550886ddbc/",
            "explorer" => "https://solscan.io/"
        ],
        "testnet" => [
            "node" => "testnet",
            "name" => "Testnet",
            "host" => "https://api.testnet.solana.com",
            "explorer" => "https://solscan.io/"
        ],
        "devnet" => [
            "node" => "devnet",
            "name" => "Devnet",
            "host" => "https://api.devnet.solana.com",
            "explorer" => "https://solscan.io/"
        ]
    ];

    private $errorCodes = [
        "parse-error" => -32700,
        "invalid-request" => -32600,
        "method-not-found" => -32601,
        "invalid-parameters" => -32602,
        "internal-error" => -32603,
    ];

    private $allowedMethods = [
        'getAccountInfo', 'getBalance', 'getBlock', 'getBlockHeight', 'getBlockProduction', 'getBlockCommitment', 'getBlocks', 'getBlocksWithLimit', 'getBlockTime', 'getClusterNodes', 'getEpochInfo', 'getEpochSchedule', 'getFeeForMessage', 'getFirstAvailableBlock', 'getGenesisHash', 'getHealth', 'getHighestSnapshotSlot', 'getIdentity', 'getInflationGovernor', 'getInflationRate', 'getInflationReward', 'getLargestAccounts', 'getLatestBlockhash', 'getLeaderSchedule', 'getMaxRetransmitSlot', 'getMaxShredInsertSlot', 'getMinimumBalanceForRentExemption', 'getMultipleAccounts', 'getProgramAccounts', 'getRecentPerformanceSamples', 'getSignaturesForAddress', 'getSignatureStatuses', 'getSlot', 'getSlotLeader', 'getSlotLeaders', 'getStakeActivation', 'getSupply', 'getTokenAccountBalance', 'getTokenAccountsByDelegate', 'getTokenAccountsByOwner', 'getTokenLargestAccounts', 'getTokenSupply', 'getTransaction', 'getTransactionCount', 'getVersion', 'getVoteAccounts', 'isBlockhashValid', 'minimumLedgerSlot', 'requestAirdrop', 'sendTransaction', 'simulateTransaction', 'accountSubscribe', 'accountUnsubscribe', 'logsSubscribe', 'logsUnsubscribe', 'programSubscribe', 'programUnsubscribe', 'signatureSubscribe', 'signatureUnsubscribe', 'slotSubscribe', 'slotUnsubscribe'
    ];

    public $cluster;

    public static $connection = null;

    private $randomKey;

    /**
     * @param string $cluster
     * @throws Exception
     */
    public function __construct(string $cluster) 
    {
        if (!array_key_exists($cluster, $this->clusters)) {
            throw new Exception('You entered an invalid cluster!');
        }

        $this->cluster = (object) $this->clusters[$cluster];

        $this->randomKey = random_int(0, 99999999);

        self::$connection = $this;
    }

    /**
     * @param string $method
     * @param array $params
     * @return object|null
     * @throws Exception
     */
    public function __call(string $method, array $params = []) : ?object
    {
        if (preg_match('/^[a-zA-Z0-9]+$/', $method) === 1) {

            if (!in_array($method, $this->allowedMethods)) {
                throw new Exception('Unallowed method: ' . $method);
            }
            
            return $this->call($method, ...$params);
        } else {
            throw new Exception('Invalid method name');
        }
    }

    /**
     * @param string $method
     * @param array $params
     * @return object|null
     * @throws Exception
     */
    public function call(string $method, ...$params) : ?object
    {
        $curl = curl_init($this->cluster->host);

        $headers = [
            "Content-Type: application/json"
        ];

        $rpc = $this->buildRpc($method, $params);

        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $rpc
        ]);

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        $this->validateResponse($response, $method, $params);

        return $response ? $response->result : null;
    }

    /**
     * @param object $response
     * @param string $method
     * @param array $params
     * @return void
     * @throws Exception
     */
    protected function validateResponse(object $response, string $method, array $params) : void
    {
        if ($response->id !== $this->randomKey) {
            throw new Exception('Invalid response');
        }

        if (isset($response->error)) {
            if ($response->error->code === $this->errorCodes['method-not-found']) {
                throw new Exception("API Error: Method {$method} not found.");
            } else {
                throw new Exception($response->error->message);
            }
        }
    }

    /**
     * @return int
     */
    public function getRandomKey() : int
    {
        return $this->randomKey;
    }
    
    /**
     * @param string $method
     * @param array $params
     * @return string
     */
    public function buildRpc(string $method, array $params) : string
    {
        return json_encode([
            'jsonrpc' => '2.0',
            'id' => $this->randomKey,
            'method' => $method,
            'params' => $params,
        ]);
    }

    /**
     * @return Connection
     */
    public static function getConnection() : ?Connection
    {
        return self::$connection;
    }
}