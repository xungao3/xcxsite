<template name="comment">
<view :style="root" :class="classnames">
	<view :class="['xg-box','xg-box-a']">
		<view class="xg-box-title xg-bg xg-clear">{{block.cmt_title}}</view>
		<view class="xg-box-cont cmt-list" :style="(block.bg_color?'background-color:'+block.bg_color+';':'')">
			<block v-for="item,index in list">
				<view class="item">
					<view class="item-top xg-clear">
						<view class="xg-fl icon xg-icon xg-icon-signout"></view>
						<view class="xg-fl time">{{item.time}}</view>
					</view>
					<view class="item-cont">{{item.content}}</view>
				</view>
			</block>
			<view class="form">
				<textarea v-model="content" name="content"></textarea>
				<button class="xg-btn xg-btn-s" @click="submit">提交</button>
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
				xgname: "xg-comment",
				list:[],
				catetitle:'',
				count:0,
				size:0,
				pmargin:'',
				content:''
			}
		},
		methods:{
			init:function(){
				const s=this;
				s.request({url:s.url('index/comments',s.extend(s.urldata(s.options),{sys:s.sys})),success:function(res){
					s.list=res.list;
				}});
			},
			submit:function(e){
				const s=this;
				var content=s.content;
				var data={content:content};
				s.request({url:s.url('index/comment',s.extend(s.urldata(s.options),{sys:s.sys})),data:data,success:function(res){
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
.xg-cont-c,.xg-cont-d{padding:0.2rem;}
.xg-box-a .xg-box-title,.xg-box-b .xg-box-title{background-color:var(--theme-color)!important;font-size:0.9rem;line-height:2.3rem;height:2.3rem;}
.xg-box{margin:var(--block-margin);border-radius:var(--radius);height:var(--height);overflow:hidden;}
.xg-box-a,.xg-box-b{border-radius:0.5rem;}
.xg-box-a .xg-box-cont{margin-top:0.5rem;background-color:var(--block-bg,rgba(0,0,0,0.1));}
.xg-box-b .xg-box-cont{background-color:var(--block-bg,rgba(0,0,0,0.1));}
.cmt-list{margin-bottom:0.5rem;overflow:hidden;}
.cmt-list .item{margin-bottom:0.3rem;padding:0.3rem;overflow:hidden;background:rgba(255,255,255,0.2);border-radius:0.3rem;}
.cmt-list .item-top{margin-bottom:0.1rem;}
.cmt-list .item-top .xg-icon{height:1rem;line-height:1.4rem;}
.cmt-list .item-cont{word-break:break-all;}
.form textarea{width:100%;height:5rem;margin-top:0.5rem;margin-bottom:0.5rem;padding:0.5rem;box-sizing:border-box;background:#fff;border:solid 1px #ccc;border-radius:0.4rem;}
.form button{margin-bottom:0.25rem;background:var(--theme-color);}
</style>