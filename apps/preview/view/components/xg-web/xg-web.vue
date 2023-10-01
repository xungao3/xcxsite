<template name="xg-web">
	<view :class="classnames" :style="root">
		<web-view class="web" v-if="src" :src="fileurl(src)"></web-view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-web",
				src:''
			}
		},
		methods:{
			render:function(){
				const s=this;
				s.src=s.blockinfo(s.block.src,s.cont);
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
			s.xginit();
			s.init();
		},
	}
</script>

<style scoped>
.web{width:100vw;height:100vh;}
</style>