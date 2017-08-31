<template>
<tbody>
  <tr>
    <th scope="row">
      <label for="minigame_settings">Settings</label>
    </th>                  
    <td>
    Top-down runner specific settings will go here.
    </td>
  </tr>
  <tr>
    <th></th>
    <td class="save">
      <loading-spinner v-if="formSubmitted"></loading-spinner>
      <button class="button button-submit" type="submit" :disabled="saveText!='Save Game'" v-else>{{ saveText }}</button>
    </td>
  </tr>
</tbody>
</template>

<script>
import Axios from 'axios'
export default {
  data () {
    return {
      title: 'PlaySearch',
      loading: false,
      error: null,
      gameType: '',
      drag: false,
      formSubmitted: false,
      saveText: 'Save Game',
      minigameOptions: []
    }
  }
}
</script>