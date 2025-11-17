<?php
// Posts tab UI and submit handled via admin-post.php action
$distribute_options = [
    '1_hour' => '1 hour',
    '6_hours' => '6 hours',
    '12_hours' => '12 hours',
    '1_day' => '1 day',
    '7_days' => '7 days',
    '30_days' => '30 days',
    'custom' => 'Custom Range'
];
$categories = get_categories(['hide_empty' => false]);
?>
<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
    <?php wp_nonce_field('wwpud_posts_action','wwpud_posts_nonce'); ?>
    <input type="hidden" name="action" value="wwpud_update_posts">
    <table class="form-table">
        <tr>
            <th>Distribute into Last</th>
            <td>
                <select name="distribute" id="wwpud-distribute">
                    <?php foreach ($distribute_options as $k=>$v): ?>
                        <option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($v); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Select range of date in which you want to spread the dates</p>
            </td>
        </tr>
        <tr class="wwpud-custom-range-row" style="display:none;">
            <th>Custom Date Range</th>
            <td>
                <input type="text" name="custom_from" id="wwpud-custom-from" placeholder="YYYY-MM-DD"> -
                <input type="text" name="custom_to" id="wwpud-custom-to" placeholder="YYYY-MM-DD">
                <p class="description">Select range of date in which you want to spread the dates</p>
            </td>
        </tr>
        <tr>
            <th>Select Categories</th>
            <td>
                <select name="cats[]" multiple size="6" style="min-width:250px;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name) . ' (' . intval($cat->count) . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Will apply on all posts if no category is selected. Select multiple categories by holding Ctrl or Command key while selecting.</p>
            </td>
        </tr>
        <tr>
            <th>Mode</th>
            <td>
                <label><input type="radio" name="mode" value="modified" checked> Modified Date</label>
                &nbsp;
                <label><input type="radio" name="mode" value="published"> Published Date</label>
                &nbsp;
                <label><input type="radio" name="mode" value="both"> Both</label>
            </td>
        </tr>
    </table>
    <p><button class="button button-primary" type="submit">Update Post Dates</button></p>
</form>

<script>
(function(){
    var sel = document.getElementById('wwpud-distribute');
    var row = document.querySelector('.wwpud-custom-range-row');
    function toggle(){ row.style.display = sel.value === 'custom' ? 'table-row' : 'none'; }
    sel.addEventListener('change', toggle);
    toggle();
})();
</script>
