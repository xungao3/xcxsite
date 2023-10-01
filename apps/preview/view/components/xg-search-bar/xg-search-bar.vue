<template name="xg-search-bar">
	<view class="root" :class="classnames" :style="[root,block.styles]">
		<view class="view">
			<view class="main" @click="mainclick">
				<input class="input" v-model="value" />
				<view class="button xg-icon xg-icon-mag" @click="btnclick"></view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "xg-search-bar",
				value:'',
			}
		},
		props:{
		},
		methods:{
			render:function(){
				const s=this;
			},
			mainclick:function(){
				const s=this;
				if(s.block.link){
					s.link(s.block.link);
				}
			},
			btnclick:function(){
				const s=this;
				s.o('xg-search',s.value);
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
.root{}
.view{width:100%;height:var(--block-height);margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);border:var(--block-border);font-size:var(--block-fontsize);font-weight:var(--block-weight);background:var(--block-bg);width:var(--block-width);color:var(--theme-color);}
.main{display:flex;width:calc(100% - 2px);border:solid 1px var(--theme-color);border-radius:1rem;overflow:hidden;box-sizing:border-box;}
.input{display:block;width:calc(100% - 2rem);height:2rem;font-size:var(--block-fontsize,0.9rem);font-weight:var(--block-weight);background:rgba(0,0,0,0);border:0;padding:0.25rem 0.9rem;line-height:1.5rem;box-sizing:border-box;color:var(--theme-color);}
.button{font-size:1.3em;width:2rem;color:var(--theme-color);text-align:center;line-height:2rem;}
</style>