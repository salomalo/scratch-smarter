<?php
global $KcSeoWPSchema;
$settings = get_option($KcSeoWPSchema->options['main_settings']);
?>
<div class="wrap">
    <h2><?php _e('Schema Settings', "wp-seo-structured-data-schema"); ?></h2>

    <div id="kcseo-settings">
        <div id="kcseo-options">
            <form id="kcseo-main-settings">
                <table width="40%" cellpadding="10" class="form-table">
                    <tr class="default">
                        <th><?php _e("Business / Org Schema", "wp-seo-structured-data-schema") ?></th>
                        <td align="left" scope="row">
                            <?php $dd = !empty($settings['site_schema']) ? $settings['site_schema'] : 'home_page'; ?>
                            <input type="radio" <?php echo($dd == 'home_page' ? 'checked' : null); ?> name="site_schema"
                                   value="home_page" id="site_schema_home"><label for="site_schema_home"><?php _e("Home page
                                only", "wp-seo-structured-data-schema") ?></label><br>
                            <input type="radio" <?php echo($dd == 'all' ? 'checked' : null); ?> name="site_schema"
                                   value="all"
                                   id="site_schema_all"><label for="site_schema_all"><?php _e("Sitewide (Apply General Settings schema
                                sitewide)", "wp-seo-structured-data-schema") ?></label><br>
                            <input type="radio" <?php echo($dd == 'off' ? 'checked' : null); ?> name="site_schema"
                                   value="off"
                                   id="site_schema_off"><label
                                    for="site_schema_off"><?php _e("Turn off (Turn off global schema)", "wp-seo-structured-data-schema") ?></label>
                        </td>
                    </tr>
                    <tr class="default">
                        <th><?php _e("Delete all data", "wp-seo-structured-data-schema") ?></th>
                        <td align="left" scope="row">
                            <?php $dd = !empty($settings['delete-data']) ? "checked" : null; ?>
                            <input type="checkbox" <?php echo $dd; ?> name="delete-data" value="1"
                                   id="delete-data"><label
                                    for="delete-data"><?php _e("Enable", "wp-seo-structured-data-schema") ?></label>
                            <p class="description"><?php _e("This will delete all schema created and applied by this plugin when plugin is
                                deleted.", "wp-seo-structured-data-schema") ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit"><input type="submit" name="submit" id="tlpSaveButton" class="button button-primary"
                                         value="<?php _e('Save Changes', "wp-seo-structured-data-schema"); ?>"></p>

                <?php wp_nonce_field($KcSeoWPSchema->nonceText(), '_kcseo_nonce'); ?>
            </form>
            <div id="response"></div>
        </div>
        <div class='kc-get-pro'>
            <h3><?php _e("Pro Version Features", "wp-seo-structured-data-schema") ?></h3>
            <ol>
                <li><?php _e("Includes Auto-fill function <---Popular", "wp-seo-structured-data-schema") ?></li>
                <li><?php _e("Supports Custom Post Types beyond default page and posts", "wp-seo-structured-data-schema") ?></li>
                <li><?php _e("Supports WordPress Multisite", "wp-seo-structured-data-schema") ?></li>
                <li><?php _e("Supports more schema types:", "wp-seo-structured-data-schema") ?>
                    <ol>
                        <li><?php _e("Books", "wp-seo-structured-data-schema") ?></li>
                        <li><?php _e("Courses", "wp-seo-structured-data-schema") ?></li>
                        <li><?php _e("Job Postings", "wp-seo-structured-data-schema") ?></li>
                        <li><?php _e("Movies", "wp-seo-structured-data-schema") ?></li>
                        <li><?php _e("Music", "wp-seo-structured-data-schema") ?></li>
                        <li><?php _e("Recipe", "wp-seo-structured-data-schema") ?></li>
                        <li><?php _e("TV Episode", "wp-seo-structured-data-schema") ?></li>
                    </ol>
                </li>
            </ol>
            <div class="kc-pro-action"><a class='button button-primary'
                                          href='https://wpsemplugins.com/downloads/wordpress-schema-plugin/'
                                          target='_blank'><?php _e("Get the Pro Version", "wp-seo-structured-data-schema") ?></a>
            </div>
        </div>
    </div>

</div>