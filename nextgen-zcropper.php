<?php
/*
Plugin Name: NextGEN ZCropper
Plugin URI: http://vogelundstrauss.de/blog/2011/08/wordpress-z-cropper/
Description: This plugin requires NextGEN gallery plugin installed. This plugin resizes and crops (!) images while uploading them. 
Version: 0.9b
Author: Vogel & Strauss
Author URI: http://vogelundstrauss.de
*/

/*  Copyright 2011  Markus Hoffmann  (email : mh@vogelundstrauss.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// error_reporting(E_ALL); /*Notice marte salla*/

require_once( WP_PLUGIN_DIR . '/nextgen-gallery-z-cropper/simpleimage.php');


if ( in_array('nggLoader', get_declared_classes())){
		
		register_activation_hook( __FILE__, 'cropperinstalls' );
		add_action('ngg_added_new_image','cropperuploadsngg',1);
		add_action('admin_menu', 'cropadminmenu');
		 if ( function_exists('register_uninstall_hook') ){
    register_uninstall_hook(__FILE__, 'cropperuninstalls');}

		//echo "Nextgen Found";
     }  
     else
     { 
     	echo "<div id=\"message\" class=\"updated fade\"><p>NextGEN Cropper:<a href=\"http://wordpress.org/extend/plugins/nextgen-gallery-z-cropper/\" target=\"_blank\">NextGEN Gallery Plugin</a> Not found</p></div>";
     }
     
     
     /*function that will add a next-gen resize page under the gallery tab*/
    function cropadminmenu() {
		$file = __FILE__;
		
		
		
		$subpage = add_submenu_page('nextgen-gallery', "Z Cropper Setup", "Z Cropper Setup", 10, $file, 'crop_options_panel');
		
	} //end of admin_menu()
	
	
	
	
   /*function that runs when the plugin is activated*/
   function cropperinstalls()
   {
   	 $nggcropper_options['px']="400";
   	 $nggcropper_options['on']="yes";
   	 $nggcropper_options['pxh']="300";
	 add_option("nggcropperoptions", $nggcropper_options, 'Options.', 'yes');
   }
   
   /*function that runs when the plugin is deactivated and deleted */
   function cropperuninstalls()
   {
   		if(get_option("nggcropperoptions"))
			{
				delete_option("nggcropperoptions");
				
			}
   }
   
   /*function that hooks on to ngg's ngg_added_new_image hook, gets the $image array and uses it to resize the image dynamically*/
   function cropperuploadsngg($image)
     {
     	global $wpdb, $ngg;
     	$cropper_nggallery						= $wpdb->prefix . 'ngg_gallery';
     	$query = "select * from $cropper_nggallery where gid='".$image['galleryID']."'" ;
     	//print_r($image);
     	//echo ' line:'. __LINE__ .' file:'. __FILE__ .' directory:'. __DIR__ .' function:'. __FUNCTION__ .' class:'. __CLASS__ .' method:'. __METHOD__ .' namespace:'. __NAMESPACE__;
     	$results = $wpdb->get_results($query);
     	//$p = getcwd();
		//echo $p;
		$width = $ngg->options['imgWidth'];
		$height = $ngg->options['imgHeight'];
		$nggarray_options = get_option("nggcropperoptions");
		// echo $width;
		// echo $height;
     	//print_r($nggarray_options);
     	 
		
			if($nggarray_options['on']=="yes")
			{
				var_dump($image);
				 $imageres = new SimplyImage();				
				 $imageres->load(ABSPATH.$results[0]->path."/".$image['filename']);
				// echo ABSPATH.$results[0]->path."/".$image['filename'];
			
   				$imageres->resize($width, $height);
   				
   			
  				$resulted =	$imageres->save(ABSPATH.$results[0]->path."/".$image['filename']);
  			}
		
     			

     }
     
     /*function that will display the options page*/
	function crop_options_panel() 
	{ 
		global $ngg;
		$message="";
		
		//print_r($nggarray_options);
		if(isset($_POST['cropperzsub']) && $_POST['cropperzsub'] == "Save Options")
		{
			$nonce = $_POST['nonce-nextgencropper'];
			if (!wp_verify_nonce($nonce, 'nextgencropper-nonce')) die ( 'Security Check - If you receive this in error, log out and back in to WordPress');
   			$nggcropper_options['on']=$_POST['cropperzon'];
   			
   			
			update_option("nggcropperoptions", $nggcropper_options);
			$message = "NextGEN cropper options updated.";
			//print_r($nggresize_options);
		}
		
		
	//update_option("em_timezone",$_POST['em_timezone']); <?php if(preg_match('/none/',get_option("em_timezone"))=='1'){
	$nggarray_options = get_option("nggcropperoptions");
	$nggarray_options2 = get_option("thumbnailsettings");
	?>
	<div class="wrap">
	<? $width = $ngg->options['imgWidth'];
		$height = $ngg->options['imgHeight']; 
		 ?>
		<h2>Nextgen Z Cropper - Setup</h2>
		<h4>Resize and Crop your images while uploading (not the thumbnails!)</h4>
		<h4><a href="http://vogelundstrauss.de/blog" target="_blank">Check further description here</a></h4>
		<br/><br/>
		<?php if ($message) : ?>
			<div id="message" class="updated fade" style="clear:both;"><p><?php echo $message; ?></p></div>
		<?php endif; ?>
		<form name="cropperzitboy" method="post" action=""/>
		<div id="inputcontrols" style="clear:both;">
			
			<label for="cropperby_op">Resize and Crop Images to  <?=$width?>px x <?=$height;?>px ?<br/>
			(You can set height and width in the image options tab of NextGEN gallery) </label>&nbsp;
			<select name="cropperzon" id="cropperzon"><option value="yes" <?php if(preg_match('/yes/',$nggarray_options['on'])=='1') { ?> selected <?php } ?> >On</option><option value="no" <?php if(preg_match('/no/',$nggarray_options['on'])=='1') { ?> selected <?php } ?>>Off</option></select><br/><br/>
			<input type="submit" name="cropperzsub" id="cropperzsub" value="Save Options"/><br/>
			

			
		</div>
		<input type="hidden" name="nonce-nextgencropper" value="<?php echo wp_create_nonce('nextgencropper-nonce'); ?>" />
		</form>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="N7BVKR4LNB8KC">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>
		<div style="position:relative;width:75%;float:left;clear:both;">
			<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fvogelundstrauss&amp;width=600&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=true&amp;header=true&amp;height=427" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:600px; height:427px;" allowTransparency="true"></iframe>
		</div>
	<?php }
?>
