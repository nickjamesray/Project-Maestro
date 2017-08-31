<?php
 
class MaestroGameBuilder_Games_API {
 
    //public $minigameOptions = array('simon'=>'Simon Says','bridgerunner'=>'Bridge Runner','gateway'=>'Gateway Code','toprunner'=>'Top-Down Runner');
    public $minigameOptions = array('bridgerunner'=>'Bridge Runner');
    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = '/maestro-game-builder/v1';
        $this->resource_name = 'posts';
    }
 
    // Register our routes.
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->resource_name, array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?P<id>[\d]+)', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'get_item' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),
            // Register our schema callback.
            'schema' => array( $this, 'get_item_schema' ),
        ) );


        /* Search for Harmony Routes */
        register_rest_route( $this->namespace, '/init', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'init' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/game/create', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'create_game' ),
                'permission_callback' => array( $this, 'create_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/game/copy/(?P<id>[\d]+)', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'copy_game' ),
                'permission_callback' => array( $this, 'create_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/game/delete/(?P<id>[\d]+)', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'delete_game' ),
                //TODO create permission callback for delete
                'permission_callback' => array( $this, 'create_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/game/(?P<id>[\d]+)/minigame/(?P<mid>[\d]+)/delete', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'delete_minigame' ),
                //TODO create permission callback for delete
                'permission_callback' => array( $this, 'create_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/games/(?P<id>[\d]+)', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'get_game' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/games/(?P<id>[\d]+)/minigames/(?P<mid>[\d]+)', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'get_minigame' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/game/save/(?P<id>[\d]+)', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'save_game' ),
                //TODO create permission callback for delete
                'permission_callback' => array( $this, 'create_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );
        register_rest_route( $this->namespace, '/games/(?P<id>[\d]+)/minigame/create', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'create_minigame' ),
                //TODO create permission callback for delete
                'permission_callback' => array( $this, 'create_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/games/(?P<id>[\d]+)/minigame/(?P<mid>[\d]+)/save', array(
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            array(
                'methods'   => 'POST',
                'callback'  => array( $this, 'save_minigame' ),
                //TODO create permission callback for delete
                'permission_callback' => array( $this, 'create_items_permissions_check' ),
            ),
            // Register our schema callback.
            //'schema' => array( $this, 'get_item_schema' ),
        ) );

    }

    /**
     * Load engine and capabilities based on user role and existing content.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function init( $request ) {
      $array = array();
      //Determine if administrator or author by permissions.
      $args = array('post_type'=>"mgb_game",'posts_per_page'=>-1,'post_status'=>array( 'publish', 'draft', 'trash' ));
      $user = wp_get_current_user();
      $array['role'] = $user->roles[0];
      if( !current_user_can( 'edit_others_posts' ) ) {
        //Not an administrator so only get this current user's games
        $args['author'] = get_current_user_id();
      }
      // Fetch existing games
      $array['games'] = array();
      $counter = 0;
      $query = new WP_Query($args);
      while($query->have_posts()) :
        $query->the_post();
        $id = get_the_ID();
        $array['games'][$counter]['title'] = html_entity_decode(get_the_title());
        $array['games'][$counter]['id'] = get_the_ID();
        $array['games'][$counter]['status'] = get_post_status();
        $array['games'][$counter]['link'] = get_permalink();
        $custom = get_post_custom();
        foreach($custom as $k=>$v){
          if(count($custom[$k])<=1){
            /* Unless it is a minigame, return as single, not array. */
            if($k!='minigames'){
              $custom[$k] = $v[0];
            }
          }
        }
        if(isset($custom['type'])){
          switch($custom['type']) {
            case 'minigame':
            $custom['typeLabel'] = 'Just Minigames';
            break;
            default:
            $custom['typeLabel'] = 'Minigames + Menu';
            break;
          }
        }
        $array['games'][$counter]['custom'] = $custom;
        /* Return meta like game type. */
        $counter++;
      endwhile;

      return rest_ensure_response($array);
    }

    public function create_game( $request ) {
      // Create post object
      $title = (string) $request->get_param( 'title' );
      $author = get_current_user_id();
      $title = htmlentities( html_entity_decode(wp_strip_all_tags($title) ) );;
      $my_post = array(
        'post_title'    =>  $title,
        'post_content'  => " ",
        'post_status'   => 'draft',
        'post_author'   => $author,
        'post_type' => 'mgb_game'
      );
      // Insert the post into the database
      $newID = wp_insert_post( $my_post, true );
      if($newID>0){
        return rest_ensure_response(array('created'=>true, 'id'=>$newID));
      }else{
        return rest_ensure_response(array('created'=>false));
      }
      
    }

    public function create_minigame( $request ) {
      $id = (int) $request['id'];
      $post = get_post( $id );
      //check for admin
      $current_user = wp_get_current_user();
      $new_post_author = $current_user->ID;
      if($new_post_author != $post->post_author && !current_user_can( 'edit_others_posts' ) ){
        /* Only admins can copy games from other users. */
        return rest_ensure_response( array('error' => 'You can only create minigames for your own games.') );
      }
      // Create minigame object
      $title = (string) $request->get_param( 'title' );
      $author = get_current_user_id();
      $title = htmlentities( html_entity_decode(wp_strip_all_tags($title) ) );;
      $my_post = array(
        'post_title'    =>  $title,
        'post_content'  => " ",
        'post_status'   => 'publish',
        'post_author'   => $author,
        'post_type' => 'mgb_game'.$id.'_minigame'
      );
      // Insert the post into the database
      $newID = wp_insert_post( $my_post, true );
      update_post_meta( $newID, 'type', $request['type'] );
      if($newID>0){
        $data = array('id'=>$newID,'title'=>$title,'type'=>$request['type']);
        return rest_ensure_response(array('created'=>true, 'minigame'=>$data));
      }else{
        return rest_ensure_response(array('created'=>false));
      }
    }

    public function get_minigame( $request ) {
      $id = (int) $request['id'];
      $mid = (int) $request['mid'];
      $post = get_post( $mid );

      if ( empty( $post ) ) {
          return rest_ensure_response( array() );
      }
      /* Get post meta. Remove array setup for single entries. */
      $custom = get_post_custom($mid);
      foreach($custom as $k=>$v){
        if(count($custom[$k])<=1){
          /* Unless it is a minigame, return as single, not array. */
          if($k!='minigame_data'){
            $custom[$k] = $v[0];
          }else{
            $custom[$k] = unserialize($v[0]);
          }
        }
      }
      $post->custom = $custom;
      /*
      ATTENTION! All active minigame options must go here so they can be selected.
      */
      $post->minigameOptions = $this->minigameOptions;
      return rest_ensure_response( $post );
      //$response = $this->prepare_item_for_response( $post, $request );

      // Return all of our post response data.
      //return $response;
    }

    public function delete_game( $request ) {
      // Create post object
      $id = (int) $request['id'];
      $post = get_post( $id );
      //check for admin
      $current_user = wp_get_current_user();
      $new_post_author = $current_user->ID;
      if($new_post_author != $post->post_author && !current_user_can( 'edit_others_posts' ) ){
        /* Only admins can copy games from other users. */
        return rest_ensure_response( array('error' => 'You can only delete your own games.') );
      }
      //delete post
      wp_delete_post($id);
      $args = array('post_type'=>'mgb_game'.$id.'_minigame','posts_per_page'=>-1);
      $query = new WP_Query($args);
      while($query->have_posts()) :
        $query->the_post();
        $minigameID = get_the_ID();
        wp_delete_post($minigameID);
      endwhile;
      return rest_ensure_response(array('deleted'=>true));
    }

    public function delete_minigame( $request ) {
      // Create post object
      $id = (int) $request['mid'];
      $game = (int) $request['id'];
      $post = get_post( $id );
      //check for admin
      $current_user = wp_get_current_user();
      $new_post_author = $current_user->ID;
      if($new_post_author != $post->post_author && !current_user_can( 'edit_others_posts' ) ){
        /* Only admins can copy games from other users. */
        return rest_ensure_response( array('error' => 'You can only delete your own minigames.') );
      }
      //delete post
      wp_delete_post($id);
      /* TODO Update content for the game it is attached to. */
      $keep = array();
      $minigames = get_post_meta($game,'minigame_order');
      foreach($minigames[0] as $minigame){
        if($minigame['id']!=$id){
          $keep[] = $minigame;
        }
      }
      update_post_meta($game,'minigame_order',$keep);
      return rest_ensure_response(array('deleted'=>true));
    }

    public function get_game( $request ) {
      $id = (int) $request['id'];
      $post = get_post( $id );

      if ( empty( $post ) ) {
          return rest_ensure_response( array() );
      }
      /* Get post meta. Remove array setup for single entries. */
      $custom = get_post_custom($id);
      foreach($custom as $k=>$v){
        if(count($custom[$k])<=1){
          /* Unless it is a minigame, return as single, not array. */
          if($k!='minigame_order'&&$k!='game_data'){
            $custom[$k] = $v[0];
          }else{
            $custom[$k] = unserialize($v[0]);
          }
        }
      }
      $post->custom = $custom;
      /* TODO - general post type, or specific to post id? */
      $args = array('post_type'=>'mgb_game'.$id.'_minigame','posts_per_page'=>-1);
      $array = array();
      $minigameIDs = array();
      $counter = 0;
      $query = new WP_Query($args);
      while($query->have_posts()) :
        $query->the_post();
        $id = get_the_ID();
        $minigameIDs[] = get_the_ID();
        $array[$counter]['title'] = html_entity_decode(get_the_title());
        $array[$counter]['id'] = get_the_ID();
        $array[$counter]['type'] = get_post_meta($id, 'type')[0];
        // $custom = get_post_custom();
        // foreach($custom as $k=>$v){
        //   if(count($custom[$k])<=1){
        //       $custom[$k] = $v[0];
        //   }
        // }
        // $array[$counter]['custom'] = $custom;
        /* Return meta like game type. */
        $counter++;
      endwhile;
      $post->minigames = $array;
      $post->minigameIDs = $minigameIDs;
      $post->minigameOptions = $this->minigameOptions;
      $post->guid = html_entity_decode($post->guid);
      return rest_ensure_response( $post );
      //$response = $this->prepare_item_for_response( $post, $request );

      // Return all of our post response data.
      //return $response;
    }

    public function save_game( $request ) {
      $id = (int) $request['id'];
      $array = array();
      $content = ' ';
      if(isset($request['description'])){
        $content = wp_strip_all_tags( $request['description'] );
      }
      /* Save Game */
      $my_post = array(
          'ID'           => $id,
          'post_title'   => $request['title'],
          'post_content' => $content,
          'post_status' => $request['status']
      );

      // Update the post into the database
      wp_update_post( $my_post );
      update_post_meta( $id, 'type', $request['type'] );
      if(isset($request['minigame_order'])){
        update_post_meta( $id, 'minigame_order', $request['minigame_order'] );
      }
      $gameData = array();
      if(isset($request['imageData'])){
        foreach( $request[ 'imageData' ] as $k=>$imageComponent ) {
          if( $imageComponent['dataUrl']!=null){
            $imageComponent['activeUrl'] = $this->upload_image( $imageComponent, $imageComponent['name'], $id );
            $array['images_saved'][$counter] = $imageComponent['activeUrl'];
            $counter++;
            $gameData['imageData'][$imageComponent['name']]['url'] = $imageComponent['activeUrl'];
            $gameData['imageData'][$imageComponent['name']]['name'] = $imageComponent['name'];
            //$minigameData['imageData'][$imageComponent['name']]['dataUrl'] = null;
          }else if( $imageComponent['activeUrl'] != null){
            /* Resave unchanged values */
            $gameData['imageData'][$imageComponent['name']]['url'] = $imageComponent['activeUrl'];
            $gameData['imageData'][$imageComponent['name']]['name'] = $imageComponent['name'];
          }
        }
        update_post_meta( $id, 'game_data',  $gameData);
      }

      //return rest_ensure_response( $post );
      $array['saved'] = true;
      $array['link'] = get_permalink($id);
      return rest_ensure_response($array);
    }

    protected function upload_image( $imageComponent, $name, $id, $mid = 0 ) {
      /* Upload image to proper place. */
      //Get the base-64 string from data
      $filteredData=substr($imageComponent['dataUrl'], strpos($imageComponent['dataUrl'], ",")+1);
       
      //Decode the string
      $unencodedData=base64_decode($filteredData);
      $uploads = wp_upload_dir();
      $path = '/maestro-game-builder/'.$id.'/';
      if( $mid > 0 ) {
        $path .= 'minigames/'.$mid.'/';
      }
      //Save the image
      if (!is_dir($uploads['basedir'].$path)) {
        // dir doesn't exist, make it
        mkdir($uploads['basedir'].$path,0775,true);
      }
      file_put_contents($uploads['basedir'].$path.$name.'.png', $unencodedData);
      return $uploads['baseurl'].$path.$name.'.png';
    }

    public function save_minigame( $request ) {
      $id = (int) $request['id'];
      $mid = (int) $request['mid'];
      $array = array();
      $content = ' ';

      update_post_meta( $mid, 'type', $request['type'] );
      /* Check for dataUrl and save */
      $counter = 0;
      $imageData = array();
      $minigameData = array();
      foreach( $request['data']['imageData'] as $k=>$imageComponent ) {
        $multi = false;
        if( isset( $imageComponent['group'] ) && !empty( $imageComponent['group'] ) ) {
          /* Multipart field, e.g. collectible. */
          $multi = true;
          /* This tracks images in a group that are still used, so we can remove the old ones from disk. */
          $liveImages = array();
          foreach( $imageComponent['group'] as $index => $multiImage ) {

            if( $multiImage['dataUrl'] != null ) {

              $name = $imageComponent['name'].'_'.$index.'.'.time();

              $multiImage['activeUrl'] = $this->upload_image( $multiImage, $name, $id, $mid );

              $array['images_saved'][$counter] = $multiImage['activeUrl'];
              $liveImages[ $imageComponent['name'] ][] = $multiImage['activeUrl'];
              $counter++;
              $minigameData['imageData'][$imageComponent['name']]['group'][ $index ]['url'] = $multiImage['activeUrl'];
              $minigameData['imageData'][$imageComponent['name']]['group'][ $index ]['name'] = $name;
            }else if( $multiImage['activeUrl'] != null){
              /* Resave unchanged values */
              $minigameData['imageData'][$imageComponent['name']]['group'][ $index ]['url'] = $multiImage['activeUrl'];
              $minigameData['imageData'][$imageComponent['name']]['group'][ $index ]['name'] = $multiImage['name'];
              $liveImages[ $imageComponent['name'] ][] = $multiImage['activeUrl'];
            }
          }
          /* TODO check old images and remove */
          // $uploads = wp_upload_dir();
          // $handle = opendir($uploads['basedir'].'/maestro-game-builder/'.$id.'/minigames/'.$mid.'/');
          // while($file = readdir($handle)){
          //     if($file !== '.' && $file !== '..'){
          //         if (strpos($file, $imageComponent['name']) !== false) {
          //             /* Existing File */
          //             if(!in_array($file, $liveImages[ $imageComponent['name'] ])){
          //               echo $file;
          //             }
          //         }
          //     }
          // }
        }else{

          if( $imageComponent['dataUrl']!=null){
            $imageComponent['activeUrl'] = $this->upload_image( $imageComponent, $imageComponent['name'], $id, $mid );
            $array['images_saved'][$counter] = $imageComponent['activeUrl'];
            $counter++;
            $minigameData['imageData'][$imageComponent['name']]['url'] = $imageComponent['activeUrl'];
            $minigameData['imageData'][$imageComponent['name']]['name'] = $imageComponent['name'];
            //$minigameData['imageData'][$imageComponent['name']]['dataUrl'] = null;
          }else if( $imageComponent['activeUrl'] != null){
            /* Resave unchanged values */
            $minigameData['imageData'][$imageComponent['name']]['url'] = $imageComponent['activeUrl'];
            $minigameData['imageData'][$imageComponent['name']]['name'] = $imageComponent['name'];
          }

        }
      }
      $minigameData['textData'] = $request['data']['textData'];
      update_post_meta( $mid, 'minigame_data',  $minigameData);

      /* Save Game */
      $my_post = array(
          'ID'           => $mid,
          'post_title'   => $request['title'],
          'post_content' => $content,
      );

      // Update the post into the database
      wp_update_post( $my_post );


      $array['saved'] = true;
      return rest_ensure_response($array);
    }

    public function copy_game( $request ) {
      global $wpdb;
      $id = (int) $request['id'];
      $post = get_post( $id );
      /*
       * if you don't want current user to be the new post author,
       * then change next couple of lines to this: $new_post_author = $post->post_author;
       */
      $current_user = wp_get_current_user();
      $new_post_author = $current_user->ID;
      if($new_post_author != $post->post_author && !current_user_can( 'edit_others_posts' ) ){
        /* Only admins can copy games from other users. */
        return rest_ensure_response( array('error' => 'You can only copy your own games.') );
      }
     
      /*
       * if post data exists, create the post duplicate
       */
      if (isset( $post ) && $post != null) {
     
        /*
         * new post data array
         */
        $args = array(
          'comment_status' => $post->comment_status,
          'ping_status'    => $post->ping_status,
          'post_author'    => $new_post_author,
          'post_content'   => $post->post_content,
          'post_excerpt'   => $post->post_excerpt,
          'post_name'      => $post->post_name,
          'post_parent'    => $post->post_parent,
          'post_password'  => $post->post_password,
          'post_status'    => 'draft',
          'post_title'     => $post->post_title." (Copy)",
          'post_type'      => $post->post_type,
          'to_ping'        => $post->to_ping,
          'menu_order'     => $post->menu_order
        );
     
        /*
         * insert the post by wp_insert_post() function
         */
        $new_post_id = wp_insert_post( $args );
     
        /*
         * get all current post terms ad set them to the new post draft
         */
        // $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        // foreach ($taxonomies as $taxonomy) {
        //   $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
        //   wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        // }
     
        /*
         * duplicate all post meta just in two SQL queries
         */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM ".$wpdb->postmeta." WHERE post_id=".$id);
        if (count($post_meta_infos)!=0) {
          $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
          foreach ($post_meta_infos as $meta_info) {
            $meta_key = $meta_info->meta_key;
            $meta_value = addslashes($meta_info->meta_value);
            $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
          }
          $sql_query.= implode(" UNION ALL ", $sql_query_sel);
          $wpdb->query($sql_query);
        }
        $this->copy_game_minigames( $id, $new_post_id, $new_post_author );
        return rest_ensure_response( array('successful' => true) );
      }
    }

    protected function copy_game_minigames( $oldID, $newID, $author ) {
      global $wpdb;
      $args = array('post_type'=>'mgb_game'.$oldID.'_minigame','posts_per_page'=>-1);
      $array = array();
      $minigameIDs = array();
      $counter = 0;
      $query = new WP_Query($args);
      while($query->have_posts()) :
        $query->the_post();

        /*
         * new post data array
         */
        $oldMinigameID = get_the_ID();
        $title = get_the_title();
        $content = get_the_content();
        $args = array(
          'post_author'    => $author,
          'post_content'   => $content,
          'post_status'    => 'publish',
          'post_title'     => $title,
          'post_type'      => 'mgb_game'.$newID.'_minigame',
        );
     
        /*
         * insert the post by wp_insert_post() function
         */
        $new_post_id = wp_insert_post( $args );
        /* Create an array to convert the meta tag*/
        $minigameIDs[$oldMinigameID] = $new_post_id;
        /*
         * get all current post terms ad set them to the new post draft
         */
        // $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        // foreach ($taxonomies as $taxonomy) {
        //   $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
        //   wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        // }
     
        /*
         * duplicate all post meta just in two SQL queries
         */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM ".$wpdb->postmeta." WHERE post_id=".$oldMinigameID);
        if (count($post_meta_infos)!=0) {
          $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
          foreach ($post_meta_infos as $meta_info) {
            $meta_key = $meta_info->meta_key;
            $meta_value = addslashes($meta_info->meta_value);
            $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
          }
          $sql_query.= implode(" UNION ALL ", $sql_query_sel);
          $wpdb->query($sql_query);
        }
      endwhile;
      /* convert the minigame_order meta for the new post to correct for IDs */
      $minigames = get_post_meta($newID,'minigame_order');
      //$minigames = $minigames[0];
      //print_r($minigames[0]);
      foreach($minigames[0] as $key => $minigame){
        if(array_key_exists($minigame['id'],$minigameIDs)){
          /* 
          $minigames is the array of old IDs that are activated
          $key is the position (indexed array)
          $minigameIDs is the array of old/new IDs
          $minigame is the oldID that references the new ID
          */
          $minigames[$key]['id'] = $minigameIDs[$minigame['id']];
        }
      }
      update_post_meta($newID,'minigame_order',$minigames);
    }

    /* Default Standards for WP Integration - TODO */


    /**
     * Check permissions for the posts.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items_permissions_check( $request ) {
        if ( ! current_user_can( 'read' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), array( 'status' => $this->authorization_status_code() ) );
        }
        return true;
    }

    public function create_items_permissions_check( $request ) {
        if ( ! current_user_can( 'publish_posts' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), array( 'status' => $this->authorization_status_code() ) );
        }
        return true;
    }
 
    /**
     * Grabs the five most recent posts and outputs them as a rest response.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_items( $request ) {
        $args = array(
            'post_per_page' => 5,
        );
        $posts = get_posts( $args );
 
        $data = array();
 
        if ( empty( $posts ) ) {
            return rest_ensure_response( $data );
        }
 
        foreach ( $posts as $post ) {
            $response = $this->prepare_item_for_response( $post, $request );
            $data[] = $this->prepare_response_for_collection( $response );
        }
 
        // Return all of our comment response data.
        return rest_ensure_response( $data );
    }
 
    /**
     * Check permissions for the posts.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_item_permissions_check( $request ) {
        if ( ! current_user_can( 'read' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), array( 'status' => $this->authorization_status_code() ) );
        }
        return true;
    }
 
    /**
     * Grabs the five most recent posts and outputs them as a rest response.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_item( $request ) {
        $id = (int) $request['id'];
        $post = get_post( $id );
 
        if ( empty( $post ) ) {
            return rest_ensure_response( array() );
        }
 
        $response = prepare_item_for_response( $post );
 
        // Return all of our post response data.
        return $response;
    }
 
    /**
     * Matches the post data to the schema we want.
     *
     * @param WP_Post $post The comment object whose response is being prepared.
     */
    public function prepare_item_for_response( $post, $request ) {
        $post_data = array();
 
        $schema = $this->get_item_schema( $request );
 
        // We are also renaming the fields to more understandable names.
        if ( isset( $schema['properties']['id'] ) ) {
            $post_data['id'] = (int) $post->ID;
        }
 
        if ( isset( $schema['properties']['content'] ) ) {
            $post_data['content'] = apply_filters( 'the_content', $post->post_content, $post );
        }
 
        return rest_ensure_response( $post_data );
    }
 
    /**
     * Prepare a response for inserting into a collection of responses.
     *
     * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
     *
     * @param WP_REST_Response $response Response object.
     * @return array Response data, ready for insertion into collection data.
     */
    public function prepare_response_for_collection( $response ) {
        if ( ! ( $response instanceof WP_REST_Response ) ) {
            return $response;
        }
 
        $data = (array) $response->get_data();
        $server = rest_get_server();
 
        if ( method_exists( $server, 'get_compact_response_links' ) ) {
            $links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
        } else {
            $links = call_user_func( array( $server, 'get_response_links' ), $response );
        }
 
        if ( ! empty( $links ) ) {
            $data['_links'] = $links;
        }
 
        return $data;
    }
 
    /**
     * Get our sample schema for a post.
     *
     * @param WP_REST_Request $request Current request.
     */
    public function get_item_schema( $request ) {
        $schema = array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title'                => 'post',
            'type'                 => 'object',
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties'           => array(
                'id' => array(
                    'description'  => esc_html__( 'Unique identifier for the object.', 'my-textdomain' ),
                    'type'         => 'integer',
                    'context'      => array( 'view', 'edit', 'embed' ),
                    'readonly'     => true,
                ),
                'content' => array(
                    'description'  => esc_html__( 'The content for the object.', 'my-textdomain' ),
                    'type'         => 'string',
                ),
            ),
        );
 
        return $schema;
    }
 
    // Sets up the proper HTTP status code for authorization.
    public function authorization_status_code() {
 
        $status = 401;
 
        if ( is_user_logged_in() ) {
            $status = 403;
        }
 
        return $status;
    }
}