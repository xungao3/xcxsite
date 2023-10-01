<template name="xg-popup">
	<view class="root" :class="[pos,classnames,anim]" :style="root">
		<view class="main" :class="[disp,pos,anim]">
			<slot></slot>
			<xg-blocks v-if="items.length" @transitionend="animend" @animationend="animend" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="items" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-blocks>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default {
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-popup",
				items:[],
				blocks:[],
				disp:'xg-hide',
				anim:'default',
				pos:'',
				in:{
					default:'xg-anim-in',
					shake:'xg-anim-shake',
					left:'xg-anim-in-left',
					right:'xg-anim-in-right',
					down:'xg-anim-in-down',
					up:'xg-anim-in-up',
				},
				out:{
					default:'xg-anim-out',
					left:'xg-anim-out-left',
					right:'xg-anim-out-right',
					down:'xg-anim-out-down',
					up:'xg-anim-out-up',
				}
			};
		},
		props: {
			name: String,
			blocksdata:[Object,Array],
		},
		watch:{
			blocksdata:{
				handler(n,o){
					const s=this;
					s.blocks=n;
					s.render();
				},
				immediate:true,
				deep:true
			}
		},
		mounted: function() {
			const s = this;
			s.xginit();
			s.render();
		},
		methods: {
			render: function() {
				const s = this;
				if(s.isobj(s.blocks)){
					s.items=s.obj2arr(s.blocks);
				}else if(s.isarr(s.blocks)&&s.blocks.length){
					s.items=s.blocks;
				}
				if(s.showself){
					s.open(s.block.popup_pos);
				}else{
					s.close();
				}
				s.o('xg-emit',function(){
					s.render();
				},1);
			},
			itemclick: function(e){
				const s = this;
				if(s.isnan(e)){
					if(e&&e.currentTarget&&e.currentTarget.dataset&&e.currentTarget.dataset.index){
						var index=e.currentTarget.dataset.index;
					}else{
						return false;
					}
				}else{
					var index=e;
				}
				if(s.isfun(s.items[index].click)){
					s.items[index].click();
				}
			},
			open: function(pos,anim) {
				const s = this;
				s.opos=pos;
				if(pos=='top'){
					pos='xg-pos-f xg-full-top';
					if(!anim)anim='down';
				}else if(pos=='right'){
					pos='xg-pos-f xg-full-right';
					if(!anim)anim='right';
				}else if(pos=='bottom'){
					pos='xg-pos-f xg-full-bottom';
					if(!anim)anim='up';
				}else if(pos=='left'){
					pos='xg-pos-f xg-full-left';
					if(!anim)anim='left';
				}else{
					s.opos='center';
					pos='xg-fix-center';
				}
				s.pos=pos;
				s.action='open';
				s.disp='';
				s.anim=s.in[anim||'default'];
			},
			close: function(anim) {
				const s = this;
				const pos=s.opos;
				if(pos=='top'){
					if(!anim)anim='up';
				}else if(pos=='right'){
					if(!anim)anim='right';
				}else if(pos=='bottom'){
					if(!anim)anim='down';
				}else if(pos=='left'){
					if(!anim)anim='left';
				}
				s.action='close';
				s.anim=s.out[anim||'default'];
			},
			animend:function(){
				const s = this;
				if(s.action=='close'){
					s.pos='';
					s.disp='xg-hide';
				}
			}
		},
		computed:{
			root:function(){
				const s = this;
				const style=s.mainstyles;
				for(let name in style){
					let name2=name.replace('--block-','--popup-');
					if(name2!=name){
						style[name2]=style[name];
						delete style[name];
					}
				}
				if(s.inxg()){
					style['--window-top']='0px';
				}
				return style;
			}
		},
	}
</script>

<style scoped>
.root{z-index:var(--popup-z-index,9999);width:var(--popup-width);height:var(--popup-height);}
.main{width:var(--popup-width);height:var(--popup-height);box-sizing:border-box;padding:var(--popup-padding,1rem);border-radius:var(--popup-radius,1rem);background:var(--popup-bg-color,#fff);display:var(--popup-display,block);flex:1;width:var(--popup-width);height:var(--popup-height);flex-direction:var(--popup-direction);margin:var(--popup-margin);padding:var(--popup-padding);border:var(--popup-border);border-radius:var(--popup-radius);background:var(--popup-bg);justify-content:var(--popup-justify-content);z-index:var(--popup-z-index,9999);}
/* #ifdef H5 */
.xg-full-top{top:0;}
.xg-full-left{top:0;height:calc(100vh - 0px);}
.xg-full-right{top:0;height:calc(100vh - 0px);}
.xg-full-bottom{}
/* #endif */
</style>
