<template name="xg-img-nav">
	<view :class="classnames">
		<scroll-view scroll-x="true">
			<view v-if="items.length" class="img-nav" :style="['--items-count:'+items.length,root]">
				<view class="img-nav-items">
					<view v-for="item,index in items" class="img-nav-item" @click="linkclick" :data-link="linkdata(item,1)">
						<image v-if="item.img" class="img-nav-img" mode="widthFix" :src="item.img"></image>
						<view class="img-nav-title">{{item.title}}</view>
					</view>
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-img-nav",
				cur:null,
				items:[]
			}
		},
		methods:{
			render:function(){
				const s=this;
				const items=[];
				if(s.block.custom){
					for(let i in s.block.custom){
						items.push(s.block.custom[i]);
					}
				}
				if(s.block.list){
					for(let i in s.block.list){
						items.push(s.block.list[i]);
					}
				}
				if(items){
					for(let i in items){
						items[i].img=s.fileurl(items[i].img||items[i].pic);
					}
					s.items=items;
				}
			},
			navcur:function(d){
				const s=this;
				return 'id-'+s.md5(JSON.stringify(s.linkdatastr(d)));
			},
			navthis:function(t){
				const s=this;
				return s.linkthis(t);
			}
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				if(s.block.wrap)style['--block-wrap']=s.block.wrap>0?'wrap':'nowrap';
				if(s.block.img_width)style['--img-width']=s.block.img_width;
				if(s.block.img_height)style['--img-height']=s.block.img_height;
				if(s.block.img_margin)style['--img-margin']=s.block.img_margin;
				if(s.block.img_padding)style['--img-padding']=s.block.img_padding;
				if(s.block.img_radius)style['--img-radius']=s.block.img_radius;
				if(s.block.item_width)style['--item-width']=s.block.item_width;
				if(s.block.item_height)style['--item-height']=s.block.item_height;
				if(s.block.item_margin)style['--item-margin']=s.block.item_margin;
				if(s.block.item_padding)style['--item-padding']=s.block.item_padding;
				if(s.block.item_radius)style['--item-radius']=s.block.item_radius;
				return style;
			}
		},
		mounted:function(){
			const s=this;
			s.xginit();
			s.render();
		},
	}
</script>

<style scoped>
.img-nav{display:block;width:var(--block-width);height:var(--block-height,auto);margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);background-color:var(--block-bg-color);font-size:0.9rem;line-height:1em;box-sizing:border-box;}
.img-nav-items{display:flex;flex-wrap:var(--block-wrap,wrap);box-sizing:border-box;}
.img-nav-item{display:flex;flex-direction:column;width:var(--item-width);height:var(--item-height,auto);margin:var(--item-margin);padding:var(--item-padding);border-radius:var(--item-radius);box-sizing:border-box;box-sizing:border-box;}
.img-nav-img{display:block;width:var(--img-width,10rem);height:var(--img-height,auto);margin:var(--img-margin);padding:var(--img-padding);border-radius:var(--img-radius);box-sizing:border-box;}
.img-nav-title{text-align:center;margin-top:0.5rem;}
</style>