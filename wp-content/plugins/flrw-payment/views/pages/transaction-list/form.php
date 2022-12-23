<form>
    <?php 
        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) { 
                ?> <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>"/> <?php 
            }
        } 
    ?>

    <select name="status">
        <option value=""><?php echo esc_html__('Filter by status', 'solpay'); ?></option>
        <option value="verified" <?php echo isset($_GET['status']) && $_GET['status'] == 'verified' ? 'selected' : null ?>>
            <?php echo esc_html__('Verified', 'solpay'); ?>
        </option>
        <option value="failed" <?php echo isset($_GET['status']) && $_GET['status'] == 'failed' ? 'selected' : null ?>>
            <?php echo esc_html__('Failed', 'solpay'); ?>
        </option>
        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : null ?>>
            <?php echo esc_html__('Pending', 'solpay'); ?>
        </option>
    </select>

    <button class="button"><?php echo esc_html__('Filter', 'solpay'); ?></button>
</form>