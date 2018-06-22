<?php

class Rating_Display {

	public $rat;

	public function __construct() {
		
		$this->rat = "debug";


	}




	public function display_rating($pid) {

		global $wpdb;

		$rating_text = get_option('lp_rating_text');
		$rating_font_size = get_option('lp_rating_text_font_size');
		$rating_image_size = get_option('lp_rating_image_size');
		$rating_change_image  = get_option('lp_rating_change_image');
     

		$rating_text_output = "Rating: ";

		if($rating_text != "") {

			$rating_text_output = $rating_text;
		}

		$rating_font_size_output = 18;

		if($rating_font_size != "") {

			$rating_font_size_output = $rating_font_size;
		}

		$rating_image_size_output = 20;

		if($rating_image_size != "") {

			$rating_image_size_output = $rating_image_size;
		}


		$rating_change_image_output = "";

		if($rating_change_image != "") {

			$rating_change_image_output = $rating_change_image;

			if( $image_attributes = wp_get_attachment_image_src(  $rating_change_image, $image_size ) ) {


				?>
					<style>
						.average-rating .dashicons {
							font-size: 0 !important;
							margin-right: 10px;
							background: url(<?php echo $image_attributes[0]; ?>) no-repeat scroll center center transparent;
							display: inline-block;
							max-width: <?php echo $rating_image_size_output; ?>px;
							background-size: cover;
						}

						.average-rating span.dashicons.dashicons-star-empty {
							background: #fff;
						}

						.average-rating .dashicons-star-empty:before {
							border: 1px solid;
						}

						.average-rating .dashicons, .average-rating .dashicons-before:before {

							width: 40px;
						    height: 40px;
						    font-size: 40px;

						}

					</style>
				<?php

			}

		}


		$stars   = '';
		//$post_id = get_the_ID();



     $sql = "SELECT * FROM ( SELECT p.post_title 'title', p.guid 'link', post_id, AVG(meta_value) AS rating, count(meta_value) 'count' FROM {$wpdb->prefix}postmeta pm";
        $sql .= " LEFT JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id";
        $sql .= " where meta_key = 'lp_rating' AND post_id = $pid ) as ratingTable ORDER BY rating DESC";

	    $result = $wpdb->get_results( $sql, 'ARRAY_A' );
		
	   //echo '<pre>'.print_r( $result, true).'</pre>';
	    $average = $result[0]['rating'];

		for ( $i = 1; $i <= $average + 1; $i++ ) {
			
			$width = intval( $i - $average > 0 ? $rating_image_size_output - ( ( $i - $average ) * $rating_image_size_output ) : $rating_image_size_output );

			if ( 0 === $width ) {
				continue;
			}

			$stars .= '<span style="overflow:hidden; width:' . $width . 'px; " class="dashicons dashicons-star-filled"></span>';

			if ( $i - $average > 0 ) {
				$stars .= '<span style="overflow:hidden; position:relative; left:-' . $width .'px;" class="dashicons dashicons-star-empty"></span>';
			}
		}



				?>
					<style>
				
						.average-rating .dashicons, .average-rating .dashicons-before:before {

							width: <?php echo $rating_image_size_output; ?>px;
						    height: <?php echo $rating_image_size_output; ?>px;
						    font-size: <?php echo $rating_image_size_output; ?>px;

						}

					</style>
				<?php
	
		

		//echo '<pre>'.print_r($rating_text, true).'</pre>';

		$custom_content  = "";

		if($stars != "") {
				$custom_content  = '<p class="average-rating" style="display: table; font-size:'.$rating_font_size_output.'px"><strong style="display: table-cell; padding-right: 10px; vertical-align: middle;  ">'.$rating_text_output.' </strong> ' . $stars .'</p>';
				$custom_content .= $content;

		}
		
		
		
		return $custom_content;




	

	}


}