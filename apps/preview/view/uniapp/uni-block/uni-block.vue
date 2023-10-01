<template name="uni-block">
	<slot></slot>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "uni-block",
				classnames:[]
			}
		},
		props:{
			src:String,
			mode:String
		},
		methods:{
			render:function(){
				const s=this;
			},
			init:function(){
				const s=this;
				s.render();
			},
		},
		computed:{
		},
		mounted:function(){
			const s=this;
			s.init();
		},
	}
</script>

<style scoped>
</style>