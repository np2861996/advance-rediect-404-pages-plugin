<?php
if (isset($_POST['ARP_Save_Options'])) {
    $arp_status = sanitize_text_field($_POST['arp_status']);
    $arp_redirect_to = sanitize_text_field($_POST['arp_redirect_to']);
    $nonce = $_POST['_wpnonce'];
    if (wp_verify_nonce($nonce, 'r404option_nounce')) {
        update_option('arp_status_404r', $arp_status);
        update_option('arp_redirect_to_404r', $arp_redirect_to);
        arp_success_option_msg_404r('Settings Saved!');
    } else {
        arp_failure_option_msg_404r('Unable to save data!');
    }
}

$arp_status = arp_get_status_404r();

$arp_redirect_to = arp_get_redirect_to_404r();

$default_tab = null;
$tab = "";
$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;

?>
<div class="arp-main-box">
    <div class="arp-container">
        <div class="arp-header">
            <h1 class="arp-h1"> <?php _e('Advance Redirect 404 Pages', 'advance-redirect-pages'); ?></h1>
        </div>

        <div class="arp-option-section">

            <div class="arp-tabbing-box">
                <ul class="arp-tab-list">

                    <li><a href="?page=advance-redirect-404-pages" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>"><?php _e('General Option', 'advance-redirect-pages'); ?></a></li>
                    <li><a href="?page=advance-redirect-404-pages&tab=arp-404-urls" class="nav-tab <?php if ($tab === 'arp-404-urls') : ?>nav-tab-active<?php endif; ?>"><?php _e('404 Logs', 'advance-redirect-pages'); ?></a></li>

                </ul>
            </div>

            <?php
            if ($tab == null) {
            ?>
                <section class="arp-section">
                    <div class='arp_inner'>
                        <form method="POST">
                            <table class="form-table">
                                <tbody>

                                    <tr valign="top">
                                        <th scope="row">Status</th>
                                        <td>

                                            <select id="satus_404r" name="arp_status">
                                                <option value="1" <?php if ($arp_status == 1) { echo "selected"; } ?>>Enabled </option>
                                                <option value="0" <?php if ($arp_status == 0) { echo "selected"; } ?>>Disabled </option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row">Redirect all 404 pages to: </th>
                                        <td>

                                            <input type="text" name="arp_redirect_to" id="arp_redirect_to" class="regular-text" value="<?php echo esc_url($arp_redirect_to); ?>">
                                            <p class="description">Links that redirect for all 404 pages.</p>

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce = wp_create_nonce('r404option_nounce'); ?>" />
                            <input class="button-primary arp-submit" type="submit" value="Update" name="ARP_Save_Options">
                        </form>
                    </div>
                </section>
            <?php
            }
            if ($tab == "arp-404-urls") {
            ?>
                <section class="arp-section">
                    <div class="arp-error-lists">

                        <table class="wp-list-table widefat striped">
                            <thead>

                                <tr>
                                    <th>#</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                    <th>URL</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                global $wpdb;
                                $table_name = $wpdb->prefix . 'arp_links_lists';

                                $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

                                $limit = 20; 
                                $offset = ($pagenum - 1) * $limit;
                                $total = $wpdb->get_var("select count(*) as total from {$table_name} ORDER BY 'time' DESC");
                                $num_of_pages = ceil($total / $limit);

                               $rows = $wpdb->get_results($wpdb->prepare("SELECT * from {$wpdb->prefix}arp_links_lists ORDER BY `time` DESC limit %d, %d", $offset,$limit  ));
                                 //$rows = $wpdb->get_results("SELECT * from $table_name ORDER BY 'time' DESC  limit $offset, $limit");
                                
                                $rowcount = $wpdb->num_rows;

                                ?>

                                <?php
                                if ($rowcount > 0) {
                                    $i = 1;
                                    foreach ($rows as $row) { ?>
                                        <tr>
                                            <td class="manage-column ss-list-width"><?php echo esc_html($i); ?></td>
                                            <td class="manage-column ss-list-width"><?php echo esc_html($row->ip_address); ?></td>
                                            <td class="manage-column ss-list-width"><?php echo esc_html($row->time); ?></td>
                                            <td class="manage-column ss-list-width"><a href="<?php echo esc_html($row->url); ?>" target="_blank" ><?php echo esc_html($row->url); ?></a></td> 
                                        </tr>
                                    <?php 
                                    $i++;
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                        <?php
                        $page_links = paginate_links( array(
                            'base'      => add_query_arg('pagenum', '%#%'),
							'current'   => max( 1, get_query_var('paged') ),
							'prev_next' => true,
                            'total'     => esc_html($num_of_pages),
                            'current'   => esc_html($pagenum),
                            'type'      => 'array',
                            'prev_text' => __('&laquo;', 'advance-redirect-pages'),
                            'next_text' => __('&raquo;', 'advance-redirect-pages'),
				
						) );

                
                        ?>
                        <div class="arp-pagination-sec">

                            <?php
                            if ( $page_links ) {
                                $data = '<ul class="arp-page-numbers">     
                                            <li>
                                                '.join( '</li><li>', $page_links ).'
                                            </li>                               
                                        </ul>';
                            }

                            echo wp_kses_post($data);

                            ?>
                        </div>
                    </div>
                </section>
            <?php
            }
            ?>
        </div>
    </div>
</div>