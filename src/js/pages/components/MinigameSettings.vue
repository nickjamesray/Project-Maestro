<template>
  <table class="form-table">
    <bridge-runner v-if="gameType=='bridgerunner'" v-bind:minigame-data="minigameData" v-on:save="$emit('save')" v-bind:form-submitted="formSubmitted" v-bind:is-dirty="isDirty" v-on:dirty="$emit('dirty')" v-bind:title="title"></bridge-runner>
    <simon-says v-if="gameType=='simon'" v-bind:minigame-data="minigameData"></simon-says>
    <top-runner v-if="gameType=='toprunner'" v-bind:minigame-data="minigameData"></top-runner>
    <gateway-code v-if="gameType=='gateway'" v-bind:minigame-data="minigameData"></gateway-code>
  </table>
</template>

<script>
import BridgeRunner from './minigame-settings/BridgeRunner.vue';
import SimonSays from './minigame-settings/SimonSays.vue';
import GatewayCode from './minigame-settings/GatewayCode.vue';
import TopRunner from './minigame-settings/TopRunner.vue';
export default {
  props: ['gameType','minigameData','formSubmitted','isDirty','title'],
  components: {
    BridgeRunner,
    SimonSays,
    GatewayCode,
    TopRunner
  },
  data () {
    return {}
  }
}
</script>