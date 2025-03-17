<?php
get_header();
if ( have_posts() ) :
    ?>
    <div class="site-header">
        <div class="search-wrapper">
            <input type="search" class="search_field" name="search_field" placeholder="Please search here..." />
        </div>
        <div class="header-inner card-list">
            <?php
            while (have_posts()) :
                the_post();
                $post_id = get_the_ID();
                $param = array('post_id' => $post_id);
                get_template_part( 'template-part/post', 'data', $param );
            endwhile;
            ?>
        </div>
    </div>
    <?php
endif;
get_footer();
?>
