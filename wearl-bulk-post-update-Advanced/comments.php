<?php
// Comments tab - placeholder simple UI
?>
<p>Update comment dates in bulk (placeholder).</p>
<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
    <?php wp_nonce_field('wwpud_comments_action','wwpud_comments_nonce'); ?>
    <input type="hidden" name="action" value="wwpud_update_comments">
    <table class="form-table">
        <tr>
            <th>Distribute into Last</th>
            <td>
                <select name="distribute">
                    <option value="7_days">7 days</option>
                    <option value="30_days">30 days</option>
                </select>
            </td>
        </tr>
    </table>
    <p><button class="button button-primary" type="submit">Update Comment Dates</button></p>
</form>
