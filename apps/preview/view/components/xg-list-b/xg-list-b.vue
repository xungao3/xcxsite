<template name="xg-list-b">
<view v-for="item,index in list" @click="linkclick" :data-link="linkdata(item,1)" class="info" :class="classnames" :style="[root,cont_style.box]">
	<view class="left" :style="cont_style.img_box"><image :style="cont_style.img" class="image" mode="widthFix" :src="fileurl(item.pic||item.img)" /></view>
	<view class="right">
		<view class="title" :style="cont_style.title">{{item.title}}</view>
		<view class="desc" :style="cont_style.desc" v-if="block.show_desc>0">{{item.description||item.desc}}</view>
		<view class="xg-clear bottom">
			<view class="xg-fl" :style="cont_style.bottom">{{item.left}}</view>
			<view class="xg-fr" :style="cont_style.bottom">{{item.right}}</view>
		</view>
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
			return{
				xgname:"xg-list-b",
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
				return style;
			}
		},
		mounted:function(){
			const s=this;
			s.xginit();
		},
	}
</script>

<style scoped>
.info{
	display:flex;
	justify-content:space-between;
	width:100%;
	margin:0 0.3rem 0.7rem 0.3rem;
	box-sizing:border-box;
}
.left{
	display:block;
	position:relative;
	overflow:hidden;
	float:left;
	width:40%;
	max-height:var(--block-img-height,6rem);
	border-radius:0.3rem;
}
.left .image{
	display:block;
	width:100%;
	height:var(--block-img-height,auto);
	border-radius:0.3rem;
}
.right{
	display:flex;
	flex-direction:column;
	justify-content:space-between;
	width:calc(60% - 0.7rem);
	height:100%;
	margin-left:0.7rem;
}
.right.nopic{
	width:100%;
}
.right .title{
	display:block;
	padding:0.2rem 0;
	word-wrap:break-word;
	word-break:break-all;
	white-space:normal;
	font-size:0.9rem;
	line-height:1.3em;
}
.right .desc{
	word-wrap:break-word;
	word-break:break-all;
	white-space:normal;
	overflow:hidden;
	font-size:0.7rem;
	line-height:1.3em;
	max-height: 3.9em;
	flex:1;
	color:#999;
}
.bottom{
	color:#999;
	font-size:0.7rem;
	line-height:1.3rem;
}
</style>