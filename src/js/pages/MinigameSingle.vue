<template>
  <div class="game-single minigame-single">
    <template v-if="!loading">
      <router-link :to="{ name: 'single-game', params: { id: $route.params.id }}" class="button button-return"><i class="fa fa-chevron-left"></i> Game Options</router-link>
      <h2 id="app-title">{{ filteredTitle }}</h2>
      <div class="col-md-12 maestro-inner">
        <form id="save-game" @submit.prevent="validateBeforeSubmit">
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <label for="gametitle">Minigame Title</label>
                </th>
                <td>
                  <input name="gametitle" type="text" id="gametitle" v-model="title" class="regular-text" v-validate="'required'">
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="gametype">Minigame Type</label>
                </th>
                <td>
                  <select name="gametype" id="gametype" v-model="gameType" v-validate="'required'" disabled="disabled" class="disabled">
                    <option value="">Choose a Game Type...</option>
                    <template v-for="(label,option) in minigameOptions">
                      <option v-bind:value="option">{{ label }}</option>
                    </template>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
          <template v-if="gameType!=''">
            <minigame-settings v-bind:game-type="gameType" v-bind:minigame-data="minigameData" v-on:save="saveMinigame" v-bind:form-submitted="formSubmitted" v-bind:is-dirty="isDirty" v-on:dirty="isDirty=true" v-bind:title="title"></minigame-settings>
          </template>
        </form>
      </div>
    </template>
    <template v-else>
      <loading-spinner></loading-spinner>
    </template>
  </div>
</template>

<script>
import Axios from 'axios'
import MinigameSettings from './components/MinigameSettings.vue'
export default {
  components: {
    MinigameSettings
  },
  data () {
    return {
      title: 'PlaySearch',
      loading: false,
      error: null,
      gameType: '',
      isDirty: false,
      drag: false,
      formSubmitted: false,
      saveText: 'Save Game',
      minigameOptions: [],
      minigameData: {
        textData: {},
        imageData: {}
      }
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
  created () {
    this.fetchData();
  },
  methods : {
    validateBeforeSubmit() {
      //console.log(this.minigameData);
      // var self = this;
      // self.error = null;
      // // Validate All returns a promise and provides the validation result.
      // this.$validator.validateAll().then(success => {
      //     if (! success) {
      //         // handle error
      //         return;
      //     }
      //     self.formSubmitted = true;
      //     Axios.post('/wp-json/maestro-game-builder/v1/game/save/'+this.$route.params.id,{
      //       title: document.getElementById('gametitle').value,
      //       type: document.getElementById('gametype').value,
      //       minigame_order: self.minigameList
      //     })
      //     .then(function (response) {
      //       console.log(response);
      //       if(response.data.saved === true){
      //        // self.$router.push({ name: 'single-game', params: { id: response.data.id }});
      //       }
      //       self.saveText = 'Saved!';
      //       setTimeout(function(){
      //         self.saveText = 'Save Game';
      //       },2000);

      //       self.formSubmitted = false;
      //     }).catch(function(error) {
      //       self.formSubmitted = false;
      //       console.log(error);
      //     });
      // }).catch(error => {
      //   console.log(error);
      // });
    },
    fetchData () {
      var self = this;
      self.loading = true;
      Axios.get('/wp-json/maestro-game-builder/v1/games/'+this.$route.params.id+'/minigames/'+this.$route.params.mid)
      .then(function (response) {
        /* What we know. */
        self.title = response.data.post_title;
        if(typeof response.data.custom.type !== 'undefined'){
          self.gameType = response.data.custom.type;
        }
        if(typeof response.data.minigameOptions !== 'undefined'){
          self.minigameOptions = response.data.minigameOptions;
        }
        console.log(response.data);
        if(typeof response.data.custom.minigame_data !== 'undefined' ) {
          self.minigameData = response.data.custom.minigame_data;
        }
        self.loading = false;
      })
      .catch(function (error) {
        console.log(error);
      });
    },
    saveMinigame () {
      var self = this;
      self.formSubmitted = true;
      /* Data components must be specific in the data object, to strip out problematic Vue components. */
      Axios.post('/wp-json/maestro-game-builder/v1/games/'+this.$route.params.id+'/minigame/'+this.$route.params.mid+'/save',{
            title: document.getElementById('gametitle').value,
            type: document.getElementById('gametype').value,
            data: self.minigameData
          })
          .then(function (response) {
            console.log(response);
            if(response.data.saved === true){
              console.log('saved!');
              self.isDirty = false;
              self.formSubmitted = false;
             // self.$router.push({ name: 'single-game', params: { id: response.data.id }});
            }

          }).catch(function(error) {
            self.formSubmitted = false;
            console.log(error);
      });
    }
  }
}
</script>