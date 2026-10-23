<?php
/**************************************************************************** 
 $ @ 作者名称: Japhia
 $ @ 创建日期: 2024-10-06 19:42:09
 $ @ 最后修改: 2024-11-18 11:16:30
 $ @ 文件路径: \wml-zib-diy\core\admins\wml-system.php
 $ @ 简要说明: 有问题联系作者：QQ:181682233 邮箱：japhia@mail.com 网址：waimao.la
 $ @ Copyright (c) 2024 by Japhia, All Rights Reserved. 
 ****************************************************************************/

if (! defined('ABSPATH')) {
	die;
}
//后台美化开关
if (wml_zib('admins_switch', false)) {

    $type = wml_zib('admins_select'); //样式
    if ($type=='1') {//字体
        // 引入样式文件
        function wml_zib_admin_style() {
            wp_enqueue_style('zib_admin_style', plugins_url('', __FILE__).'/zib/admin_style.css');
        }
        add_action('admin_enqueue_scripts', 'wml_zib_admin_style');
    }elseif ($type=='2') {
        require_once 'lv/admin-theme.php';
    }elseif ($type=='3') {
        require_once 'kuhei/admin-theme.php';
    }elseif ($type=='4') {
        require_once 'hong/admin-theme.php';
    }
}

//前端输出
//移除版本号
if (wml_zib('remove_version', false)) {
    // 头部
    remove_action('wp_head', 'wp_generator');
    //rss
    add_filter('the_generator', '__return_empty_string');
}

//移除加载文件版本号
if (wml_zib('remove_file_version', false)) {
    function removeFileVersion($src)
    {
        if (strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
    add_filter('style_loader_src', 'removeFileVersion', PHP_INT_MAX);
    add_filter('script_loader_src', 'removeFileVersion', PHP_INT_MAX);
}

//移除dns-prefetch
if (wml_zib('remove_dns_prefetch', false)) {
    remove_action('wp_head', 'wp_resource_hints', 2);
}

//移除头部json链接
if (wml_zib('remove_dns_prefetch', false)) {
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('template_redirect', 'rest_output_link_header');
}

//移除文章页面前后页meta
if (wml_zib('remove_post_meta', false)) {
    //移除前后文、第一篇文章、主页meta信息
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
}

//移除文章头部feed
if (wml_zib('remove_post_feed', false)) {
    //移除feed
    remove_action('wp_head', 'feed_links', 2);//文章和评论feed
    remove_action('wp_head', 'feed_links_extra', 3); //分类等feed
}

//移除Dashicons
if (wml_zib('remove_dashicons', false)) {
    function removeDashicons()
    {
        if (!is_admin_bar_showing() && !is_customize_preview()) {
            wp_dequeue_style('dashicons');
            wp_deregister_style('dashicons');
        }
    }
    add_action('wp_print_styles', 'removeDashicons', 9999);
}

//移除RSD
if (wml_zib('remove_rsd', false)) {
    remove_action('wp_head', 'rsd_link');
}

//移除wlwmanifest
if (wml_zib('remove_wlwmanifest', false)) {
    remove_action('wp_head', 'wlwmanifest_link');
}

//移除短链接
if (wml_zib('remove_shortlink', false)) {
    remove_action('wp_head', 'wp_shortlink_wp_head');
}


//系统加速
//禁用translations_api
if (wml_zib('ban_translations_api', false)) {
    add_filter('translations_api', '__return_true');
}

//禁用current_screen
if (wml_zib('ban_current_screen', false)) {
    add_filter('current_screen', '__return_true');
}

//禁用wp_check_php_version
if (wml_zib('ban_wp_check_php_version', false)) {
    add_filter('wp_check_php_version', '__return_true');
    remove_action('admin_init', 'wp_check_php_version');
    add_filter('wp_is_php_version_acceptable', function ($is_acceptable, $required_version) {
        return true;
    }, 10, 2);
}

//禁用wp_check_browser_version
if (wml_zib('ban_wp_check_browser_version', false)) {
    add_filter('wp_check_browser_version', '__return_true');
    remove_action('admin_head', 'wp_check_browser_version');
}

//系统加速
//关闭后台登录页面logo
if (wml_zib('remove_login_logo', false)) {
    add_action('login_footer', function () {
        ?>
        <script>
            window.addEventListener('load', function () {
                // 所有资源加载完成后的处理逻辑
                jQuery('#login>h1:first-child').remove();
            });
        </script>
        <?php
    });
}

//关闭前台顶部管理工具条
if (wml_zib('close_admin_bar', false)) {
    add_filter('show_admin_bar', '__return_false');
}

//关闭登录页面语言选择
if (wml_zib('close_login_translate', false)) {
    add_filter('login_display_language_dropdown', '__return_false');
}

//关闭仪表盘左上角WordPress的icon
if (wml_zib('remove_dashboard_icon', false)) {
    add_action('wp_before_admin_bar_render', function () {
        global $wp_admin_bar;

        $wp_admin_bar->remove_menu('wp-logo');
    }, 0);
}

//关闭仪表盘左上角WordPress的icon
if (wml_zib('remove_dashboard_content', false)) {
    function wml_remove_dashboard_content() {
        global $wp_meta_boxes;
        // 以下这一行代码将删除 "快速发布" 模块
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        // 以下这一行代码将删除 "引入链接" 模块
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        // 以下这一行代码将删除 "插件" 模块
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        // 以下这一行代码将删除 "近期评论" 模块
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        // 以下这一行代码将删除 "近期草稿" 模块
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
        // 以下这一行代码将删除 "WordPress 开发日志" 模块
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        // 以下这一行代码将删除 "其它 WordPress 新闻" 模块
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
        // 以下这一行代码将删除 "概况" 模块
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        // 以下这一行代码将删除 "概况" 模块
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
        // 以下这一行代码将删除 "动态" 模块
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
        // 以下这一行代码将删除 "Redis" 模块
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_rediscache']);
        // 以下这一行代码将删除 "WPJAM" 模块
        unset($wp_meta_boxes['dashboard']['normal']['core']['column4-sortables']);
    } 
    add_action('wp_dashboard_setup', 'wml_remove_dashboard_content' );
}

//移除后台选项按钮
if (wml_zib('remove_houtai_xuanxiang', false)) {
    add_action('in_admin_header', function(){
        add_filter('screen_options_show_screen', '__return_false');
        add_filter('hidden_columns', '__return_empty_array');
    });
}

//移除后台帮助按钮
if (wml_zib('remove_houtai_bangzhu', false)) {
    add_action('in_admin_header', function(){
        global $current_screen;
        $current_screen->remove_help_tabs();
    });
}

//移除后台右下角版本号
if (wml_zib('remove_houtai_banbenhao', false)) {
    function change_footer_admin () {return '';}
    add_filter('admin_footer_text', 'change_footer_admin', 9999);
    function change_footer_version() {return '';}
    add_filter( 'update_footer', 'change_footer_version', 9999);
}

//移除感谢您使用wordpress创作
if (wml_zib('remove_houtai_ganxiesy', false)) {
    function my_admin_footer_text(){
        return "";
        }
        function my_update_footer()
        {
        return "";
        }
    add_filter( 'admin_footer_text', 'my_admin_footer_text', 99999999 );
    add_filter( 'update_footer', 'my_update_footer', 99999999 );
}

//功能禁用
if (wml_zib('close_rest_api', false)) {
    //屏蔽 REST API
    add_filter('rest_enabled', '__return_false');
    add_filter('rest_jsonp_enabled', '__return_false');
}
if (wml_zib('close_pingback', false)) {
    add_filter('xmlrpc_methods', function ($methods) {
        $methods['pingback.ping'] = '__return_false';
        $methods['pingback.extensions.getPingbacks'] = '__return_false';
        return $methods;
    });
    //禁用 pingbacks, enclosures, trackbacks
    remove_action('do_pings', 'do_all_pings');
    //去掉 _encloseme 和 do_ping 操作。
    remove_action('publish_post', '_publish_post_hook');
}
if (wml_zib('close_xml_rpc', false)) {
    //关闭XML-RPC接口
    add_filter('xmlrpc_enabled', '__return_false');
    add_filter('xmlrpc_methods', '__return_empty_array');
}
if (wml_zib('close_emoji', false)) {
    //禁止emoji
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('embed_head', 'print_emoji_detection_script');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
if (wml_zib('close_revision', false)) {//关闭保存修订版本
    define('WP_POST_REVISIONS', false);
    add_filter('wp_revisions_to_keep', '__return_false');
}
if (wml_zib('close_auto_save_post', false)) {//关闭文章自动保存
    define('AUTOSAVE_INTERVAL', false);
    add_action('admin_print_scripts', function () {
        wp_deregister_script('autosave');
    });
}
if (wml_zib('close_image_height_limit', false)) {//关闭图像高度限制
    define('WP_POST_REVISIONS', false);
    add_filter('big_image_size_threshold', '__return_false');
}
if (wml_zib('close_image_creat_size', false)) {//禁止生成多种图像尺寸
    add_filter('intermediate_image_sizes_advanced', function () {
        return [];
    });
    add_filter('big_image_size_threshold', '__return_false');
}
if (wml_zib('close_image_srcset', false)) {//禁止图片设置多种尺寸
    add_filter('wp_calculate_image_srcset', '__return_false');
}
if (wml_zib('close_image_attributes', false)) {//禁止插入图片添加属性
    add_filter('post_thumbnail_html', function ($html) {
        $html = preg_replace('/width="(\d*)"\s+height="(\d*)"\s+class=\"[^\"]*\"/', "", $html);
        $html = preg_replace('/  /', "", $html);

        return $html;
    }, 10);
    add_filter('image_send_to_editor', function ($html) {
        $html = preg_replace('/width="(\d*)"\s+height="(\d*)"\s+class=\"[^\"]*\"/', "", $html);
        $html = preg_replace('/  /', "", $html);
        return $html;
    }, 10);
}
if (wml_zib('close_transcoding', false)) {//关闭字符转码
    add_filter('run_wptexturize', '__return_false');
}
if (wml_zib('close_auto_embeds', false)) {//禁止Auto Embeds
    remove_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);
}
if (wml_zib('close_post_embeds', false)) {//禁止文章Embeds
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
}
if (wml_zib('restore_gutenberg', false)) {//禁止新版古藤堡编辑器
    add_filter('use_block_editor_for_post', '__return_false');
}
if (wml_zib('restore_widget_gutenberg', false)) {//禁止小工具区块编辑器
    add_filter('gutenberg_use_widgets_block_editor', '__return_false');
    add_filter('use_widgets_block_editor', '__return_false');
}
if (wml_zib('close_core_update', false)) {//关闭WordPress核心更新
    // 彻底关闭自动更新
    add_filter('automatic_updater_disabled', '__return_true');
    // 关闭更新检查定时作业
    remove_action('init', 'wp_schedule_update_checks');
    //  移除已有的版本检查定时作业
    wp_clear_scheduled_hook('wp_version_check');
    // 移除已有的自动更新定时作业
    wp_clear_scheduled_hook('wp_maybe_auto_update');
    // 移除后台内核更新检查
    remove_action('admin_init', '_maybe_update_core');
    add_filter('pre_site_transient_update_core', function ($a) {
        return null;
    });
}
if (wml_zib('close_theme_update', false)) {//关闭主题自动更新
    wp_clear_scheduled_hook('wp_update_themes');
    add_filter('auto_update_theme', '__return_false');
    remove_action('load-themes.php', 'wp_update_themes');
    remove_action('load-update.php', 'wp_update_themes');
    remove_action('load-update-core.php', 'wp_update_themes');
    remove_action('admin_init', '_maybe_update_themes');
    add_filter('pre_set_site_transient_update_themes', function ($a) {
        return null;
    });
}
if (wml_zib('close_plugin_update', false)) {//关闭插件自动更新
    wp_clear_scheduled_hook('wp_update_plugins');
    add_filter('auto_update_plugin', '__return_false');
    add_filter('pre_site_transient_update_plugins', function ($a) {
        return null;
    });
    remove_action('load-plugins.php', 'wp_update_plugins');
    remove_action('load-update.php', 'wp_update_plugins');
    remove_action('load-update-core.php', 'wp_update_plugins');
    remove_action('admin_init', '_maybe_update_plugins');
}
if (wml_zib('close_mail_update_user_info_note', false)) {//关闭用户信息邮件通知
    add_filter('send_password_change_email', '__return_false');
    add_filter('email_change_email', '__return_false');
    if (!function_exists('wp_password_change_notification')) {
        function wp_password_change_notification($user)
        {
            return null;
        }
    }
}
if (wml_zib('close_mail_register_note', false)) {//关闭注册邮件通知
    add_filter('wp_new_user_notification_email_admin', '__return_false');
    add_filter('wp_new_user_notification_email', '__return_false');
}
if (wml_zib('close_email_check', false)) {//屏蔽定期邮箱验证
    add_filter('admin_email_check_interval', '__return_false');
}


//隐藏菜单
if (wml_zib('hide_menu_dashboard', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'index.php' ); //仪表盘
    });
}
if (wml_zib('hide_menu_upload', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'upload.php' ); //多媒体
    });
}
if (wml_zib('hide_menu_page', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'edit.php?post_type=page' ); //页面
    });
}
if (wml_zib('hide_menu_comments', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'edit-comments.php' ); //评论
    });
}
if (wml_zib('hide_menu_links', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'link-manager.php' ); //链接
    });
}
if (wml_zib('hide_menu_plugins', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'plugins.php' ); //插件
    });
}
if (wml_zib('hide_menu_themes', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'themes.php' ); //外观
    });
}
if (wml_zib('hide_menu_users', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'users.php' ); //用户
    });
}
if (wml_zib('hide_menu_tools', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'tools.php' ); //工具
    });
}
if (wml_zib('hide_menu_general', false)) {
    add_action( 'admin_menu', function(){
        remove_menu_page( 'options-general.php' ); //设置
    });
}
if (wml_zib('hide_menu_edit', false)) {
    /* 禁止主题插件编辑 */
    define('DISALLOW_FILE_EDIT', true);
    /* 禁止后台主题插件上传安装 */
    define('DISALLOW_FILE_MODS',true);
}


