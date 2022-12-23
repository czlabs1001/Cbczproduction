<?php 

namespace BeycanPress\SolPay\WooCommerce\Pages;

use \Beycan\WPTable\Table;
use \Beycan\SolanaWeb3\Utils;
use \Beycan\SolanaWeb3\Connection;
use \Beycan\SolanaWeb3\Transaction;
use \BeycanPress\SolPay\WooCommerce\PluginHero\Page;
use \BeycanPress\SolPay\WooCommerce\Models\Transaction as TransactionModel;
use \BeycanPress\SolPay\WooCommerce\Services\Verifier;

/**
 * Transaction list page
 */
class TransactionList extends Page
{   
    /**
     * Class construct
     * @return void
     */
    public function __construct()
    {
        parent::__construct([
            'pageName' => esc_html__('Flwr Pay', 'solpay'),
            'subMenuPageName' => esc_html__('Transaction list', 'solpay'),
            'icon' => $this->getImageUrl('menu.png'),
            'subMenu' => true
        ]);
    }

    /**
     * @return void
     */
    public function page() : void
    {
        (new Verifier())->verifyPendingTransactions();

        $transaction = new TransactionModel();

        if (isset($_GET['id']) && $transaction->delete(['id' => absint($_GET['id'])])) {
            $this->notice(esc_html__('Successfully deleted!', 'solpay'), 'success', true);
        }

        $table = (new Table($transaction))->setColumns([
            'transactionId' => esc_html__('Transaction id', 'solpay'),
            'usedCluster'   => esc_html__('Used cluster', 'solpay'),
            'usedWallet'    => esc_html__('Used wallet', 'solpay'),
            'paymentPrice'  => esc_html__('Payment price', 'solpay'),
            'orderId'       => esc_html__('Order id', 'solpay'),
            'orderPrice'    => esc_html__('Order price', 'solpay'),
            'discount'      => esc_html__('Discount', 'solpay'),
            'senderAddress' => esc_html__('Sender address', 'solpay'),
            'status'        => esc_html__('Status', 'solpay'),
            'createdAt'     => esc_html__('Created at', 'solpay'),
            'delete'        => esc_html__('Delete', 'solpay')
        ])
        ->setOrderQuery(['createdAt', 'desc'])
        ->setOptions([
            'search' => [
                'id' => 'search-box',
                'title' => esc_html__('Search...', 'solpay')
            ]
        ])
        ->addHooks([
            'transactionId' => function($transaction) {
                $paymentInfo = unserialize($transaction->paymentInfo);
                new Connection($paymentInfo->usedCluster->node);
                $url = (new Transaction($transaction->transactionId))->getUrl();
                return '<a href="'.esc_url($url).'" target="_blank">'.esc_html($transaction->transactionId).'</a>';
            },
            'usedCluster' => function($transaction) {
                $paymentInfo = unserialize($transaction->paymentInfo);
                return esc_html($paymentInfo->usedCluster->name);
            },
            'usedWallet' => function($transaction) {
                $paymentInfo = unserialize($transaction->paymentInfo);
                return isset($paymentInfo->usedWallet) ? esc_html($paymentInfo->usedWallet) : null;
            },
            'paymentPrice' => function($transaction) {
                $paymentInfo = unserialize($transaction->paymentInfo);
                $paymentPrice = Utils::toString($paymentInfo->paymentPrice, $paymentInfo->selectedCurrency->decimals);
                return esc_html($paymentPrice . " " . $paymentInfo->paymentCurrency);
            },
            'orderPrice' => function($transaction) {
                $order = unserialize($transaction->paymentInfo)->order;
                return esc_html($order->price . " " . $order->currency);
            },
            'discount' => function($transaction) {
                $paymentInfo = unserialize($transaction->paymentInfo);
                if (isset($paymentInfo->discountRate)) {
                    return esc_html__('Discount: ', 'solpay') . " " . $paymentInfo->discountRate . ' % <br><br>' . esc_html__('Real price: ', 'solpay') . " " . $paymentInfo->realPrice . " " . $paymentInfo->paymentCurrency;
                }
                return esc_html__('No discount', 'solpay');
            },
            'senderAddress' => function($transaction) {
                $paymentInfo = unserialize($transaction->paymentInfo);
                $node = $paymentInfo->usedCluster->node;
                $explorer = $paymentInfo->usedCluster->explorer;
                $url = $explorer . 'account/' . $paymentInfo->senderAddress;
                $url .= $node != 'mainnet-beta' ? '?cluster=' . $node : '';
                return '<a href="'.esc_url($url).'" target="_blank">'.esc_html($paymentInfo->senderAddress).'</a>';
            },
            'status' => function($transaction) {
                if ($transaction->status == 'pending') {
                    return esc_html__('Pending', 'solpay');
                } elseif ($transaction->status == 'verified') {
                    return esc_html__('Verified', 'solpay');
                } elseif ($transaction->status == 'failed') {
                    return esc_html__('Failed', 'solpay');
                }
            },
            'delete' => function($transaction) {
                if (strtolower($transaction->status) == 'pending') return;
                return '<a class="button" href="'.$this->getCurrentUrl() . '&id=' . $transaction->id.'">'.esc_html__('Delete', 'solpay').'</a>';
            }
        ])
        ->addHeaderElements(function() {
            return $this->view('pages/transaction-list/form');
        })
        ->setSortableColumns([
            'createdAt'
        ])
        ->createDataList(function(object $model, array $orderQuery, int $limit, int $offset) {
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $s = sanitize_text_field($_GET['status']);
                if (isset($dataList)) {
                    $dataList = array_filter($dataList, function($obj) use ($s) {
                        return $obj->status == $s;
                    });
                    $dataListCount = count($dataList);
                } else {
                    $dataList = $model->findBy([
                        'status' => $s,
                    ], $orderQuery, $limit, $offset);
                    $dataListCount = $model->getCount([
                        'status' => $s,
                    ]);
                }

                return [$dataList, $dataListCount];
            } 

            return null;
        });

        $this->viewEcho('pages/transaction-list/index', [
            'table' => $table
        ]);
    }

}