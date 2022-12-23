<?php

namespace BeycanPress\SolPay\WooCommerce\Models;

use Beycan\Moodel\AbstractModel;

/**
 * Transaction table model
 */
class Transaction extends AbstractModel 
{
    public function __construct()
    {
        parent::__construct([
            'transactionId' => [
                'type' => 'string',
                'length' => 100,
                'index' => [
                    'type' => 'unique'
                ]
            ],
            'orderId' => [
                'type' => 'integer'
            ],
            'userId' => [
                'type' => 'integer'
            ],
            'status' => [
                'type' => 'string',
                'length' => 10
            ],
            'paymentInfo' => [
                'type' => 'text'
            ],
            'createdAt' => [
                'type' => 'timestamp',
                'default' => 'current_timestamp',
            ],
        ]);
    }

    
    public function search(string $text) : array
    {
        return $this->getResults(str_ireplace(
            '%s', 
            '%' . $this->db->esc_like($text) . '%', "
            SELECT * FROM {$this->tableName} 
            WHERE transactionId LIKE '%s' 
            OR userId LIKE '%s' 
            OR status LIKE '%s' 
            OR orderId LIKE '%s'
            OR paymentInfo LIKE '%s'
			ORDER BY id DESC
        "));
    }

    
    public function searchCount(string $text) : float
    {
        return (int) $this->getVar(str_ireplace(
            '%s', 
            '%' . $this->db->esc_like($text) . '%', "
            SELECT COUNT(id) FROM {$this->tableName} 
            WHERE transactionId LIKE '%s' 
            OR userId LIKE '%s' 
            OR status LIKE '%s' 
            OR orderId LIKE '%s'
            OR paymentInfo LIKE '%s'
        "));
    }
}