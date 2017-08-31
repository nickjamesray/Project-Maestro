<template>
  <div id="sp-dialog-popup-outer">
    <div class="container">
      <div class="row">
        <div class="col-sm-6 col-sm-offset-3" id="sp-dialog-popup-inner">
          <template v-if="boxType=='createGame'">
            <form id="create-game" @submit.prevent="createGame">
              <p class="center">
                What's the name of your new game?
              </p>
              <div class="control has-icon has-icon-right">
                  <label class="label">Game Title: </label>
                  <span class="new-game-title" v-bind:class="{ 'has-content': newGameTitle }">
                    <input v-model="newGameTitle" v-validate="'required'" name="title" data-vv-name="title" id="title" type="text">
                  </span>
              </div>
              <p class="control">
                <loading-spinner v-if="formSubmitted"></loading-spinner>
                <button class="button button-confirm" v-bind:class="{ active: newGameTitle }" :disabled="!newGameTitle" type="submit" v-else>
                  <template v-if="newGameTitle">
                    Make it happen!
                  </template>
                  <template v-else>
                    Be clever...
                  </template>
                </button>
                <button class="button button-cancel" v-on:click="$emit('close');" v-if="!formSubmitted" type="button">Cancel</button>
              </p> 
            </form>
          </template>
          <template v-if="boxType=='createMinigame'">
            <form id="create-minigame" @submit.prevent="createMinigame">
              <p class="center">
                Choose the type of minigame you want to make, and give it a unique name.
              </p>
              <div class="control has-icon has-icon-right">
                <select name="gametype" id="gametype" v-model="minigameType" v-validate="'required'">
                  <option value="">Choose a Minigame Type...</option>
                  <template v-for="(label,option) in minigameOptions">
                    <option v-bind:value="option">{{ label }}</option>
                  </template>
                </select>
              </div>
              <div class="control has-icon has-icon-right">
                  <label class="label">Game Title: </label>
                  <span class="new-game-title has-content">
                    <input v-model="newMinigameTitle" v-validate="'required'" name="title" data-vv-name="title" id="title" type="text">
                  </span>
              </div>
              <p class="control">
                <loading-spinner v-if="formSubmitted"></loading-spinner>
                <button class="button button-confirm" v-bind:class="{ active: newMinigameTitle }" :disabled="!newMinigameTitle||!minigameType" type="submit" v-else>
                  <template v-if="newMinigameTitle&&minigameType">
                    Make it happen!
                  </template>
                  <template v-else-if="minigameType">
                    Give me a name...
                  </template>
                  <template v-else>
                    Give me a type...
                  </template>
                </button>
                <button class="button button-cancel" v-on:click="$emit('close');" v-if="!formSubmitted" type="button">Cancel</button>
              </p> 
            </form>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Axios from 'axios'
import Cropper from 'cropperjs'
/* boxType controls which component appears here. */
export default {
  props: ['boxType','game','minigameOptions'],
  data () {
    return {
      saveText: 'Save',
      newGameTitle: null,
      formSubmitted: false,
      minigameType: '',
      newMinigameTitle: null
    }
  },
  methods: {
    createGame() {
      var self = this;
      self.error = null;
      // Validate All returns a promise and provides the validation result.
      this.$validator.validateAll().then(success => {
          if (! success) {
              // handle error
              return;
          }
          self.formSubmitted = true;
          Axios.post('/wp-json/maestro-game-builder/v1/game/create',{
            title: document.getElementById('title').value
          })
          .then(function (response) {
            console.log(response);
            if(response.data.created === true){
              self.$router.push({ name: 'single-game', params: { id: response.data.id }});
            }
            self.formSubmitted = false;
          }).catch(function(error) {
            self.formSubmitted = false;
            console.log(error);
          });
      }).catch(error => {
        console.log(error);
      });
    },
    createMinigame() {
      var self = this;
      self.error = null;
      // Validate All returns a promise and provides the validation result.
      this.$validator.validateAll().then(success => {
          if (! success) {
              // handle error
              return;
          }
          self.formSubmitted = true;
          Axios.post('/wp-json/maestro-game-builder/v1/games/'+self.game+'/minigame/create',{
            title: document.getElementById('title').value,
            type: self.minigameType
          })
          .then(function (response) {
            console.log(response);
            if(response.data.created === true){
              //self.$router.push({ name: 'single-minigame', params: { id: self.game, mid: response.data.id }});
              /* Return user to list and push array. */
              self.$emit('minigame',response.data.minigame);
            }
            self.formSubmitted = false;
          }).catch(function(error) {
            self.formSubmitted = false;
            console.log(error);
          });
      }).catch(error => {
        console.log(error);
      });
    }
  }
}
</script>