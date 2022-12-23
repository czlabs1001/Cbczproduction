<?php

namespace BeycanPress\SolPay\WooCommerce\Payment;

use \Beycan\SolanaWeb3\Utils;
use \Beycan\SolanaWeb3\Connection;
use \Beycan\SolanaWeb3\Transaction;
use \BeycanPress\SolPay\WooCommerce\PluginHero\Helpers;
use \BeycanPress\SolPay\WooCommerce\Models\Transaction as TransactionModel;
use \BeycanPress\SolPay\WooCommerce\Services\Verifier;

class Details
{
    use Helpers;

    /**
     * @var object
     */
    private $verifier;

    public function __construct()
    {
        $this->verifier = new Verifier();

        // Ürün listesi yüklenmeden önce beklemede olan işlemleri doğrular
        add_action('woocommerce_before_account_orders', function() {
            $this->verifier->verifyPendingTransactions(get_current_user_id());
        });

        // Detay sayfasını teşekkür ve sipariş gösterim sayfasına dahil eder
        add_action('woocommerce_view_order', array($this, 'init'), 4);
        add_action('woocommerce_thankyou_'. Gateway::$gateway , array($this, 'init'), 1);
    }

    /**
     * Detay bölümünü yükler
     * @param int $orderId
     * @return void
     */
    public function init($orderId) : void
    {
        // Detay yüklenmeden önce beklemede olan işlemleri doğrular
        $this->verifier->verifyPendingTransactions(get_current_user_id());

        $order = wc_get_order($orderId);

        if (Gateway::$gateway == $order->get_payment_method()) {
            $transaction = (new TransactionModel())->findOneBy([
                'orderId' => $orderId
            ], ['id', 'DESC']);
    
            if ($order->get_status() == 'pending') {
                $this->viewEcho('payment/pending', ['payUrl' => $order->get_checkout_payment_url(true)]);
            } elseif (!is_null($transaction)) {
                $paymentInfo = unserialize($transaction->paymentInfo);
                $paymentPrice = Utils::toString($paymentInfo->paymentPrice, $paymentInfo->selectedCurrency->decimals);
                new Connection($paymentInfo->usedCluster->node);
                $transactionUrl = (new Transaction($transaction->transactionId))->getUrl();
                $this->viewEcho('payment/details', compact('transaction', 'paymentPrice', 'paymentInfo', 'transactionUrl'));
            }
        }
    }

}
