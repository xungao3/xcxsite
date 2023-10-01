<template name="xg-list-a">
<view v-for="item,index in list" @click="linkclick" :data-link="linkdata(item,1)" class="xg-list-item xg-col-3 xg-m-col-4 xg-s-col-6 xg-n-col-6" :class="classnames" :style="[root,cont_style.box]">
	<view class="xg-list-pic" :style="cont_style.img_box"><image :style="cont_style.img" class="image" mode="widthFix" :src="fileurl(item.pic||item.img)" /></view>
	<view class="xg-list-title" :style="cont_style.title">{{item.title}}</view>
	<view class="xg-list-desc" :style="cont_style.desc" v-if="block.show_desc>0">{{item.description||item.desc}}</view>
	<view class="xg-clear xg-list-bottom">
		<view class="xg-fl" :style="cont_style.bottom">{{item.left}}</view>
		<view class="xg-fr" :style="cont_style.bottom">{{item.right}}</view>
	</view>
</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		components:{
		},
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-list-a",
			}
		},
		watch:{
			list:{
				handler(n,o){
					const s=this;
					s.items=n;
				},
				immediate:true,
				deep:true,
			}
		},
		props:{
			list:[Object,Array],
			cont_style:Object,
			box_style:Object
		},
		methods:{
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				if(s.block.img_height)style['--block-img-height']=s.block.img_height;
				if(s.block.desc_color)style['--block-desc-color']=s.block.desc_color;
				return style;
			},
			rootclass:function(){
				const s=this;
			}
		},
		mounted:function(){
			const s=this;
			s.xginit();
		},
	}
</script>

<style scoped>
.image{display:block;width:100%;height:var(--block-img-height);border-radius:0.25rem;overflow:hidden;}
.xg-list-item{display:flex;flex-direction:column;justify-content:space-between;padding:0.25rem;box-sizing:border-box;}
.xg-list-title{font-size:0.8rem;}
.xg-list-pic{width:100%;height:var(--block-img-height);border-radius:0.25rem;overflow:hidden;}
.xg-list-desc{font-size:0.7rem;line-height:1.3em;max-height:3.9em;flex:1;color:var(--block-desc-color,#000);overflow:hidden;}
.xg-list-bottom{font-size:0.7rem;margin-top:auto;line-height:1.3em;color:var(--block-desc-color,#000);}
.xg-list-bottom view{font-size:0.7rem;}
</style>