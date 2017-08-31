<template>
  <div id="sp-dialog-popup-outer">
    <div class="container">
      <div class="row">
        <div class="col-sm-12" id="sp-dialog-popup-inner">
          <h4 class="sp-modal-title">Select the image part you want for this game asset.</h4>
          <p class="sp-modal-title-description">Crop zone is locked to artwork height/width proportion.</p>
          <div class="container-fluid crop-image-popup-inner">
            <div class="row-fluid">
              <div class="col-sm-5">
                <img id="crop-image" v-bind:src="rawImage" />
                <div class="rotation-slider">
                  <h5>Rotation:</h5>
                  <vue-slider @drag-end="rotationDragEnd" :max="360"></vue-slider>
                </div>
              </div>
              <div class="col-sm-6 col-sm-offset-1">
                <div class="canvas-shell">
                  <canvas v-bind:width="canvasWidth" v-bind:height="canvasHeight" style="border:1px solid #BBB;" v-insert-image="cropData" v-on:click="getPixelColor($event)" id="crop-canvas" :class="{'color-select' : selectColor == true}"></canvas>
                  <template v-if="imageData[imageKey].alpha">
                    <div class="mgb-alpha-checkbox-wrap">
                      <input type="checkbox" id="alpha-checkbox" :value="selectColor" @change="selectColor = !selectColor"> Check this box then click the color above that you want to filter out.
                    </div>
                  </template>
                </div>
                <button class="button button-confirm" v-on:click="saveImage" v-if="!formSubmitted&&saveActive==true" type="button">Save</button>
                <button class="button button-cancel" v-on:click="$emit('close');" v-if="!formSubmitted" type="button">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Axios from 'axios'
import Cropper from 'cropperjs'
import vueSlider from 'vue-slider-component';
/* boxType controls which component appears here. */
export default {
  components: {
    vueSlider
  },
  props: ['game','minigameOptions','imageKey','imageData','multiKey'],
  data () {
    return {
      saveText: 'Save',
      formSubmitted: false,
      cropper: null,
      cropData: null,
      saveActive: false,
      selectColor: false,
      alphaActive: false,
      tolerance: 30,
      filterColor: {
        r: 255,
        g: 255,
        b: 255
      },
      customFilter : false
    }
  },
  mounted () {
    /* Better PNG compression for assets. */
    console.log(this.multiKey);
    var self = this;
    var image = document.getElementById('crop-image');
    this.cropper = new Cropper(image, {
      aspectRatio: self.imageData[self.imageKey].width / self.imageData[self.imageKey].height,
      cropend: function(e) {
        self.manualCrop();
      }
    });
  },
  computed : {
    rawImage () {
      return this.imageData[this.imageKey].rawUrl;
    },
    canvasWidth () {
      return this.imageData[this.imageKey].width;
    },
    canvasHeight () {
      if(typeof this.imageData[this.imageKey].floorSprite !== 'undefined' && this.imageData[this.imageKey].floorSprite == true ) {
        return this.imageData[this.imageKey].height + 20;
      }
      return this.imageData[this.imageKey].height;
    }
  },
  directives: {
    insertImage: function(canvasElement, binding, vnode) {
      if(binding.value){
        /* Data is inserted now so we can save */
        vnode.context.saveActive = true;
        /* Find the element we need and the ratio. */
        var ctx = canvasElement.getContext("2d");
        // Clear the canvas
        var originalHeight = vnode.context.imageData[vnode.context.imageKey].height;
        var height = vnode.context.imageData[vnode.context.imageKey].height;
        var width = vnode.context.imageData[vnode.context.imageKey].width;
        /* If this is a floor sprite we add one for reference, but just for alignment purposes. */
        if(typeof vnode.context.imageData[vnode.context.imageKey].floorSprite !== 'undefined' && vnode.context.imageData[vnode.context.imageKey].floorSprite == true ) {
          height = height + 20;
        }
        ctx.clearRect(0, 0, width, height);
        // Insert stuff into canvas
       // ctx.fillStyle = "black";
       // ctx.font = "20px Georgia";
       /* This needs customized based on tile or not. */
        //var pat=ctx.createPattern(binding.value,"repeat");
        //ctx.rect(0,300-100,600,100);
        //ctx.fillStyle=pat;
        //ctx.fill();
        /* TODO adjust this to options pulled from vnode.context */
        ctx.drawImage(binding.value, 0, 0,width, originalHeight);
        /* for Floor */
        if(typeof vnode.context.imageData[vnode.context.imageKey].floorSprite !== 'undefined' && vnode.context.imageData[vnode.context.imageKey].floorSprite == true ) {
          ctx.fillStyle = "black";
          ctx.rect(0,height-20,width,20);
          ctx.fill();
        }
        /* Remove White (fuzzy) */
        if(typeof vnode.context.imageData[vnode.context.imageKey].alpha !== 'undefined' && vnode.context.imageData[vnode.context.imageKey].alpha == true){

          var imgd = ctx.getImageData(0, 0, width, height),
              pix = imgd.data,
              newColor = {r:0,g:0,b:0, a:0};
          /* Alpha is Active. */
         // if(typeof vnode.context.imageData[vnode.context.imageKey].alphaOption !== 'undefined' && vnode.context.alphaActive == true ) {
            console.log(vnode.context.alphaActive);
            /* So we can toggle on alpha-optional images. */
            for (var i = 0, n = pix.length; i <n; i += 4) {
              var r = pix[i],
                  g = pix[i+1],
                  b = pix[i+2];

              if(!vnode.context.customFilter){
                // If its white then change it
                if(r>=255-vnode.context.tolerance && g>=255-vnode.context.tolerance && b>=255-vnode.context.tolerance ){ 
                    // Change the white to whatever.
                    pix[i] = newColor.r;
                    pix[i+1] = newColor.g;
                    pix[i+2] = newColor.b;
                    pix[i+3] = newColor.a;
                }              
              }else{
                // If its x color then change it
                if(Math.abs(r-vnode.context.filterColor.r)<vnode.context.tolerance && Math.abs(g-vnode.context.filterColor.g)<vnode.context.tolerance && Math.abs(b-vnode.context.filterColor.b)<vnode.context.tolerance ){ 
                    // Change the white to whatever.
                    pix[i] = newColor.r;
                    pix[i+1] = newColor.g;
                    pix[i+2] = newColor.b;
                    pix[i+3] = newColor.a;
                }
              }
            }
      //    }
          ctx.putImageData(imgd, 0, 0);
        }
      }
    }
  },
  methods: {
    saveImage () {
      var canvasElement = document.getElementById('crop-canvas');
      /* If Floor Sprite we must remove the floor */
      if(typeof this.imageData[this.imageKey].floorSprite !== 'undefined' && this.imageData[this.imageKey].floorSprite == true ) {
        var ctx = canvasElement.getContext("2d");
        var data = ctx.getImageData(0,0,this.imageData[this.imageKey].width,this.imageData[this.imageKey].height);
        ctx.clearRect(0, 0, this.imageData[this.imageKey].width, this.imageData[this.imageKey].height+20);
        ctx.canvas.height = this.imageData[this.imageKey].height;
        ctx.putImageData(data, 0, 0);
      }
      CanvasPngCompression.replaceToDataURL();
      var dataUrl = canvasElement.toDataURL('image/png',0.1);
      if(this.multiKey>-1){
        var multiData = {
          dataUrl: dataUrl,
          rawUrl: null,
          activeUrl: null
        };
        this.imageData[this.imageKey]['group'].push(multiData);
      }else{
        this.imageData[this.imageKey].dataUrl = dataUrl;
      }
      
      this.$emit('save');
    },
    getEventLocation (event){
        var rect = event.target.getBoundingClientRect();
        return {
          x: Math.round((event.clientX-rect.left)/(rect.right-rect.left)*event.target.width),
          y: Math.round((event.clientY-rect.top)/(rect.bottom-rect.top)*event.target.height)
        };
    },
    getPixelColor (event) {
      if(this.selectColor){
        // Get the coordinates of the click
        var eventLocation = this.getEventLocation(event);
        // Get the data of the pixel according to the location generate by the getEventLocation function
        var context = event.target.getContext('2d');
        var pixelData = context.getImageData(eventLocation.x, eventLocation.y, 1, 1).data; 
        if(pixelData[3]>10){
          /* This is an opaque selection. Update our canvas. */
          this.filterColor = {
            r: pixelData[0],
            g: pixelData[1],
            b: pixelData[2]
          }
          this.manualCrop();
          this.selectColor = false;
          document.getElementById("alpha-checkbox").checked = false;
          this.customFilter = true;
          this.alphaActive=true;
        }
      }
    },
    rotationDragEnd (slider) {
      this.cropper.rotateTo(slider.currentIndex);
      this.manualCrop();
    },
    manualCrop () {
      this.cropData = this.cropper.getCroppedCanvas({
        width: this.imageData[this.imageKey].width,
        height: this.imageData[this.imageKey].height
      });
    }
  }
}
</script>