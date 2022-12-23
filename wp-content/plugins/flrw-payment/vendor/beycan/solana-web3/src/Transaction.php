<?php

namespace Beycan\SolanaWeb3;

use Exception;

final class Transaction
{
    /**
     * Connection
     * @var Connection
     */
    private $connection;
    
    /**
     * Transaction signature
     * @var string
     */
    private $signature;

    /**
     * Transaction data
     * @var object
     */
    private $data;

    /**
     * @param string $signature
     * @throws Exception
     */
    public function __construct(string $signature)
    {
        if (!$this->connection = Connection::getConnection()) {
            throw new Exception("Please create a connection first!");
        }

        $this->signature = $signature;

        $this->data = $this->getData();
    }

    /**
     * @return string
     */
    public function getSignature() : string
    {
        return $this->signature;
    }

    /**
     * @return object|null
     */
    public function getData() : ?object
    {
        return $this->data = $this->connection->getTransaction($this->signature);
    }

    /**
     * @return string
     */
    public function verify() : string
    {
        if (is_null($this->data)) {
            return 'pending';
        } else {
            if (is_null($this->data->meta->err)) {
                return 'verified';
            } else {
                return 'failed';
            }
        }
    }

    /**
     * @return string
     */
    public function verifyWithLoop() : string
    {
        if (is_null($this->data = $this->getData())) {
            return $this->verifyWithLoop();
        } else {
            return $this->verify();
        }
    }

    /**
     * @param float $amount
     * @param string|null $tokenAddress
     * @return string
     */
    public function verifyAmount(float $amount, ?string $tokenAddress = null) : string
    {

        if (is_null($tokenAddress) || $tokenAddress == 'SOL') {
            $beforeBalance = $this->data->meta->preBalances[1];
            $afterBalance = $this->data->meta->postBalances[1];
            $diff = Utils::toString(Utils::toDec(($afterBalance - $beforeBalance), 9), 9);
            $amount = Utils::toString($amount, 9);
        } else {
            $decimals = $this->data->meta->preTokenBalances[1]->uiTokenAmount->decimals;
            $beforeBalance = $this->data->meta->preTokenBalances[1]->uiTokenAmount->uiAmount;
            $afterBalance = $this->data->meta->postTokenBalances[1]->uiTokenAmount->uiAmount;
            $diff = Utils::toString(($afterBalance - $beforeBalance), $decimals);
            $amount = Utils::toString($amount, $decimals);
        }
        
        return $diff == $amount ? 'verified' : 'failed';
    }

    /**
     * @param float $amount
     * @param string|null $tokenAddress
     * @return string
     */
    public function verifyWithAmount(float $amount, ?string $tokenAddress = null) : string
    {
        $result = $this->verify();

        if ($result == 'verified') {
            return $this->verifyAmount($amount, $tokenAddress);
        } else {
            return $result;
        }
    }

    /**
     * @return string
     */
    public function getUrl() 
    {
        $node = $this->connection->cluster->node;
        $url  = $this->connection->cluster->explorer . "tx/" . $this->signature;
        $url .= $node != 'mainnet-beta' ? '?cluster=' . $node : '';
        return $url;
    }
}