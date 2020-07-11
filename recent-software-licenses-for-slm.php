<?php  /*
Plugin Name:        Recent Software Licenses for SLM
Plugin URI:         https://rocketapps.com.au/
Description:        Custom dashboard widget to show recent software licenses created.
Version:            1.0
Author:             Michael Ott
Author URI:         https://rocketapps.com.au/
*/

/* Create Recent Software Licenses dashboard widget */
function rsl_dashboard_widget_function() { 

    /* Only show the widget if the SLM plugin is activated */
    if ( is_plugin_active( 'software-license-manager/slm_bootstrap.php' ) ) { ?>

        <style>
            #rsl_dashboard_widget .inside {
                padding: 0;
            }
            .rsl-list strong {
                display: inline-block;
                width: 70px;
            }
            #rsl_dashboard_widget .inside {
                margin: 0;
                height: 380px;
                overflow: auto;
            }
            #rsl_dashboard_widget .inside p {
                padding: 15px;
                line-height: 1.5em;
                margin: 0;
                border-bottom: solid 1px #ccd0d4;
            }
            #rsl_dashboard_widget .inside p:last-child {
                border: none;
            }
        </style>

        <?php global $wpdb;
        $lic_table = $wpdb->prefix . "lic_key_tbl";

        $lic_items = $wpdb->get_results("
            SELECT * 
            FROM $lic_table
            ORDER BY id DESC
            LIMIT 10
        ");
        $log_count   = count($lic_items);
        $date_format = get_option('date_format');
        foreach ( $lic_items as $log_item ) { 
            $user_email = get_user_by( 'email', $log_item->email );
        ?>
            <p class="rsl-list">
                <strong>Name:</strong><a href="<?php echo admin_url() . 'user-edit.php?user_id=' . $user_email->ID; ?>"><?php echo $log_item->first_name; ?> <?php echo $log_item->last_name; ?></a> (<?php echo $log_item->email; ?>)<br />
                <strong>Key:</strong><?php echo $log_item->license_key; ?><br />
                <strong>Product:</strong><?php echo $log_item->product_ref; ?> (<?php echo $log_item->max_allowed_domains; ?> domain)<br />
                <strong>Status:</strong><?php echo ucfirst($log_item->lic_status); ?><br />
                <strong>Date:</strong><?php echo date($date_format, strtotime($log_item->date_created)); ?>
            </p>
        <?php }

    } else {
        echo '<p>The Software License Manager plugin is not activated.</p>';
    }
}
function add_rsl_dashboard_widgets() {
    wp_add_dashboard_widget('rsl_dashboard_widget', 'Recent Software Licenses', 'rsl_dashboard_widget_function');
}
add_action('wp_dashboard_setup', 'add_rsl_dashboard_widgets' );
