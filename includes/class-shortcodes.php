<?php




class Rating_Shortcodes extends Rating_App {

	public $rating_display;

	public function __construct() {

		$this->rating_display = new Rating_Display;

		add_filter( 'the_excerpt', 'shortcode_unautop');
		add_filter( 'the_excerpt', 'do_shortcode');
		add_filter( 'get_the_excerpt', 'do_shortcode', 5 );
		add_shortcode('lp_ratings', array($this, 'rating_shortcode_display'));

	}

	public function rating_shortcode_display() {

		$content = '<div class="clear clearfix"></div>';
    	$content .= '<div class="lp-rating-display-container">';
		$content .= $this->rating_display->display_rating();
		$content .= '</div>';

		return $content;

	} 



}