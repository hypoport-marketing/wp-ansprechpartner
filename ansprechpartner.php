<?php
/**
 * Plugin Name: Hypoport Ansprechpartner
 * Plugin URI:
 * Description: Erstellen und Bearbeiten von Ansprechpartnern
 * Author: Hypoport AG
 * Author URI: http://www.hypoport.de
 * Version: 1.2
 * GitHub Plugin URI: hypoport-marketing/wp-ansprechpartner
 */

 // initialize person of contact post type.
 add_action('init', 'hp_poc_register_post_type');
 function hp_poc_register_post_type() {
     $supports = array('title', 'editor', 'thumbnail', 'revisions');
     $labels = array(
         'name' => 'Ansprechpartner',
         'singular_name' => 'Ansprechpartner',
         'all_items' => 'Alle Ansprechpartner',
         'add_new' => 'Ansprechpartner erstellen',
         'add_new_item' => 'Ansprechpartner hinzufügen',
         'edit_item' => 'Ansprechpartner bearbeiten',
         'new_item' => 'Neuer Ansprechpartner',
         'view_item' => 'Ansprechpartner anzeigen',
         'search_items' => 'Ansprechpartner suchen',
         'not_found' => 'Kein Ansprechpartner gefunden',
         'not_found_in_trash' => 'Keine Ansprechpartner im Papierkorb',
     );

     $args = array(
         'supports' => $supports,
         'labels' => $labels,
         'description' => 'Post für Ansprechpartner',
         'public' => true,
         'author' => true,
         'exclude_from_search' => false,
         'publicly_queryable' => true,
         '_builtin' => false,
         'show_ui' => true,
         'show_in_nav_menus' => true,
         'menu_position' => 20,
         'show_in_menu' => true,
         'hierarchical' => false,
         'has_archive' => true,
         'can_export' => true,
         'show_in_rest' => true,
         'query_var' => 'ansprechpartner',
         'rewrite' => array('slug' => 'ansprechpartner', 'with_front' => true),

     );
     register_post_type('hp_poc_person', $args);
 }


 function hp_poc_admin_menu_icon() {
     echo '
 		<style>
 			#adminmenu #menu-posts-hp_poc_person div.wp-menu-image:before { content: "\f337"; }
 		</style>
 	';
 }
 add_action('admin_head', 'hp_poc_admin_menu_icon');


 // Add the taxonomies of post type.
 function hp_poc_taxonomies() {
     $labels = array(
         'name' => _x('Stadt', 'taxonomy general name'),
         'singular_name' => _x('Stadt', 'taxonomy singular name'),
         'search_items' => __('Stadt suchen'),
         'all_items' => __('Alle Städte'),
         'parent_item' => null,
         'parent_item_colon' => null,
         'edit_item' => __('Stadt bearbeiten'),
         'update_item' => __('Stadt aktualisieren'),
         'add_new_item' => __('Neue Stadt hinzufügen'),
         'new_item_name' => __('Neue Stadt'),
         'menu_name' => __('Städte'),
     );
     $args = array(
         'labels' => $labels,
         'hierarchical' => false,
         'rewrite' => array( 'slug' => 'stadt' ),
     );
     register_taxonomy('hp_poc_city', array('hp_poc_person'), $args);

     $labels = array(
         'name' => _x('Bundesland', 'taxonomy general name'),
         'singular_name' => _x('Bundesland', 'taxonomy singular name'),
         'search_items' => __('Bundesland suchen'),
         'all_items' => __('Alle Bundesländer'),
         'parent_item' => null,
         'parent_item_colon' => null,
         'edit_item' => __('Bundesland bearbeiten'),
         'update_item' => __('Bundesland aktualisieren'),
         'add_new_item' => __('Neues Bundesland hinzufügen'),
         'new_item_name' => __('Neues Bundesland'),
         'menu_name' => __('Bundesländer'),
     );
     $args = array(
         'labels' => $labels,
         'hierarchical' => false,
         'show_in_rest' => true,
         'rewrite' => array( 'slug' => 'bundesland' ),
     );
     register_taxonomy('hp_poc_country', array('hp_poc_person'), $args);

     $labels = array(
         'name' => _x('Arbeitsbereich', 'taxonomy general name'),
         'singular_name' => _x('Arbeitsbereich', 'taxonomy singular name'),
         'search_items' => __('Arbeitsbereich suchen'),
         'all_items' => __('Alle Arbeitsbereiche'),
         'parent_item' => null,
         'parent_item_colon' => null,
         'edit_item' => __('Arbeitsbereich bearbeiten'),
         'update_item' => __('Arbeitsbereich aktualisieren'),
         'add_new_item' => __('Neuen Arbeitsbereich hinzufügen'),
         'new_item_name' => __('Neuen Arbeitsbereich'),
         'menu_name' => __('Arbeitsbereiche'),
     );
     $args = array(
         'labels' => $labels,
         'hierarchical' => false,
         'show_in_rest' => true,
         'rewrite' => array( 'slug' => 'bereich' ),
     );
     register_taxonomy('hp_poc_area', array('hp_poc_person'), $args);
 }
 add_action('init', 'hp_poc_taxonomies', 0);


 function hp_poc_add_post_meta_boxes() {
   add_meta_box(
     'hp-poc-metabox',      // Unique ID
     esc_html__('Ansprechpartner-Daten'),    // Title
     'hp_poc_post_meta_box',   // Callback function
     'hp_poc_person',         // Admin page (or post type)
     'side',         // Context
     'low'         // Priority
   );
 }
 // Add the person meta boxes.
 add_action( 'add_meta_boxes', 'hp_poc_add_post_meta_boxes' );

 function hp_poc_post_meta_box( $object, $box ) {

   echo wp_nonce_field( basename( __FILE__ ), 'hp_poc_meta_nonce' , false);

   echo '<p>
     <label for="hp-poc-function" style="margin-bottom: 5px; display: block;"><strong>Funktionsbezeichnung</strong></label>
     <input class="widefat" type="text" name="hp_poc_function" id="hp-poc-function" value="'.esc_attr( get_post_meta( $object->ID, 'hp_poc_function', true ) ).'" size="200" />
   </p>';
}


function hp_poc_save_post_meta_boxes( $post_id, $post ) {

  if ( !isset( $_POST['hp_poc_meta_nonce'] ) || !wp_verify_nonce( $_POST['hp_poc_meta_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  $post_type = get_post_type_object( $post->post_type );

  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

    $meta_key = 'hp_poc_function';
    $new_meta_value = ( isset( $_POST['hp_poc_function'] ) ? sanitize_text_field( $_POST['hp_poc_function'] ) : '' );
    $meta_value = get_post_meta( $post_id, $meta_key, true );

    if ( $new_meta_value && '' == $meta_value )
      add_post_meta( $post_id, $meta_key, $new_meta_value, true );

    elseif ( $new_meta_value && $new_meta_value != $meta_value )
      update_post_meta( $post_id, $meta_key, $new_meta_value );

    elseif ( '' == $new_meta_value && $meta_value )
      delete_post_meta( $post_id, $meta_key, $meta_value );
}
// Save post meta on the 'save_post' hook.
add_action( 'save_post', 'hp_poc_save_post_meta_boxes', 10, 2 );

function hp_poc_add_post_columns($columns) {
  return array_merge($columns,
    array(
        'function' =>__( 'Funktion'),
        'area' =>__( 'Arbeitsbereich'),
        'city' =>__( 'Stadt'),
        'country' =>__( 'Bundesland'),
      )
    );
}
// post columns.
add_filter( 'manage_hp_poc_person_posts_columns', 'hp_poc_add_post_columns');

function hp_poc_custom_column( $column, $post_id ) {
  global $wpdb;
  switch ( $column ) {
    case 'function':
      echo get_post_meta( $post_id , 'hp_poc_function' , true );
    break;
    case 'area':
      $mlist = "";
      $types = $wpdb->get_results("SELECT name FROM $wpdb->posts LEFT OUTER JOIN $wpdb->term_relationships rs ON ID = object_id LEFT OUTER JOIN $wpdb->terms t ON rs.term_taxonomy_id = t.term_id LEFT OUTER JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_taxonomy_id WHERE ID = {$post_id} And taxonomy IN('hp_poc_area')");
      foreach($types as $loopId => $type) {
          $mlist .= $type->name.', ';
      }
      echo substr($mlist,0,-2);
    break;
    case 'city':
        $mlist = "";
        $types = $wpdb->get_results("SELECT name FROM $wpdb->posts LEFT OUTER JOIN $wpdb->term_relationships rs ON ID = object_id LEFT OUTER JOIN $wpdb->terms t ON rs.term_taxonomy_id = t.term_id LEFT OUTER JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_taxonomy_id WHERE ID = {$post_id} And taxonomy IN('hp_poc_city')");
        foreach($types as $loopId => $type) {
            $mlist .= $type->name.', ';
        }
        echo substr($mlist,0,-2);
    break;
    case 'country':
        $mlist = "";
        $types = $wpdb->get_results("SELECT name FROM $wpdb->posts LEFT OUTER JOIN $wpdb->term_relationships rs ON ID = object_id LEFT OUTER JOIN $wpdb->terms t ON rs.term_taxonomy_id = t.term_id LEFT OUTER JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_taxonomy_id WHERE ID = {$post_id} And taxonomy IN('hp_poc_country')");
        foreach($types as $loopId => $type) {
            $mlist .= $type->name.', ';
        }
        echo substr($mlist,0,-2);
    break;
  }
}
add_action( 'manage_hp_poc_person_posts_custom_column' , 'hp_poc_custom_column', 10, 2 );

// Register the column as sortable.
function hp_poc_register_sortable_columns( $columns ) {
  $columns['function'] = 'Funktion';
  $columns['area'] = 'Arbeitsbereich';
  $columns['city'] = 'Stadt';
  $columns['country'] = 'Bundesland';
  return $columns;
}
add_filter( 'manage_edit-hp_poc_person_sortable_columns', 'hp_poc_register_sortable_columns' );

// shortcode: listing
function hp_poc_shortcode_listing_func( $atts ) {
  global $wpdb;

  // register scriptassets
  wp_enqueue_script('hp_poc_widget', plugin_dir_url(__FILE__) . 'js/poc.js', array('jquery'));
  wp_enqueue_style('hp_poc_widget', plugin_dir_url(__FILE__) . 'css/poc.css' );
  wp_enqueue_script('hp_poc_select2', plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery'));
  wp_enqueue_style('hp_poc_select2', plugin_dir_url(__FILE__) . 'css/select2.min.css' );

   $atts = shortcode_atts( array(
     'sort' => 'asc',
     'filter' => '',
     'custom' => '', // comma separeted list od person ids
     //'category_name' => '',
     'limit' => '',
   ), $atts, 'ansprechpartner' );

   $results = "";

   $queryParams = array(
      'post_type' => 'hp_poc_person',
      'post_status' => 'publish',
      //'meta_key' => 'hp_poc_startdate',
      //'orderby' => 'meta_value',
      'order' => 'ASC',
    );

    // custom ids.
    if(!empty(esc_attr($atts['custom']))) {
      $queryParams['post__in'] = explode( ',', esc_attr($atts['custom']) );
      $queryParams['orderby'] = 'post__in';
    }

   // limit results.
   if(!empty(esc_attr($atts['limit']))) {
     $limitQueryParams = array(
       'posts_per_page' => esc_attr($atts['limit']),
     );

     $queryParams = array_merge($queryParams, $limitQueryParams);
   }

  $query = new WP_Query($queryParams);

  $city_filter = array(); // city filter
  $country_filter = array(); // country filter
  $area_filter = array(); // area filter

  while ($query->have_posts()) {
      $query->the_post();
      $post_id = get_the_ID();
      $post_title = get_the_title();

      $function = get_post_meta( $post_id , 'hp_poc_function' , true );

      $citystr = "";
      $citys = get_the_terms($post_id,'hp_poc_city');
      if(is_array($citys) && sizeof($citys) > 0):
          foreach($citys as $ind => $city):
            $citystr .= ($city->slug).($ind+1 < sizeof($citys) ? "," : '');
            $city_filter[$city->slug] = $city->name;
          endforeach;
      endif;

      $countrystr = "";
      $countries = get_the_terms($post_id,'hp_poc_country');
      if(is_array($countries) && sizeof($countries) > 0):
          foreach($countries as $ind => $country):
            $countrystr .= ($country->slug).($ind+1 < sizeof($countries) ? "," : '');
            $country_filter[$country->slug] = $country->name;
          endforeach;
      endif;

      $areastr = "";
      $areas = get_the_terms($post_id,'hp_poc_area');
      if(is_array($areas) && sizeof($areas) > 0):
          foreach($areas as $ind => $area):
            $areastr .= ($area->slug).($ind+1 < sizeof($areas) ? "," : '');
          endforeach;
      endif;

      $results .= '
      <div class="hp_poc_row" data-city="'.$citystr.'" data-country="'.$countrystr.'" data-area="'.$areastr.'" data-name="'.mb_strtolower($post_title).'">';
            $results .= '<div class="outer-wrapper">';
            $post = get_post($post_id);

            $content = get_the_content();
            $content = wpautop( $content, true );

            $image = get_the_post_thumbnail_url($post);
            if(strlen($image) > 0) $results .= '<div class="image"><img src="'.$image.'"/></div>';

            $results .= '<div class="inner-wrapper">';
            $results .= '<h4>'.get_the_title().'</h4>';
            if($function != ""):
              $results .= '<div class="function">'.$function.'</div>';
            endif;
            if(strlen($content) > 0) $results .= '<div class="post-content">' . $content . '</div>';

            if($areastr != ""):
              $results .= '<i class="' . $areastr . '"></i>';
            endif;

            $results .= '</div>';

        $results .= '
      </div></div>
      ';
  }

  // set filter.
  $filter = '';
  if(!empty(esc_attr($atts['filter']))) {

    $filter_attr = esc_attr($atts['filter']);
    switch ($filter_attr) {
      case 'search': // autocomplete search
        $filter_content = '<form class="poc-search-form" role="search" method="get" onsubmit="return false" action="' . esc_url( home_url( $wp->request ) ) .'">
            <div class="form-group">
              <input type="text" name="poc-name" class="search-autocomplete" placeholder="Search...">
            </div>
            <button type="submit" class="fa fa-search"></button>
          </form>';

          $filter = '<div class="hp_poc_search">' . $filter_content . '</div>';
          break;
      case 'city': // group by city
        // sort array.
        asort($city_filter);
        $filter_content = '<div class="city-wrapper"><select name="cities" class="poc-filter" id="poc-city-filter">
            <option value="">Stadt wählen</option>';
        foreach($city_filter as $key => $name) {
          $filter_content .= '<option value="'. $key .'">'. $name .'</option>';
        }
        $filter_content .= '</select></div>';

        $filter = '<div class="hp_poc_filter">' . $filter_content . '</div>';
        break;
      case 'country': // group by country
        // sort array.
        asort($country_filter);
        $filter_content = '<div class="country-wrapper"><select name="countries" class="poc-filter" id="poc-country-filter">
            <option value="">Bundesland wählen</option>';
        foreach($country_filter as $key => $name) {
          $filter_content .= '<option value="'. $key .'">'. $name .'</option>';
        }
        $filter_content .= '</select></div>';

        $filter = '<div class="hp_poc_filter">' . $filter_content . '</div>';
        break;
    }
  }

  if(isset($results) && !empty($results)) {
    $results = '<div class="hp_poc_rows">'.$results.'</div>';
  }

  if(isset($filter) && !empty($filter)) {
    $results = '';
  }

  $output = '<div class="poc">
    '.$filter.'
    '.$results.'
  </div>';

  wp_reset_query();

 return $output;
}
add_shortcode( 'ansprechpartner', 'hp_poc_shortcode_listing_func' );

function poc_excerpt_truncate($text, $length = 100, $options = array()) {
    $default = array(
        'ending' => '...', 'exact' => false, 'html' => true
    );
    $options = array_merge($default, $options);
    extract($options);

    if ($html) {
        if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        $totalLength = mb_strlen(strip_tags($ending));
        $openTags = array();
        $truncate = '';

        preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
        foreach ($tags as $tag) {
            if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                    array_unshift($openTags, $tag[2]);
                } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                    $pos = array_search($closeTag[1], $openTags);
                    if ($pos !== false) {
                        array_splice($openTags, $pos, 1);
                    }
                }
            }
            $truncate .= $tag[1];

            $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
            if ($contentLength + $totalLength > $length) {
                $left = $length - $totalLength;
                $entitiesLength = 0;
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entitiesLength <= $left) {
                            $left--;
                            $entitiesLength += mb_strlen($entity[0]);
                        } else {
                            break;
                        }
                    }
                }

                $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                break;
            } else {
                $truncate .= $tag[3];
                $totalLength += $contentLength;
            }
            if ($totalLength >= $length) {
                break;
            }
        }
    } else {
        if (mb_strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
        }
    }
    if (!$exact) {
        $spacepos = mb_strrpos($truncate, ' ');
        if (isset($spacepos)) {
            if ($html) {
                $bits = mb_substr($truncate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                if (!empty($droppedTags)) {
                    foreach ($droppedTags as $closingTag) {
                        if (!in_array($closingTag[1], $openTags)) {
                            array_unshift($openTags, $closingTag[1]);
                        }
                    }
                }
            }
            $truncate = mb_substr($truncate, 0, $spacepos);
        }
    }
    $truncate .= $ending;

    if ($html) {
        foreach ($openTags as $tag) {
            $truncate .= '</'.$tag.'>';
        }
    }

    return $truncate;
}


function poc_person_enqueues() {


  wp_enqueue_style(
    'jquery-auto-complete',
    'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
    array(),
    '1.12.1'
  );

  wp_enqueue_script(
    'jquery-auto-complete',
    'https://code.jquery.com/ui/1.12.1/jquery-ui.js',
    array( 'jquery' ),
    '1.12.1',
    true
  );


  //wp_enqueue_script('global', plugin_dir_url(__FILE__) . 'js/poc.js', array('jquery'), '1.0.0', true);

	wp_localize_script(
		'global',
		'global',
		array(
			'ajax' => admin_url( 'admin-ajax.php' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'poc_person_enqueues' );

function poc_person_ajax_search() {

	$results = new WP_Query( array(
		'post_type'     => array( 'hp_poc_person' ),
		'post_status'   => 'publish',
		'nopaging'      => true,
		'posts_per_page'=> 100,
		'post_title_like' => stripslashes( $_REQUEST['search'] ),
	) );

	$items = array();

	if ( !empty( $results->posts ) ) {
		foreach ( $results->posts as $result ) {
			$items[$result->post_title] = $result->post_title;
		}
	}

  asort($items);

	wp_send_json_success( $items );
}

add_filter( 'posts_where', 'title_like_posts_where', 10, 2 );
function title_like_posts_where( $where, $wp_query ) {
    global $wpdb;
    if ( $post_title_like = $wp_query->get( 'post_title_like' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $post_title_like ) ) . '%\'';
    }
    return $where;
}

add_action( 'wp_ajax_search_site',        'poc_person_ajax_search' );
add_action( 'wp_ajax_nopriv_search_site', 'poc_person_ajax_search' );



add_action( 'init', 'better_rest_api_featured_images_init', 12 );
/**
 * Register our enhanced better_featured_image field to all public post types
 * that support post thumbnails.
 *
 * @since  1.0.0
 */
function better_rest_api_featured_images_init() {

	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;
		$supports_thumbnail = post_type_supports( $post_type_name, 'thumbnail' );

		// Only proceed if the post type is set to be accessible over the REST API
		// and supports featured images.
		if ( $show_in_rest && $supports_thumbnail && $post_type_name ==  "hp_poc_person") {

			// Compatibility with the REST API v2 beta 9+
			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
					'featured_image',
					array(
						'get_callback' => 'better_rest_api_featured_images_get_field',
						'schema'       => null,
					)
				);
			} elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
					'featured_image',
					array(
						'get_callback' => 'better_rest_api_featured_images_get_field',
						'schema'       => null,
					)
				);
			}

      register_rest_field( $post_type_name,
          'funktionsbezeichnung',
          array(
              'get_callback'      => 'hp_poc_funktion_field',
              'update_callback'   => null,
              'schema'            => null,
          )
      );


		}
	}
}

/**
 * Return the better_featured_image field.
 *
 * @since   1.0.0
 *
 * @param   object  $object      The response object.
 * @param   string  $field_name  The name of the field to add.
 * @param   object  $request     The WP_REST_Request object.
 *
 * @return  object|null
 */
function better_rest_api_featured_images_get_field( $object, $field_name, $request ) {

	// Only proceed if the post has a featured image.
	if ( ! empty( $object['featured_media'] ) ) {
		$image_id = (int)$object['featured_media'];
	} elseif ( ! empty( $object['featured_image'] ) ) {
		// This was added for backwards compatibility with < WP REST API v2 Beta 11.
		$image_id = (int)$object['featured_image'];
	} else {
		return null;
	}

	$image = get_post( $image_id );

	if ( ! $image ) {
		return null;
	}

	// This is taken from WP_REST_Attachments_Controller::prepare_item_for_response().
	$featured_image['id']            = $image_id;
	$featured_image['alt_text']      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	$featured_image['caption']       = $image->post_excerpt;
	$featured_image['description']   = $image->post_content;
	$featured_image['media_type']    = wp_attachment_is_image( $image_id ) ? 'image' : 'file';
	$featured_image['media_details'] = wp_get_attachment_metadata( $image_id );
	$featured_image['post']          = ! empty( $image->post_parent ) ? (int) $image->post_parent : null;
	$featured_image['source_url']    = wp_get_attachment_url( $image_id );

	if ( empty( $featured_image['media_details'] ) ) {
		$featured_image['media_details'] = new stdClass;
	} elseif ( ! empty( $featured_image['media_details']['sizes'] ) ) {
		$img_url_basename = wp_basename( $featured_image['source_url'] );
		foreach ( $featured_image['media_details']['sizes'] as $size => &$size_data ) {
			$image_src = wp_get_attachment_image_src( $image_id, $size );
			if ( ! $image_src ) {
				continue;
			}
			$size_data['source_url'] = $image_src[0];
		}
	} elseif ( is_string( $featured_image['media_details'] ) ) {
		// This was added to work around conflicts with plugins that cause
		// wp_get_attachment_metadata() to return a string.
		$featured_image['media_details'] = new stdClass();
		$featured_image['media_details']->sizes = new stdClass();
	} else {
		$featured_image['media_details']['sizes'] = new stdClass;
	}

	return apply_filters( 'better_rest_api_featured_image', $featured_image, $image_id );
}

function hp_poc_funktion_field( $post, $field_name, $request) {
    return get_post_meta( $post['id'], 'hp_poc_function', true );
}
