<template>
<tbody>
  <tr>
    <th></th>
    <td class="save">
      <loading-spinner v-if="formSubmitted"></loading-spinner>
      <button class="button button-submit" :disabled="saveText!='Save Minigame'" v-on:click="saveMinigame();$event.preventDefault();" v-else>{{ saveText }}</button>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="minigame-setting-tabs">
      <ul class="tabs">
        <li v-on:click="activeTab = 'content'" :class="{'active' : activeTab=='content'}">Content</li>
        <li v-on:click="activeTab = 'artwork'" :class="{'active' : activeTab=='artwork'}">Artwork</li>
        <!-- <li v-on:click="activeTab = 'controls'" :class="{'active' : activeTab=='controls'}">Controls</li>-->
       <li v-on:click="activeTab = 'preview'" :class="{'active' : activeTab=='preview'}">Play!</li>
      </ul>
      <section class="tab" v-show="activeTab == 'content'">
        <table>
          <tbody>
            <tr>
              <th scope="row">
                <label for="minigame_settings">Minigame Overview</label>
              </th>                  
              <td>
              <p>Collect objects using the spacebar or touching the screen (if a touchscreen) to jump. When you have collected all necessary objects the game is won.</p>
              <p><strong>Hint: </strong><em>Feel free to copy/paste this text into the "Instructions" field below so your player will know what to do.</em></p>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="minigame_settings">Help/Intro Text</label>
                <button class="maestro-view-source" v-on:click="codeBoxOpen = codeData.help">
                  <i class="fa fa-code"></i><span> View Code</span>
                </button>
              </th>                  
              <td>
                <wysiwyg v-bind:id="$route.params.mid" v-bind:content="textData.help.text" field="intro-text"></wysiwyg>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="minigame_settings">Victory Title</label>
              </th>                  
              <td>
                <input type="text" name="victoryTitle" v-model="textData.success.title">
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="minigame_settings">Victory Text</label>
                <button class="maestro-view-source" v-on:click="codeBoxOpen = codeData.success">
                  <i class="fa fa-code"></i><span> View Code</span>
                </button>
              </th>                  
              <td>
                <wysiwyg v-bind:id="$route.params.mid" v-bind:content="textData.success.text" field="victory-text"></wysiwyg>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
      <section class="tab" v-show="activeTab == 'artwork'">
        <table>
          <tbody>
            <tr>
              <th scope="row">
                <label>Base Layer</label>
                <button class="maestro-view-source" v-on:click="codeBoxOpen = codeData.base">
                  <i class="fa fa-code"></i><span> View Code</span>
                </button>
              </th>                  
              <td>
                <button v-on:click="mediaUploader('baseLayer')" v-if="!getImage('baseLayer')" class="upload">Upload an Image</button>
                <p class="description"><em>Background image for the level.</em></p>
                <template  v-if="getImage('baseLayer')">
                  <div class="mgb-image">
                    <img v-bind:src="getImage('baseLayer')" />
                    <i class="fa fa-close" v-on:click="removeImage('baseLayer')"></i>
                  </div>
                </template>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label>Sprite (player)</label>
                  <button class="maestro-view-source" v-on:click="codeBoxOpen = codeData.sprite">
                  <i class="fa fa-code"></i><span> View Code</span>
                </button>
              </th>                  
              <td>
                <button v-on:click="mediaUploader('avatar')" v-if="!getImage('avatar')" class="upload">Upload an Image</button>
                <p class="description"><em>Four poses that animate running/jumping.</em></p>
                <template  v-if="getImage('avatar')">
                  <div class="mgb-image">
                    <img v-bind:src="getImage('avatar')" />
                    <i class="fa fa-close" v-on:click="removeImage('avatar')"></i>
                  </div>
                </template>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label>Obstacle</label>
                  <button class="maestro-view-source" v-on:click="codeBoxOpen = codeData.obstacle1">
                  <i class="fa fa-code"></i><span> View Code</span>
                </button>
              </th>                  
              <td>
                <button v-on:click="mediaUploader('obstacle1')" v-if="!getImage('obstacle1')" class="upload">Upload an Image</button>
                <p class="description"><em>Blocks that can be jumped on or over.</em></p>
                <template  v-if="getImage('obstacle1')">
                  <div class="mgb-image">
                    <img v-bind:src="getImage('obstacle1')" />
                    <i class="fa fa-close" v-on:click="removeImage('obstacle1')"></i>
                  </div>
                </template>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label>Collectibles</label>
                  <button class="maestro-view-source" v-on:click="codeBoxOpen = codeData.collectibles">
                  <i class="fa fa-code"></i><span> View Code</span>
                </button>
              </th>                  
              <td>
                <p class="description"><em>One or more items the player collects on contact (up to 4).</em></p>
                <draggable v-model="imageData.collectibles.group" @start="drag=true" @end="drag=false" v-on:add="$emit('dirty');" v-on:remove="$emit('dirty');" v-on:change="$emit('dirty');" :list="imageData.collectibles.group" class="maestro-multi-list" :options="{group:'collectibles',handle:'.move-handle'}" v-bind:class="{ 'empty-list': imageData.collectibles.group.length<1 }">
                   <div class="maestro-multi-float maestro-collectibles" v-for="(collectible,index) in imageData.collectibles.group">
                      <template  v-if="getImage('collectibles',index)">
                        <div class="mgb-image">
                          <img v-bind:src="getImage('collectibles',index)" draggable="false"/>
                          <i class="fa fa-close" v-on:click="removeImage('collectibles',index)"></i>
                        </div>
                      </template>
                    <div class="move-handle"><div class="move-handle-inner"><i class="fa fa-arrows"></i></div></div>
                   </div>
                </draggable>
                <button v-on:click="mediaUploader('collectibles',imageData.collectibles.group.length)" v-if="imageData.collectibles.group.length<4" class="upload multi-upload">Add a Collectible</button>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label>Platforms</label>
                  <button class="maestro-view-source" v-on:click="codeBoxOpen = codeData.platforms">
                  <i class="fa fa-code"></i><span> View Code</span>
                </button>
              </th>                  
              <td>
                <p class="description"><em>Blocks that make up the ground. (up to 6, will display randomly).</em></p>
                <draggable v-model="imageData.platforms.group" @start="drag=true" @end="drag=false" v-on:add="$emit('dirty');" v-on:remove="$emit('dirty');" v-on:change="$emit('dirty');" :list="imageData.platforms.group" class="maestro-multi-list" :options="{group:'platforms'}" v-bind:class="{ 'empty-list': imageData.collectibles.group.length<1 }">
                   <div class="maestro-multi-float maestro-platforms" v-for="(platform,index) in imageData.platforms.group">
                      <template  v-if="getImage('platforms',index)">
                        <div class="mgb-image">
                          <img v-bind:src="getImage('platforms',index)" draggable="false"/>
                          <i class="fa fa-close" v-on:click="removeImage('platforms',index)"></i>
                        </div>
                      </template>
                    <div class="move-handle"><div class="move-handle-inner"><i class="fa fa-arrows"></i></div></div>
                   </div>
                </draggable>
                <button v-on:click="mediaUploader('platforms',imageData.platforms.group.length)" v-if="imageData.platforms.group.length<6" class="upload multi-upload">Add a Platform Design</button>
              </td>
            </tr> 
          </tbody>
        </table>
      </section>
      <section class="tab" v-show="activeTab == 'controls'">
        controls
      </section>
      <section class="tab" v-show="activeTab == 'preview'">
        <div class="MGB-preview-wrap">
        <iframe :src="'/wp-admin/admin.php?page=maestro-game-builder&preview=BridgeRunner&game='+$route.params.id+'&minigame='+$route.params.mid" v-if="activeTab == 'preview'&&!isDirty"></iframe>
        <template v-else>
          <p>You must save changes before playing this minigame.</p>
          <loading-spinner v-if="formSubmitted"></loading-spinner>
          <button class="button button-submit" :disabled="saveText!='Save Minigame'" v-on:click="saveMinigame();$event.preventDefault();" v-else>{{ saveText }}</button>
        </template>
        </div>
      </section>
    </td>
  </tr>
  <!-- Crop boxes -->
  <crop-box v-if="cropImageKey" v-on:close="cropImageKey = null;multiKey=-1" v-on:save="$emit('dirty');cropImageKey = null;multiKey=-1" v-bind:game="$route.params.id" v-bind:image-key="cropImageKey" v-bind:image-data="imageData" v-bind:multi-key="multiKey"></crop-box>
  <codebox v-on:close="codeBoxOpen = false;" v-if="codeBoxOpen" v-bind:code-components="codeBoxOpen" v-bind:file="codeFile"></codebox>
</tbody>
</template>

<script>
import Axios from 'axios'
import draggable from 'vuedraggable'
export default {
  props: ['minigameData','formSubmitted','isDirty','title'],
  components: {
    draggable,
  },
  data () {
    return {
      codeBoxOpen: false,
      codeFile: 'BridgeRunner.js',
      codeData: {
        help: {
          lineKey: 'Help/Intro Text',
          description: 'This content appears before the minigame begins.'
        },
        success: {
          lineKey: 'Victory Text',
          description: 'This content appears when the game is won.'
        },
        base: {
          lineKey: 'Base Layer',
          description: 'Background image for the level.'
        },
        sprite: {
          lineKey: 'Sprite',
          description: 'Four poses that animate running/jumping.'
        },
        obstacle1: {
          lineKey: 'Obstacle',
          description: 'Blocks that can be jumped on or over.'
        },
        collectibles: {
          lineKey: 'Collectibles',
          description: 'Objects the player can collect through contact.'
        },
        platforms: {
          lineKey: 'Platforms',
          description: 'Blocks that make up the ground.'
        }
      },
      loading: false,
      error: null,
      gameType: '',
      drag: false,
      saveText: 'Save Minigame',
      activeTab: 'content',
      multiKey: -1,
      imageData: {
        baseLayer: {
          name: 'baseLayer',
          layer: 0,
          activeUrl: null,
          rawUrl: null,
          dataUrl: null,
          height: 600,
          width: 5110,
          tile: false,
          alpha: false
        },
        avatar: {
          name: 'avatar',
          layer: 5,
          activeUrl: null,
          rawUrl: null,
          dataUrl: null,
          height: 514,
          width: 1307,
          tile: false,
          alpha: true,
          floorSprite: true
        },
        obstacle1: {
          name: 'obstacle1',
          layer: 5,
          activeUrl: null,
          rawUrl: null,
          dataUrl: null,
          height: 64,
          width: 65,
          tile: false,
          alpha: false
        },
        collectibles: {
          name: 'collectibles',
          layer: 5,
          height: 86,
          width: 46,
          group: [],
          tile: false,
          alpha: true
        },
        platforms: {
          name: 'platforms',
          layer: 5,
          height: 140,
          width: 259,
          group: [],
          tile: false,
          alpha: true,
          alphaOption: true
        }
      },
      textData : {
        help: {
          title : '',
          text : ''
        },
        success: {
          title: '',
          text: ''
        }        
      },
      cropImageKey: null
    }
  },
  computed: {

  },
  created () {
    this.minigameData.activeGame = 'bridge-runner';
    /* Reboot from a save. */
    if( typeof this.minigameData.imageData !== 'undefined' ){
      var self = this;
      /* We restore the URL from the active minigame. */
        for (var p in this.minigameData.imageData) {
 
          if( typeof self.minigameData.imageData[p].group !== 'undefined' && self.minigameData.imageData[p].group.length > 0 ) {
            /* Group */
            for(var p2 = 0; self.minigameData.imageData[p].group.length > p2; p2++ ) {
              var multiData = {
                dataUrl: null,
                rawUrl: null,
                activeUrl: self.minigameData.imageData[p].group[p2].url
              };
              self.imageData[p].group.push(multiData);
            }
          }else{
            if( self.minigameData.imageData.hasOwnProperty(p) ) {
              self.imageData[p].activeUrl = self.minigameData.imageData[p].url;
            }            
          }
        } 
    }
    if(typeof this.minigameData.textData.success !== 'undefined' ){
      /* success exists so the object has been saved before. */
      this.textData = this.minigameData.textData;
    }
  },
  beforeCreate () {

  },
  beforeDestroy () {
    /* Remove listeners. */
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
    saveMinigame() {
      /* Format Data for Saving */
      this.minigameData.imageData = this.imageData;
      this.textData.help.text = tinyMCE.editors['intro-text'].getContent();
      this.textData.success.text = tinyMCE.editors['victory-text'].getContent();
      this.minigameData.textData = this.textData;
      this.$emit('save');
    }
  }
}
</script>