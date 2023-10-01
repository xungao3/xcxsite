<template name="swiper-item">
	<view class="view" :class="classnames" ref="view"><slot></slot></view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "swiper-item",
				classnames:[]
			}
		},
		props:{
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
			root:function(){
				const s=this;
				const style=s.mainstyles;
				return style;
			}
		},
		mounted:function(){
			const s=this;
			s.init();
		},
	}
</script>

<style scoped>
.view{overflow:hidden;}
.view.scrollx{overflow-x:auto;}
.view.scrolly{overflow-y:auto;}
</style>