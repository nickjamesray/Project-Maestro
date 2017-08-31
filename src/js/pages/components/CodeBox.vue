<template>
  <div id="sp-dialog-popup-outer">
    <div class="container">
      <div class="row">
        <div class="col-sm-12" id="sp-dialog-popup-inner">
          <h4 class="sp-modal-title">{{ codeComponents.lineKey }}</h4>
          <p class="sp-modal-title-description sp-code-description">{{ codeComponents.description }}</p>
          <div class="sp-code-box-controls">
            <button v-on:click="showOnlyHighlights" class="maestro-view-source maestro-source-toggle" v-bind:class="{ 'active': filterActive }">Show Where "{{ codeComponents.lineKey }}" Is Used</button>
            <button v-on:click="showAll" class="maestro-view-source maestro-source-toggle" v-bind:class="{ 'active': !filterActive }">Show All</button>
          </div>
          <pre class="language-javascript sp-code-box" v-bind:class="{ 'sp-filter-active': filterActive }"><code class="language-javascript" v-html="fileData"></code></pre>
          <button class="maestro-box-close" v-on:click="$emit('close')"><i class="fa fa-close"></i></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Prism from 'prismjs'
import 'prismjs/plugins/line-numbers/prism-line-numbers.js'
import Axios from 'axios'
export default {
  props: ['codeComponents','file'],
  data () {
    return {
      fileData: null,
      fileLines: [],
      loading: false,
      commentCache: false,
      highlightCache: false,
      filterActive: false
    }
  },
  created () {
    this.fetchData();
  },
  methods: {
    showAll () {
      var lines = document.querySelectorAll('.sp-line');
      for (var i = 0; i < lines.length; i++) {
        lines[i].style.display = 'initial';
      }
      this.filterActive = false;
    },
    showOnlyHighlights () {
      var lines = document.querySelectorAll('.sp-line');
      for (var i = 0; i < lines.length; i++) {
        lines[i].style.display = 'none';
      }
      var highlights = document.querySelectorAll('.sp-line-highlight');
      for (var i = 0; i < highlights.length; i++) {
        highlights[i].style.display = 'inherit';
      }
      this.filterActive = true;      
    },
    fixHTML (html){
      var div = document.createElement('div');
      var formattedHTML = html;
      if(this.commentCache){
        formattedHTML = '<span class="token comment">' + html;
      }
      if(html.indexOf('token comment')>-1){
        this.commentCache = true;
      }
      /* To preserve comment formatting. Either the end of the comment or it was a single-line. */
      if(html.indexOf('*/')>-1||html.indexOf('//')>-1){
        this.commentCache = false;
      }
      div.innerHTML=formattedHTML;
      return (div.innerHTML);
    },
    fetchData () {
      var self = this;
      self.loading = true;
      var now = Math.floor(Date.now() / 1000);
      Axios.get(MaestroPluginBase+'/maestro-game-builder/minigames/views/'+this.file+'?refresh='+now)
      .then(function (response) {
        var tempData = Prism.highlight(response.data, Prism.languages.javascript);
        self.fileLines = tempData.split(/\n/);
        self.fileData = '';
        var highlight = '';
        for(var i = 0; i < self.fileLines.length; i++) {
          /* Toggles Highligting */
          if(self.fileLines[i].indexOf('/** End '+self.codeComponents.lineKey+' **/')>-1){
            self.highlightCache = false;
          }
          if(self.highlightCache&&highlight!=' sp-line-highlight'){
            highlight = ' sp-line-highlight';
            self.fileData += '<span class="sp-highlight-group">';
          }else if(!self.highlightCache&&highlight!=''){
            highlight = '';
            self.fileData += '</span>';
          }
          self.fileData += '<span class="sp-line sp-line-'+(i+1)+highlight+'"><span class="line-number">'+(i+1)+'</span>'+self.fixHTML(self.fileLines[i])+'</span>';
          if(self.fileLines[i].indexOf('/** '+self.codeComponents.lineKey+' **/')>-1){
            self.highlightCache = true;
          }
        }
        //self.showOnlyHighlights();
        self.loading = false;
      })
      .catch(function (error) {
        console.log(error);
      });
    }
  }
}
</script>