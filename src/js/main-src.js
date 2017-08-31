import Axios from 'axios'
Axios.defaults.headers.common['X-WP-Nonce'] = wpApiSettings.nonce
// Axios.defaults.maxContentLength = 5000
import Lockr from 'lockr'
import Vue from '../vendor/vue.js'
// import Vue from 'vue'
import VeeValidate from 'vee-validate'
// import Lodash from 'lodash'
import VueRouter from 'vue-router'
import App from './MainApp.vue'
import Home from './pages/Home.vue'
import GameSingle from './pages/GameSingle.vue'

//Minigame list can be a page where we describe the various types of minigames. 
//import MinigameList from './pages/MinigameList.vue'
import MinigameSingle from './pages/MinigameSingle.vue'

// Components/Partials
import InfoBox from './pages/components/InfoBox.vue'
import CropBox from './pages/components/CropBox.vue'
import DialogBox from './pages/components/DialogBox.vue'
import WYSIWYG from './pages/components/WP-WYSIWYG.vue'
import CodeBox from './pages/components/CodeBox.vue'

const config = {
  errorBagName: 'errors', // change if property conflicts.
  fieldsBagName: 'fields',
  delay: 0,
  locale: 'en',
  dictionary: null,
  strict: true,
  enableAutoClasses: false,
  classNames: {
    touched: 'touched', // the control has been blurred
    untouched: 'untouched', // the control hasn't been blurred
    valid: 'valid', // model is valid
    invalid: 'invalid', // model is invalid
    pristine: 'pristine', // control has not been interacted with
    dirty: 'dirty' // control has been interacted with
  }
}

Vue.use(VeeValidate, config)

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'hash',
  base: '/wp-admin/admin.php',
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/game/:id',
      name: 'single-game',
      component: GameSingle
    },
    // {
    //   path: '/game/:id/minigames',
    //   name: 'minigames-list',
    //   component: MinigameList
    // },
    {
      path: '/game/:id/minigame/:mid',
      name: 'single-minigame',
      component: MinigameSingle
    },
    {
      path: '*',
      redirect: '/'
    }
  ]
})

Vue.component('loading-spinner', {
  template: '<div class="spin-block"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>'
})
Vue.component('dialog-box', DialogBox )
Vue.component('info-box', InfoBox )
Vue.component('crop-box', CropBox )
Vue.component('wysiwyg', WYSIWYG )
Vue.component('codebox', CodeBox)

//import 'prismjs'
//import 'prismjs/plugins/line-numbers/prism-line-numbers.js'
//import 'prismjs/themes/prism.css'

//import Prism from 'vue-prism-component'
//Vue.component('prism', Prism )

// 4. Create and mount the root instance.
// Make sure to inject the router with the router option to make the
// whole app router-aware.
/* isChanged makes */
// var MGB_isChanged = false;
// router.beforeEach(function (to, from, next) {
//     // this route requires auth, check if logged in
//     // if not, redirect to login page.
//     // ?next='+to.path can be used to pass redirect value
//     if(MGB_isChanged==true){
//        if (confirm("You have unsaved changes. If you wish to save, click 'Cancel' and save first before proceeding.") == true) {
//         next();
//       }      
//     }else{
//       next();
//     }  
// });

const app = new Vue({
  router,
  render: h => h(App)
}).$mount('#maestro-app')

console.log('Welcome!')
