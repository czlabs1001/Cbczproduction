<?php

namespace BeycanPress\SolPay\WooCommerce\Services;

use \Beycan\SolanaWeb3\Connection;
use \Beycan\SolanaWeb3\Transaction;
use \BeycanPress\SolPay\WooCommerce\PluginHero\Helpers;
use \BeycanPress\SolPay\WooCommerce\Models\Transaction as TransactionModel;

class Verifier
{
    use Helpers;
    
    public function __construct()
    {
        $this->transaction = new TransactionModel();
    }
    
    /**
     * Beklemede olan işlemleri doğrular
     * @param int $userId
     * @return void
     */
    public function verifyPendingTransactions($userId = 0) : void
    {
        if ($userId == 0) {
            $transactions = $this->transaction->findBy([
                'status' => 'pending'
            ]);
        } else {
            $transactions = $this->transaction->findBy([
                'status' => 'pending',
                'userId' => $userId
            ]);
        }
        
        if (empty($transactions)) return;

        $uniqureTransactions = [];
        foreach($transactions as $transaction) {
            $uniqureTransactions[$transaction->orderId] = $transaction;
        }

        $transactions = array_values($uniqureTransactions);

        foreach ($transactions as $key => $transaction) {
            
            $paymentInfo = unserialize($transaction->paymentInfo);

            try {
        
                $result = $this->verifyTransaction($paymentInfo);

                $paymentInfo->status = $result == 'failed' ? 'failed' : 'verified';

                if ($result == 'pending') continue;

                if ($order = wc_get_order($paymentInfo->order->id)) {

                    if ($result == 'failed') {
                        $this->updateOrderAsFail($order, $transaction->id);
                    } else {
                        $this->updateOrderAsComplete($order, $transaction->id);
                    }

                    do_action(
                        "SolPay/WooCommerce/PaymentFinished", 
                        $this->userId, $order, $paymentInfo
                    );

                } else {
                    $this->transaction->update(['status' => 'failed'], ['id' => $transaction->id]);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * Siparişi tamamlandı veya işleme alındı olarak gönceller
     * @param object $order
     * @param string $transactionId
     * @return void
     */
    public function updateOrderAsComplete(object $order, string $transactionId) : void
    {
        if ($this->setting('paymentCompleteOrderStatus') == 'wc-completed') {
            $note = esc_html__('Your order is complete.', 'solpay');
        } else {
            $note = esc_html__('Your order is processing.', 'solpay');
        }

        $this->transaction->update(['status' => 'verified'], ['id' => $transactionId]);
        
        $order->payment_complete();

        $order->update_status($this->setting('paymentCompleteOrderStatus'), $note);

    }

    /**
     * Siparişi failed olarak günceller
     * @param object $order
     * @param string $transactionId
     * @return void
     */
    public function updateOrderAsFail(object $order, string $transactionId) : void
    {
        $this->transaction->update(['status' => 'failed'], ['id' => $transactionId]);

        $order->update_status('wc-failed', esc_html__('Payment not verified via Blockchain!', 'solpay'));
    }

    /**
     * @param object $paymentInfo
     * @return string
     */
    public function verifyTransaction(object $paymentInfo) : string
    {
        // Connect JSON-RPC api
        new Connection($paymentInfo->usedCluster->node);
        
        $transaction = new Transaction($paymentInfo->transactionId);

        $result = $transaction->verifyWithAmount(
            $paymentInfo->paymentPrice, 
            $paymentInfo->selectedCurrency->address
        );

        return $result;
    }
}
