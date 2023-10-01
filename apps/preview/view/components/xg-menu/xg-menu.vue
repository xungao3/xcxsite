<template name="xg-menu">
	<view :class="classnames" :style="root">
		<view class="menu-padding"></view>
		<view class="menu-pos block block-menu" :data-bid="block.bid" :data-block="block.block">
			<view class="menu">
				<view v-for="item,index in items" :class="['item',linkthis(item.link)?' cur':'']" @click="linkclick" :data-link="item.link" :data-pagename="pagename">
					<view v-if="item.icon" :class="['icon','xg-icon',item.icon]"></view>
					<image v-if="item.img" class="img" mode="aspectFit" :src="fileurl(item.img)"></image>
					<view class="text">{{item.name}}</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-menu",
				items:[]
			}
		},
		methods:{
			init:function(){
				const s=this;
			},
			render:function(){
				const s=this;
				s.items=s.block.data;
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
.menu-padding{height:3.15rem;}
.menu-pos{position:fixed;width:100%;left:0;bottom:0;}
.menu{display:flex;background-color:var(--block-bg,#fff);border-top:solid 1px var(--theme-color);margin:var(--block-margin);border-radius:var(--block-radius);font-size:0.9rem;}
.menu .item{display:block;flex:1;padding:0.2rem;text-align:center;color:var(--text-color)}
.menu .item.cur{display:block;flex:1;padding:0.2rem;text-align:center;}
.menu .item .icon{font-size:1.5rem;line-height:1em;}
.menu .item.cur .text,.menu .item.cur .icon{color:var(--theme-color);}
</style>