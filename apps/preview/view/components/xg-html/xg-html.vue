<template name="xg-html">
	<view :class="classnames" :style="root">
		<view v-if="showself" class="content" v-html="html" @click.stop="itemclick"></view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-html",
				html:''
			}
		},
		methods:{
			render:function(){
				const s=this;
				s.html=s.rehtml(s.blockinfo(s.block.html,s.cont));
			},
			itemclick:function(){
				const s=this;
				if(s.block&&s.block.data_url){
					s.blocksubmit();
					return;
				}
				s.$emit('click',s.dataIndex);
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
.content{color:var(--text-color);margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);text-align:var(--block-align);border:var(--block-border);background:var(--block-bg);font-size:var(--block-fontsize);font-weight:var(--block-weight);}
</style>