<template>
  <tbody>
    <tr>
      <th scope="row">
        <label for="minigame_settings">Settings</label>
      </th>                  
      <td>
      Simon specific settings will go here.
      <button v-on:click="mediaUploader">Upload an Image</button>
      <img v-if="backgroundImg" v-bind:src="backgroundImg" />
      </td>
    </tr>
    <tr>
      <th></th>
      <td class="save">
        <loading-spinner v-if="formSubmitted"></loading-spinner>
        <button class="button button-submit" type="submit" :disabled="saveText!='Save Minigame'" v-on:click="$emit('save');" v-else>{{ saveText }}</button>
      </td>
    </tr>
    <tr>
    <td>
      <div id="gameDiv"></div>
    </td>
    </tr>
    <crop-box v-if="backgroundImg" v-bind:box-type="'cropImage'" v-on:close="backgroundImg=null;" v-bind:game="$route.params.id" v-bind:image="backgroundImg"></crop-box>
  </tbody>
</template>

<script>

//import SimonSays from '../../../games/simon-says';
//import PlaySearch from '../../../games/Utils';
//import SimonSays from '../../../games/SimonSays';

/*
For each image, check the ratio. If it works, skip the info box. If it doesn't, load the info box with cropper and create a placeholder canvas to demo the placement (must be tailored for each game).
*/
export default {
  props: ['minigameData'],
  data () {
    return {
      gamePreview: null,
      formSubmitted: false,
      saveText: 'Save Minigame',
      backgroundImg: null,

      // simon: [],
      // pIHold: null,
      // pIPos: 0,
      // timeConstant: null,
      // timer: 0,
      // music: [],
      // whiteUp: {},
      // whiteI: null,
      // redUp: {},
      // redI: null,
      // blueUp: {},
      // blueI: null,
      // button1: {},
      // button2: {},
      // button3: {},
      // button4: {},
      // button5: {},
      // button6: {},
      // buttonGrouper: null,
      // startButton: null,
      // simonButton: null,
      // bkg: null,
      // picture: null,
      // sequenceLoop: null,
      // fx: null,
      // sequencePos: 0,
      // listPos: 0,
      // menuBox: [],
      // violinIntro: null,
      // pianoIntro: null,
      // wrong: null,
      // playerChoice: null,
      // background: null,
      // player: null,
      // startButton: null,
      // menuButton: null,
      // menu_Home: null,
      // menu_Resume: null
    }
  },
  created () {
    //window.PIXI = require('../../../../../node_modules/phaser/build/custom/pixi');
    //window.p2 = require('../../../../../node_modules/phaser/build/custom/p2');
    //window.Phaser = require('../../../../../node_modules/phaser/build/custom/phaser-split');
    //var PlaySearch = require('../../../games/Utils');
    //PlaySearch.SimonSays = require('../../../games/SimonSays');
    //console.log(PlaySearch);
    this.minigameData.activeGame = 'simon';
  },
  mounted () {
    var self = this;
    // this.gamePreview = new Phaser.Game(800, 600, Phaser.AUTO, 'gameDiv', { 
    //     preload: self.preload,
    //     create: self.create,
    //     update: self.update,
    // });
  },
  methods: {
    submitMinigame() {

    },
    mediaUploader() {
      var self = this;
      var media_uploader = wp.media({
          frame:    "post", 
          state:    "insert", 
          multiple: false
      });

      media_uploader.on("insert", function(){
          var json = media_uploader.state().get("selection").first().toJSON();
          console.log(json);
          // var image_url = json.url;
          // var image_caption = json.caption;
          // var image_title = json.title;
          self.backgroundImg = json.url;
      });

      media_uploader.open();
    }
    // preload () {
    //   console.log(this.gamePreview);
    //   this.gamePreview.load.spritesheet('buttons', '/games/simon/images/buttons_150x100.png', 150, 100); 
  
    //   //!--------MEMORY IMAGES --- TEACHER INPUT
    //   this.gamePreview.load.image('mem0', '/games/simon/images/memory0.jpg');    
    //   this.gamePreview.load.image('mem1', '/games/simon/images/memory1.jpg');
      
    //   //!---------BACKGROUND AND AVATAR IMAGE
    //   this.gamePreview.load.image('background', '/games/simon/images/clouds_generic.jpg');
    //   this.gamePreview.load.image('player', '/games/simon/images/player_circle.png'); //placed on left hand side
      
      
    //   this.gamePreview.load.spritesheet('menu', '/games/simon/images/menuButtons.png', 200,100);
      
    //   //TOOT TOOT PRELOAD THE AUDIO HERE -- TEACHER INPUT?
    //   this.gamePreview.load.audio('sfx', '/games/simon/sounds/efgabc.ogg');
    //   this.gamePreview.load.audio('violinIntro', '/games/simon/sounds/kreutzer_violinIntro_loop.ogg');
    //   this.gamePreview.load.audio('pianoIntro', '/games/simon/sounds/kreutzer_piano_intro.ogg');
    //   this.gamePreview.load.audio('wrong', '/games/simon/sounds/wrong.ogg');
    // },
    // create () {

    //   this.simon = [ //these lists set the pattern for simon. 
    //   //We'll need a way to add an arry of numbers
    //     [1,2,4,2,1],
        
    //     [4,1,1,2,5,1,6],
        
    //     [0]

    //   ];
    //   this.timeConstant = Phaser.Timer.SECOND;


    //   this.menuBox = [];


      
    //   //this.createAudio();
      
      
    //   //add the background
      
    //   this.background = this.gamePreview.add.image(0,0, 'background');
    //   this.background.scale.setTo(0.75,0.75);   
      
    //   this.player = this.gamePreview.add.image(0, this.gamePreview.world.centerY, 'player');
      
      
    //   //all 6 buttons
    //   this.button1 = this.drawPlayerOptions((this.gamePreview.world.width/24), this.gamePreview.world.centerY+150, 'buttons', 0, 1);
    //   this.button1.scale.setTo(0.75,0.75);
    //   this.button1.val = 1;
    //   //this.button1.sfx = this.gamePreview.add.audio('sfx');
      
    //   this.button2 = this.drawPlayerOptions((this.gamePreview.world.width/24)+120, this.gamePreview.world.centerY+150, 'buttons', 2, 1);
    //   this.button2.scale.setTo(0.75,0.75);
    //   this.button2.val = 2;
      
    //   this.button3 = this.drawPlayerOptions((this.gamePreview.world.width/24)+240, this.gamePreview.world.centerY+150, 'buttons', 4, 1);
    //   this.button3.scale.setTo(0.75,0.75);
    //   this.button3.val = 3;
      
    //   this.button4 = this.drawPlayerOptions((this.gamePreview.world.width/24)+360, this.gamePreview.world.centerY+150, 'buttons', 6, 1);
    //   this.button4.scale.setTo(0.75,0.75);
    //   this.button4.val = 4;
      
    //   this.button5 = this.drawPlayerOptions((this.gamePreview.world.width/24)+480, this.gamePreview.world.centerY+150, 'buttons', 8, 1);
    //   this.button5.scale.setTo(0.75,0.75);
    //   this.button5.val = 5;
      
    //   this.button6 = this.drawPlayerOptions((this.gamePreview.world.width/24)+600, this.gamePreview.world.centerY+150, 'buttons', 10, 1);
    //   this.button6.scale.setTo(0.75,0.75);
    //   this.button6.val = 6;
      
    //   this.buttonGrouper = [this.button1, this.button2, this.button3, this.button4, this.button5, this.button6]; //make it easier to access

      
      
    //   this.startButton = this.gamePreview.add.button(this.gamePreview.width-10, 10, 'menu', this.LoopF, this,14,12,13);
    //   this.startButton.anchor.setTo(1,0)
    //   this.startButton.scale.setTo(0.5, 0.5);
    //   this.menuButton = this.gamePreview.add.button(5,5,'menu', this.menuPress, this, 3,1,2); // hover, static, press
    //   this.menuButton.scale.setTo(0.5, 0.5);
      
      
    //   //this.menuBox = this.createRectangle(this.gamePreview.world.centerX-150, 100, 300,300, 0xb4b3b3, 0x000000);
    //   this.menu_Home = this.gamePreview.add.button(this.menuBox.x+50, this.menuBox.y+35, 'menu', this.menuOptions, this, 5,4,6);
    //   this.menu_Home.scale.setTo(1,1);
    //   this.menu_Home.val = "home";
    //   this.menu_Resume = this.gamePreview.add.button(this.menuBox.x+50, this.menuBox.y+145, 'menu', this.menuOptions, this, 9,8,10);
    //   this.menu_Resume.scale.setTo(1,1);
    //   this.menu_Resume.val = "resume";
    
      
    //   this.menuBox.visible = false;
    //   this.menu_Home.visible = false;
    //   this.menu_Resume.visible = false;
        
    //     /* Force resize to trigger */
    //  // this.resize(this.gamePreview.width,this.gamePreview.height);
    // },
    // update () {

    // },
    // createAudio () {
    //   this.fx = this.gamePreview.add.audio('sfx');
    //   this.fx.allowMultiple = true;
      
    //   this.fx.addMarker('1', 1, 2.0);    //e note
    //   this.fx.addMarker('2', 4.2,2.0);   //f note
    //   this.fx.addMarker('3', 7.5, 2.0);  //g note
    //   this.fx.addMarker('4', 10.7, 2.0); //a note
    //   this.fx.addMarker('5', 13.5, 1.5); //b note
    //   this.fx.addMarker('6', 16, 1.7);   //c note
      
      
    //   this.violinIntro = this.gamePreview.add.audio('violinIntro');    
    //   this.pianoIntro = this.gamePreview.add.audio('pianoIntro');    
    //   this.music = [this.violinIntro, this.pianoIntro];
      
    //   this.wrong = this.game.add.audio('wrong');
    //   this.wrong.volume = 0.5;   
    // },
    // drawPlayerOptions (x, y, sprite, key, scale) {
    //   var obj = this.gamePreview.add.sprite(x, y, sprite);
    //   obj.frame = key;
    //   obj.scale.set(scale);
    //   obj.inputEnabled = true;
    //   //obj.events.onInputDown.add(this.playerChoice,this.gamePreview);
    //   return obj;
    // },
    // LoopF () {
    //         //if you're in the middle of clicking player buttons pressing start again will reset
    //   this.pIPos = 0; //reset our player input position
    //   this.listPos = 0; //reset our sequence list position      
      
      
    //   if(this.picture != null){//for the start since we haven't defined our photo.
    //       this.fadeOut(this.picture);
    //   }
      
    //   if(this.sequencePos < this.simon.length){
    //       this.sequenceLoop = this.gamePreview.time.events.loop(Phaser.Timer.SECOND, this.updateSequence, this.gamePreview);
    //   }
    //   if(this.sequencePos === this.simon.length -1){
    //       this.sequencePos = 0;
    //   }
    // },
    // fadeOut (sprite) {
    //   this.gamePreview.time.events.add(Phaser.Timer.SECOND * 1, function(){
    //       this.gamePreview.add.tween(sprite).to( { alpha: 0 }, 800, Phaser.Easing.Linear.None, true);
    //   }, this.gamePreview);
    // }
  }
}
</script>