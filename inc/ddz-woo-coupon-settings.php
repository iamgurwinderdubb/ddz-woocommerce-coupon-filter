<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
* 
*/
class DDZ_testimonial_settings
{
	
	public function __construct()
	{
		$this->ddz_testimonial_display();
	}

	private function ddz_testimonial_display()
	{ 
		?>
		 <div class="wrap">
            <h1>Settings</h1>
            <div class="container">
            	<div class="col-md-12">
            		<form method="POST" action="#" enctype="multipart/form-data">
            		
	               	<?php $this->ddz_display_settings_fields(); ?>
	               	<div class="form-group">
	                   <button type="submit" name="ddz_settings" class="button button-primary" value="save">Save Changes</button>
	                </div>
	                </p>
	            </form>
            	</div>
	            
        	</div>
        </div>
        <?php
	}

	private function ddz_display_settings_fields() {
		if ($_POST) {
			
			$this->ddz_arugment_update('ddz_settings');
			wp_redirect( admin_url('admin.php?page=ddz-options') );
			exit();
			
		}

		$options = $this->ddz_argument_val('ddz_settings');
		

		?>
		<table class="form-table">
			<tbody>
				<tr><th style="font-size: 20px;">Pop up settings</th></tr>
				<tr>
					<th><label for="blogname">Email Website Link in Pop up</label></th>
					<td><input type="checkbox" name="ddz_share_link_display" <?php echo ($options['ddz_share_link_display'] == 'on') ? 'checked' : ''; ?>></td>
				</tr>
				<tr>
					<th><label for="blogname">Display share Link in Pop up</label></th>
					<td><input type="checkbox" name="ddz_website_link_display" <?php echo ($options['ddz_website_link_display'] == 'on') ? 'checked' : ''; ?>></td>
				</tr>
				<hr>
				<tr><th style="font-size: 20px;">Email settings</th></tr>
				<tr>
					<th><label for="emailsubject">Enter Email subject</label></th>
					<td><input type="text" name="ddz_email_subject" value="<?php echo $options['ddz_email_subject']; ?>"></td>
				</tr>
				<tr>
					<th><label for="emailsubject">Enter Email Body</label></th>
					<td><textarea name="ddz_email_body"><?php echo $options['ddz_email_body']; ?>
					</textarea>
					</td>
				</tr>
			</tbody>
			
		</table>
		
		
		<?php
	}

	private function ddz_return_arr( $field ){

	  switch($field){
	        case 'ddz_settings':
	            $variables = array(
	                                
	                                'ddz_share_link_display' => 1,
	                                'ddz_website_link_display' => 1,
	                                'ddz_email_subject' => 'Coupon',
	                                'ddz_email_body' => 'Email Body',
	                              );
	        break;
	       
	  }
	    return $variables;
	}

	private function ddz_argument_val( $field ){
	    $variables = $this->ddz_return_arr( $field );
	    foreach($variables as $key => $value){
	        if( get_option( $key )===FALSE ) add_option($key, $value);
	        else $variables[$key] = get_option($key);
	    }
	    return $variables;
	}

	/*
	// function for saving values in optios table
	*/
	private function ddz_arugment_update( $field ){
	    $variables = $this->ddz_return_arr( $field );
	    foreach($variables as $key => $value){
	        if(get_option($key)===FALSE){
	            if(!isset($_REQUEST[$key])){
	                add_option($key, '');
	                //return;
	            }elseif(is_array($_REQUEST[$key])){
	                add_option($key, serialize($_REQUEST[$key]));
	            }else { add_option($key, $_REQUEST[$key]);}
	        }else{
	            if(!isset($_REQUEST[$key])){
	                update_option($key, '');
	                //return;
	            }elseif(is_array($_REQUEST[$key])){
	                update_option($key, serialize($_REQUEST[$key]));
	            }else{
	                update_option($key, $_REQUEST[$key]);
	            }
	        }
	    }

	}


}

new DDZ_testimonial_settings;