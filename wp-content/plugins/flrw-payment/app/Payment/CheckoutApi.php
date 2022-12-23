<?php

namespace BeycanPress\SolPay\WooCommerce\Payment;

use \Beycan\Response;
use \Beycan\CurrencyConverter;
use \Beycan\SolanaWeb3\Connection;
use \BeycanPress\SolPay\WooCommerce\Settings;
use \BeycanPress\SolPay\WooCommerce\PluginHero\Api;
use \BeycanPress\SolPay\WooCommerce\Models\Transaction;
use \BeycanPress\SolPay\WooCommerce\Services\Verifier;

class CheckoutApi extends Api
{
    /**
     * @var object
     */
    private $order;

    /**
     * @var int
     */
    private $userId;

    public function __construct()
    {
        $this->userId = get_current_user_id();
        $this->addRoutes([
            'solpay-api/woocommerce' => [
                'check-payment' => [
                    'callback' => 'checkPayment',
                    'methods' => ['POST']
                ],
                'save-transaction' => [
                    'callback' => 'saveTransaction',
                    'methods' => ['POST']
                ],
                'payment-finished' => [
                    'callback' => 'paymentFinished',
                    'methods' => ['POST']
                ],
                'currency-converter' => [
                    'callback' => 'currencyConverter',
                    'methods' => ['POST']
                ]
            ]
        ]);
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function checkPayment(\WP_REST_Request $request) : void
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));

        if ($this->order->get_status() != 'pending') {
            Response::error(esc_html__('This order is not waiting for payment.', 'solpay'), [
                'redirect' => $this->order->get_view_order_url()
            ]);
        }

        if (!$paymentPrice = $this->calculatePaymentPrice($paymentInfo->selectedCurrency)) {
            Response::error(esc_html__('There was a problem converting currency!', 'solpay'), null, 'CCERR');
        }

        Response::success(null, [
            'paymentPrice' => $paymentPrice,
            'receiver' => $this->setting('walletAddress')
        ]);
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function paymentFinished(\WP_REST_Request $request) : void
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));
        
        if (!isset($paymentInfo->transactionId)) {
            Response::badRequest(esc_html__('Please enter a valid data.', 'solpay'));
        }

        $txModel = new Transaction();
        if (!$transaction = $txModel->findOneBy(['transactionId' => $paymentInfo->transactionId])) {
            Response::error(esc_html__('Transaction record not found!', 'solpay'));
        }

        $verifier = new Verifier();

        try {
            $result = $verifier->verifyTransaction($paymentInfo);
        } catch (\Exception $e) {
            Response::error(esc_html__('Payment not verified via Blockchain', 'solpay'), [
                'redirect' => 'reload'
            ]);
        }

        $paymentInfo->status = $result == 'failed' ? 'failed' : 'verified';

        do_action(
            "SolPay/WooCommerce/PaymentFinished", 
            $this->userId, $this->order, $paymentInfo
        );

        //if ($result == 'verified') {
            $verifier->updateOrderAsComplete($this->order, $transaction->id);
            Response::success(esc_html__('Payment completed successfully', 'solpay'), [
                'redirect' => $this->order->get_checkout_order_received_url()
            ]);
        // } else {
        //     $verifier->updateOrderAsFail($this->order, $transaction->id);
        //     Response::error(esc_html__('Payment not verified via Blockchain', 'solpay'), [
        //         'redirect' => $this->order->get_view_order_url()
        //     ]);
        // }
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function saveTransaction(\WP_REST_Request $request) : void
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));
        
        $transactionData = [
            'transactionId' => $paymentInfo->transactionId,
            'orderId' => $paymentInfo->order->id,
            'userId' => $this->userId,
            'status' => 'pending',
            'paymentInfo' => serialize($paymentInfo)
        ];
        
        (new Transaction())->insert($transactionData);

        $this->order->update_meta_data(
            esc_html__('Cluster', 'solpay'),
            $paymentInfo->usedCluster->name
        );

        $this->order->update_meta_data(
            esc_html__('Transaction id', 'solpay'),
            $paymentInfo->transactionId
        );

        $this->order->update_meta_data(
            esc_html__('Payment currency', 'solpay'),
            $paymentInfo->paymentCurrency
        );

        $this->order->update_meta_data(
            esc_html__('Payment price', 'solpay'),
            $paymentInfo->paymentPrice
        );

        if (isset($paymentInfo->discountRate)) {
            $this->order->update_meta_data(
                esc_html__('Discount', 'solpay'),
                $paymentInfo->discountRate . ' %'
            );

            $this->order->update_meta_data(
                esc_html__('Real price', 'solpay'),
                $paymentInfo->realPrice . " " . $paymentInfo->paymentCurrency
            );
        }

        $this->order->update_meta_data(
            esc_html__('Sender address', 'solpay'),
            $paymentInfo->senderAddress
        );

        $this->order->update_status('wc-on-hold');

        $this->order->save();

        do_action(
            "SolPay/WooCommerce/PaymentStarted", 
            $this->userId, $this->order, $paymentInfo
        );

        Response::success();
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function currencyConverter(\WP_REST_Request $request) : void
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));

        if (!$paymentPrice = $this->calculatePaymentPrice($paymentInfo->selectedCurrency)) {
            Response::error(esc_html__('There was a problem converting currency!', 'solpay'), null, 'CCERR');
        }

        Response::success(null, [
            'paymentPrice' => $paymentPrice
        ]);
    }

    /**
     * @param string $paymentInfo
     * @return object
     */
    public function validatePaymentInfo(string $paymentInfo) : object
    {
        $paymentInfo = !is_null($paymentInfo) ? $this->parseJson($paymentInfo) : false;
        
        if (!$paymentInfo || !isset($paymentInfo->order)) {
            Response::badRequest(esc_html__('Please enter a valid data.', 'solpay'));
        }

        if (!$this->order = wc_get_order($paymentInfo->order->id)) {
            Response::error(esc_html__('The relevant order was not found!', 'solpay'), null, 'NOOR');
        }

        return $paymentInfo;
    }

    /**
     * @param object $selectedCurrency
     * @return null|float
     */
    public function calculatePaymentPrice(object $selectedCurrency) : ?float
    {
        $customTokens = Settings::getCustomTokens();
        $tokenDiscounts = Settings::getTokenDiscounts();

        $orderPrice = (float) $this->order->get_total();
        $orderCurrency = $this->order->get_currency();
        $paymentCurrency = $selectedCurrency->symbol;

        $paymentPrice = apply_filters(
            "SolPay/WooCommerce/CurrencyConverter", 
            "no-custom-converter", 
            $selectedCurrency, 
            $orderCurrency, 
            $orderPrice
        );

        if (is_null($paymentPrice)) return null;

        if ($paymentPrice == 'no-custom-converter') {
            if (isset($customTokens[$paymentCurrency])) {
                $customToken = $customTokens[$paymentCurrency];
                if ($customToken[$orderCurrency]) {
                    $paymentPrice = $this->toFixed(($orderPrice / $customToken[$orderCurrency]), 6);
                } else {
                    return null;
                }
            } else {
                try {
                    $converter = new CurrencyConverter($this->setting('converter'), $this->setting('coinMarketCapApiKey'));
                    $paymentPrice = $converter->convert($orderCurrency, $paymentCurrency, $orderPrice);
        
                    if (!$paymentPrice) return null;
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        if (isset($tokenDiscounts[$paymentCurrency])) {
            $discountRate = $tokenDiscounts[$paymentCurrency];
            $discountPrice = ($paymentPrice * $discountRate) / 100;
            return $this->toFixed(($paymentPrice - $discountPrice), 6);
        }

        return $this->toFixed($paymentPrice, 6);
    }
}
