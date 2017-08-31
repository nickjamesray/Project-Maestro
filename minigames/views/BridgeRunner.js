/*
Game Type: Bridge Runner
Description: Collect Objects with a constant moving sprite. Mind the gaps.
*/
MaestroGameBuilder.BridgeRunner = function (game) {};

MaestroGameBuilder.BridgeRunner.prototype = {
	preload: function () {
		/* TODO create multiple instances in one game by flushing cache and loading from object if minigame ID is different. */
	},
	create: function () {
		MaestroGameBuilder.Options.container.style.backgroundImage = "none";

		/** Sprite **/
		/*
		- Defaults are set here.
		*/
		this.player;
		// How fast the player runs
		this.player_speed = 450;
		// How fast the player goes up when they jump
		this.initial_jump_velocity = 450;
		// How fast the player falls down when they are done jumping
		this.initial_jump_pullback = 375;
		/** End Sprite **/

		this.background0;
		this.background1;
		this.endLight;
		this.nDame;
		this.eHouse;
		this.dLane;

		this.cursors;
		this.jumpButton;

		this.collectible; 
		this.camera_offset = 150;

		this.points;
		this.ui_group;
		this.menuButton;

		this.collect_group;

		this.sign0;
		this.sign1;
		this.sign2;
		this.short0;
		this.short1;
		this.med;
		this.long;
		this.gap = 350; //gap between floor pieces

		this.short1a;
		this.short1b;
		this.short1c;
		this.short1d;
		this.short1e;

		this.short0a;
		this.short0b;
		this.short0c;
		this.short0d;


		this.wrapping = true;
		this.stopped = false;



		this.block_group;
		this.blockCollide;
		this.block_running_x = this.game_width + 200;
		this.dubble_last = false;
		this.block;

		this.killButton;
		this.reviveButton;

		this.obstacle_timer;
		this.obstacle_delay = 20;       
		
		//add the background
		this.game.stage.backgroundColor = '#d3d3d3';
		 
		/* TODO Calulation to determine proportional height/width. 5130w 600h =  */
		var boundsWidth = (this.game.height*5130)/600;
		this.game.world.setBounds(0,0,5130, 600);    
	  
		this.points = 0;
	 
	   
		this.wrapping = true;
		this.stopped = false; 
		
		/** Base Layer **/
		/* 
		The image is tagged as 'bkg1' and this loads it in.
		The anchor is set to the bottom-left so it aligns with the floor. 
		*/
		this.backgroundTile = this.game.add.tileSprite(0,600,5110,600, 'bkg1_'+this.game.state.current);
		this.backgroundTile.anchor.setTo(0,1);
		/** End Base Layer **/

		/* These fields are not yet modified through the CMS. */
		if(typeof this.game.playData[this.game.state.current].customBG === 'undefined'||this.game.playData[this.game.state.current].customBG == false){
			this.eHouse = this.game.add.sprite(200,0,'eHouse_'+this.game.state.current);
			this.nDame = this.game.add.sprite(0,0,'nDame_'+this.game.state.current);
			this.dLane = this.game.add.sprite(0,0,'dLane_'+this.game.state.current);
		}

		
		
		this.endLight = this.game.add.sprite(this.game.world.width, 0, 'endLight_'+this.game.state.current);   
		this.endLight.reset(this.game.world.width-this.endLight.width, 0);
		
			
		this.menuButton = this.game.add.button(10, 10, "menuButton", MGBUtils.pauseGame, this);
		this.menuButton.fixedToCamera = true;
		
		/** Collectibles **/
		/*
		Each collectible shows up in two spots.
		First, they are added to ui_group, which is what displays in the top right.
		Later they will be individually placed in the level for the player to collect.
		*/
		this.ui_group = this.game.add.group(); 
		/* So your progress doesn't also scroll */   
		this.ui_group.fixedToCamera = true;
		/* 
		This code runs whenever custom images are used.
		It detects how many there are (up to 4) and adds them to the display.
		*/
		if(typeof this.game.playData[this.game.state.current].customCollectibles !== 'undefined'){
			for(var i = 0; i < this.game.playData[this.game.state.current].customCollectibles; i++){
				this.ui_group.create(this.game.camera.width - (50+(i*50)), 10, 'collectible_'+this.game.state.current+'_'+i);
				this.ui_group.children[i].alpha = 0.2;
			}			
		}else{
			for(var i = 0; i < 4; i++){
				this.ui_group.create(this.game.camera.width - (50+(i*50)), 10, 'uiCollectible_'+this.game.state.current);
				this.ui_group.children[i].alpha = 0.2;
			}
		}
		/** End Collectibles **/
		
		/** Sprite **/
		/*
		- The image created in WordPress is loaded as BridgeRunnerPlayer
		- Several poses are added, and the default (run) is triggered
		*/
		this.player = this.game.add.sprite(0, 200, 'BridgeRunnerPlayer_'+this.game.state.current,0);
		/* 
		- Run is the default, and has three poses (0, 1 and 2)
		- In code, 0 often represents 'first' where 1 might be used in normal language.
		*/
		this.player.animations.add('run', [0, 1, 2], 6, true);
		// Jump is the final pose (3)
		this.player.animations.add('jump', [3], 6, true);
		this.player.animations.play('run');
		// The sprite is sized so it is not a giant or too small relative to the level.
		this.player.scale.setTo(0.2,0.2);
	  	/** End Sprite **/
		this.PlaceGround();
		
		this.cursors = this.game.input.keyboard.createCursorKeys();
		this.jumpButton = this.game.input.keyboard.addKey(Phaser.Keyboard.SPACEBAR);
		this.killButton = this.game.input.keyboard.addKey(Phaser.Keyboard.ONE);
		this.reviveButton = this.game.input.keyboard.addKey(Phaser.Keyboard.TWO);
		
		/** Sprite **/
		/*
		The game's camera is set to the sprite so it can't run off-screen.
		*/
		this.game.camera.focusOnXY(this.player.x + this.camera_offset, this.player.y);
		/** End Sprite **/
		
		this.PlaceBlocks();
		this.PlaceCollectibles();
		
		/* Force resize to trigger */
		this.resize(this.game.width,this.game.height);

		/** Help/Intro Text **/
		/*
		this.game.textData is where Help/Intro Text is stored.
		MGBUtils.launchModal() triggers the popup.
		*/
		MGBUtils.launchModal(this.game.textData[this.game.state.current].help.title,this.game.textData[this.game.state.current].help.text,this);
		/** End Help/Intro Text **/
	},
	resize: function (width, height) {
		MGBUtils.scaleSprite(this.menuButton, width, height / 3, 50, .7);
		this.menuButton.x = 10;
		this.menuButton.y = 10;

		//this.ui_group.x = 0;
		//this.ui_group.y = 0;

		/*
		Resize happens immediately, so we wait to remove the fade/curtain 
		until all the elements have jumped into place.
		*/
		MaestroGameBuilder.Options.container.className = '';
	},
	/** Collectibles **/
	/*
	Using the same images as the progress group,
	each collectible has a specific position based on other elements.
	*/
	PlaceCollectibles: function(){
		this.collect_group = this.game.add.group();
		this.collect_group.enableBody = true;
		this.collect_group.physicalBodyType = Phaser.Physics.ARCADE;
		/*
		X and Y are coordinates for the collectibles, which are calculated 
		based on the position and size of other elements.
		*/
		var locations = [
			{
				x: this.block_group.children[1].position.x + (this.block_group.children[1].body.width/4),
				y: this.block_group.children[1].position.y - (this.block_group.children[1].body.height*1.75)
			},
			{
				x: this.short1c.position.x,
				y: this.short1c.position.y-(this.short1c.body.height*1.55)
			},
			{
				x: this.sign1.position.x+(this.sign1.body.width*0.75),
				y: this.sign1.position.y-(this.sign1.body.height*1.25)
			},
			{
				x: this.game.world.width - 80,
				y: this.short1e.position.y - (this.short1.body.height*1.5)
			}
		];
		if(typeof this.game.playData[this.game.state.current].customCollectibles !== 'undefined'){
			for(var i = 0; i < this.game.playData[this.game.state.current].customCollectibles; i++){
				var id = 'collectible_'+this.game.state.current+'_'+i;
				this.collect_group.create(0,0,id);
				this.collect_group.children[i].reset(locations[i].x, locations[i].y);
				this.collect_group.children[i].collectID = i;
			}
		}else{
			for(var i = 0; i < 4; i++){
				var id = 'collectible_'+this.game.state.current;
				this.collect_group.create(0,0,id);
				this.collect_group.children[i].reset(locations[i].x, locations[i].y);
				this.collect_group.children[i].collectID = i;
			}
		}
	},
	/** End Collectibles **/
	/** Platforms **/
	PlaceGround: function(){
		// Check for custom images.
		if(typeof this.game.playData[this.game.state.current].customPlatforms !== 'undefined'){
			/* If custom platforms, only one size is used. */
			var id = 'platform_'+this.game.state.current+'_';
			/* Programmatically assign sprites based on number of unique platforms */
			var labelArray = [
			'sign0',
			'sign1',
			'sign2',
			'short1',
			'med',
			'long',
			'short1a',
			'short1b',
			'short1c',
			'short1d',
			'short1e',
			'short0a',
			'short0b',
			'short0c',
			'short0d'
			];
			var platformCycle = 0;
			/* 
			This code cycles through all the platforms uploaded and adds them to the game.
			Not completely random, but also not in order from left to right.
			*/
			for(var i = 0; i < labelArray.length; i++) {
				this[ labelArray[ i ] ] = this.game.add.sprite(0,0,id+platformCycle);
				platformCycle++;
				/* Reset our toggle. */
				if( platformCycle >= this.game.playData[this.game.state.current].customPlatforms ) {
					platformCycle = 0;
				}
			}

		}else{
			/* Default artwork is attached if there is no custom artwork. */
			this.sign0 = this.game.add.sprite(0, 0, 'sign0_'+this.game.state.current);
			this.sign1 = this.game.add.sprite(0, 0, 'sign1_'+this.game.state.current);
			this.sign2 = this.game.add.sprite(0,0, 'sign2_'+this.game.state.current);
			this.short1 = this.game.add.sprite(0,0,'short1_'+this.game.state.current);		
			this.med = this.game.add.sprite(0,0,'med_'+this.game.state.current);
			this.long = this.game.add.sprite(0,0,'long_'+this.game.state.current);
			this.short1a = this.game.add.sprite(0,0,'short1_'+this.game.state.current);
			this.short1b = this.game.add.sprite(0,0,'short1_'+this.game.state.current);
			this.short1c = this.game.add.sprite(0,0,'short1_'+this.game.state.current);
			this.short1d = this.game.add.sprite(0,0,'short1_'+this.game.state.current);
			this.short1e = this.game.add.sprite(0,0,'short1_'+this.game.state.current);
			this.short0a = this.game.add.sprite(0,0,'short0_'+this.game.state.current);
			this.short0b = this.game.add.sprite(0,0,'short0_'+this.game.state.current);
			this.short0c = this.game.add.sprite(0,0,'short0_'+this.game.state.current);
			this.short0d = this.game.add.sprite(0,0,'short0_'+this.game.state.current);			
		}
		
		/** Sprite **/
		/*
		- In Phaser.js this code allows different objects to bump into each other
		- For our purposes we want the player to impact the various obstacles and platforms
		  so they must jump and also will not fall through the floor!
		*/
		this.game.physics.arcade.enable([this.player, this.sign0, this.sign1, this.sign2, this.short1, this.med, this.long, this.short1a, this.short0a, this.short1b,this.short0b, this.short1c,this.short1d, this.short0c, this.short0d, this.short1e]);
		/** End Sprite **/
		
		/*
		body.enable means, unlike a background, these objects affect gameplay.
		In this case, the objects make up the ground.
		*/
		this.sign0.body.enable = true;
		this.sign1.body.enable = true;
		this.sign2.body.enable = true;
		
		this.short1.body.enable = true;
		this.med.body.enable = true;
		this.long.body.enable = true;
		
		this.short1a.body.enable = true; 
		this.short1b.body.enable = true; 
		this.short1c.body.enable = true;
		this.short1d.body.enable = true;
		this.short1e.body.enable = true;
		
		this.short0a.body.enable = true;
		this.short0b.body.enable = true;   
		this.short0c.body.enable = true;
		this.short0d.body.enable = true;
		
		this.sign0.reset(0,this.game.world.height - 140);
		this.sign0.body.immovable = true;
		
		this.long.body.immovable = true;    
		this.long.reset(this.sign0.body.width, this.game.world.height - 140);

		this.short1.body.immovable = true;   
		this.short1.reset(this.long.position.x + this.long.body.width, this.game.world.height - 140);
		
		this.sign2.body.immovable = true;    
		this.sign2.reset(this.short1.position.x + this.short1.body.width + this.gap, this.game.world.height - 140);
		
		this.short0a.body.immovable = true;
		this.short0a.reset(this.sign2.position.x+this.sign2.body.width, this.game.world.height -140);
		
		
		this.short1a.body.immovable = true;
		this.short1a.reset(this.short0a.position.x+this.short0a.body.width, this.game.world.height-140);
		
		this.short1b.body.immovable = true;
		this.short1b.reset(this.short1a.position.x+this.short1a.body.width, this.game.world.height-140);
		
		this.short0b.body.immovable = true;
		this.short0b.reset(this.short1b.position.x+this.short1b.body.width, this.game.world.height-140);
		
		this.short1c.body.immovable = true;
		this.short1c.reset(this.short0b.position.x+this.short0b.body.width, this.game.world.height-140);
		//next gap
		this.short1d.body.immovable = true;
		this.short1d.reset(this.short1c.position.x+this.short1c.body.width+this.gap, this.game.world.height-140);
		//gap
		this.sign1.body.immovable = true;
		this.sign1.reset(this.short1d.position.x+this.short1d.body.width+this.gap, this.game.world.height-140);
		
		
		//short0-short0-med-gap-short1
		this.short0c.body.immovable = true;
		this.short0c.reset(this.sign1.position.x+this.sign1.body.width, this.game.world.height-140);
		
		this.short0d.body.immovable = true;
		this.short0d.reset(this.short0c.position.x+this.short0c.body.width, this.game.world.height-140);
		
		this.med.body.immovable = true;
		this.med.reset(this.short0d.position.x+this.short0d.body.width,
				 this.game.world.height-140);
		
		//gap
		this.short1e.body.immovable = true;
		this.short1e.reset(this.med.position.x+this.med.body.width+this.gap,
					 this.game.world.height-140);
		

		if(typeof this.game.playData[this.game.state.current].customBG === 'undefined'||this.game.playData[this.game.state.current].customBG == false){
			/* If there is a custom background we ignore the middle layer. */
			this.nDame.reset(this.short0a.position.x,0);
			this.dLane.reset(this.sign1.position.x + 10, 0);
		}
		/** End Platforms **/
		/** Sprite **/
		/*
		- body.gravity gives the player weight so when they go up, they come down.
		- the anchor is also set to the bottom center, 
		  which in human terms would be between the feet, 
		  so the player is always running on the floor and not 'in' the floor.
		*/
		this.player.body.gravity.y = 800;
		this.player.anchor.setTo(0.5, 1);
		/** End Sprite **/
	},
	/** Obstacle **/
	PlaceBlocks: function(){    
		// Make blocks    
		this.block_group = this.game.add.group();     
		
		/* Obstacles are given mass so they affect the player if touched. */
		this.block_group.enableBody = true;
		this.block_group.physicalBodyType = Phaser.Physics.ARCADE;
	  
		/*
		Eight blocks are added to the world, and positioned specifically below.
		*/
		for(var i = 0; i < 9; i++){
			this.block_group.create(100*i, this.game.world.height-200, 'block_'+this.game.state.current);  
			this.block_group.children[i].body.immovable = true;
		}
		
		//bottom right first block
		this.block_group.children[0].reset(this.short1.position.x+this.short1.body.width - this.block_group.children[0].body.width, this.game.world.height-200);
		
		//top right first block
		this.block_group.children[1].reset(this.block_group.children[0].position.x, this.game.world.height-264);
		//left lower first block
		this.block_group.children[2].reset(this.block_group.children[0].position.x-this.block_group.children[0].body.width, this.game.world.height-200);
		
		this.block_group.children[3].reset(this.short1b.position.x+this.block_group.children[3].body.width, this.game.world.height-200);
		
		this.block_group.children[4].reset(this.sign1.position.x+this.sign1.body.width-this.block_group.children[4].body.width, this.game.world.height-200);
		
		this.block_group.children[5].reset(this.med.position.x-this.med.body.width/8, this.game.world.height-200);
		this.block_group.children[6].reset(this.med.position.x, this.game.world.height-200);
		this.block_group.children[8].reset(this.med.position.x+this.med.body.width/2, this.game.world.height-200);
	},
	/** End Obstacle **/
	/** Collectibles **/
	/*
	When the player touches a collectible, this triggers.
	The score is increased, the collectible disappears from the main level, and the top right version becomes fully visible.
	*/
	collectCollide: function(obj1, obj2){     
		obj2.kill();
		this.ui_group.children[obj2.collectID].alpha = 1;
		this.points++;
		/* Check victory */
		if(typeof this.game.playData[this.game.state.current].customCollectibles !== 'undefined') {
			var victory = this.game.playData[this.game.state.current].customCollectibles;
		}else{
			var victory = 4;
		}
		if(this.points >= victory) {
			if(MaestroGameBuilder.Options.progress.stillPlaying==false&&MaestroGameBuilder.Options.progress.levels[MaestroGameBuilder.Options.progress.nextLevel.index].complete!=true){
				MaestroGameBuilder.Options.progress.levels[MaestroGameBuilder.Options.progress.nextLevel.index].complete = true;
				MaestroGameBuilder.Options.progress.nextLevel.index++;
			}
			/** Victory Text **/
			/*
			- this.game.textData stores the Victory Text
			- MGBUtils.launchModal triggers the modal
			- After dismissing the modal, a fade class is applied to an
			  html element overtop the game, dimming it to black
			- After a short delay (to allow the fade to take effect),
			  self.state.start sends us back to the main menu 
			  (or replay if it is a single minigame)
			- var self = this makes a reference to the game (as opposed to the modal),
			  which makes it more clear which we want to control.
			*/
			var self = this;
			MGBUtils.launchModal(this.game.textData[this.game.state.current].success.title,this.game.textData[this.game.state.current].success.text,this,function(){
				/* TODO victory text then back to home*/
				MaestroGameBuilder.Options.container.className = 'fade';
				/* Time parameter matches the CSS transition length so fade completes */
				window.setTimeout(function(){
					/* Controlled by Plugin */
					self.game.paused = false;
					/* Proceeds to next level */
					MaestroGameBuilder.Options.progress++;
					if( MaestroGameBuilder.Options.gameType = 'minigames' ) {
						console.log(MaestroGameBuilder.Options.progress);
						self.state.start(MaestroGameBuilder.Options.order[MaestroGameBuilder.Options.progress]);
					}else{
						self.state.start(MaestroGameBuilder.Options.indexState);
					}
					
					
					//MGBUtils.resumeGame(self);
				},300);
			});
			/** End Victory Text **/
		}
	},
	/** End Collectibles **/
	/** Sprite **/
	// Update runs constantly during gameplay
	update: function(){
		// Here we check to see if the player has collided with a platform or obstacle
		this.game.physics.arcade.collide(this.player,[this.sign0, this.long, this.short1, this.sign1, this.sign2, this.short1a, this.short0a, this.short1b,this.short0b, this.short1c,this.short1d, this.short0c, this.short0d, this.med, this.short1e, this.block_group]);
		// Here we check to see if the player has collided with a collectible
		this.game.physics.arcade.collide(this.player, this.collect_group, this.collectCollide, null, this);
	   	// If the player is lower than the world's height, they fell in a pit and must restart.
		if(this.player.position.y > this.game.world.height + this.player.body.height){
			this.playerDead();
		}
		// If the player is alive
		if(this.player.alive){  
			// The speed that the player runs through the level, set previously.      
			this.player.body.velocity.x = this.player_speed;
			// We check for the jump button. If it is pressed, we change to the jump pose.
			if((this.game.input.pointer1.isDown || this.jumpButton.isDown) && (this.player.body.onFloor() || this.player.body.touching.down)){
				this.player.body.velocity.y = -this.initial_jump_velocity;
				this.player.animations.play('jump');
			}else if(!this.player.body.onFloor() && !this.player.body.touching.down){
				// When the player is not jumping we control the downward motion.
				if(this.player.y <= 150){
					this.player.body.velocity.y = +this.initial_jump_pullback;
				}
			}else if(this.player.body.onFloor() || this.player.body.touching.down){
				// Player is on the ground so back to the running pose.
				this.player.animations.play('run');
				
			}
			// 
			if( (this.jumpButton.isUp && this.game.input.pointer1.isUp ) && !this.player.body.touching.down && this.player.y<= 500){
				
				this.player.body.velocity.y = +this.initial_jump_pullback;
			}
			if(!this.wrapping && this.player.x < this.game.width){
				this.wrapping = true;
			}else if(this.player.x >= this.game.width){
				this.wrapping = false;
			}
			// This control helps us repeat the level if the player missed an object.
			this.game.world.wrap(this.player, 0, true, true, false);
			// This control keeps the camera following the player
			this.game.camera.focusOnXY(this.player.x+this.camera_offset, this.player.y);
		}       
	},
	playerDead: function(){
		/*
		- Reset the player's position and start the game.
		*/
		this.player.alive = false;
		this.player.body.velocity.x = 0;
		
		this.player.position.x = 100;
		this.player.position.y = this.game.world.centerY;
		
		this.game.time.events.add(Phaser.Timer.SECOND * 1, function(){
		   this.player.alive = true;
		}, this);
		
	}
	/** End Sprite **/
};