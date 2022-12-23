<?php

namespace BeycanPress\SolPay\WooCommerce;

class Lang
{
    public static function get() : array
    {
        return [
            "connect" => esc_html__('Connect wallet', 'solpay'),
            "walletNotDetected" => esc_html__('The wallet you want to connect to could not be detected!', 'solpay'),
            "waitingConnection" => esc_html__("Establishing connection please wait!", 'solpay'),
            "waiting" => esc_html__('Waiting...', 'solpay'),
            "connectionRefused" => esc_html__('Connection refused!', 'solpay'),
            "connectedWallet" => esc_html__('Connected wallet: ', 'solpay'),
            "connectedCluster" => esc_html__('Connected cluster: ', 'solpay'),
            "connectedAccount" => esc_html__('Connected account: ', 'solpay'),
            "orderPrice" => esc_html__('Order price: ', 'solpay'),
            "donateAmount" => esc_html__('Donate amount: ', 'solpay'),
            "donate" => esc_html__('Donate', 'solpay'),
            "pleaseEnterDonateAmount" => esc_html__('Please enter donation amount', 'solpay'),
            "confirmDonate" => esc_html__('Confirm donate', 'solpay'),
            "donateRejected" => esc_html__('Donate rejected', 'solpay'),
            "paymentPrice" => esc_html__('Payment price: ', 'solpay'),
            "paymentCurrency" => esc_html__('Payment currency: ', 'solpay'),
            "notCurrencySelected" => esc_html__('Not currency selected', 'solpay'),
            "notCurrencySelectedPay" => esc_html__('Please select the currency you want to pay in', 'solpay'),
            "notCurrencySelectedDonate" => esc_html__('Please select the currency you want to donate in', 'solpay'),
            "payWith" => esc_html__('Pay with', 'solpay'),
            "confirmPayment" => esc_html__('Confirm payment', 'solpay'),
            "cancel" => esc_html__('Cancel', 'solpay'),
            "confirm" => esc_html__('Confirm', 'solpay'),
            "insufficientBalance" => esc_html__('Insufficient balance!', 'solpay'),
            "paymentRejected" => esc_html__('Payment rejected', 'solpay'),
            "paymentFailed" => esc_html__('Payment not verified via Blockchain', 'solpay'),
            "unexpectedError" => esc_html__('An unexpected error has occurred', 'solpay'),
            "pleaseWait" => esc_html__('Please wait...', 'solpay'),
            "confirmWithWallet" => esc_html__('Confirm this action in your wallet', 'solpay'),
            "verifyTransaction" => esc_html__('Awaiting verification of payment via blockchain. Please do not close the page.', 'solpay'),
            "transactionId" => esc_html__('Transaction Id: ', 'solpay'),
            "completedDonate" => esc_html__('Donation sent successfully', 'solpay'),
            "completedPayment" => esc_html__('Payment completed successfully', 'solpay'),
            "disconnect" => esc_html__('Disconnect', 'solpay'),
            'problemConvertingCurrency' => esc_html__('There was a problem converting currency!', 'solpay'),
            "notAcceptedCurrency" => esc_html__("Not accepted currency", 'solpay'),
            "discountRate" => esc_html__("{discountRate}% discount on purchases with this coin.", 'solpay'),
            "connectionFailed" => esc_html__("There was a problem establishing the connection!", 'solpay'),
            "notFoundInfuraId" => esc_html__("Infura id is required for WalletConnect", 'solpay'),
            'alreadyProcessing' => esc_html__("A connection request is already pending, check the wallet plugin!", 'solpay'),
            'transferAmount' => esc_html__('The transfer amount cannot be less than zero.', 'solpay'),
            'nonSupportedCurrency' => esc_html__('The currency you selected is not supported on this network!', 'solpay'),
            "timeOut" => esc_html__('The connection request timed out.', 'solpay')
        ];
    }

}