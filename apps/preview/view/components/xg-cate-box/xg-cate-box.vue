<template name="cate-box">
	<view :class="classnames" :style="root">
		<view :class="['xg-box','xg-box-'+block.box_theme]" :style="box_style.box">
			<view v-if="block.list_theme!='e'||title||block.title" class="xg-box-title xg-bg xg-clear" :style="box_style.title">
				<view class="xg-fl" :style="box_style.title_text">{{title||block.title}}</view>
				<view :style="box_style.title_text" v-if="block.toplink" class="more" @click="linkclick" :data-link="linkdata(block.toplink,1)">更多</view>
			</view>
			<view class="xg-box-cont" :class="'xg-cont-'+block.list_theme" :style="box_style.content">
				<view class="xg-row">
					<xg-list-a class="xg-list-a" v-if="block.list_theme=='a'" :blockdata="block" :list="list" :cont_style="cont_style"></xg-list-a>
					<xg-list-b v-if="block.list_theme=='b'" :blockdata="block" :list="list" :cont_style="cont_style"></xg-list-b>
					<xg-list-c v-if="block.list_theme=='c'" :blockdata="block" :list="list" :cont_style="cont_style"></xg-list-c>
					<xg-list-d v-if="block.list_theme=='d'" :blockdata="block" :list="list" :cont_style="cont_style"></xg-list-d>
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
				xgname: "xg-cate-box",
				title: "",
				list:[],
				cont_style:{},
				box_style:{}
			}
		},
		methods:{
			render:function(){
				const s=this;
				if(s.block.list_url){
					s.request({url:s.url(s.block.list_url),success:function(data){
						const list=s.bottominfo(data.list||data);
						s.list=list;
					},dataType:'json'});
				}else if(s.cateid){
					const block=s.block;
					const name=block.bid+'-'+s.cateid;
					const file=s.cachepath(name,'custom',s.sys);
					const param={cid:s.cateid,bid:s.block.bid,sys:s.sys,count:s.block.show_count};
					s.request({url:s.url(file,param),success:function(data){
						if(s.isarr(data)){
							const list=s.bottominfo(data.list||data);
							s.list=list;
						}else if(s.isobj(data)&&data.block=='cate-box'){
							s.block=data;
						}
					}});
					s.title=s.cates[s.cateid].title;
				}else{
					if(s.block.toplink){
						if(s.block.toplink[0]){
							s.block.toplink=s.block.toplink[0];
						}
					}
					const list=s.bottominfo(s.block.list);
					s.list=list;
				}
				s.cont_style=(s.block.cont_list_style||{});
				s.box_style=(s.block.box_style||{});
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
		},
	}
</script>

<style scoped>
.xg-cont-c,.xg-cont-d{padding:0.2rem;}
.xg-box-a .xg-box-title,.xg-box-b .xg-box-title{background-color:var(--theme-color)!important;font-size:0.9rem;line-height:2.3rem;height:2.3rem;}
.xg-box{margin:var(--block-margin);border-radius:var(--block-radius);height:var(--height);overflow:hidden;}
.xg-box-a,.xg-box-b{border-radius:0.5rem;}
.xg-box-a .xg-box-cont{margin-top:0.5rem;padding:0.25rem;background-color:var(--block-bg,rgba(0,0,0,0.1));}
.xg-box-b .xg-box-cont{background-color:var(--block-bg,rgba(0,0,0,0.1));}
.xg-list-a{display:flex;width:100%;max-width:100%;flex-wrap:wrap;}
.more{float:right;margin-right:1rem;}
</style>