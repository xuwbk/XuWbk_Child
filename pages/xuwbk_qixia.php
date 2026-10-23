<?php
/**
 * XuWbk-旗下站点 页面模板
 * @package XuWbk
 * @author 轩玮
 * @version 1.0
 * @created 2024-10-27 23:08:55
 * @modified 2025-06-23 16:27:00
 * @description 由《轩玮博客》开发用于WordPress to zibll主题的美化《XuWbk主题》
 * @contact QQ:6050640 邮箱：6050640@qq.com 网址：www.xuwbk.com
 * @copyright Copyright (c) 2025 by XuWbk.Com, All Rights Reserved.
 */

// 使用CSF框架的选项获取方式
$options = get_option('XuWbk');
$xuwbk_qixia = isset($options['xuwbk_qixia']) ? $options['xuwbk_qixia'] : false;
if (!($xuwbk_qixia === true || $xuwbk_qixia === '1' || $xuwbk_qixia === 1)) {
    // 如果开关关闭，显示空白页（保留头部和尾部）
    get_header();
    echo '<main class="container"><div class="content-wrap"><div class="content-layout xy-article"><div class="nopw-sm box-body theme-box radius8 main-bg main-shadow"></div></div></div></main>';
    get_footer();
    exit();
}

// 获取链接列表
get_header();
$header_style = zib_get_page_header_style();
?>

<main class="container">
<div class="content-wrap">
<div class="content-layout">


<div class="page-cover theme-box radius8 main-shadow">
<img class="fit-cover no-scale lazyload" src="<?php echo isset($options['qixia_bj']) ? $options['qixia_bj'] : ''; ?>" data-src="<?php echo isset($options['qixia_bj']) ? $options['qixia_bj'] : ''; ?>">
<div class="absolute page-mask"></div>
<div class="list-inline box-body abs-center text-center">
<div class="title-h-center">
<h3><?php echo isset($options['qixia_name']) ? $options['qixia_name'] : ''; ?></h3>
         </div>
     </div>
</div>      


<article class="article page-article main-bg theme-box box-body radius8 main-shadow">
<div class="wp-posts-content">

<?php
if(isset($options['qixia_zd']) && is_array($options['qixia_zd'])){
    foreach($options['qixia_zd'] as $val) {
        echo '<div class="wp-block-zibllblock-feature feature feature-default" data-icon="'.$val['icon'].'" data-color="'.$val['color'].'"><div class="feature-icon"><i style="color:'.$val['color'].'" class="'.$val['icon'].'"></i></div><div class="feature-title"><a href="'.$val['link']['url'].'" target="'.$val['link']['target'].'" rel="noreferrer noopener">'.$val['name'].'</a></div><div class="feature-note">'.$val['info'].'</div></div>';
    }
}
?>
</div>
</article>
     </div>
</div>
    </main>
    
<?php
get_footer();