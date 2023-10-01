<template name="xg-list-c">
	<view v-for="item,index in list" @click="linkclick" :data-link="linkdata(item,1)" class="xg-col-3 xg-col-6 xg-n-col-12" :class="classnames" :style="cont_style.box">
		<view class="title" :style="cont_style.title"><view class="info-left" v-if="item.left">{{item.left}}</view><view class="info-info xg-omit">{{item.title}}</view><view class="info-right" v-if="item.right">{{item.right}}</view></view>
		<view class="desc" :style="cont_style.desc" v-if="block.show_desc>0">{{item.description}}</view>
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
				xgname: "xg-list-c",
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
.title{display:flex;margin:0.2rem;padding:0.2rem;font-size:0.9rem;background-color:rgba(255,255,255,0.3);border-radius:0.4rem;}
.info-info{flex:1;}
.info-info,.info-left,.info-right{margin:0.2rem;}
.desc{font-size:0.7rem;line-height:1.3em;max-height:3.9em;flex:1;color:#999;}
</style>