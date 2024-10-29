<div class="ticketsys-widget" style="margin-bottom:10px;">
    <button href="#data" id="inline" class="btn btn-success"><i class="fa fa-comment-o fa-lg"></i> <?php _e('Contact us', 'ticketsys'); ?></button>
    <div style="display:none !important;">
        <div id="data">
            <?php
            include plugin_dir_path(__FILE__) . './index.php';
            ?>
        </div>
    </div>
</div>

<script>
var $ = jQuery.noConflict();
 $(document).ready(function() {
    $(function($) {
        $('.ticketsys-widget button#inline').fancybox({
            height: 700,
            width: 520,
            fitToView: false,
            autoSize: true
        });
    });
});
</script>