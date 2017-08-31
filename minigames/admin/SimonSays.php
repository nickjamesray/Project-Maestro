<?php

class MGB_SimonSays {

	/* Loads default values. */
	public $assets = array(
		'baseLayer' => array(
			'name'	=> 'bkg1',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/bkgSingle.png'
			),
		'structure1' => array(
			'name' 	=> 'nDame',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/nDame.png'
			),
		'structure2' => array(
			'name'	=> 'eHouse',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/eHouse.png'
			),
		'structure3' => array(
			'name'	=> 'dLane',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/dLane.png'
			),
		'avatar' => array(
			'name'  => 'BridgeRunnerPlayer',
			'type'	=> 'spritesheet',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/AvatarSprite@3x.png',
			'frameHeight' => 504,
			'frameWidth'  => 325
			),
		'collectible1' => array(
			'name'	=> 'collectible',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/worldNote.png'
			),
		/*Need to add more collectibles for variety*/
		'collectibleList' => array(
			'name'	=> 'uiCollectible',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/uiNote.png'
			),
		'obstacle1' => array(
			'name'	=> 'block',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/block.png'
			),
		/* TODO Randomize obstacles */
		'endFade'	=> array(
			'name'	=> 'endLight',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/endLight.png'
			),
		'location1'	=> array(
			'name'	=> 'sign0',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/sign0.png'
			),
		'location2' => array(
			'name'	=> 'sign1',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/sign1.png'
			),
		'location3'	=> array(
			'name'	=> 'sign2',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/sign2.png'
			),
		'shortFloor1' => array(
			'name'	=> 'short0',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/short0.png'
			),
		'shortFloor2' => array(
			'name'	=> 'short1',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/short1.png'
			),
		'mediumFloor' => array(
			'name'	=> 'med',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/med.png'
			),
		'longFloor' => array(
			'name'	=> 'long',
			'type'	=> 'image',
			'source'=> '/maestro-game-builder/minigames/default_assets/bridge-runner/long.png'
			)
		);
	public $textData = array(
		'help' => array(
			'title' => 'The Arrival',
			'text' => '<p>George Bridgetower\'s life and career took him across Europe - from Esterhazy Castle in Hungary, where he served the king as a child and learned from the composer Josef Haydn, to his premiere public performance at the Drury Lane Theatre in London, to Vienna where he performed with Ludwig van Beethoven. Help Maestro navigate his way through landmarks from Bridgetower\'s life and collect pieces of the bridge along the way!</p><p>Collect all 4 musical notes. Use the spacebar to jump.</p>'
			),
		'success' => array(
			'title' => 'You Did it!',
			'text' => '<p>You did it! Maestro now has the notes he needs to start rebuilding the bridge.</p>',
			'button' => 'OK'
			)
		);
	/* This will populate with filtered labels */
	public $instanceImageData = array();

	function __construct($data=null){
		/* False runs dev mode, e.g. flushing cache. */
		return $this->render($data,false);
	}

	public function render($data=null,$production=false){
		if($data==null){
			return;
		}
		ob_start();
		?>
		/* Simon Says Class */
		<?php
		/* Text Assets */
		foreach ( $this->textData as $event=>$atts ){
			foreach ( $atts as $customKey => $attribute ) {
				if ( isset( $data['textData'][ $event ][ $customKey ] ) && $data['textData'][ $event ][ $customKey ] !== null && trim($data['textData'][ $event ][ $customKey ]) !== '' && strip_tags(trim($data['textData'][ $event ][ $customKey ])) != '' ){
					$this->textData[ $event ][ $customKey ] = $data['textData'][ $event ][ $customKey ];
				}
			}
		}

		$text = json_encode($this->textData);
		echo 'this.game.textData["'.$data['id'].'"] = '.$text.';';  
		echo 'this.game.playData["'.$data['id'].'"] = {};';
		/* Visual Assets */
		foreach ( $this->assets as $customKey=>$atts ){
			/* Check for WordPress Value */
			if ( isset( $data['imageData'][ $customKey ] ) && $data['imageData'][ $customKey ] !== null ){
				$atts['source'] = $data['imageData'][ $customKey ]['url'].'?token='.rand(0, 2000);
				/* Hide buildings for custom layering. */
				if($customKey=='baseLayer'){
					echo 'this.game.playData["'.$data['id'].'"].customBG = true;';
				}
			}else{
				/* Prepend base URL */
				$atts['source'] = $data['base'].$atts['source'];
			}
			$atts['name'] = $atts['name'].'_'.$data['id'];
			$this->instanceImageData[$customKey.'_'.$data['id']] = $atts;
			/* Apply Code */
			switch($atts['type']){
				case 'spritesheet':
				echo 'this.game.load.'.$atts['type'].'("'.$atts['name'].'", "'.$atts['source'].'",'.$atts['frameWidth'].','.$atts['frameHeight'].');'."\n";
				break;
				default:
				/* Image */
				echo 'this.game.load.'.$atts['type'].'("'.$atts['name'].'", "'.$atts['source'].'");'."\n";
				break;
			}
			
		}
		$images = json_encode($this->instanceImageData);
		echo 'this.game.imageData["'.$data['id'].'"] = '.$images.';';
		?>
		<?php
		$script = ob_get_contents();
		ob_end_flush();
		return $script;
	}
}