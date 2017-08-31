<template>
  <div class="game-single">
    <template v-if="!loading">
      <h2 id="app-title">{{ filteredTitle }}</h2>
      <div class="col-md-12 maestro-inner">
        <form id="save-game" @submit.prevent="validateBeforeSubmit">
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <label for="gametitle">Game Title</label>
                </th>
                <td>
                  <input name="gametitle" type="text" id="gametitle" v-model="title" class="regular-text" v-validate="'required'">
                  <a class="mgb-permalink" v-bind:href="webLink" target="_blank" v-if="webLink">{{ webLink }}</a>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="gametype">Game Type</label>
                </th>
                <td v-bind:class="{ chosen: gameType!='' }" >
                  <select name="gametype" id="gametype" v-model="gameType" v-validate="'required'" v-on:change="isDirty = true;" disabled>
                    <option value="">Choose a Game Type...</option>
                    <option value="minigame">Just Minigames</option>
                    <option value="minigame_menu">Minigames + Menu</option>
                  </select>
                  <p class="description">The type of game you want to build.</p>
                  <ul>
                    <template v-if="gameType!='minigame_menu'">
                      <li><strong>Just Minigames:</strong><br />The game begins and ends with one or more minigames. Good for short demos or testing out content.</li>
                      <li>The option to include a menu is coming soon!</li>
                    </template>
                    <template v-if="gameType!='minigame'">
                      <li><strong>Minigames + Menu:</strong><br />Pick up to 5 minigames to include in this game, which will also have a main menu (where players can choose minigames to play). You can choose whether to start with a minigame or the main menu, and how many minigames must be completed before the game is won and the player victorious.</li>
                    </template>
                  </ul>
                </td>
              </tr>
              <template v-if="gameType!=''">
                <tr>
                  <th scope="row">
                    <label for="gamestatus">Game Status</label>
                  </th>
                  <td>
                    <select name="gamestatus" id="gamestatus" v-model="gameStatus" v-validate="'required'" v-on:change="isDirty = true;">
                      <option value="draft">Draft</option>
                      <option value="publish">Publish</option>
                    </select>
                    <p class="description">The type of game you want to build.</p>
                    <ul>
                      <template v-if="gameType!='minigame_menu'">
                        <li><strong>Just Minigames:</strong><br />The game begins and ends with one or more minigames. Good for short demos or testing out content.</li>
                      </template>
                      <template v-if="gameType!='minigame'">
                        <li><strong>Minigames + Menu:</strong><br />Pick up to 5 minigames to include in this game, which will also have a main menu (where players can choose minigames to play). You can choose whether to start with a minigame or the main menu, and how many minigames must be completed before the game is won and the player victorious.</li>
                      </template>
                    </ul>
                  </td>
                </tr>
              <!-- Title screen -->
              <!--
                <tr>
                  <th>Title Screen</th>
                  <td>
                    <p><input type="checkbox" v-model="hasTitleScreen" v-on:change="hasTitleScreen = $event.target.checked"> Use Title Screen?</p>
                    <p class="description">If selected, this will be the first screen the player sees.</p>
                    <template v-if="hasTitleScreen">                
                      <table>
                        <tbody>
                          <tr>
                            <td>
                            Stuff
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </template>
                  </td>
                </tr>
            -->
                <tr>
                  <th></th>
                  <td class="save">
                    <loading-spinner v-if="formSubmitted"></loading-spinner>
                    <button class="button button-submit" type="submit" :disabled="saveText!='Save Game'" v-else>{{ saveText }}</button>
                  </td>
                </tr>
                <template v-if="gameType=='minigame_menu'">
                  Coming soon.
                </template>
                <template v-else>
                  <tr>
                    <th scope="row">
                      <label for="minigame_list">Active Minigames</label>
                    </th>                  
                    <td>
                      <p class="description">Your game will begin with the block at the top of this list, and end at the bottom. Click and drag to reorder.</p>
                      <draggable v-model="minigameList" @start="drag=true" @end="drag=false" v-on:add="isDirty=true" v-on:remove="isDirty=true" v-on:change="isDirty=true;" :list="minigameList" class="minigame-list" :options="{group:'minigames',handle:'.move-handle'}" v-bind:class="{ 'empty-list': minigameList.length<1 }">
                         <div v-for="(minigame,index) in minigameList">
                         <h5>{{minigame.title}}</h5>
                         <p><em>Type: {{ minigameOptions[minigame.type] }}</em></p>
                         <div class="move-handle"><div class="move-handle-inner"><i class="fa fa-arrows"></i></div></div>
                         <a v-on:click="selectedMinigame=minigame.id;editMinigame()">Edit</a>
                         <a class="delete" v-on:click="deleteID = minigame.id; deleteTitle = minigame.title;deleteArrayKey=index;deleteArray='used';">Delete</a>
                         </div>
                      </draggable>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <label for="minigame_list">Unused Minigames</label>
                    </th>                  
                    <td>
                      <p class="description">Minigames you can create/edit/modify but that are not currently part of the final game.</p>
                      <draggable v-model="unusedMinigameList" @start="drag=true" @end="drag=false" :list="unusedMinigameList" class="minigame-list" :options="{group:'minigames',handle:'.move-handle'}" v-bind:class="{ 'empty-unused-list': unusedMinigameList.length<1 }">
                         <div v-for="(minigame,index) in unusedMinigameList">
                         <h5>{{minigame.title}}</h5>
                         <p><em>Type: {{ minigameOptions[minigame.type] }}</em></p>
                         <div class="move-handle"><div class="move-handle-inner"><i class="fa fa-arrows"></i></div></div>
                         <a v-on:click="selectedMinigame=minigame.id;editMinigame()">Edit</a>
                         <a class="delete" v-on:click="deleteID = minigame.id; deleteTitle = minigame.title;deleteArrayKey=index;deleteArray='unused';">Delete</a>
                         </div>
                      </draggable>
                      <button class="button button-submit" v-on:click="createMinigame=true;" type="button">Add New Minigame</button>
                    </td>
                  </tr>
                </template>
                <tr>
                  <th scope="row">
                    <label>Menu Button</label>
                  </th>                  
                  <td>
                    <p class="description"><em>This button stays top left during all minigames, and allows players to read help text or quit the game.</em></p>
                    <button v-on:click="mediaUploader('menuButton')" v-if="!getImage('menuButton')" class="upload">Upload an Image</button>
                    <template  v-if="getImage('menuButton')">
                      <div class="mgb-image">
                        <img v-bind:src="getImage('menuButton')" />
                        <i class="fa fa-close" v-on:click="removeImage('menuButton')"></i>
                      </div>
                    </template>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </form>
      </div>
    </template>
    <template v-else>
      <loading-spinner></loading-spinner>
    </template>
    <info-box v-if="createMinigame" v-on:minigame="addMinigame" v-bind:box-type="'createMinigame'" v-on:close="createMinigame=false;" v-bind:game="$route.params.id" v-bind:minigame-options="minigameOptions"></info-box>
    <dialog-box v-if="deleteTitle" v-bind:message="dialogMessage" v-on:no="deleteID = null; deleteTitle = null; deleteArrayKey = null; deleteArray = null;" v-on:yes="deleteMinigame"></dialog-box>
    <dialog-box v-if="saveBeforeEditingPrompt" v-bind:message="saveDialogMessage" v-on:no="isDirty=false;editMinigame()" v-on:yes="validateBeforeSubmit" v-bind:cancel-button="true" v-on:close="selectedMinigame=null;saveBeforeEditingPrompt=false;"></dialog-box>
      <!-- Crop boxes -->
      <crop-box v-if="cropImageKey" v-on:close="cropImageKey = null;multiKey=-1" v-on:save="isDirty=true;cropImageKey = null;multiKey=-1" v-bind:game="$route.params.id" v-bind:image-key="cropImageKey" v-bind:image-data="imageData" v-bind:multi-key="multiKey"></crop-box>
  </div>
</template>

<script>
import Axios from 'axios'
import draggable from 'vuedraggable'
export default {
  components: {
    draggable,
  },
  data () {
    return {
      title: 'PlaySearch',
      webLink: null,
      gameStatus: 'draft',
      loading: false,
      error: null,
      gameType: 'minigame',
      minigameList: [],
      minigameOptions: [],
      unusedMinigameList: [],
      imageData: {
        menuButton: {
          name: 'menuButton',
          layer: 5,
          activeUrl: null,
          rawUrl: null,
          dataUrl: null,
          height: 216,
          width: 216,
          tile: false,
          alpha: true
        },
      },
      cropImageKey: null,
      multiKey: -1,
      drag: false,
      formSubmitted: false,
      saveText: 'Save Game',
      createMinigame: false,
      isDirty: false,
      deleteTitle: '',
      deleteID: null,
      deleteArrayKey: null,
      deleteArray: null,
      selectedMinigame: false,
      saveBeforeEditingPrompt: false,
      hasTitleScreen: false
    }
  },
  computed: {
    filteredTitle () {
      return (this.title!='') ? this.title : '(untitled)';
    },
    dragOptions () {
      return  {
        animation: 0,
        group: 'description',
        disabled: !this.editable,
        ghostClass: 'ghost'
      };
    },
    dialogMessage () {
      return "Are you sure you want to delete "+this.deleteTitle+"?";
    },
    saveDialogMessage () {
      return "Save changes to "+this.title+" before editing the minigame?";
    }
  },
  watch: {
    isDragging (newValue) {
      if (newValue){
        this.delayedDragging= true
        return
      }
      this.$nextTick( () =>{
           this.delayedDragging =false
      })
    }
  },
  mounted () {
    var self = this;
    this.$nextTick(function(){
          window.addEventListener("beforeunload",self.checkChanges);
    });
  },
  beforeDestroy() {
    var self = this;
    window.removeEventListener('beforeunload', self.checkChanges);
  },
  created () {
    this.fetchData();
  },
  methods : {
    getImage (key,multiKey) {
      var multiKey = (typeof multiKey !== 'undefined' ) ? multiKey : -1;
      var dataSource;
      if(multiKey>-1){
        dataSource = this.imageData[key]['group'][multiKey];
      }else{
        dataSource = this.imageData[key];
      }
      if(dataSource.activeUrl){
        /* Token prevents browser caching on changed images. */
        return dataSource.activeUrl+ '?token='+Math.floor(Math.random() * 2000);
      }else if(dataSource.dataUrl){
        return dataSource.dataUrl;
      }
      return false;
    },
    mediaUploader(component, multiKey) {
      var multiKey = (typeof multiKey !== 'undefined' ) ? multiKey : -1;
      var self = this;
      this.cropperTag = component;
     // wp.media.model.settings.post.id = self.$route.params.mid;
      var media_uploader = wp.media({
          title: 'Select Artwork',
          button: {
            text: 'Use This Artwork'
          },
          library: {
              type: 'image'
              //HERE IS THE MAGIC. Set your own post ID var
     //         uploadedTo : wp.media.model.settings.post.id
          },
          frame:    "post", 
          state:    "insert", 
          multiple: false
      });
      media_uploader.on("insert", function(){
          var json = media_uploader.state().get("selection").first().toJSON();

          self.imageData[component].rawUrl = json.url;
          if(multiKey>-1){
            self.multiKey = multiKey;
          }
          self.cropImageKey = component;
      });

      media_uploader.open();
    },
    removeImage(key, multiKey ) {
      var multiKey = (typeof multiKey !== 'undefined' ) ? multiKey : -1;
      if(multiKey>-1){
        if( this.imageData[key]['group'].length < 2 ) {
          this.imageData[key]['group'] = [];
        }else{
          this.imageData[key]['group'].splice(multiKey, 1);
        }
      }else{
        this.imageData[key].dataUrl = null;
        this.imageData[key].activeUrl = null;
      }
      
    },
    checkChanges (e) {
      if (this.formSubmitted || !this.isDirty) {
          return undefined;
      }

      var confirmationMessage = 'It looks like you have been editing something. '
                              + 'If you leave before saving, your changes will be lost.';

      (e || window.event).returnValue = confirmationMessage; //Gecko + IE
      return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    },
    addMinigame (data) {
      console.log(data);
      this.createMinigame = false;
      this.unusedMinigameList.push(data);
    },
    editMinigame () {
      if(this.isDirty){
        console.log('wait');
        this.saveBeforeEditingPrompt = true;
        return;
      }
      this.$router.push({ name: 'single-minigame', params: { id: this.$route.params.id, mid: this.selectedMinigame }});
    },
    deleteMinigame() {
      var self = this;
      //self.loading = true;
      /* This dismisses the dialog box. */
      self.deleteTitle = null;
      Axios.post('/wp-json/maestro-game-builder/v1/game/'+this.$route.params.id+'/minigame/'+self.deleteID+'/delete')
      .then(function (response) {
        console.log(response);
        if(response.data.deleted === true){
          /* Refresh the game list. */
          if(self.deleteArray=='used'){
            self.minigameList.splice(self.deleteArrayKey,1);
          }else{
            self.unusedMinigameList.splice(self.deleteArrayKey,1);
          }
          self.deleteID = null;
          self.deleteArrayKey = null;
          self.deleteArray = null;
         // self.loading = false;
        }else{
         // self.loading = false;
        }
      }).catch(function(error) {
        //self.loading = false;
        console.log(error);
      });
    },
    validateBeforeSubmit() {
      var self = this;
      self.error = null;
      // Validate All returns a promise and provides the validation result.
      this.$validator.validateAll().then(success => {
          if (! success) {
              // handle error
              if(self.saveBeforeEditingPrompt){
                self.saveBeforeEditingPrompt = false;
              }
              return;
          }
          self.formSubmitted = true;
          Axios.post('/wp-json/maestro-game-builder/v1/game/save/'+this.$route.params.id,{
            title: document.getElementById('gametitle').value,
            type: document.getElementById('gametype').value,
            status: document.getElementById('gamestatus').value,
            minigame_order: self.minigameList,
            imageData: self.imageData
          })
          .then(function (response) {
            console.log(response);
            if(response.data.saved === true){
              if(typeof response.data.link !== 'undefined'){
                self.webLink = response.data.link;
              }
              if(self.selectedMinigame){
                self.isDirty = false;
                self.editMinigame();
              }
             // self.$router.push({ name: 'single-game', params: { id: response.data.id }});
            }
            self.saveText = 'Saved!';
            setTimeout(function(){
              self.saveText = 'Save Game';
            },2000);

            self.formSubmitted = false;
            self.isDirty = false;
          }).catch(function(error) {
            self.formSubmitted = false;
            console.log(error);
          });
      }).catch(error => {
        console.log(error);
      });
    },
    fetchData () {
      var self = this;
      self.loading = true;
      Axios.get('/wp-json/maestro-game-builder/v1/games/'+this.$route.params.id)
      .then(function (response) {
        /* What we know. */
        self.title = response.data.post_title;
        self.webLink = response.data.guid;
        self.gameStatus = response.data.post_status;
        if(typeof response.data.custom.type !== 'undefined'){
          self.gameType = response.data.custom.type;
        }
        if(typeof response.data.minigames !== 'undefined') {
          /* Should always be defined if there is a minigame order. */
          self.unusedMinigameList = [];
          response.data.minigames.forEach(function(createdMinigame){
            var active = false;
            if(typeof response.data.custom.minigame_order !== 'undefined'){
              for(var i=0; i<response.data.custom.minigame_order.length; i++){
                if(createdMinigame.id==response.data.custom.minigame_order[i].id){
                  active = true;
                   /* Update the minigame title just in case. */
                  response.data.custom.minigame_order[i].title = createdMinigame.title;
                  response.data.custom.minigame_order[i].type = createdMinigame.type;
                }
                if(response.data.minigameIDs.indexOf(response.data.custom.minigame_order[i].id)<0){
                  /* Game was deleted. Remove from array. */
                  response.data.custom.minigame_order.splice(i,1);
                }             
              }
            }
            if(!active){
              self.unusedMinigameList.push(createdMinigame);
            }
          });
        }
        /* ordering */
        if(typeof response.data.custom.minigame_order !== 'undefined') {
          self.minigameList = response.data.custom.minigame_order;
        }else{
          self.minigameList = [];
        }
        /* A list of all current available minigames. */
        if(typeof response.data.minigameOptions !== 'undefined'){
          self.minigameOptions = response.data.minigameOptions;
        }
        console.log(response.data);
        /* Menu Button */
        if(typeof response.data.custom.game_data !== 'undefined' && typeof response.data.custom.game_data.imageData !== 'undefined' ){
          for (var p in response.data.custom.game_data.imageData ) {
              if( response.data.custom.game_data.imageData.hasOwnProperty(p) ) {
                self.imageData[p].activeUrl = response.data.custom.game_data.imageData[p].url;
              }            
          } 
        }
        
        self.loading = false;
      })
      .catch(function (error) {
        console.log(error);
      });
    }
  }
}
</script>