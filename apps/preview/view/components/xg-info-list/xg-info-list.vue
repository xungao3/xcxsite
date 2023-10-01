<template name="info-list">
	<view :class="classnames" :style="root">
		<view :class="'xg-box xg-box-'+block.box_theme+''" :style="box_style.box">
			<view v-if="block.list_theme!='e'||block.title" class="xg-box-title xg-bg xg-clear" :style="box_style.title">
				<view :style="box_style.title_text">{{block.title||'内容列表'}}</view>
			</view>
			<view class="xg-box-cont" :class="'xg-cont-'+block.list_theme" :style="box_style.content">
				<view class="xg-row" v-if="conts&&((isobj(conts)&&!iseobj(conts))||(isarr(conts)&&conts.length))">
					<xg-list-a class="xg-list-a" v-if="block.list_theme=='a'" :blockdata="block" :list="conts" :cont_style="cont_style"></xg-list-a>
					<xg-list-b v-if="block.list_theme=='b'" :blockdata="block" :list="conts" :cont_style="cont_style"></xg-list-b>
					<xg-list-c v-if="block.list_theme=='c'" :blockdata="block" :list="conts" :cont_style="cont_style"></xg-list-c>
					<xg-list-d v-if="block.list_theme=='d'" :blockdata="block" :list="conts" :cont_style="cont_style"></xg-list-d>
					<xg-list-e class="xg-list-e" v-if="block.list_theme=='e'" :blockdata="block" :list="conts" :cont_style="cont_style"></xg-list-e>
				</view>
				<view v-if="!conts||!conts.length" class="xg-mg-5 xg-center">无内容</view>
				<xg-page v-if="block.pagenav=='link'&&countdata" :theme="block.theme_color" :countdata="countdata" :pagesize="size" :style="{margin:pmargin}"></xg-page>
			</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js'
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-info-list",
				catetitle:'',
				size:0,
				pmargin:'',
				cont_style:{},
				box_style:{},
				ops:{},
				requested:false,
				keywords:'',
				countdata:0,
			}
		},
		methods:{
			render:function(){
				const s=this;
				s.size=s.block.pagesize;
				s.cont_style=(s.block.cont_list_style||{});
				s.box_style=(s.block.box_style||{});
				if(s.cid&&!s.requested){
					s.countdata=s.count;
					s.getconts();
				}
				s.o(`options-change-${s.cid}`,function(ops){
					if(s.requested)return;
					s.conts=[];
					s.ops=ops;
					s.getconts(s.ops);
				});
				s.o(`xg-search`,function(keywords){
					if(s.keywords!=keywords){
						s.conts=[];
						s.page=1;
						s.g.options.page=1;
						s.keywords=keywords;
					}
					s.request({url:s.url('app/index/search',{sys:s.sys,cid:s.cid,page:s.page,pagesize:s.block.pagesize,keywords}),success:function(data){
						s.countdata=0;
						s.conts=s.bottominfo(data.data);
					},dataType:'json'});
				});
				s.o(`reachbottom`,function(){
					s.page++;
					s.countdata=0;
					s.getconts(s.ops);
				});
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
.xg-list-e{width:100%;}
.xg-box-a .xg-box-title,.xg-box-b .xg-box-title{background-color:var(--theme-color)!important;font-size:0.9rem;line-height:2.3rem;height:2.3rem;}
.xg-box-c .xg-box-title{display:none;}
.xg-box{margin:var(--block-margin);border-radius:var(--block-radius);height:var(--height);overflow:hidden;}
.xg-box-a,.xg-box-b{border-radius:0.5rem;}
.xg-box-a .xg-box-cont{margin-top:0.5rem;padding:0.25rem;background-color:var(--block-bg,rgba(0,0,0,0.1));}
.xg-box-b .xg-box-cont{background-color:var(--block-bg,rgba(0,0,0,0.1));}
.xg-list-a{display:flex;width:100%;max-width:100%;flex-wrap:wrap;}
</style>