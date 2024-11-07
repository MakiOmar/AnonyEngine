<?php
/**
 * Archive filter class for subcategory filtering in WooCommerce.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class ALMOUKHLIF_Archive_Filter
 * Handles subcategory filtering and AJAX-based pagination in WooCommerce archives.
 */
class ANONY_Woo_Archive_Sub_Cat_Filter {

	/**
	 * Constructor to initialize hooks.
	 */
	public function __construct() {
		add_action( 'woocommerce_before_shop_loop', array( $this, 'display_subcategory_filter' ), 15 );
		add_action( 'wp_ajax_filter_products_by_subcategory', array( $this, 'filter_products_by_subcategory' ) );
		add_action( 'wp_ajax_nopriv_filter_products_by_subcategory', array( $this, 'filter_products_by_subcategory' ) );
		add_action( 'wp_footer', array( $this, 'add_inline_js' ) );
	}

	/**
	 * Displays a dropdown filter for subcategories in WooCommerce category pages.
	 *
	 * @return void
	 */
	public function display_subcategory_filter() {
		if ( is_product_category() ) {
			$parent_id     = get_queried_object_id();
			$args          = array(
				'taxonomy'   => 'product_cat',
				'parent'     => $parent_id,
				'hide_empty' => false,
			);
			$subcategories = get_terms( $args );

			if ( ! empty( $subcategories ) && ! is_wp_error( $subcategories ) ) {
				echo '<select id="subcategory-filter">';
				echo '<option value="">' . esc_html__( 'حدد القسم الفرعي', 'textdomain' ) . '</option>';

				foreach ( $subcategories as $subcategory ) {
					echo '<option value="' . esc_attr( $subcategory->term_id ) . '">' . esc_html( $subcategory->name ) . '</option>';
				}

				echo '</select>';
			}
		}
	}

	/**
	 * Renders the product loop item HTML for each product.
	 *
	 * @return void
	 */
	public function product_loop_item_html() {
		?>
		<li class="product">
			<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
			<div class="product-details">
				<?php
				do_action( 'woocommerce_before_shop_loop_item_title' );
				do_action( 'woocommerce_shop_loop_item_title' );
				do_action( 'woocommerce_after_shop_loop_item_title' );
				?>
			</div>
			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
		</li>
		<?php
	}

	/**
	 * Handles the AJAX request to filter products by subcategory.
	 *
	 * @return void
	 */
	public function filter_products_by_subcategory() {
		check_ajax_referer( 'filter_products_nonce', 'security' );

		$subcategory_id = isset( $_POST['subcategory_id'] ) ? intval( $_POST['subcategory_id'] ) : 0;
		$paged          = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$posts_per_page = 12;

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'paged'          => $paged,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $subcategory_id,
				),
			),
		);

		$current_user = wp_get_current_user();
		if ( ! is_user_logged_in() || in_array( 'customer', (array) $current_user->roles, true ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'terms'    => array( 'exclude-from-catalog' ),
				'field'    => 'name',
				'operator' => 'NOT IN',
			);
		}

		$query = new WP_Query( $args );

		ob_start();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				global $product;
				$this->product_loop_item_html( $product );
			}
		} else {
			echo '<li>' . esc_html__( 'عفوا! لا توجد منتجات.', 'textdomain' ) . '</li>';
		}

		$product_html = ob_get_clean();

		$total_pages     = $query->max_num_pages;
		$pagination_html = '';

		if ( $total_pages > 1 ) {
			$pagination_html .= '<nav class="woocommerce-pagination" aria-label="Product Pagination">';
			$pagination_html .= '<ul class="page-numbers">';

			if ( $paged > 1 ) {
				$pagination_html .= '<li><a class="prev page-numbers" href="#" data-page="' . ( $paged - 1 ) . '">←</a></li>';
			}

			for ( $i = 1; $i <= $total_pages; $i++ ) {
				if ( $i === $paged ) {
					$pagination_html .= '<li><span aria-current="page" class="page-numbers current">' . $i . '</span></li>';
				} else {
					$pagination_html .= '<li><a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a></li>';
				}
			}

			if ( $paged < $total_pages ) {
				$pagination_html .= '<li><a class="next page-numbers" href="#" data-page="' . ( $paged + 1 ) . '">→</a></li>';
			}

			$pagination_html .= '</ul>';
			$pagination_html .= '</nav>';
		}

		wp_send_json_success(
			array(
				'products'   => $product_html,
				'pagination' => $pagination_html,
			)
		);

		wp_reset_postdata();
		exit;
	}

	/**
	 * Adds inline JavaScript for handling AJAX-based product filtering and pagination.
	 *
	 * @return void
	 */
	public function add_inline_js() {
		if ( is_product_category() ) {
			$nonce = wp_create_nonce( 'filter_products_nonce' );
			?>
			<script>
				jQuery(document).ready(function($) {
					function loadProducts(subcategoryId, page = 1) {
						$.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type: 'POST',
							data: {
								action: 'filter_products_by_subcategory',
								subcategory_id: subcategoryId,
								page: page,
								security: '<?php echo esc_js( $nonce ); ?>'
							},
							beforeSend: function() {
								$('ul.products').html('<li><?php echo esc_html__( 'جاري تحميل المنتجات....', 'textdomain' ); ?></li>');
								$('.woocommerce-pagination').remove();
							},
							success: function(response) {
								if (response.success) {
									$('ul.products').html(response.data.products);
									if (response.data.pagination) {
										$('ul.products').after(response.data.pagination);
									}
									$(document.body).trigger('wc_fragments_loaded');
								}
							}
						});
					}
 
					$('#subcategory-filter').on('change', function() {
						var subcategoryId = $(this).val();
						loadProducts(subcategoryId);
					});
 
					$(document).on('click', '.woocommerce-pagination .page-numbers', function(e) {
						e.preventDefault();
						var page = $(this).data('page');
						var subcategoryId = $('#subcategory-filter').val();
						if (page) {
							loadProducts(subcategoryId, page);
						}
					});
				});
			</script>
			<?php
		}
	}
}

