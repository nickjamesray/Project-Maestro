<template>
  <div id="sp-dialog-popup-outer">
    <div class="container">
      <div class="row">
        <div class="col-sm-6 col-sm-offset-3" id="sp-dialog-popup-inner">
          <p>{{ message }}</p>
          <button class="button button-confirm" v-on:click="yes">Yes</button>
          <button class="button button-cancel" v-on:click="no">No</button>
          <button class="button button-cancel" v-on:click="cancel" v-if="cancelButton">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['message','cancelButton'],
  data () {
    return {}
  },
  methods: {
    yes (e) {
      e.preventDefault();
      this.$emit('yes');
      return false;
    },
    no (e) {
      e.preventDefault();
      this.$emit('no');
      return false;
    },
    cancel (e) {
      e.preventDefault();
      this.$emit('close');
      return false;
    }
  }
}
</script>