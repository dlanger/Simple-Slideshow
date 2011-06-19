<?php 

add_action( 'admin_init', 'sss_settings_init' );
add_action( 'admin_menu', 'sss_load_menu' );
add_filter( 'plugin_action_links', 'sss_add_action_link', 10, 2 );
add_filter( 'contextual_help', 'sss_contextual_help_handler', 10, 3);

require_once 'simpleslider-admin-help.php';

function sss_settings_init() {
	register_setting( 'sss_settings', 'sss_settings', 'sss_settings_validate');
	add_settings_section( 'sss_settings_main', '', 
		'sss_settings_text', 'wp_simpleslideshow' );
	add_settings_field( 'sss_size', 'Image size', 'sss_settings_size', 
		'wp_simpleslideshow', 'sss_settings_main');
	add_settings_field( 'sss_transition_speed', 'Transition speed', 
		'sss_settings_transition_speed', 'wp_simpleslideshow', 
		'sss_settings_main');
	add_settings_field( 'sss_link_click', 'Click image to open full-size ' . 
		'version in a new window', 'sss_settings_link_click', 
		'wp_simpleslideshow', 'sss_settings_main');
	add_settings_field( 'sss_link_target', 'Link target', 
		'sss_settings_link_target', 'wp_simpleslideshow', 
		'sss_settings_main');
	//TODO Add menu option for JS image counter
}

function sss_load_menu() {
	global $sss_menu_hook_name;
	$sss_menu_hook_name = add_options_page( 'Simple Slideshow Settings', 'Simple Slideshow', 
		'manage_options', 'wp_simpleslideshow', 'sss_admin_menu');
}

function sss_settings_text() {
	
	echo 'Options set here become the <em>default</em>, but can still be ', 
			'changed on a per-show basis by using attributes. For more ', 
			'information about the meaning of each option, please click ',
			'the \'Help\' link above.';
}

//@TODO - Make this text better
function sss_contextual_help_handler( $help, $screen_id, $screen) {
	global $sss_menu_hook_name;
	
	if( $screen_id == $sss_menu_hook_name ) 
		$help = $sss_contextual_help;
	
	return $help;
}

function sss_settings_size() {
	$opts = get_option( 'sss_settings' );
	$sizes = get_intermediate_image_sizes();
	$curr = sss_settings_defaults('size');
	
	if( $opts and isset( $opts[ 'size' ] ) )
		$curr = $opts[ 'size' ];
	 	
	echo '<select id="sss_size" name="sss_settings[size]">';
	foreach( $sizes as $size ) {	
		echo '<option ';
		if( $size == $curr )
			echo 'selected ';			
		echo 'value="', $size, '">', ucfirst($size) ,'</option>';
	}
	echo '</select>';	
}

function sss_settings_transition_speed() {
	$opts = get_option( 'sss_settings' );
	$curr = sss_settings_defaults('transition_speed');
		
	if( $opts and isset( $opts[ 'transition_speed' ] ) )
		$curr = $opts[ 'transition_speed' ];
		
	echo '<input type="number" min="1" max="1000" step="10" value="', 
			$curr, '" id="sss_transition_speed" ',
			'name="sss_settings[transition_speed]">';
}

function sss_settings_link_click() {
	$opts = get_option( 'sss_settings' );
	$curr = sss_settings_defaults('link_click');
		
	if( $opts and isset( $opts[ 'link_click' ] ) )
		$curr = $opts[ 'link_click' ];
	
	echo '<select id="sss_link_click" name="sss_settings[link_click]">',
			'<option ';
	if( ! $curr )
		echo 'selected ';
	echo 'value="0">No</option><option ';
	if( $curr )
		echo 'selected ';
	echo 'value="1">Yes</option></select>';	
}

function sss_settings_link_target() {
	$opts = get_option( 'sss_settings' );
	$curr = sss_settings_defaults('link_target');
		
	if( $opts and isset( $opts[ 'link_target' ] ) )
		$curr = $opts[ 'link_target' ];
		
	echo '<select id="sss_target" name="sss_settings[link_target]"><option ';
	if( 'attach' == $curr )
		echo 'selected ';
	echo 'value="attach">Attachment page</option><option ';
	if( 'direct' == $curr )
		echo 'selected ';
	echo 'value="direct">Image file</option></select>';	
}

// From http://www.wpmods.com/adding-plugin-action-links
function sss_add_action_link( $links, $file ){
	static $this_plugin;
	
	if( ! $this_plugin ) {
		// Adjust to reflect filename of main plugin file
		$this_plugin = plugin_basename( str_replace( '-admin', '', __FILE__ ) );
	}
	
	if( $file == $this_plugin ) {
		$settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin' . 
			'/options-general.php?page=wp_simpleslideshow">Settings</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}

function sss_admin_menu() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient privileges to access this ' .
			'page. Please contact your administrator.' ) );
	}
	//@TODO Add some jQuery themed CSS into the admin-specific CSS page to that the tabs look right 
?>

<div class="wrap">
<div id="icon-options-general" class="icon32"></div>
<h2>Simple Slideshow</h2>

<p>Thanks for downloading Simple Slideshow. If you like it, please feel free 
to give it a positive review at Wordpress.org so that others can 
learn about it.</p>

<p><b>Developers:</b> Got an idea on how to make Simple Slideshow better? Fork 
it from <a href="#">GitHub</a> and send in a pull request!</p> 

<script>
	jQuery(function() { jQuery("#tabs").tabs(); });
</script>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Settings</a></li>
		<li><a href="#tabs-2">Instructions</a></li>
	</ul>

	<div id="tabs-1">
		<p>
			<form method="post" action="options.php">			
				<?php 
					settings_fields( 'sss_settings' );
					do_settings_sections( 'wp_simpleslideshow' );
				?>
				<p class="submit">
					<input type="submit" class="button-primary" value="Save Changes">
				</p>
			</form>
		</p>
	</div>
	
	<div id="tabs-2"> 
		<p><?php echo $sss_tab_help; ?></p>
	</div>

</div>

<?php 	
}
?>
