<template name="xg-main">
	<view :class="classnames" :style="root">
		<xg-blocks v-if="blocks&&blocks.length" :blocksdata="blocks" :contsdata="conts" :contdata="cont" :userdata="user" blocksfrom="main"></xg-blocks>
		<view v-if="showloading" class="xg-loading">
			<view class="xg-fix-bg"></view>
			<view class="xg-loading-dot xg-fix-center">
				<view class="div"></view>
				<view class="div"></view>
				<view class="div"></view>
			</view>
		</view>
	</view>
	<!-- #ifdef MP-WEIXIN -->
		<xg-wx-login :show="showwxlogin"></xg-wx-login>
		<xg-wx-mobile :show="showwxmobile"></xg-wx-mobile>
	<!-- #endif -->
</template>

<script>
	import {basemixin} from '../../js/mixin.js'
	export default{
		mixins:[basemixin],
		data(){
			return {
				xgname:"xg-main",
				blocks:[],
				user:null,
				showloading:false,
				conts:null,
				cont:null,
				showwxlogin:false,
				showwxmobile:false,
			}
		},
		props:{
		},
		methods:{
			init:function(){
				const s=this;
				s.inithooks(s);
				s.datas();
				s.o('xg-emit',function(d){
					if(d.key=='wxlogin'){
						s.showwxlogin=!s.showwxlogin;
					}
					if(d.key=='wxmobile'){
						s.showwxmobile=!s.showwxmobile;
					}
				});
			}
		},
		mounted:function(){
			const s=this;
			s.init();
		},
		computed:{
			root:function(){
				const s=this;
				let color=s.theme_color||s.c.theme_color;
				const style={'--theme-color':color};
				return style;
			}
		},
	}
</script>

<style scoped>
.xg-loading .div{background:var(--theme-color,#fff)!important;border:solid 1px #fff;}
.xg-loading-dot,.xg-loading-dot-2{display:flex;}
.xg-loading-dot .div,.xg-loading-dot-2 .div{width:1rem;height:1rem;margin:0.1rem;border-radius:50%;animation-name:xg-loading-dot;animation-duration:0.5s;animation-iteration-count:infinite;animation-timing-function:linear;}
.xg-loading-dot-2 .div{animation-name:xg-loading-dot-2;margin:0.1rem;animation-duration:1s;animation-timing-function:ease;}
.xg-loading-dot .div:nth-child(1){animation-delay:0s;}
.xg-loading-dot .div:nth-child(2){animation-delay:0.1s;}
.xg-loading-dot .div:nth-child(3){animation-delay:0.2s;}
@keyframes xg-loading-dot{0%{transform:translateY(0%);}25%{transform:translateY(70%);}75%{transform:translateY(-70%);}100%{transform:translateY(0%);}}
.xg-loading-dot-2 .div:nth-child(1){animation-delay:-0.6s;}
.xg-loading-dot-2 .div:nth-child(2){animation-delay:-0.45s;}
.xg-loading-dot-2 .div:nth-child(3){animation-delay:-0.3s;}
.xg-loading-dot-2 .div:nth-child(4){animation-delay:-0.15s;}
.xg-loading-dot-2 .div:nth-child(5){animation-delay:0s;}
@keyframes xg-loading-dot-2{0%{transform:scale(110%,110%);opacity:0.8;}40%{transform:scale(200%,200%);opacity:0.4;}80%{transform:scale(110%,110%);opacity:0.8;}100%{transform:scale(110%,110%);opacity:0.8;}}
</style>