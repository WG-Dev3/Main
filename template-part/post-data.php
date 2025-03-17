<?php
$post_id = isset( $args['post_id'] ) ? $args['post_id'] : '';
?>
<div class="card">
    <a class="card-image" href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>">
        <?php echo get_the_post_thumbnail( $post_id ); ?>
    </a>
    <h2 class="post-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
    <p class="post-excerpt"><?php echo wp_kses_post( get_the_excerpt( $post_id ) ); ?></p>
</div>