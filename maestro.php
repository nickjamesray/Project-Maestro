<?php
/*
Plugin Name: Maestro Game Builder
Plugin URI: httMGB://projectmaestro.org
Description: NEH-funded WordPress tool for building knowledge-based games.
Version: 1.0
Author: Stone Soup Productions, Inc.
License: GPL2
*/
require_once('api.php');

require_once('minigames/admin/index.php');

class MaestroGameBuilder {

	public $scriptList = array(
		'bridgerunner'=>'BridgeRunner'
	);

	function __construct(){
		add_action( 'init', array($this,'register_games') );
		add_action( 'admin_enqueue_scripts', array($this,'game_admin_enqueue') );
		add_action('admin_menu', array($this,'register_menu'));
		add_action( 'rest_api_init', array( $this, 'register_api_routes' ) );
		add_action( 'admin_head', array( $this, 'add_menu_button_style' ) );
		add_filter('the_content', array($this, 'game_filter_the_content'));
		add_filter('admin_head',array($this,'show_tiny_mce'));
		/* Public facing */
		add_action( 'wp_enqueue_scripts', array($this, 'game_front_enqueue' ) );
		
	}

	public function register_api_routes() {
		$controller = new MaestroGameBuilder_Games_API();
    	$controller->register_routes();
	}

	public function register_menu() {
		add_menu_page(
	        __( 'List Games', 'textdomain' ),
	        __( 'Games','textdomain' ),
	        'manage_options',
	        'maestro-game-builder',
	        array($this,'games_callback'),
	        'none'
	    );
	}

	public function games_callback() {
		?>
		<script>var MaestroPluginBase = '<?php echo plugins_url(); ?>';</script>
		<div id="maestro-app"></div>
		<?php
	}

	public function add_menu_button_style() {
		echo "<style>
		#adminmenu #toplevel_page_maestro-game-builder .wp-menu-image:before {
		    content: '\\f11b';
		    font-family: 'FontAwesome';
		    line-height: 18px;
		}</style>";
	}

	public function register_games() {
		global $post;
		$labels = array(
		'name'               => _x( 'Games', 'post type general name', 'your-plugin-textdomain' ),
		'singular_name'      => _x( 'Game', 'post type singular name', 'your-plugin-textdomain' ),
		'menu_name'          => _x( 'Games', 'admin menu', 'your-plugin-textdomain' ),
		'name_admin_bar'     => _x( 'Game', 'add new on admin bar', 'your-plugin-textdomain' ),
		'add_new'            => _x( 'Add New', 'book', 'your-plugin-textdomain' ),
		'add_new_item'       => __( 'Add New Game', 'your-plugin-textdomain' ),
		'new_item'           => __( 'New Game', 'your-plugin-textdomain' ),
		'edit_item'          => __( 'Edit Game', 'your-plugin-textdomain' ),
		'view_item'          => __( 'View Game', 'your-plugin-textdomain' ),
		'all_items'          => __( 'All Games', 'your-plugin-textdomain' ),
		'search_items'       => __( 'Search Games', 'your-plugin-textdomain' ),
		'parent_item_colon'  => __( 'Parent Games:', 'your-plugin-textdomain' ),
		'not_found'          => __( 'No games found.', 'your-plugin-textdomain' ),
		'not_found_in_trash' => __( 'No games found in Trash.', 'your-plugin-textdomain' )
		);

		$args = array(
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"show_ui" => false,
			"has_archive" => false,
			"show_in_menu" => true,
			"exclude_from_search" => true,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "game", "with_front" => true ),
			"query_var" => false,
							
		);
		register_post_type( "mgb_game", $args );
	}
	public function game_admin_enqueue($hook) {
		/* WYSIWYG TODO */
//		wp_enqueue_script('editor');

		wp_register_style( 'fontawesome', 'http:////maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
		wp_enqueue_style( 'fontawesome');
		
	    if( in_array($hook, array('toplevel_page_maestro-game-builder') ) ){
	    	if(isset($_REQUEST['preview'])){
	    		echo $this->game_preview_iframe($_REQUEST['preview']);
	    		exit;
	    	}
			wp_register_style( 'maestro-game-menu', plugins_url() . '/maestro-game-builder/dist/css/main-dist.css');
			wp_enqueue_style( 'maestro-game-menu');
			wp_enqueue_media();
			wp_enqueue_script( 'maestro-game-png-compress', plugins_url() . '/maestro-game-builder/node_modules/canvas-png-compression/dist/bundle.js', array(), false, true);
	       	wp_enqueue_script( 'maestro-game-admin-script', plugins_url() . '/maestro-game-builder/dist/js/main-dist.js', array('wp-api'), false, true );
	    }
	}

	public function show_tiny_mce() {
        // conditions here
        wp_enqueue_script( 'common' );
        wp_enqueue_script( 'jquery-color' );
        wp_print_scripts('editor');
        if (function_exists('add_thickbox')) add_thickbox();
        wp_print_scripts('media-upload');
        if (function_exists('wp_tiny_mce')) wp_tiny_mce();
        wp_admin_css();
        wp_enqueue_script('utils');
        do_action("admin_print_styles-post-php");
        do_action('admin_print_styles');
        //wp_enqueue_script('wplink');
        wp_enqueue_style('editor-buttons');
    }

	public function game_front_enqueue() {
		global $post;
		global $gameData;
    	if( 'mgb_game' == $post->post_type ){
			wp_register_style( 'maestro-game-builder-css', plugins_url() . '/maestro-game-builder/dist/css/maestro.css');
			wp_enqueue_style( 'maestro-game-builder-css');
			//wp_enqueue_script( 'maestro-game-builder-hermite', plugins_url() . '/maestro-game-builder/src/vendor/hermite.js', array(), false, true );
			wp_enqueue_script( 'maestro-game-builder-phaser', plugins_url().'/maestro-game-builder/minigames/phaser.min.js', array(), false, true );
			wp_enqueue_script( 'maestro-game-builder-utils', plugins_url().'/maestro-game-builder/minigames/views/Utils.js', array('maestro-game-builder-phaser'), false, true);
			$gameData = get_post_custom($post->ID);
			$minigameIDs = array();
			foreach($gameData as $k=>$v){
				if(count($gameData[$k])<=1){
				  /* Unless it is a minigame, return as single, not array. */
				  if($k!='minigame_order'&&$k!='game_data'){
				    $gameData[$k] = $v[0];
				  }else{
				    $gameData[$k] = unserialize($v[0]);
				    foreach($gameData[$k] as $minigame){
				    	$minigameIDs[] = $minigame['id'];
				    }
				  }
				}
			}
			$args = array('post_type'=>'mgb_game'.$post->ID.'_minigame','posts_per_page'=>-1,'post__in'=>$minigameIDs);
			$query = new WP_Query($args);
			while($query->have_posts()) :
				$query->the_post();
			$mid = get_the_ID();
			$minigames[$mid]['title'] = html_entity_decode(get_the_title());
			$minigames[$mid]['id'] = get_the_ID();
			$minigames[$mid]['type'] = get_post_meta($mid, 'type')[0];
			$minigames[$mid]['script'] = $this->scriptList[$minigames[$mid]['type']];
			/* Get post meta. Remove array setup for single entries. */
			$minigames[$mid]['custom'] = get_post_custom($mid);
			foreach($minigames[$mid]['custom'] as $k=>$v){
				if(count($minigames[$mid]['custom'][$k])<=1){
					/* Unless it is a minigame, return as single, not array. */
					if($k!='minigame_data'){
						$minigames[$mid]['custom'][$k] = $v[0];
					}else{
						$minigames[$mid]['custom'][$k] = unserialize($v[0]);
					}
				}
			}
			/* Return meta like game type. */
			$gameData['minigames'] = $minigames;
			$lastScript = 'maestro-game-builder-utils';
			endwhile;
			wp_reset_query();
			$scriptsLoaded = array();
			foreach($minigames as $minigame){
				if(!in_array($minigame['script'],$scriptsLoaded)) {
					$lastScript = 'maestro-game-builder-'.$minigame['type'];
					wp_enqueue_script( $lastScript, plugins_url().'/maestro-game-builder/minigames/views/'.$minigame['script'].'.js', array('maestro-game-builder-utils'), false, true);
					$scriptsLoaded[] = $minigame['script'];
				}
			}
			wp_add_inline_script($lastScript,$this->game_front_inline());
    	}
	}


	public function game_preview_iframe($minigame) {
		$minigamePost = get_post($_REQUEST['minigame']);
		$custom = get_post_custom($_REQUEST['minigame']);
		$minigameData = unserialize($custom['minigame_data'][0]);
		$minigameData['base'] = plugins_url();
		$minigameData['id'] = $minigamePost->ID;
		$minigameData['textData']['help']['title'] = $minigamePost->post_title;
		ob_start();
		?>
		<link rel="stylesheet" href="<?php echo plugins_url(); ?>/maestro-game-builder/dist/css/maestro.css">
		<script src="<?php echo plugins_url(); ?>/maestro-game-builder/minigames/phaser.min.js"></script>
        <script src="<?php echo plugins_url(); ?>/maestro-game-builder/minigames/views/Utils.js"></script>
        <script src="<?php echo plugins_url(); ?>/maestro-game-builder/minigames/views/<?php echo $minigame; ?>.js?t=1"></script> 
        <script>
        	MaestroGameBuilder.Options.urlBase = '<?php echo plugins_url(); ?>/maestro-game-builder/';
        	/*
			For iframed and unique minigames, we offer a replay.
			*/
			MaestroGameBuilder.Replay = function (game) {};

			MaestroGameBuilder.Replay.prototype =  {
			    init: function () {

			    },
			    preload: function () {
			    	
			        this.load.image("replay", "<?php echo plugins_url(); ?>/maestro-game-builder/minigames/default_assets/replay/reload-6x-white.png");
			    },
			    create: function () {
			        MaestroGameBuilder.Options.container.className = '';
			        this.replayButton = this.game.add.button(this.game.width/2, this.game.height/2, "replay", this.replay, this);
			        this.replayButton.anchor.setTo(.5);
			    },
			    replay: function () {
			    	MaestroGameBuilder.Options.progress = 0;
			    	this.state.start("<?php echo $minigameData['id']; ?>");
			    }	
			};
			/* Everything here below renders dynamically based on WP options.*/
			MaestroGameBuilder.Loading = function (game) {};

			MaestroGameBuilder.Loading.prototype = {
			    init: function () {
			    },
			    preload: function () {
			    	/* Text Container */
			    	this.game.textData = {};
			    	/* Image Container */
			    	this.game.imageData = {};
			    	this.game.playData = {};
			    	this.game.load.removeAll();
			    	<?php
			    	/* Load assets from Class. */
					$class = 'MGB_'.$minigame;
					$assets = new $class($minigameData);
					?>
					/* Provide Order to the minigames. */
					MaestroGameBuilder.Options.progress = 0;
					MaestroGameBuilder.Options.order = [<?php echo $minigameData['id']; ?>,'Replay'];

					/* Default Index View */
					MaestroGameBuilder.Options.indexState = 'Replay';
			    	MaestroGameBuilder.Options.URL = "<?php echo 'http://beta.searchforharmony.org/'; ?>";
			    	MaestroGameBuilder.Options.gameType = 'minigames_menu';
			    	/* For the menu search, this activates the level in LevelMenu */
			    	MaestroGameBuilder.Options.searchMode = false;
			    	MaestroGameBuilder.Options.pauseIconFound = false;
			    	<?php $start = $minigame; ?>
			    	MaestroGameBuilder.Options.StartLevel = '<?php echo $start; ?>';
			    	MaestroGameBuilder.Options.hasNotes = false;
			    	/* This object stores progress and will also be retrievable from a browser cookie. */
			    	/*MaestroGameBuilder.Options.progress = {
			    		gameComplete: false,
			    		stillPlaying: false,
			    		levels : [
				    		{	
				    			key: "BridgeRunner",
				    			complete: false,
				    			unlocked: true
				    		},
				    		{
				    			key: "SimonSays",
				    			complete: false,
				    			code: "4_6_8_10",
				    			
				    			unlocked: false
				    		},
				    		{
				    			key: "Search",
				    			complete: false,
				    			code: "1_2_3_4",
				    			
				    			unlocked: false
				    		},
				    		{
				    			key: "Gateway",
				    			complete: false,
				    			code: "1_8_15_1",
				    			unlocked: false
				    		},
				    		{
				    			key: "TopDownBridge",
				    			complete: false,
				    			code: "8_10_12_15",
				    			unlocked: false
				    		}
			    		],
			    		nextLevel : {
			    			index: 0
			    		}
			    	};
			    	*/
			    	MaestroGameBuilder.Options.playData = {};
			    	/*
					All Game Assets are loaded here.
			    	*/
					this.load.image('playbutton', MaestroGameBuilder.Options.urlBase+'minigames/default_assets/title/Play.png');
					this.load.image('aboutbutton', MaestroGameBuilder.Options.urlBase+'minigames/default_assets/title/About.png');
					this.load.image('menuButton',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/overview/MenuButton@3x.png');
					this.load.image('layerPause',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/LayerPause@3x.png');
					this.load.image('pauseLayerPlayButton',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/PlayButton@3x.png');
					this.load.image('pauseLayerHelpButton',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/HelpButton@3x.png');
					this.load.image('pauseLayerQuitButton',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/QuitButton@3x.png');
					this.load.image('layerModal',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/ModalMenu@3x.png');
					this.load.image('OK',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/OK@3x.png');

					this.game.load.image('LevelMenuNote_3x',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/overview/Note@3x.png');
			    },
			    create: function () {
			       this.state.start("<?php echo $minigamePost->ID; ?>");
			    }
			};


			var MGBGame;
			window.onload = function () {
				/* Specific to inline, we use the body tag. */
        		var body = document.getElementsByTagName('body')[0];
				var gameInner = document.createElement('div');
				gameInner.id = 'MGBGameDiv';
				var gameShell = document.createElement('div');
				gameShell.id = 'MGBGameWrap';
				gameShell.appendChild(gameInner);
				body.appendChild(gameShell);
				MaestroGameBuilder.Options.container = document.getElementById('MGBGameDiv');

				MaestroGameBuilder.Options.baseWidth = MaestroGameBuilder.Options.baseWidth*.6;
				MaestroGameBuilder.Options.height = MaestroGameBuilder.Options.height*.6;

				MGBGame = new Phaser.Game(MaestroGameBuilder.Options.baseWidth, MaestroGameBuilder.Options.baseHeight, Phaser.AUTO, 'MGBGameDiv',null,true);	
				MGBGame.state.add("Boot", MaestroGameBuilder.Boot);
				MGBGame.state.add("Loading", MaestroGameBuilder.Loading);
				MGBGame.state.add('Replay',MaestroGameBuilder.Replay);

				MGBGame.state.add("<?php echo $minigamePost->ID; ?>", MaestroGameBuilder.<?php echo $minigame; ?>);
				MGBGame.state.start("Boot");
			}
		</script>
		<?php
		$script = ob_get_contents();
		ob_end_clean();
		return $script;
	}

	public function game_front_inline() {
		global $gameData;
		$count = count($gameData['minigame_order']);
		if($count<2){
			$replayMinigame = $gameData['minigame_order'][0]['id'];
		}
		ob_start();
		?>
        <script type="text/javascript">
        	MaestroGameBuilder.Options.urlBase = '<?php echo plugins_url(); ?>/maestro-game-builder/';
        	/*
			For iframed and unique minigames, we offer a replay.
			*/
			MaestroGameBuilder.Replay = function (game) {};

			MaestroGameBuilder.Replay.prototype =  {
			    init: function () {

			    },
			    preload: function () {
			        this.load.image("replay", "<?php echo plugins_url(); ?>/maestro-game-builder/minigames/default_assets/replay/reload-6x-white.png");
			    },
			    create: function () {
			        MaestroGameBuilder.Options.container.className = '';
			        this.replayButton = this.game.add.button(this.game.width/2, this.game.height/2, "replay", this.replay, this);
			        this.replayButton.anchor.setTo(.5);
			        this.resize(this.game.width,this.game.height);
			    },
			    resize: function (width, height) {
			    	MGBUtils.scaleSprite(this.replayButton, width, height / 3, 50, 1);
					this.replayButton.x = width/2;
					this.replayButton.y = height/2;
			    },
			    replay: function () {
			    	/* Check for title screen first. */
			    	if(MaestroGameBuilder.Options.order.length>0){
			    		MaestroGameBuilder.Options.progress = 0;
			    		this.state.start(MaestroGameBuilder.Options.order[0]);
			    	}else{
			    		/* Default to Level Menu */
			    		this.state.start("<?php echo $replayMinigame; ?>");
			    	}
			    }	
			};
			/* Everything here below renders dynamically based on WP options.*/
			MaestroGameBuilder.Loading = function (game) {};

			MaestroGameBuilder.Loading.prototype = {
			    init: function () {
			    },
			    preload: function () {
			    	/* Text Container */
			    	this.game.textData = {};
			    	/* Image Container */
			    	this.game.imageData = {};
			    	this.game.playData = {};
			    	this.game.load.removeAll();
			    	<?php
			    	/* Load assets from Class. */
			    	foreach($gameData['minigame_order'] as $minigameData){
			    		$gameData['minigames'][$minigameData['id']]['custom']['minigame_data']['base'] = plugins_url();
			    		$gameData['minigames'][$minigameData['id']]['custom']['minigame_data']['id'] = $minigameData['id'];
			    		$gameData['minigames'][$minigameData['id']]['custom']['minigame_data']['textData']['help']['title'] = get_the_title($minigameData['id']);
			    		$class = 'MGB_'.$gameData['minigames'][$minigameData['id']]['script'];
						$assets = new $class($gameData['minigames'][$minigameData['id']]['custom']['minigame_data']);
			    	}
					?>
					/* Provide Order to the minigames. */
					MaestroGameBuilder.Options.progress = 0;
					MaestroGameBuilder.Options.order = [];
					MaestroGameBuilder.Options.indexState = 'Replay';
					<?php
					if($gameData['type']=='minigame') {
						foreach($gameData['minigame_order'] as $minigame) { ?>
							MaestroGameBuilder.Options.order.push(<?php echo $minigame['id']; ?>);
					<?php	
						}
					}
					?>
					/* Replay ends all minigames if there is not a title. */
					MaestroGameBuilder.Options.order.push("Replay");
					/* For use in the Sharing Tools. */
			    	MaestroGameBuilder.Options.URL = "<?php echo get_permalink(); ?>";

			    	MaestroGameBuilder.Options.gameType = '<?php echo $gameData['type']; ?>';
			    	<?php if($gameData['type']=='minigames_menu') : ?>
			    	/* For the menu search, this activates the level in LevelMenu */

			    	MaestroGameBuilder.Options.searchMode = false;
			    	MaestroGameBuilder.Options.pauseIconFound = false;
			    	<?php endif; ?>
			    	MaestroGameBuilder.Options.StartLevel = '<?php echo $start; ?>';
			    	/* TODO Specific to SFH */
			    	MaestroGameBuilder.Options.hasNotes = false;

			    	/* This object stores progress and will also be retrievable from a browser cookie. */
			    	/*
 			    	MaestroGameBuilder.Options.progress = {
			    		gameComplete: false,
			    		stillPlaying: false,
			    		levels : [
				    		{	
				    			key: "BridgeRunner",
				    			complete: false,
				    			unlocked: true
				    		},
				    		{
				    			key: "SimonSays",
				    			complete: false,
				    			code: "4_6_8_10",
				    			
				    			unlocked: false
				    		},
				    		{
				    			key: "Search",
				    			complete: false,
				    			code: "1_2_3_4",
				    			
				    			unlocked: false
				    		},
				    		{
				    			key: "Gateway",
				    			complete: false,
				    			code: "1_8_15_1",
				    			unlocked: false
				    		},
				    		{
				    			key: "TopDownBridge",
				    			complete: false,
				    			code: "8_10_12_15",
				    			unlocked: false
				    		}
			    		],
			    		nextLevel : {
			    			index: 0
			    		}
			    	}; 
			    	*/
			    	MaestroGameBuilder.Options.playData = {};
			    	/*
					All Game Assets are loaded here.
			    	*/
					this.load.image('playbutton', MaestroGameBuilder.Options.urlBase+'minigames/default_assets/title/Play.png');
					this.load.image('aboutbutton', MaestroGameBuilder.Options.urlBase+'minigames/default_assets/title/About.png');
					<?php
					if( isset($gameData['game_data']['imageData']['menuButton'])) : ?>
						this.load.image('menuButton', '<?php echo $gameData['game_data']['imageData']['menuButton']['url']; ?>');
					<?php else : ?>
						this.load.image('menuButton',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/overview/MenuButton@3x.png');
					<?php endif; ?>
					this.load.image('layerPause',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/LayerPause@3x.png');
					this.load.image('pauseLayerPlayButton',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/PlayButton@3x.png');
					this.load.image('pauseLayerHelpButton',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/HelpButton@3x.png');
					this.load.image('pauseLayerQuitButton',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/QuitButton@3x.png');
					this.load.image('layerModal',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/ModalMenu@3x.png');
					this.load.image('OK',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/general/OK@3x.png');
					this.game.load.image('LevelMenuNote_3x',MaestroGameBuilder.Options.urlBase+'minigames/default_assets/overview/Note@3x.png');
			    },
			    create: function () {
			       this.state.start(MaestroGameBuilder.Options.order[0]);
			    }
			};


			var MGBGame;
			window.onload = function () {
				/* Specific to inline, we use the body tag. */
        		//var body = document.getElementsByTagName('body')[0];
				//var gameInner = document.createElement('div');
				//gameInner.id = 'MGBGameDiv';
				//var gameShell = document.createElement('div');
				//gameShell.id = 'MGBGameWrap';
				//gameShell.appendChild(gameInner);
				//body.appendChild(gameShell);
				MaestroGameBuilder.Options.container = document.getElementById('MGBGameDiv');

				MaestroGameBuilder.Options.baseWidth = MaestroGameBuilder.Options.baseWidth*.6;
				MaestroGameBuilder.Options.height = MaestroGameBuilder.Options.height*.6;

				MGBGame = new Phaser.Game(MaestroGameBuilder.Options.baseWidth, MaestroGameBuilder.Options.baseHeight, Phaser.AUTO, 'MGBGameDiv',null,true);	
				MGBGame.state.add("Boot", MaestroGameBuilder.Boot);
				MGBGame.state.add("Loading", MaestroGameBuilder.Loading);
				MGBGame.state.add('Replay',MaestroGameBuilder.Replay);
				<?php foreach($gameData['minigame_order'] as $minigameData) : ?>
				MGBGame.state.add("<?php echo $minigameData['id']; ?>", MaestroGameBuilder.<?php echo $gameData['minigames'][$minigameData['id']]['script']; ?>);
				<?php endforeach; ?>
				MGBGame.state.start("Boot");
			}
		</script>
		<?php
		$script = ob_get_contents();
		ob_end_clean();
		return $script;
	}

	public function game_filter_the_content($content)
	{
	    if (is_singular('mgb_game') && in_the_loop()) {
	        // change stuff
	        $content = 
	        '<div id="MGBGameWrap"><div id="MGBGameDiv" class="fade"></div>
        	<!--<div id="MGBGameMeta">
				<span class="MGBGameMetaTitle">The Search for Harmony</span>
				<span class="MGBGameMetaAuthor"><a href="#">Stone Soup Productions, Inc.</a></span>
        	</div>-->
        	</div>'.$content;
	    }

	    return $content;
	}

}



new MaestroGameBuilder();