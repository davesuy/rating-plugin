<?php


class Rating_Checking {
	

	public function __construct() {

		add_action( 'template_redirect', array($this, 'lp_check_for_rating' ));
		

	}


	public function lp_check_for_rating() {
  
		$rating_types = get_option( 'lp_rating_types', array() );

		// if( is_array( $rating_types ) && count( $rating_types ) > 0 && is_singular( $rating_types ) ) { 
		//    $rate_id = get_the_id();
		//     $ratingCookie = isset( $_COOKIE['lp_rating'] ) ? unserialize( base64_decode( $_COOKIE['lp_rating'] ) ) : array();
		//  if( ! in_array( $rate_id, $ratingCookie ) ) { 
		//         //This content has not been rated yet by that user 
		//   		add_filter( 'the_content', array($this, 'lp_rating_render' ));
		       
		//   } 
		// }

		if( current_user_can('administrator') ) { 

			add_filter( 'the_content', array($this, 'lp_rating_render' ));

		}
    
	}

	public function lp_rating_render($content) {

		$rating_types = get_option( 'lp_rating_types', array() );
	     //echo '<pre>'.print_r($rating_types, true).'hh</pre>';
	    $ratingValues = 5;

	    $content = $content;

	    ob_start();
	    ?>
	   
	    <div id="contentRating" class="lp-rating" style="text-align: inherit">
	      <!--   <button type="button" id="toggleRating" class="active">
	            <span class="text">
	                <?php //_e( 'Rate It', 'lp' ); ?>
	            </span>
	            <span class="arrow"></span>
	        </button>  -->
	        <p id="messageRating" style="margin-bottom: 5px"><span class="text"><small>(Note: Only the Admin can see this.)</small></span></p>

	        <div id="entryRating" class="lp-rating-content active" style="background: transparent; margin-bottom: 40px">
	            <div class="errors" id="ratingErrors"></div>

	            <div class="rating-container">
	                <?php for( $i = $ratingValues; $i >= 1; $i-- ) {
	                 
	                        echo '<input type="radio" name="ratingValue" value="' . $i . '" id="rating' . $i . '"/>';;
	                        
	                        echo '<label for="rating' . $i . '">';
	                            echo $i;
	                        echo '</label>';
	                 
	                }
	                ?>
	            
	            </div>

	            <button type="button" class="button" data-rate="<?php echo get_the_id(); ?>"id="submitRating"><?php _e( 'Submit', 'lp' ); ?></button>
	        </div>



	    </div>



	    <?php

	    $content .= ob_get_contents();

	    ob_end_clean();

	    return $content;
	}


}