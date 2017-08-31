<template>
    <div class="home">
      <h2 id="app-title">Maestro Game Builder</h2>
      <template v-if="!loading">
        <template v-if="hasGames">
          <div class="col-md-4 col-sm-6 existing-game-block" v-for="game in games">
            <router-link :to="{ name: 'single-game', params: { id: game.id }}">
              <h3>{{ game.title }}<em v-if="game.status=='draft'"><br/>(draft)</em></h3>
            </router-link>
            <span class="game-meta">
              <h5 v-if="game.custom.type">{{ game.custom.typeLabel }}</h5>
              <h5 v-else>Needs configured.</h5>
              <ul>
                <li v-if="game.link">
                  <a v-bind:href="game.link" target="_blank"><i class="fa fa-gamepad"></i>Play</a>
                </li>
                <li>
                  <router-link :to="{ name: 'single-game', params: { id: game.id }}"><i class="fa fa-pencil"></i>Edit</router-link>
                </li>
                <li>
                  <a href="#" v-on:click="copyGame(game.id)"><i class="fa fa-copy"></i>Copy</a>
                </li>
                <li>
                  <a href="#" v-on:click="deleteID = game.id; deleteTitle = game.title"><i class="fa fa-close"></i>Delete</a>
                </li>
              </ul>
            </span>
          </div>
          <div class="col-md-4 col-sm-6 existing-game-block create-game">
            <a href="#" v-on:click="create=true;">
              <span>
                <i class="fa fa-plus"></i>
                Create New Game
              </span>
            </a>
          </div>
        </template>
        <template v-else>
          <div class="col-md-8 col-md-offset-2 maestro-inner">
            <h3 class="center">Welcome to the <em>Maestro</em> game builder,<br />built with love by the creators of <em>The Search for Harmony</em>.<br />You can use this tool to create fun minigame-based experiences.</h3>
            <p class="center">
              To get started, go ahead and name your very first game (you can change it later if you want).
            </p>
            <form id="create-game" @submit.prevent="validateBeforeSubmit">
              <div class="control has-icon has-icon-right">
                  <label class="label">Game Title: </label>
                  <span class="new-game-title" v-bind:class="{ 'has-content': newGameTitle }">
                    <input v-model="newGameTitle" v-validate="'required'" name="title" data-vv-name="title" id="title" type="text">
                  </span>
              </div>
              <p class="control">
                <loading-spinner v-if="formSubmitted"></loading-spinner>
                <button class="button button-submit" v-bind:class="{ active: newGameTitle }" :disabled="!newGameTitle" type="submit" v-else>
                  <template v-if="newGameTitle">
                    Make it happen!
                  </template>
                  <template v-else>
                    Be clever...
                  </template>
                </button>
              </p> 
            </form>
          </div>
        </template>
      </template>
      <template v-else>
        <loading-spinner></loading-spinner>
      </template>
      <dialog-box v-if="deleteTitle" v-bind:message="dialogMessage" v-on:no="deleteID = null; deleteTitle = null;" v-on:yes="deleteGame"></dialog-box>
      <info-box v-if="create" v-on:close="create = false;" v-bind:box-type="'createGame'"></info-box>
    </div>
</template>

<script>
import Axios from 'axios'
export default {
  data () {
    return {
      title: 'Main',
      loading: false,
      error: null,
      hasGames: false,
      games: [],
      newGameTitle: null,
      formSubmitted: false,
      deleteID : null,
      deleteTitle : null,
      create : false
    }
  },
  computed : {
    dialogMessage () {
      return "Are you sure you want to delete "+this.deleteTitle+"?";
    }
  },
  created () {
      this.fetchData();
  },
  methods : {
    fetchData () {
      var self = this;
      self.games = [];
      self.hasGames = false;
      self.loading = true;
      Axios.get('/wp-json/maestro-game-builder/v1/init')
      .then(function (response) {
        console.log(response);
        if(response.data.games.length>0){
          self.hasGames = true;
          self.games = response.data.games;
        }else{
          setTimeout(function(){
            document.getElementById("title").focus();
          },20);
        }
        self.loading = false;
      })
      .catch(function (error) {
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
    copyGame(id) {
      var self = this;
      self.loading = true;
      Axios.post('/wp-json/maestro-game-builder/v1/game/copy/'+id)
      .then(function (response) {
        console.log(response);
        if(response.data.successful === true){
          /* Refresh the game list. */
          self.fetchData();
        }else{
          self.loading = false;
        }
      }).catch(function(error) {
        self.loading = false;
        console.log(error);
      });
    },
    deleteGame() {
      var self = this;
      self.loading = true;
      /* This dismisses the dialog box. */
      self.deleteTitle = null;
      Axios.post('/wp-json/maestro-game-builder/v1/game/delete/'+self.deleteID)
      .then(function (response) {
        console.log(response);
        if(response.data.deleted === true){
          /* Refresh the game list. */
          self.deleteID = null;
          self.fetchData();
        }else{
          self.loading = false;
        }
      }).catch(function(error) {
        self.loading = false;
        console.log(error);
      });
    }
  }
}
</script>