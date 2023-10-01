<template name="xg-image">
	<view @click="linkclick" class="xg-image" :class="[block.classnames,classnames]" :style="[root,block.styles]">
		<image v-if="block.picurl&&!varurl" class="image" :mode="mode" :src="block.picurl" @click="linkclick"></image>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-image",
				varurl:true,
				mode:'widthFix'
			}
		},
		methods:{
			render:function(){
				const s=this;
				if(s.block&&s.block.picurl){
					if(s.cont){
						const url=s.blockinfo(s.block.picurl,s.cont);
						if(url){
							s.block.picurl=s.fileurl(url);
							s.varurl=false;
						}
					}else if(!/\[.*?\]/.test(s.block.picurl)){
						s.varurl=false;
					}
				}
			}
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
		}
	}
</script>

<style scoped>
.xg-image{margin:var(--block-margin);width:var(--block-width,100%);height:var(--block-height,100%);padding:var(--block-padding);border-radius:var(--block-radius);overflow:hidden;}
.xg-image .image{width:var(--block-width);height:var(--block-height);}
</style>