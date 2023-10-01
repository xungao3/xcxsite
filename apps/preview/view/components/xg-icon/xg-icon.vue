<template name="xg-icon">
	<view :class="classnames" :style="[root,block.styles]"><view v-if="showself" class="content xg-icon" :class="[icon,clsnames]" @click="linkclick"></view></view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "xg-icon",
				icon:'',
				clsnames:''
			}
		},
		props:{
			dataIndex:[Number,String]
		},
		methods:{
			render:function(){
				const s=this;
				s.icon=s.blockinfo(s.block.icon,s.cont);
				s.clsnames=s.block.classnames;
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
.content{color:var(--text-color);width:var(--block-width);height:var(--block-height);margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);text-align:var(--block-align);line-height:var(--block-line-height);border:var(--block-border);background:var(--block-bg);font-size:var(--block-fontsize);font-weight:var(--block-weight);box-sizing:border-box;}
</style>