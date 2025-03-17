<?php
/**
 * Template Name: User Profile Page
 * Description: A custom template for displaying and editing user profiles.
 */
 
// Ensure the user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

acf_form_head();
get_header();

// Get current user information
$current_user = wp_get_current_user();
?>
<main id="content" class="site-main page type-page status-publish">
	<?php
	// Query for the profile page
	$profile_user_id = get_post_meta(get_the_ID(), 'profile_user_id', true);
	if ((int) $profile_user_id !== $current_user->ID) {
		?>
		<section id="user_profile_header" class="user_profile_header">
			<h1><?php echo esc_html(get_field('user_heading')); ?></h1>
			<p><?php echo esc_html(get_field('user_content')); ?></p>
		</section>
		
		<section id="user_profile_product" class="user_profile_product">
			<h3>Products:</h3>
			<?php
			$products = get_field('user_product_repeater');
			?>
			<ul>
				<?php
				if ($products) :
					foreach ($products as $product) :
						// Check if the product image is set, otherwise set a default image
						$product_image = isset($product['product_image']) && !empty($product['product_image']) ? esc_url($product['product_image']['url']) : 'https://placehold.co/600x400'; // Set your default image path here
					
						echo '<li>';
						echo '<div class="product_image"><img src="' . $product_image . '" alt="' . esc_attr($product['product_heading']) . '"></div>';
						echo '<div class="product_content"><h4>' . esc_html($product['product_heading']) . '</h4>';
						echo '<p>' . esc_html($product['product_content']) . '</p>';
						echo '<p><strong>Price:</strong> ' . esc_html($product['product_price']) . '</p>';
						echo '<a class="product_btn" href="' . esc_url($product['product_link']['url']) . '" target="_blank">View Product</a>';
						echo '</div></li>';
					endforeach;
				else :
					echo '<p>No products found.</p>';
				endif;
				?>
			</ul>
		</section>
		<?php
		get_footer();
		exit;
	}

	// Display user profile information
	?>
	<div class="user-profile-page">
		<h1><?php echo esc_html($current_user->display_name); ?>'s Profile</h1>

		<p><strong>Username:</strong> <?php echo esc_html($current_user->user_login); ?></p>
		<p><strong>Email:</strong> <?php echo esc_html($current_user->user_email); ?></p>

		<h2>Edit Your Profile</h2>

		<?php
		// Use Advanced Custom Fields (ACF) form for user-specific fields
		if (function_exists('acf_form')) {
			acf_form([
				'post_id'       => get_the_ID(), // The current page's ID
				'field_groups'  => ['group_6746a2b4da94b'], // Replace with your ACF field group ID or key
				'form'          => true,
				'submit_value'  => 'Save Profile',
				'return'        => add_query_arg('updated', 'true', get_permalink()), // Redirect on success
			]);
		} else {
			echo '<p>ACF plugin is not active.</p>';
		}
		?>
	</div>
	
	<section id="user_profile_header" class="user_profile_header">
		<h1><?php echo esc_html(get_field('user_heading')); ?></h1>
		<p><?php echo esc_html(get_field('user_content')); ?></p>
	</section>
	
	<section id="user_profile_product" class="user_profile_product">
		<h3>Products:</h3>
		<?php
		$products = get_field('user_product_repeater');
		?>
		<ul>
			<?php
			if ($products) :
				foreach ($products as $product) :
					// Check if the product image is set, otherwise set a default image
					$product_image = isset($product['product_image']) && !empty($product['product_image']) ? esc_url($product['product_image']['url']) : 'https://placehold.co/600x400'; // Set your default image path here
				
					echo '<li>';
					echo '<div class="product_image"><img src="' . $product_image . '" alt="' . esc_attr($product['product_heading']) . '"></div>';
					echo '<div class="product_content"><h4>' . esc_html($product['product_heading']) . '</h4>';
					echo '<p>' . esc_html($product['product_content']) . '</p>';
					echo '<p><strong>Price:</strong> ' . esc_html($product['product_price']) . '</p>';
					echo '<a class="product_btn" href="' . esc_url($product['product_link']['url']) . '" target="_blank">View Product</a>';
					echo '</div></li>';
				endforeach;
			else :
				echo '<p>No products found.</p>';
			endif;
			?>
		</ul>
	</section>
</main>
<?php
get_footer();