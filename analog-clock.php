<?php
/**
 * @package Analog_Colck_Display_Widget 
 * @version 1.0
 */
/*
Plugin Name: Analog Clock display Widget
Plugin URI: https://nerghum.com/my-portfolio/analog-clock-plugin/
Description: This is an analog clock plugin to show analog clock in your sidebar or any widget area. Easy to use and show user local time.
Author: Nerghum
Version: 1.0
Author URI: http://nerghum.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
/**
 * This files should always remain compatible with the minimum version of
 * PHP supported by WordPress.
 */

// bloack direct access
if ( ! defined('ABSPATH' ) ) exit;
add_action('wp_enqueue_scripts','analog_clock_css');

// define stylesheet
function analog_clock_css(){
    wp_register_style( 'style.css', plugin_dir_url( __FILE__ ) . 'style.css', array());
    wp_enqueue_style( 'style.css');
}
// define widget class
class nerghum_analog_clock extends WP_Widget{
    public function __construct(){

        parent::__construct('nerghum_analog_clock', 'Analog Clock Widget', array(
            'description' => 'Analog Clock Display in widget area'
        ));

    }

    // find user ip for geating timezone
    public function widget($arg, $value){

        ob_start();
        function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

$ip_address = get_client_ip();

// Get JSON object ip to time convertion
// timezoneapi is a  3rd Party service that help convert ip to timezone.
$jsondata = wp_remote_get("http://timezoneapi.io/api/ip/?" . $ip_address);

// Decode to php formet
$body = wp_remote_retrieve_body( $jsondata );
$data = json_decode($body, true);

// Request OK?
if($data['meta']['code'] == '200'){

    $second  = $data['data']['datetime']['seconds'];
    $minute = $data['data']['datetime']['minutes'];
    $hour = $data['data']['datetime']['hour_12_wolz'];
    $hour = $hour*30;
    $hour += $minute*0.5;
}
?>
<!-- ui css -->
<section id="analog_clock">
<style type="text/css">
	@keyframes sec {
    from {transform: rotate(<?php echo $second*6; ?>deg);}
    to {transform: rotate(<?php echo ($second*6)+360; ?>deg);}
    }
    @keyframes min {
        from {transform: rotate(<?php echo $minute*6; ?>deg);}
        to {transform: rotate(<?php echo ($minute*6)+360; ?>deg);}
    }
    @keyframes hour {
        from {transform: rotate(<?php echo $hour;?>deg);}
        to {transform: rotate(<?php echo $hour+360;?>deg);}
    }
</style>
<!-- ui area -->
    <?php echo $arg['before_title']; ?><?php echo $value['title']; ?><?php echo $arg['after_title']; ?>
    <hr/>
	<div class="n-clock">
		<div class="hour"><img src="<?php echo plugins_url( '/images/main.png', __FILE__ );?>" alt=""></div>
		<div class="min"></div>
		<div class="sec"></div>
	</div>
</section>

        <?php echo ob_get_clean();
    }
    public function form($value){
        
        if (!isset($value['title'])) { $value['title']= ""; }

        ?>
        <p>

            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $value['title']; ?>">
        </p>
        <?php
    }
}

add_action('widgets_init', 'analog_clock_w');
function analog_clock_w(){
    register_widget('nerghum_analog_clock');
}

?>