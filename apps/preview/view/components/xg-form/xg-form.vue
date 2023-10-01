<template name="xg-form">
	<form class="main block block-blocks" :class="[classnames]" :style="root" @submit="submit">
		<xg-blocks v-if="items.length" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="items" :theme_color="block.theme_color||theme_color"></xg-blocks>
	</form>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default {
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-form",
				items:[],
			};
		},
		props:{
		},
		mounted: function() {
			const s = this;
			s.xginit();
			s.render();
		},
		methods: {
			submit:function(e){
				console.log(e.detail);
			},
			render: function() {
				const s = this;
				if(s.isobj(s.block.blocks)){
					s.items=s.obj2arr(s.block.blocks);
				}else if(s.isarr(s.block.blocks)&&s.block.blocks.length){
					s.items=s.block.blocks;
				}
			}
		},
		computed:{
			root:function(){
				const s = this;
				const style=s.mainstyles;
				for(let name in style){
					let name2=name.replace('--block-','--form-');
					if(name2!=name){
						style[name2]=style[name];
						delete style[name];
					}
				}
				return style;
			}
		},
	}
</script>

<style scoped>
.main{width:var(--form-width);height:var(--form-height);box-sizing:border-box;padding:var(--form-padding,1rem);border-radius:var(--form-radius,1rem);background:var(--form-bg-color,#fff);display:var(--form-display,block);flex:1;width:var(--form-width);height:var(--form-height);flex-direction:var(--form-direction);margin:var(--form-margin);padding:var(--form-padding);border:var(--form-border);border-radius:var(--form-radius);background:var(--form-bg);justify-content:var(--form-justify-content);z-index:var(--form-z-index);}
</style>
