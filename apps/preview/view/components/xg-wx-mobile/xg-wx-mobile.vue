<template name="xg-wx-mobile">
	<view class="wx-mobile" :class="classnames" v-if="showing">
		<view class='header'><image src='/static/images/mobile-login.png'></image></view>
		<view class='content'><text>请允许获取你的手机号</text></view>
		<button class='bottom' type='primary' @getphonenumber="wxmobile" open-type="getPhoneNumber">授权获取</button>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-wx-mobile",
				showing:false,
			}
		},
		props:{
			show:Boolean
		},
		watch:{
			show:{
				handler(n,o){
					const s=this;
					s.showing=n;
				},
				immediate:true,
			},
		},
		methods:{
		},
		mounted:function(){
			const s=this;
			s.xginit();
		}
	}
</script>

<style scoped>
.wx-mobile{
	position: fixed;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: #fff;
	z-index: 99999999;
}
.header {
	margin: 150rpx auto 0rpx auto;
	padding: 70rpx 0;
	border-bottom: 1px solid #e5e5e5;
	text-align: center;
	width: 650rpx;
}

.header image {
	width: 200rpx;
	height: 200rpx;
}

.content {
	margin-bottom: 50rpx;
	text-align: center;
}

.content text {
	display: block;
	color: #9d9d9d;
	margin-top: 40rpx;
}

.bottom {
	border-radius: 80rpx;
	margin: 70rpx 50rpx;
	font-size: 35rpx;
}
</style>