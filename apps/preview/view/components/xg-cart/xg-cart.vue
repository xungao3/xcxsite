<template name="xg-cart">
<view :style="root" :class="classnames">
	<view class="cart">
		<view class="cart-title">{{block.cart_title}}</view>
		<block v-for="item,index in list">
			<view class="item">
				<view class="cover"><image class="image" :src="item.pic" mode="widthFix"></image></view>
				<view class="info">
					<view class="title">{{item.title}}</view>
					<view class="price">{{item.price}}</view>
					<!-- <view class="attrs">
						<view class="attr" v-for="attr,attri in item.attrs">{{attr}}</view>
					</view> -->
				</view>
			</view>
		</block>
	</view>
</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-cart",
				list:[],
				count:0,
				size:0,
			}
		},
		methods:{
			init:function(){
				const s=this;
				s.request({url:s.url('index/cart_items',{sys:s.sys}),success:function(res){
					s.list=res.list;
				}});
			},
			submit:function(e){
				const s=this;
				s.request({url:s.url('index/buy',{sys:s.sys}),data:data,success:function(res){
					if(res.ok===true){
						s.content='';
						s.list=res.list;
					}else{
						s.showmsg(res.msg);
					}
				},method:'POST'});
			}
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
.cart{margin:var(--block-margin);padding:var(--block-padding);border:var(--block-border);border-radius:var(--block-radius);background:var(--block-bg);}
.item{display:flex;}
.cover{width:30%;}
.cover .image{width:100%;}
.info{width:calc(70% - 1rem);margin-left:1rem;}
</style>