<template name="xg-video">
	<view :class="classnames" :style="root">
		<video class="video" :src="fileurl(src)" controls></video>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-video",
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
.video{color:var(--text-color);margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);text-align:var(--block-align);border:var(--block-border);background-color:var(--block-bg-color);font-size:var(--block-fontsize);font-weight:var(--block-weight);width:100%;}
</style>