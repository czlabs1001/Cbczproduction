<section class="solpay-woocommerce-woocommerce-order-details">
    <h2 class="woocommerce-order-details__title"><?php echo esc_html__('SolPay payment details', 'solpay'); ?></h2>
    <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
        <tr>
            <th scope="row">
                <?php echo esc_html__('Price: ', 'solpay'); ?>
            </th>
            <td>
                <?php echo esc_html($paymentPrice); ?> <?php echo esc_html($paymentInfo->paymentCurrency); ?>
            </td>
        </tr>
        <?php if (isset($paymentInfo->discountRate)) : ?>
            <tr>
                <th scope="row">
                    <?php echo esc_html__('Discount: ', 'solpay'); ?>
                </th>
                <td>
                    <?php echo esc_html($paymentInfo->discountRate . ' %'); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo esc_html__('Real price: ', 'solpay'); ?>
                </th>
                <td>
                    <?php echo esc_html($paymentInfo->realPrice . " " . $paymentInfo->paymentCurrency); ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <th scope="row">
                <?php echo esc_html__('Status: ', 'solpay'); ?>
            </th>
            <td>
                <?php
                    if ($transaction->status == 'pending') {
                        echo esc_html__('Pending', 'solpay');
                    } elseif ($transaction->status == 'verified') {
                        echo esc_html__('Verified', 'solpay');
                    } elseif ($transaction->status == 'failed') {
                        echo esc_html__('Failed', 'solpay');
                    }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php echo esc_html__('Transaction id: ', 'solpay'); ?>
            </th>
            <td>
                <a href="<?php echo esc_url($transactionUrl); ?>" target="_blank" style="word-break: break-word">
                    <?php echo esc_html($transaction->transactionId); ?>
                </a>
            </td>
        </tr>
    </table>
</section>