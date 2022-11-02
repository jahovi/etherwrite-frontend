<template>
    <div>
        <div v-if="show" v-html="task"></div>
    </div>
</template>
<script>
import Communication from "../classes/communication";
export default {        
	data: function(){
		return {
			show: false,
			task: null,
		};
	},
	name: "taskView",       
	beforeMount: async function(){
		try{       
			const cmid = this.$store.getters.getCMID;
			const req = await Communication.webservice("getTaskDescription", {cmid}); 
			if(req.success !== true){
				if(req.data){
					this.$store.commit("setAlertWithTimeout", ["alert-danger", req.data, 3000]);                        
				} else {
					this.$store.commit("setAlertWithTimeout", ["alert-danger", this.$store.getters.getStrings.unknown_error, 3000]);
				}
				return;
			}  
			this.show = true;                
			this.task = req.data;
		} catch(error){
			this.$store.commit("setAlertWithTimeout", ["alert-danger", this.$store.getters.getStrings.unknown_error, 3000]);
		}
	},
};
</script>
<style scoped>
</style>