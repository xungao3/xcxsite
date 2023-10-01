<template name="uni-block">
	<view ref="main"><view style="display:inline-block;width:0;height:0;overflow:hidden;"><input type="radio" :value="value"></view><slot></slot></view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "radio",
				classnames:[]
			}
		},
		props:{
			value:String
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