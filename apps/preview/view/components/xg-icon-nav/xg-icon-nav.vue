<template name="xg-icon-nav">
	<view :style="root" class="root" :class="classnames">
		<view v-if="data" v-for="item,index in data" class="icon-nav-item" @click="linkclick" :data-link="item.link" :data-pagename="pagename" :style="(item.bg_color?'background-color:'+item.bg_color+';':'')">
			<view v-if="item.font" class="icon-nav-icon xg-icon" :class="item.font" :style="(item.icon_color?'color:'+item.icon_color+';':'')"></view>
			<image v-if="item.img" class="icon-nav-img" mode="aspectFit" :src="fileurl(item.img)"></image>
			<view class="icon-nav-text" :style="(item.text_color?'color:'+item.text_color+';':'')">{{item.title}}</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-icon-nav",
				cur:null,
				data:null
			}
		},
		methods:{
			init:function(){
				var s=this;
				var data=[];
				for(let i in s.block.icon_nav_list){
					let item=s.block.icon_nav_list[i];
					if(item.id)item.id=item.id.toString();
					if(item.cid)item.cid=item.cid.toString();
					if(item.icon){
						if(item.icon.substr(0,7)=='xg-icon'){
							item.font=item.icon;
						}else{
							item.img=item.icon;
						}
					}
					data.push(item);
				}
				s.data=data;
			},
			navcur:function(d){
				var s=this;
				return 'id-'+s.md5(JSON.stringify(s.linkdatastr(d)));
			},
			navthis:function(t){
				var s=this;
				return s.linkthis(t);
			}
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				if(s.block.icon_margin){
					const iconmargin=s.style4in1(s.block.icon_margin);
					style['--icon-margin-left']=iconmargin.left;
					style['--icon-margin-right']=iconmargin.right;
				}
				if(s.block.icon_width)style['--icon-width']=s.block.icon_width;
				if(s.block.icon_height)style['--icon-height']=s.block.icon_height;
				if(s.block.icon_radius)style['--icon-radius']=s.block.icon_radius;
				if(s.block.icon_bg_width)style['--icon-bg-width']=s.block.icon_bg_width;
				if(s.block.icon_bg_height)style['--icon-bg-height']=s.block.icon_bg_height;
				if(s.block.icon_bg_radius)style['--icon-bg-radius']=s.block.icon_bg_radius;
				if(s.block.icon_margin)style['--icon-margin']=s.block.icon_margin;
				if(s.block.icon_padding)style['--icon-bg-padding']=s.block.icon_padding;
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
.root{display:flex;flex-wrap:wrap;margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);background:var(--block-bg);font-size:0.9rem;line-height:1em;}
.icon-nav-item{width:calc(var(--icon-bg-width) - var(--icon-margin-left) - var(--icon-margin-right));height:var(--icon-bg-height);margin:var(--icon-margin);padding:var(--icon-padding);border-radius:var(--icon-bg-radius);text-align:center;box-sizing:border-box;}
.icon-nav-icon{display:inline-block;width:'100%';border-radius:var(--icon-radius);font-size:var(--icon-width,2rem);line-height:var(--icon-height,1em);text-align:center;margin:auto;}
.icon-nav-img{display:inline-block;width:var(--icon-width,2rem);height:var(--icon-height,2rem);border-radius:var(--icon-radius);margin:auto;}
.icon-nav-text{font-size:var(--block-fontsize,0.8rem);color:var(--text-color,#000)}
</style>