PlaySearch.LevelMenu = function (game) {};

PlaySearch.LevelMenu.prototype = {
    create: function () {
    	//
    	PlaySearch.Options.container.style.backgroundImage = "url('/full-game/assets/testbgmain.png')";
       // this.background = this.add.image(0, 0, "background");
       // this.background.anchor.setTo(0.5);
       // this.background.height = this.game.height;
       // this.background.width = this.game.width;
		
        //this.title = this.game.add.image(this.world.centerX, this.world.centerY - this.game.height / 3, "gametitle");
        //this.title.anchor.setTo(0.5);
        //PSUtils.scaleSprite(this.title, this.game.width, this.game.height / 3, 50, 1);
			
        this.playButton = this.game.add.button(this.world.centerX, this.world.centerY, "playbutton", this.playTheGame, this);
        this.playButton.anchor.setTo(0.5);
        this.playButton.frame = 0;
        this.playButton.clicked = false;
        PSUtils.scaleSprite(this.playButton, this.game.width, this.game.height / 3, 50, 1);

        this.aboutButton = this.game.add.button(this.game.width, this.game.height, "aboutbutton", this.playTheGame, this);
        this.aboutButton.anchor.setTo(1);
        this.aboutButton.frame = 0;
        PSUtils.scaleSprite(this.aboutButton, this.game.width, this.game.height / 3, 50, 1);

        // this.infoButton = this.game.add.button(this.world.centerX - TheGame.Params.iconSize / 2 , this.world.centerY + this.game.height / 3, "settings", this.viewGameHelp, this);
        // this.infoButton.anchor.setTo(0.5);
        // this.infoButton.frame = 4;
        // this.infoButton.clicked = false;
        // PSUtils.scaleSprite(this.infoButton, this.game.width, this.game.height / 3, 50, 0.5);
        // this.infoButton.x = this.world.centerX - this.infoButton.width / 2;
		
        // this.audioButton = this.game.add.button(this.world.centerX + TheGame.Params.iconSize / 2 , this.world.centerY + this.game.height / 3, "settings", this.setAudio, this);
        // this.audioButton.anchor.setTo(0.5);
        // this.audioButton.frame = 2;
        // this.audioButton.clicked = false;
        // PSUtils.scaleSprite(this.audioButton, this.game.width, this.game.height / 3, 50, 0.5);
        // this.audioButton.x = this.world.centerX + this.infoButton.width / 2;

        
        /* Force resize to trigger */
        this.resize(this.game.width,this.game.height);
    },
	resize: function (width, height) {
		//this.background.height = height;
		//this.background.width = width;

		// PSUtils.scaleSprite(this.title, width, height / 3, 50, 1);
		// this.title.x = this.world.centerX;
		// this.title.y = this.world.centerY - height / 3;

		PSUtils.scaleSprite(this.playButton, width, height / 3, 50, .75);
		this.playButton.x = this.world.centerX;
		this.playButton.y = this.world.centerY + 30;

		PSUtils.scaleSprite(this.aboutButton, width, height / 3, 50, .6);
		this.aboutButton.x = this.game.width - 10;
		this.aboutButton.y = this.game.height - 10;

		// PSUtils.scaleSprite(this.infoButton, width, height / 3, 50, 0.5);
		// this.infoButton.x = this.world.centerX - this.infoButton.width / 2;
		// this.infoButton.y = this.world.centerY + height / 3;

		// PSUtils.scaleSprite(this.audioButton, width, height / 3, 50, 0.5);
		// this.audioButton.x = this.world.centerX + this.audioButton.width / 2;
		// this.audioButton.y = this.world.centerY + height / 3;

		/*
		Resize happens immediately, so we wait to remove the fade/curtain 
		until all the elements have jumped into place.
		*/
		PlaySearch.Options.container.className = '';
	},
    playTheGame: function (button) {
        if (!button.clicked) {
            button.clicked = true;
            /* Controlled by Plugin */
            this.state.start("LevelMenu");
        }
    },
    viewGameHelp: function (button) {
        if (!button.clicked) {
            button.clicked = true;
        }
    },
    setAudio: function (button) {
        if (!button.clicked) {
            button.clicked = true;
        }
    }
};