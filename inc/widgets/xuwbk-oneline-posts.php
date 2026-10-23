<?php
/**
 * XuWbk子主题 - 单行文章列表小部件
 * 覆盖父主题的widget_ui_oneline_posts小部件
 */

if (!defined('ABSPATH')) {
    exit;
}

class XuWbk_Oneline_Posts_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget = array(
            'w_id'        => 'widget_ui_oneline_posts',
            'w_name'      => _name('单行文章列表'),
            'classname'   => '',
            'description' => '显示文章列表，只显示一行，自动横向滚动',
        );
        parent::__construct($widget['w_id'], $widget['w_name'], $widget);
    }

    public function widget($args, $instance)
    {
        if (!zib_widget_is_show($instance)) {
            return;
        }
        extract($args);
        
        // 使用辅助函数获取显示数量
        $display_limit = xuwbk_get_card_display_number();
        
        $defaults = array(
            'title'        => '',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'in_affix'     => '',
            'type'         => 'auto',
            'limit'        => $display_limit,
            'limit_day'    => '',
            'topics'       => '',
            'cat'          => '',
            'orderby'      => 'views',
            'order'        => 'DESC',
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        $instance['limit'] = $display_limit;
        
        $orderby  = $instance['orderby'];

        $mini_title = $instance['mini_title'];
        if ($mini_title) {
            $mini_title = '<small class="ml10">' . $mini_title . '</small>';
        }
        $title    = $instance['title'];
        $more_but = '';
        if ($instance['more_but'] && $instance['more_but_url']) {
            $more_but = '<div class="pull-right em09 mt3"><a href="' . $instance['more_but_url'] . '" class="muted-2-color">' . $instance['more_but'] . '</a></div>';
        }
        $mini_title .= $more_but;

        if ($title) {
            $title = '<div class="box-body notop"><div class="title-theme">' . $title . $mini_title . '</div></div>';
        }

        $in_affix = $instance['in_affix'] ? ' data-affix="true"' : '';
        echo '<div' . $in_affix . ' class="theme-box">';
        echo $title;

        $args = array(
            'post_status'         => 'publish',
            'cat'                 => str_replace('，', ',', $instance['cat']),
            'order'               => isset($instance['order']) && $instance['order'] == 'asc' ? 'ASC' : 'DESC',
            'showposts'           => 20, // 查询足够多的文章供Swiper滚动
            'no_found_rows'       => true,
            'ignore_sticky_posts' => 1,
        );

        if ($orderby !== 'views' && $orderby !== 'favorite' && $orderby !== 'like') {
            $args['orderby'] = $orderby;
        } else {
            $args['orderby']    = 'meta_value_num';
            $args['meta_query'] = array(
                array('key' => $orderby, 'order' => $args['order']),
            );
        }
        if ($instance['topics']) {
            $args['tax_query'] = array(
                array('taxonomy' => 'topics', 'terms' => preg_split("/,|，|\s|\n/", $instance['topics'])),
            );
        }
        if ($instance['limit_day'] > 0) {
            $current_time = current_time('Y-m-d H:i:s');
            $args['date_query'] = array(
                array(
                    'after'     => date('Y-m-d H:i:s', strtotime('-' . $instance['limit_day'] . ' day', strtotime($current_time))),
                    'before'    => $current_time,
                    'inclusive' => true,
                ),
            );
        }

        $list_args = array('type' => 'card');
        $the_query = new WP_Query($args);
        
        if ($the_query->have_posts()) {
            $slides_per_view = xuwbk_get_card_display_number();
            
            // 动态计算每个卡片的宽度（包含20px间距的补偿）
            $gap = 20;
            $width_percent = 100 / $slides_per_view;
            $width_offset = $gap * ($slides_per_view - 1) / $slides_per_view;
            
            // 内联样式：确保即使CSS加载异常或AJAX刷新后也能正确显示
            echo '<style>
            .swiper-container.swiper-scroll[data-slidesPerView="' . $slides_per_view . '"] .swiper-wrapper,
            .swiper-container.swiper-scroll[data-slidesperview="' . $slides_per_view . '"] .swiper-wrapper {
                display: flex !important;
                flex-wrap: nowrap !important;
            }
            /* 初始化前 .posts-item，初始化后 .swiper-slide 都会被选中 */
            .swiper-container.swiper-scroll[data-slidesPerView="' . $slides_per_view . '"] .swiper-wrapper > .posts-item,
            .swiper-container.swiper-scroll[data-slidesPerView="' . $slides_per_view . '"] .swiper-wrapper > .swiper-slide,
            .swiper-container.swiper-scroll[data-slidesperview="' . $slides_per_view . '"] .swiper-wrapper > .posts-item,
            .swiper-container.swiper-scroll[data-slidesperview="' . $slides_per_view . '"] .swiper-wrapper > .swiper-slide {
                width: calc(' . $width_percent . '% - ' . $width_offset . 'px) !important;
                flex: 0 0 calc(' . $width_percent . '% - ' . $width_offset . 'px) !important;
                box-sizing: border-box !important;
                flex-shrink: 0 !important;
            }
            @media (max-width: 768px) {
                .swiper-container.swiper-scroll[data-slidesPerView="' . $slides_per_view . '"] .swiper-wrapper > .posts-item,
                .swiper-container.swiper-scroll[data-slidesPerView="' . $slides_per_view . '"] .swiper-wrapper > .swiper-slide,
                .swiper-container.swiper-scroll[data-slidesperview="' . $slides_per_view . '"] .swiper-wrapper > .posts-item,
                .swiper-container.swiper-scroll[data-slidesperview="' . $slides_per_view . '"] .swiper-wrapper > .swiper-slide {
                    width: calc(33.333% - 13.333333px) !important;
                    flex: 0 0 calc(33.333% - 13.333333px) !important;
                }
            }
            @media (max-width: 480px) {
                .swiper-container.swiper-scroll[data-slidesPerView="' . $slides_per_view . '"] .swiper-wrapper > .posts-item,
                .swiper-container.swiper-scroll[data-slidesPerView="' . $slides_per_view . '"] .swiper-wrapper > .swiper-slide,
                .swiper-container.swiper-scroll[data-slidesperview="' . $slides_per_view . '"] .swiper-wrapper > .posts-item,
                .swiper-container.swiper-scroll[data-slidesperview="' . $slides_per_view . '"] .swiper-wrapper > .swiper-slide {
                    width: calc(50% - 10px) !important;
                    flex: 0 0 calc(50% - 10px) !important;
                }
            }
            </style>';
            
            echo '<div class="swiper-container swiper-scroll" data-slideClass="posts-item" data-slidesPerView="' . $slides_per_view . '" scroll-nogroup="true">';
            echo '<div class="swiper-wrapper">';  
            zib_posts_list($list_args, $the_query);
            echo '</div>';
            echo '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
            echo '</div>';
            
            // Swiper 初始化后强制覆盖 slidesPerView（父主题硬编码为 'auto'）
            echo '<script>
            (function(){
                var container = document.currentScript.previousElementSibling;
                if (!container) return;
                
                function fixSwiperSlides() {
                    if (!container.swiper) return;
                    var spv = parseInt(container.getAttribute("data-slidesPerView")) || ' . $slides_per_view . ';
                    container.swiper.params.slidesPerView = spv;
                    container.swiper.params.loop = false;
                    container.swiper.loopDestroy();
                    container.swiper.update();
                }
                
                // 方式1: MutationObserver 监听 Swiper 初始化（添加 swiper-container-initialized 类）
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(m) {
                        if (m.type === "attributes" && m.attributeName === "class") {
                            if (container.classList.contains("swiper-container-initialized")) {
                                fixSwiperSlides();
                                observer.disconnect();
                            }
                        }
                    });
                });
                observer.observe(container, { attributes: true, attributeFilter: ["class"] });
                
                // 方式2: 兜底 - 延迟检测
                setTimeout(function() {
                    if (container.swiper && container.swiper.params.slidesPerView === "auto") {
                        fixSwiperSlides();
                        observer.disconnect();
                    }
                }, 2000);
            })();
            </script>';
        }
        echo '</div>';
    }

    public function form($instance)
    {
        $defaults = array(
            'title'        => '热门文章',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'in_affix'     => '',
            'limit'        => 6,
            'limit_day'    => '',
            'type'         => 'auto',
            'topics'       => '',
            'cat'          => '',
            'orderby'      => 'views',
        );
        $instance     = wp_parse_args((array) $instance, $defaults);
        $page_input[] = array(
            'name'  => __('标题：', 'zib_language'),
            'id'    => $this->get_field_name('title'),
            'std'   => $instance['title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('副标题：', 'zib_language'),
            'id'    => $this->get_field_name('mini_title'),
            'std'   => $instance['mini_title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->文案：', 'zib_language'),
            'id'    => $this->get_field_name('more_but'),
            'std'   => $instance['more_but'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->链接：', 'zib_language'),
            'id'    => $this->get_field_name('more_but_url'),
            'std'   => $instance['more_but_url'],
            'desc'  => '设置为任意链接',
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );

        echo zib_get_widget_show_type_input($instance, $this->get_field_name('show_type'));
        echo zib_edit_input_construct($page_input);
        ?>

		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['in_affix'], 'on'); ?> id="<?php echo $this->get_field_id('in_affix'); ?>" name="<?php echo $this->get_field_name('in_affix'); ?>"> 侧栏随动（仅在侧边栏有效）
			</label>
		</p>
		<p>
			<?php zib_cat_help()?>
			<input style="width:100%;" id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>" type="text" value="<?php echo str_replace('，', ',', $instance['cat']); ?>" size="24" />
		</p>
		<p>
			<?php zib_topics_help()?>
			<input style="width:100%;" id="<?php echo $this->get_field_id('topics'); ?>" name="<?php echo $this->get_field_name('topics'); ?>" type="text" value="<?php echo $instance['topics']; ?>" size="24" />
		</p>
		<p>
			<label>
				显示数目：
				<input style="width:100%;" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo $instance['limit']; ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				限制时间（最近X天）：
				<input style="width:100%;" name="<?php echo $this->get_field_name('limit_day') ?>" type="number" value="<?php echo $instance['limit_day'] ?>" size="24" />
			</label>
		</p>

		<p>
			<label>
				排序方式：
				<select style="width:100%;" name="<?php echo $this->get_field_name('orderby') ?>">
					<option value="views" <?php selected('views', isset($instance['orderby']) ? $instance['orderby'] : 'views'); ?>>浏览量</option>
					<option value="favorite" <?php selected('favorite', isset($instance['orderby']) ? $instance['orderby'] : 'views'); ?>>收藏量</option>
					<option value="like" <?php selected('like', isset($instance['orderby']) ? $instance['orderby'] : 'views'); ?>>点赞数</option>
					<option value="comment_count" <?php selected('comment_count', isset($instance['orderby']) ? $instance['orderby'] : 'views'); ?>>评论数</option>
					<option value="date" <?php selected('date', isset($instance['orderby']) ? $instance['orderby'] : 'views'); ?>>发布时间</option>
					<option value="modified" <?php selected('modified', isset($instance['orderby']) ? $instance['orderby'] : 'views'); ?>>修改时间</option>
					<option value="rand" <?php selected('rand', isset($instance['orderby']) ? $instance['orderby'] : 'views'); ?>>随机排序</option>
				</select>
			</label>
		</p>
		<p>
			<label>
				顺序：
				<select style="width:100%;" name="<?php echo $this->get_field_name('order') ?>">
					<option value="desc" <?php selected('desc', isset($instance['order']) ? $instance['order'] : 'desc'); ?>>降序</option>
					<option value="asc" <?php selected('asc', isset($instance['order']) ? $instance['order'] : 'desc'); ?>>升序</option>
				</select>
			</label>
		</p>
        <?php
    }
}

/**
 * 注销父主题的小部件并注册子主题的小部件
 */
function xuwbk_override_oneline_posts_widget()
{
    // 注销父主题的小部件
    unregister_widget('widget_ui_oneline_posts');
    
    // 注册子主题的小部件
    register_widget('XuWbk_Oneline_Posts_Widget');
}
add_action('widgets_init', 'xuwbk_override_oneline_posts_widget', 20);
