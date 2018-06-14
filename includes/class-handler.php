<?php

class Rating_handler extends Rating_App {
	
	public function __construct() {

		add_action( 'wp_ajax_lp_submit_rating', array($this, 'lp_submit_rating' ));
		add_action( 'wp_ajax_nopriv_lp_submit_rating', array($this, 'lp_submit_rating' ));


		add_action( 'wp_ajax_lp_clear_rating', array($this, 'lp_clear_rating' ));
		add_action( 'wp_ajax_nopriv_lp_clear_rating', array($this, 'lp_clear_rating' ));
	
	}


	/**
	 * Submitting Rating
	 * @return string  JSON encoded array
	 */
	public function lp_submit_rating() {

	    check_ajax_referer( 'lp_rating', '_wpnonce', true );
	    $result = array( 'success' => 1, 'message' => '' );

	    $ratingCookie = isset( $_COOKIE['lp_rating'] ) ? unserialize( base64_decode( $_COOKIE['lp_rating'] ) ) : array();
	    $rate_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
	 
	    if( ! $ratingCookie ) {
	        $ratingCookie = array();
	    }
	    
	    $ratingCookie = array();
	    if( $rate_id > 0 ) {

	        if( ! in_array( $rate_id, $ratingCookie ) ) {

	            $rate_value = isset( $_POST['rating'] ) ? $_POST['rating'] : 0;
	            if( $rate_value > 0 ) {
	                
	                $success = add_post_meta( $rate_id, 'lp_rating', $rate_value );
	                
	                if( $success ) {

	                    $result['message'] = __( 'Thank you for rating!', 'lp' );
	                    $ratingCookie[] = $rate_id;
	                    $expire = time() + 30*DAY_IN_SECONDS;
	                    setcookie( 'lp_rating', base64_encode(serialize( $ratingCookie )), $expire, COOKIEPATH, COOKIE_DOMAIN );
	                    $_COOKIE['lp_rating'] = base64_encode(serialize( $ratingCookie ));
	                }

	            } else {
	                $result['success'] = 0;
	                $result['message'] = __( 'Something went wrong. Try to rate later', 'lp' );
	            }

	        } else {
	            $result['success'] = 0;
	            $result['message'] = __( 'You have already rated this content.', 'lp' );
	        }
	    } else {
	        $result['success'] = 0;
	        $result['message'] = __( 'Something went wrong. Try to rate later', 'lp' );
	    }

	    echo json_encode( $result );
	    wp_die();
	}



	public function lp_clear_rating() {

	    check_ajax_referer( 'lp_rating', '_wpnonce', true );
	    	
	    	$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
	    	$result = array( 'success' => 1, 'message' => '' );	

	  		 $success  = delete_post_meta($post_id, 'lp_rating');

	  		 if($success) {
	  		 		$result['success'] = "Data Cleared";
	  		 }

	  		echo json_encode( $result );
	    wp_die();
	}

}