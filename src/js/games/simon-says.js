var PlaySearch = require('./Utils');
/*
Game Type: Simon Says
Description: Repeat the sequence of buttons pressed.
*/
//SimonSays = function (game) {};

function SimonSays(game) {
    this.create = function () {
        //PlaySearch.Options.container.style.backgroundImage = "none";
        this.simon = [ //these lists set the pattern for simon. 
      //We'll need a way to add an arry of numbers
            [1,2,4,2,1],
            
            [4,1,1,2,5,1,6],
            
            [0]

        ];

        this.pIHold; //temporary variable to hold the value of the player's button;
        this.pIPos; //how many times has the player hit a button on this round?

        this.timeConstant = Phaser.Timer.SECOND;
        this.timer = 0;

        this.music = []; 

        this.whiteUp = {}; //player button
        this.whiteI; //indicator sprite to show what simon says

        this.redUp = {};//player button//player button
        this.redI;//indicator sprite to show what simon says 

        this.blueUp = {};//player button
        this.blueI;//indicator sprite to show what simon says

        this.button1 = {};
        this.button2 = {};
        this.button3 = {};
        this.button4 = {};
        this.button5 = {};
        this.button6 = {};

        this.buttonGrouper;
        this.startButton;
        this.simonButton; //this is the sprite for simon's actions

        this.bkg; //background

        this.picture; //memory picture variable

        this.sequenceLoop; //variable to hold the loop so we can specifically stop it

        this.fx;

        this.sequencePos; //numeric representation of where we are in our list of lists 
        this.listPos; //refers to position in list of values
        this.menuBox = [];

        this.listPos = 0;
        this.sequencePos = 0;
        this.pIPos = 0;
        
        
        this.createAudio();
        
        
        //add the background
        
        this.background = this.game.add.image(0,0, 'background');
        this.background.scale.setTo(0.75,0.75);   
        
        this.player = this.game.add.image(0, this.game.world.centerY, 'player');
        
        
        //all 6 buttons
        this.button1 = this.drawPlayerOptions((this.game.world.width/24), this.game.world.centerY+150, 'buttons', 0, 1);
        this.button1.scale.setTo(0.75,0.75);
        this.button1.val = 1;
        this.button1.sfx = this.game.add.audio('sfx');
        
        this.button2 = this.drawPlayerOptions((this.game.world.width/24)+120, this.game.world.centerY+150, 'buttons', 2, 1);
        this.button2.scale.setTo(0.75,0.75);
        this.button2.val = 2;
        
        this.button3 = this.drawPlayerOptions((this.game.world.width/24)+240, this.game.world.centerY+150, 'buttons', 4, 1);
        this.button3.scale.setTo(0.75,0.75);
        this.button3.val = 3;
        
        this.button4 = this.drawPlayerOptions((this.game.world.width/24)+360, this.game.world.centerY+150, 'buttons', 6, 1);
        this.button4.scale.setTo(0.75,0.75);
        this.button4.val = 4;
        
        this.button5 = this.drawPlayerOptions((this.game.world.width/24)+480, this.game.world.centerY+150, 'buttons', 8, 1);
        this.button5.scale.setTo(0.75,0.75);
        this.button5.val = 5;
        
        this.button6 = this.drawPlayerOptions((this.game.world.width/24)+600, this.game.world.centerY+150, 'buttons', 10, 1);
        this.button6.scale.setTo(0.75,0.75);
        this.button6.val = 6;
        
        this.buttonGrouper = [this.button1, this.button2, this.button3, this.button4, this.button5, this.button6]; //make it easier to access

        
        
        this.startButton = this.game.add.button(this.game.width-10, 10, 'menu', this.LoopF, this,14,12,13);
        this.startButton.anchor.setTo(1,0)
        this.startButton.scale.setTo(0.5, 0.5);
        this.menuButton = this.game.add.button(5,5,'menu', this.menuPress, this, 3,1,2); // hover, static, press
        this.menuButton.scale.setTo(0.5, 0.5);
        
        
        this.menuBox = this.createRectangle(this.game.world.centerX-150, 100, 300,300, 0xb4b3b3, 0x000000);
        this.menu_Home = this.game.add.button(this.menuBox.x+50, this.menuBox.y+35, 'menu', this.menuOptions, this, 5,4,6);
        this.menu_Home.scale.setTo(1,1);
        this.menu_Home.val = "home";
        this.menu_Resume = this.game.add.button(this.menuBox.x+50, this.menuBox.y+145, 'menu', this.menuOptions, this, 9,8,10);
        this.menu_Resume.scale.setTo(1,1);
        this.menu_Resume.val = "resume";
      
        
        this.menuBox.visible = false;
        this.menu_Home.visible = false;
        this.menu_Resume.visible = false;
        
        /* Force resize to trigger */
        this.resize(this.game.width,this.game.height);
    }

    this.resize = function (width, height) {
        this.background.height = height;
        this.background.width = width;

        // PSUtils.scaleSprite(this.title, width, height / 3, 50, 1);
        // this.title.x = this.world.centerX;
        // this.title.y = this.world.centerY - height / 3;
        var self = this;
        var counter = 1;
        this.buttonGrouper.forEach(function(button){
            PSUtils.scaleSprite(button, width, height / 3, 50, 1);
            button.anchor.setTo(0.5,0);
            button.x = (self.game.world.width/7)*counter;
            button.y = self.game.world.height - 100;
            counter++;
        });

        PSUtils.scaleSprite(this.startButton, width, height /3, 50, .75);
        this.startButton.x = this.game.world.width-10;
        this.startButton.y = 10;
        /*
        Resize happens immediately, so we wait to remove the fade/curtain 
        until all the elements have jumped into place.
        */
        PlaySearch.Options.container.className = '';
    }

    this.createAudio = function(){
        //single audio file with lots of piano notes to reference
        this.fx = this.game.add.audio('sfx');
        this.fx.allowMultiple = true;
        
        this.fx.addMarker('1', 1, 2.0);    //e note
        this.fx.addMarker('2', 4.2,2.0);   //f note
        this.fx.addMarker('3', 7.5, 2.0);  //g note
        this.fx.addMarker('4', 10.7, 2.0); //a note
        this.fx.addMarker('5', 13.5, 1.5); //b note
        this.fx.addMarker('6', 16, 1.7);   //c note
        
        
        this.violinIntro = this.game.add.audio('violinIntro');    
        this.pianoIntro = this.game.add.audio('pianoIntro');    
        this.music = [this.violinIntro, this.pianoIntro];
        
        this.wrong = this.game.add.audio('wrong');
        this.wrong.volume = 0.5;   
    }
    
    this.menuPress = function(){ //calls when menu button is pressed
        if(this.menuBox.visible === false){
            this.menuBox.visible = true;
            this.menu_Home.visible = true;
            this.menu_Resume.visible = true;

        }else{
            this.menuBox.visible = false;
            this.menu_Home.visible = false;
            this.menu_Resume.visible = false;

        }    
    }

    this.menuOptions = function(Object){
        console.log(Object.val);
        if(Object.val === "resume"){
            this.menuPress();
        }
        if(Object.val === "home"){
            //take us back home
            PlaySearch.Options.container.className = 'fade';
            /* Time parameter matches the CSS transition length so fade completes */
            var self = this;
            window.setTimeout(function(){
                /* Controlled by Plugin */
                self.state.start("StartScreen");
            },300);
            console.log("return to overview")
        }
    }

    this.LoopF = function(){    
     
        //if you're in the middle of clicking player buttons pressing start again will reset
        this.pIPos = 0; //reset our player input position
        this.listPos = 0; //reset our sequence list position      
        
        
        if(this.picture != null){//for the start since we haven't defined our photo.
            this.fadeOut(this.picture);
        }
        
        if(this.sequencePos < this.simon.length){
            this.sequenceLoop = this.game.time.events.loop(Phaser.Timer.SECOND, this.updateSequence, this);
        }
        if(this.sequencePos === this.simon.length -1){
            this.sequencePos = 0;
        }
        
    }

    this.updateSequence = function(){ 
        //this shows the simon commands for the player to mimic
        //start at the beginning of the list within our sequences
        if(this.listPos < this.simon[this.sequencePos].length){ 
            
            this.fx.play(this.simon[this.sequencePos][this.listPos]);//play sound
            
            //draw a smaller version of the key the player needs to hit and have it float up to the middle 
            //before fading away
            this.simonButton = this.drawSprite((this.game.world.width/7)*this.simon[this.sequencePos][this.listPos],   this.game.world.centerY+150, this.buttonGrouper[this.simon[this.sequencePos][this.listPos]-1].key, this.buttonGrouper[this.simon[this.sequencePos][this.listPos]-1].frame, 1);
            PSUtils.scaleSprite(this.simonButton, this.game.world.width, this.game.world.height /3, 50, 1);
            this.game.add.tween(this.simonButton).to({y:this.game.world.centerY}, 1000, "Linear", true);
            this.fadeOut(this.simonButton);

            this.listPos++; //increment down the current list
        }else{        
            this.game.time.events.remove(this.sequenceLoop);  //stop the loop 
        }  
    }

    ///BUTTON DISPLAY FUNCTIONS
    this.disableButton = function(object){
        object.inputEnabled = false;
        this.game.add.tween(object).to( { alpha: 0.1 }, 800, Phaser.Easing.Linear.None, true);
        console.log(object + " is disabled");
    }

    ////DRAW THINGS
    this.drawSprite = function(x, y, sprite, key, scale){
        var obj = this.game.add.sprite(x, y, sprite);
        obj.anchor.setTo(0.5);
        obj.alpha = 0;
        this.fadeIn(obj, 1200);
        obj.frame = key;
        obj.scale.set(scale);
        return obj
    }

    this.fadeIn = function(sprite, time){
        this.game.add.tween(sprite).to( { alpha: 1 }, time, Phaser.Easing.Linear.None, true);
    }

    this.fadeOut = function(sprite){
        this.game.time.events.add(Phaser.Timer.SECOND * 1, function(){
            this.game.add.tween(sprite).to( { alpha: 0 }, 800, Phaser.Easing.Linear.None, true);
        }, this);
    }

    this.drawPlayerOptions = function(x, y, sprite, key, scale){
        var obj = this.game.add.sprite(x, y, sprite);
        obj.frame = key;
        obj.scale.set(scale);
        obj.inputEnabled = true;
        obj.events.onInputDown.add(this.playerChoice,this);
        return obj;
    }

    this.changeSprite = function(object, key){
        object.frame = key;
    }

    //Action
    this.playerChoice = function(object){   
        var tmpInt = object.frame
        this.changeSprite(object, 1); //button pressed down
        this.game.time.events.add(Phaser.Timer.SECOND * 0.2, function(){ //change the button to look 'up'
           this.changeSprite(object, 0);
        }, this); 
        
        if(this.pIPos < this.simon[this.sequencePos].length){
          if(object.val === this.simon[this.sequencePos][this.pIPos]){
             console.log(object.val);
             
             this.fx.play(object.val); //play sound        
            
             
             this.pIPos++; //not done, then advance in this list        
            }else{
                this.wrong.play();
                this.game.time.events.remove(this.sequenceLoop);
            }
        }
        
        this.game.time.events.add(Phaser.Timer.SECOND * 0.2, function(){ //reset the button to eht correct color up position
            this.changeSprite(object, tmpInt);
        }, this);
        
        if(this.pIPos === this.simon[this.sequencePos].length){ //if you've hit every button in a sequence, draw the image
            
            if(this.sequencePos === 0){
                this.music[0].play();
                this.picture = this.drawSprite(this.game.world.centerX-62, 100, 'mem0', 0, 0.5);            
            }
            if(this.sequencePos === 1){
                this.music[1].play();
                this.picture = this.drawSprite(this.game.world.centerX-75, 100, 'mem1', 0, 0.5);
            }
             
            this.sequencePos++; //set the next list from our simon sequence to run
        }
        
    }

    this.createRectangle = function(x, y, w, h, fillHex, outline) {
        var graphics = this.game.add.graphics(x, y);
        // set a fill and line style
        graphics.beginFill(fillHex);
        graphics.lineStyle(1, outline, 1);
        
        // draw a shape
        graphics.moveTo(0,0);
        graphics.lineTo(w, 0);
        graphics.lineTo(w, h);
        graphics.lineTo(0, h);
        graphics.lineTo(0, 0);
        graphics.endFill();    
        graphics.fixedToCamera = true;    
        return graphics;
    }
};
module.exports = SimonSays