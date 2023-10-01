<template name="xg-wx-login">
<view class="wx-login" :class="classnames" v-if="showing">
	<view class='header'><image src='/static/images/wx-login.png'></image></view>
	<view class='content'>
		<view>申请获取以下权限</view>
		<text>获得你的公开信息(昵称，头像等)</text>
	</view>
	<button class='bottom' type='primary' v-if="canIUseGetUserProfile" @click="getUserProfile">授权登录</button>
	<button class='bottom' type='primary' @getuserinfo="wxlogin" open-type="getUserInfo" v-else>授权登录</button>
</view>
<xg-wx-mobile :show="mobileshowing"></xg-wx-mobile>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-wx-login",
				canIUseGetUserProfile:false,
				code:'',
				showing:false,
				mobileshowing:false,
			}
		},
		props:{
			show:Boolean,
			showmobile:Boolean,
		},
		watch:{
			show:{
				handler(n,o){
					const s=this;
					s.showing=n;
				},
				immediate:true,
			},
			showmobile:{
				handler(n,o){
					const s=this;
					s.mobileshowing=n;
				},
			},
		},
		methods:{
			getUserProfile(e) {
				const s = this;
				// s.getcount++;
				// if (s.getcount > 200) {
				// 	s.msg('请点击右上角三个点的按钮，然后点击“重新进入小程序”！');
				// 	return;
				// }
				wx.getUserProfile({
					lang: 'zh_CN',
					desc: '用来获取用户信息', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
					success: function(e) {
						// if(!e.encryptedData){
						//   s.request({
						//     url: s.url('vote/errlog',{sid:app.sid}),
						//     method:'POST',
						//     data:{errname:'signerr',errdata:JSON.stringify(e)}
						//   })
						// }
						s.request({
							url: s.url('user/wxlogin'),
							method: 'POST',
							data: {
								appid: s.c.wxappid||s.c.appid,
								encryptedData: e.encryptedData,
								signature: e.signature,
								rawData: e.rawData,
								iv: e.iv,
								code: s.code,
								userinfo: JSON.stringify(e.userInfo)
							},
							success: function(res) {
								if (res.ok) {
									uni.$emit('userinfo',res.user);
									s.showing=false;
								}else if(res.msg){
									s.msg(res.msg);
								}
							}
						});
					},
					fail: function(res) {
						console.log(res);
					}
				});
			},

			initwxlogin: function() {
				const s = this;
				if (wx.getUserProfile) {
					s.canIUseGetUserProfile=true;
					wx.login({
						success: function(res) {
							if (res.code) {
								s.code = res.code;
							}
						}
					});
				}
			},

			wxlogin: function(e) {
				const s = this;
				wx.login({
					success: function(res) {
						wx.getUserInfo({
							withCredentials: true,
							lang: 'zh_CN',
							success: function(e) {
								console.log(e);
								s.request({
									url: s.url('user/wxlogin'),
									method: 'POST',
									data: {
										appid: s.c.wxappid||s.c.appid,
										encryptedData: e.encryptedData,
										signature: e.signature,
										rawData: e.rawData,
										iv: e.iv,
										code: res.code
									},
									success: function(res) {
										if (res.ok) {
											uni.$emit('userinfo',res.user);
											s.showing=false;
										} else if (res.msg) {
											s.msg(res.msg);
										}
									},
									fail: function() {
										s.msg('获取授权信息失败1')
									}
								})
							},
							fail: function() {
								s.msg('获取授权信息失败2')
							}
						});
					}
				})
			},
		},
		mounted:function(){
			const s=this;
			s.xginit();
			s.initwxlogin();
		}
	}
</script>

<style scoped>
.wx-login{
	position: fixed;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: #fff;
}

.header {
	margin: 0 auto 2rem auto;
	padding: 2rem 0;
	border-bottom: 1px solid #ccc;
	text-align: center;
	width: 19rem;
}

.header image {
	width: 6rem;
	height: 6rem;
}

.content {
	margin-bottom: 1.5rem;
	text-align: center;
}

.content text {
	display: block;
	color: #9d9d9d;
	margin-top: 1.2rem;
}

.bottom {
	width: calc(100% - 3rem);
	line-height: 2rem;
	border-radius: 2.5rem;
	margin: 2rem 1.5rem;
	font-size: 1rem;
}
</style>