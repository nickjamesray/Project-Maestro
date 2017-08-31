<template>
	<div id="maestro-app">
		<template v-if="!loading">
			<div class="container-fluid">
			  	<div class="row">
			  		<div class="col-sm-12">
			  			<div class="container-fluid">
			  				<div class="row-fluid main">
						  		<router-view></router-view>
			  				</div>
			  			</div>
			  		</div>
			  	</div>
			</div>
		</template>
		<template v-else>
			<loading-spinner></loading-spinner>
		</template>
	</div>
</template>


<script>
import Axios from 'axios'
export default {
    data () {
	    return {
	    	loading: false
	    }
	}
}
</script>