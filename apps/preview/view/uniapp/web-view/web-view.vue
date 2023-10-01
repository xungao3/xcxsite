<template name="web-view">
	<iframe class="root" :class="classnames" :src="src"></iframe>
</template>

<script>
import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "web-view",
				classnames:[]
			}
		},
		props:{
			src:String,
		},
		methods:{
			render:function(){
				const s=this;
			},
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				return style;
			}
		},
		mounted:function(){
			const s=this;
		},
	}
</script>

<style scoped>
.root{overflow:hidden;width:100vw;height:100vh;}
</style>