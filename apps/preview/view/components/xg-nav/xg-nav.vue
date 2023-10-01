<template name="xg-nav">
<view :class="classnames" :style="root">
	<view class="main">
		<scroll-view scroll-x="true" scroll-y="true" :class="'nav-'+block.nav_theme" :scroll-into-view="cur">
			<view class='nav'>
				<view v-if="show_home" @click="linkclick" :id="itemid({})" :data-link="linkdata({},1)" :class="itemclass({})">
					<text class="text">首页</text>
					<view class="line"></view>
				</view>
				<block v-if="block.nav_theme!='d'" v-for="item,index in items">
					<view @click="linkclick" v-if="item.title" :id="itemid(item)" :data-link="linkdata(item,1)" :class="itemclass(item)">
						<text class="text">{{item.title}}</text>
						<view v-if="isthis(item)" class="line"></view>
					</view>
				</block>
				<xg-nav-d v-if="block.nav_theme=='d'" :itemsdata="items" :blockdata="block"></xg-nav-d>
			</view>
		</scroll-view>
	</view>
</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-nav",
				cur:null,
				items:[],
				show_home:false,
			}
		},
		methods:{
			init:function(){
				const s=this;
				if(s.block.nav_source=='sysnav'){
					s.cachefile(`nav-${s.block.bid}`,'nav',{sys:s.sys,bid:s.block.bid},function(data){
						s.items=data;
					});
				}else{
					s.items=s.block.data;
				}
				setTimeout(function(){
					s.cur=s.itemid(s.options);
					if(s.block.nav_index===1||s.block.nav_index==='1'){
						s.show_home=true;
					}
				},100);
			},
			itemid:function(d){
				const s=this;
				return 'id-'+s.md5(JSON.stringify(s.linkdata(d)));
			},
			itemclass:function(d){
				const s=this;
				return ['link',(s.isthis(d)?'cur':'')];
			},
			isthis:function(t){
				const s=this;
				if(s.pagename=='index'&&s.iseobj(t))return true;
				return s.linkthis(t);
			}
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				if(s.block.bg_color){
					style['--block-bg']=s.block.bg_color;
					style['--nav-c-bg-color']=s.block.bg_color;
				}else{
					style['--nav-c-bg-color']=style['--theme-color'];
				}
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
.main{margin:var(--block-margin);border-radius:var(--block-radius);font-size:0.9rem;}
.nav{display:flex;flex-wrap:nowrap;height:2.4rem;background-color:var(--block-bg);}
.nav .link{display:block;flex-shrink:0;word-spacing:normal;word-break:normal;padding:0.5rem;line-height:1rem;}
.nav .link .line{height:0.2rem;margin-top:0.08rem;border-radius:0.2rem;background:var(--theme-color);}
.nav .link .text{display:inline-block;}
.nav .link.cur .text{color:var(--theme-color);}

.nav-a .nav{margin-bottom:0.3rem;}
.nav-a .nav .link{margin-right:-0.8rem;}
.nav-a .nav .link.cur{margin-right:-0.5rem;}
.nav-a .nav .link.cur .text{font-weight:bold;color:var(--theme-color);}
.nav-a .nav .link .text{display:inline-block;padding:0.2rem 0.3rem;color:var(--text-color);}
.nav-a .nav .link .line{display:none;}
.nav-a .nav .link.cur .line{display:block;}

.nav-b .nav{background:var(--theme-color);}
.nav-b .nav .link{background:var(--theme-color);padding:0.7rem 0 0.7rem 0.8rem;}
.nav-b .nav .link:last-child{padding-right:0.8rem;}
.nav-b .nav .link .line{display:none;}
.nav-b .nav .link .text{color:var(--text-color);}
.nav-b .nav .link.cur .text{color:#fff;font-weight:bold;}
.nav-b .nav .link.cur .line{display:block;}

.nav-c{overflow:hidden;}
.nav-c .nav{background-color:var(--nav-c-bg-color,--theme-color);background-image:linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.25) 100%);}
.nav-c .nav .link{padding:0.7rem 0.8rem;border-right:solid 1px rgba(255,255,255,0.15);border-left:solid 1px rgba(0,0,0,0.15);background-color:var(--nav-c-bg-color,--theme-color);background-image:linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.25) 100%);}
.nav-c .nav .link .text{color:var(--text-color,#fff);}
.nav-c .nav .link.cur .text{color:#fff;font-weight:bold;}
.nav-c .nav .link .line{display:none;}
.nav-c .nav .link:first-child{border-left:none;}
.nav-c .nav .link:last-child{box-shadow:1px 0 0 0 rgba(0,0,0,0.15);}

.nav-d{height:var(--block-height,100vh);}
.nav-d .nav{display:flex;flex-direction:column;height:auto;}
.nav-d .nav .link{display:flex;}
.nav-d .nav .link .line{display:none;}
</style>