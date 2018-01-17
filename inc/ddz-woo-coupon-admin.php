<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


/**
* 
*/
class DDZ_woo_coupon_admin 
{
	
	function __construct()
	{
		 add_action( 'add_meta_boxes', array($this,'woo_coupon_add_meta_box' ));
		 add_action( 'save_post', array($this,'ddz_woo_coupon_metabox_save' ));
		 add_action( 'init',array( $this, 'ddz_woo_coupon_categories' ) );
		 add_shortcode( 'ddz_woo_coupons', array( $this, 'ddz_woo_coupons_display' ) );

		 add_action('wp_ajax_get_coupon_id', array( $this, 'get_coupon_id_callback' ));
add_action('wp_ajax_nopriv_get_coupon_id', array( $this, 'get_coupon_id_callback' ));

	}

	public function ddz_woo_coupon_categories() {
	
			$labels = array(
				'name'              => __( 'Coupon categories', 'woocommerce-coupon-taxonomy' ),
				'singular_name'     => __( 'Category', 'woocommerce-coupon-taxonomy' ),
				'menu_name'         => _x( 'Categories', 'Admin menu name', 'woocommerce-coupon-taxonomy' ),
				'search_items'      => __( 'Search Coupon categories', 'woocommerce-coupon-taxonomy' ),
				'all_items'         => __( 'All categories', 'woocommerce-coupon-taxonomy' ),
				'parent_item'       => __( 'Parent category', 'woocommerce-coupon-taxonomy' ),
				'parent_item_colon' => __( 'Parent category:', 'woocommerce-coupon-taxonomy' ),
				'edit_item'         => __( 'Edit coupon category', 'woocommerce-coupon-taxonomy' ),
				'update_item'       => __( 'Update category', 'woocommerce-coupon-taxonomy' ),
				'add_new_item'      => __( 'Add new coupon category', 'woocommerce-coupon-taxonomy' ),
				'new_item_name'     => __( 'New category name', 'woocommerce-coupon-taxonomy' ),
				'not_found'         => __( 'No categories found', 'woocommerce-coupon-taxonomy' ),
			);
			register_taxonomy( 'coupon_category', array('shop_coupon'), array(
				'hierarchical' => true,
				'labels' => $labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'query_var' => true,
				'show_in_menu' => true,
				'public' => true,
				'rewrite' => array( 'slug' => 'coupon_category' ),
			) );
			register_taxonomy_for_object_type( 'coupon_category', 'shop_coupon' );
		}

	public function woo_coupon_add_meta_box()
	{
		 add_meta_box('woo_coupon_meta',__( 'Coupon meta' ),array($this,'ddz_woo_coupon_metabox_render' ),'shop_coupon','normal'
    );
	}

	public function ddz_woo_coupon_metabox_render($post)
	{
	   	global $post;

		$ddz_woo_link =  get_post_meta( $post->ID,'ddz_woo_link', true );
		?>
		<div class="panel-wrap">
			<div class="panel woocommerce_options_panel">
				<p class="form-field ">
					<label for="coupon_amount">Coupon External Site Link</label>
					<input type="text" name="ddz_woo_link" value="<?php echo $ddz_woo_link;?>"> 
				</p>
				<p class="form-field ">
					<label for="coupon_img">Coupon Image</label>
					<?php $im_id = get_post_meta( $post->ID, 'ddz_woo_coupon_logo', true );
					if ($im_id) {
					 	$image = wp_get_attachment_url( $im_id );
					 	echo '<img id="ddz_img_url" src="'.$image.'" width="200px" height="auto" >';
					 	echo '<input type="hidden" name="ddz_woo_coupon_logo" value="'.$im_id.'" />';
					 }  else {
					 	echo '<img id="ddz_img_url" src="" width="200px" height="auto" >';
					 	echo '<input type="hidden" name="ddz_woo_coupon_logo" id="ddz_logo" value="">';
					 }
					
					?>
					
					<input type="submit"  name="button" id="upload_image_button" class="button" value="Add Logo"/>
					
				</p>
				<p class="form-field ">
					<label for="coupon_description">Coupon Descritpion</label>
					<?php 
						$coupon_desc = get_post_meta( $post->ID, 'ddz_woo_desc', true );
						if ($coupon_desc == '') {
							$content = '';
						} else {
							$content = $coupon_desc;
						}
						$editor_id = 'ddz_woo_desc';
						wp_editor( $content, $editor_id );
					?>
				</p>
			</div>
			
		</div>
		

		<?php

	}

	public function ddz_woo_coupon_metabox_save($post_id){

    if( isset( $_REQUEST ) ){
        update_post_meta( $post_id, 'ddz_woo_link',wp_kses_post($_POST['ddz_woo_link']  ) );
        update_post_meta( $post_id, 'ddz_woo_coupon_logo',wp_kses_post($_POST['ddz_woo_coupon_logo']  ) );
        update_post_meta( $post_id, 'ddz_woo_desc',wp_kses_post($_POST['ddz_woo_desc']  ) );

        
    }
	}

	public function ddz_woo_coupons_display()
	{
		global $wp;

		if ($_POST) {
			ob_clean();
			

			$subject = get_option( 'ddz_email_subject' );
			$body = get_option( 'ddz_email_body' );
			$body .= '<br><label>Coupon Code : </label><strong>'.$_POST['coupon_code'].'</strong><br>';
			$body .= '<br><a target="_blank" href="'.$_POST['product_link'].'">Link to Page</a><br><p>Thanks</p>';
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail( $_POST['email_address'], $subject, $body ,$headers );
			wp_redirect( home_url( $wp->request ) );
			exit();
		}
		$args = array(
				'posts_per_page'   => -1,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'shop_coupon',
				'post_status'      => 'publish',
				'suppress_filters' => true 
			);
		$coupons = get_posts( $args );
		$args = array(
		    'taxonomy'   => "product_cat",
		    'number'     => '',
		  
		);
		$terms = get_terms($args);
		//print_r($product_categories);
		?>
		<div>
			<h2>Filter</h2>
			<div id="filters" class="button-group"> 
				<select id="filter-select" >
					<option value="*" selected>Select</option>
					<?php 	 
					// $args = array('number' => '',);
					// $terms = get_terms('coupon_category', $args );
					
					foreach ($terms as $term) {
						echo '<option value=".'.$term->slug.'">'.$term->name.'</option>';
					}

				 ?>
				</select> 
				<!-- <button class="button is-checked" data-filter="*">show all</button> -->
				
			</div>
		</div>

		<div id="coupon-loop" class="grid">
			<?php 
			foreach ($coupons as $coupon) {

			$meta = get_post_meta($coupon->ID);
			$categories = unserialize($meta['product_categories'][0]);
			$cats = '';
			foreach ($categories as $key ) {
				 $term_data = get_term( $key, 'product_cat');
				 $cats .= $term_data->slug.' ';
			}
			
			?>
			<div class="coupon-container transition <?php echo $cats; ?>" data-category="transition">
				<?php 
					 	 $date_expires = get_post_meta($coupon->ID,'date_expires',true);
					 	 if ($date_expires > time()) {
					 	 	echo '<div class="newitem">New</div>';
					 	 } ?>
	    		
					<div class="cc-topsection">

						<div class="project-wrap">
							<div class="project-item item-shadow coupon-cover" style="position:relative;">
								 <?php $im_id = get_post_meta( $coupon->ID, 'ddz_woo_coupon_logo', true );
									if ($im_id) {
									 	$image = wp_get_attachment_url( $im_id );
									 	echo '<img id="ddz_img_url" src="'.$image.'"  >';

									 } else {
									 	echo '<div class="img-discount">'.$this->dd_woo_coupon_discount_type($coupon->ID).'</div>';
									 	echo '<img id="ddz_img_url" src="'.DDZ_IMAGES_URL.'coupon.png"  >';

									 }
									 ?>
								 <div class="project-hover">
									<div class="project-hover-content">
									<h3 class="project-title">About Offer</h3>
									<div class="cc-infosection"><?php 
										// $desc = '';
										 $desc = get_post_meta($coupon->ID,'ddz_woo_desc',true);

									

									echo $this->truncateString($desc, 150, false) . "..";

										?>
									 	
									 </div>
									
									</div>
								</div>

							</div>
						</div>
					    
				</div>
				 <div class="exdate"><?php echo $this->ddz_woo_coupon_expire($coupon->ID); ?></div>
				<div class="cborder"></div>
				
				
				<div class="offer-get-code-link cpnbtn"> 
					<div class="p1"></div>
					<div class="p2">
						<div class="t1"></div>
						<div class="t1">
							<div class="t2"></div>
						</div>
					</div><span id="myBtn" data-id="<?php echo $coupon->ID; ?>" >Show Coupon Code</span>
				</div>
				
			</div>

			<?php
				}
			?>
		</div>
		
		<div id="myModal" class="modal couponmodal">
		</div>
		<script type="text/javascript">

					var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

					jQuery(function($) {
					jQuery('body').on('click', '#myBtn', function() {
						var coupon_id = $(this).data("id");
						
						if(coupon_id != '') {
							var data = {
								action: 'get_coupon_id',
								type: 'post',
								coupon_id: coupon_id
							}
							
							jQuery.post(ajaxurl, data, function(response) {
								jQuery('#myModal').css({"display": "block"}).html(response);

							
								
							});
						}
					});
					});
				</script>
		

		<?php
		}

		
		
	public function truncateString($str, $chars, $to_space, $replacement="...") {
   if($chars > strlen($str)) return $str;

   $str = substr($str, 0, $chars);
   $space_pos = strrpos($str, " ");
   if($to_space && $space_pos >= 0) 
       $str = substr($str, 0, strrpos($str, " "));

   return($str . $replacement);
}



	public function get_coupon_id_callback() {
		$post_id = $_POST['coupon_id'];
		
		?>
		

	  <!-- Modal content -->
	  <div class="modal-content">
	    <!-- <span id="close">&times;</span> -->
	    <div class="md-content">
	    	<div class="cd-modal-content1 ">
	    		<div class="cd-modal-header">
	    			
	    			<div class="offer-logo">
	    				<?php $im_id = get_post_meta( $post_id, 'ddz_woo_coupon_logo', true );
							if ($im_id) {
							 	$image = wp_get_attachment_url( $im_id );
							 	echo '<img id="ddz_img_url" src="'.$image.'" width="100px" >';
							 } 
							 ?>
	    				
	    			</div>
	    			<div class="offer-description">
	    				<h3><?php echo $this->dd_woo_coupon_discount_type($post_id); ?></h3>
	    				<div class="title" data-type="html" data-model="offerTitle"  style="height: auto;"><?php echo get_post_meta($post_id,'ddz_woo_desc',true); ?></div>
	    			</div>
	    		</div>
	    	</div>
	    	<div class="down-arrow"></div>
	    	<div class="cd-modal-bottom ">
	    		
	    		<div class="popup-code-block">
	    			<span class="code-txt" id="coupon_code" data-type="html" ><?php echo get_the_title($post_id); ?></span>
	    			<span class="copy-btn " id="coupon_btn" data-model="couponCode" data-copied="true" onclick="copyToClipboard('#coupon_code')">COPY</span>
	    		</div>
	    		<?php 
	    		; 
	    		if (get_option( 'ddz_website_link_display' ) == 'on') {
	    			?>
	    			<div class="modal-link-btn">
	    				<a target="_blank" class="btn-modal btn-4 btn-4a icon-arrow-right" href="<?php echo get_post_meta($post_id,'ddz_woo_link',true); ?>">Link to Page</a>
	    			</div>
	    			<?php
	    		}
	    		?>
	    		
	    	</div>
	    	<?php $share_link = get_option( 'ddz_share_link_display' ); 

		    	if ($share_link == 'on') {
		    		?>
		    		<hr>
			    	<div class="modal-subscribe">
			    		<form method="POST" action="#">
			    			<input type="hidden" name="coupon_code" value="<?php echo get_the_title($post_id); ?>">
			    			<input type="hidden" name="product_link" value="<?php  echo get_post_meta($post_id,'ddz_woo_link',true); ?>">
			    			<div  style="display: inline-block;">
			    			<input type="email" name="email_address" value="" placeholder="name@email.com" required />
			    		</div>
							<button type="submit" >Send Mail</button>
			    		</form>
			    		
			    	</div>
		    		<?php
		    	}  ?>
	    	

	    </div>
	  </div>
  <script type="text/javascript">
  	window.onclick = function(event) {
	    if (event.target == modal) {
	        modal.style.display = "none";
	    }
	}
  </script>

		
		<?php
		exit();
	}

	public function ddz_woo_coupon_expire($post_id)
	{
		$date_expires = get_post_meta($post_id,'date_expires',true);
		if ($date_expires > time()) {
			return 'Expire on '.date('d-m-Y',$date_expires);
		} elseif ($date_expires < time()) {
			return '<span>Coupon Expired</span>';
		}
		   
	}

	public function dd_woo_coupon_discount_type($post_id)
	{
		
    	$discount_type = get_post_meta($post_id,'discount_type',true); 
    	
    	if ($discount_type == 'percent') {
    		return '<span>'.get_post_meta($post_id,'coupon_amount',true).'% off</span>';
    	} elseif ($discount_type == 'fixed_cart') {
    		return '<span>$'.get_post_meta($post_id,'coupon_amount',true).' off</span>';
    	} elseif ($discount_type == 'fixed_product') {
    		return '<span>$'.get_post_meta($post_id,'coupon_amount',true).' off</span>';
    	}
									   
	}

}

new DDZ_woo_coupon_admin;




