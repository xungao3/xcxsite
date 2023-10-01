<template>
	<view :class="classnames">
		<view :style="root" class="sidebar">
			<scroll-view class="bar-left" scroll-y="true" scroll-with-animation :scroll-into-view="'tab-'+curtab">
				<view class="list" v-if="barcates.length" v-for="(item,index) in barcates" :key="index" @click="tabclick(index)" :id="'tab-'+index">
					<view class="item" :class="{active:curtab==index}">{{item.text}}</view>
				</view>
			</scroll-view>
			<view class="bar-right">
				<view class="bar-right-top">
					<xg-checkbox-picker ref="picker" :showbtn="0" mode="column" :datas="option" @change="opchange" v-if="option.length>1"></xg-checkbox-picker>
					<xg-checkbox :theme_color="block.theme_color" :multiple="0" :showicon="0" :value="opvalue" mode="line" :datas="option[0]" @change="opchange" v-if="option.length===1"></xg-checkbox>
				</view>
				<view>
					<scroll-view class="scroll-view" @scrolltolower="reachbottom" scroll-y="true" @scroll="scroll" :scroll-into-view="'bar-'+curitem" scroll-with-animation>
						<view v-if="group" v-for="(item,index) in conts" :key="index" :id="'bar-'+index">
							<view class="title" v-if="item.title">{{item.title}}</view>
							<view class="list">
								<view class="item" v-for="(child,index2) in item.children" :key="index2" @click="linkclick" :data-link="linkdata(child,1)">
									<image class="image" mode="widthFix" :src="fileurl(child.img||child.pic)"></image>
									<text class="text">{{child.title}}</text>
								</view>
							</view>
						</view>
						<view v-if="!group" class="list">
							<view class="item" v-for="(item,index) in conts" :key="index" @click="linkclick" :data-link="linkdata(item,1)">
								<image class="image" mode="widthFix" :src="fileurl(item.img||item.pic)"></image>
								<text class="text">{{item.title}}</text>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default {
		mixins:[mixin],
		data(){
			return{
				xgname: "xg-sidebar",
				curtab:0,
				curitem:0,
				barcates:[],
				catekey:'',
				conts:[],
				taburl:'',
				option:[],
				page:1,
				opdata:{},
				pid:0,
				opvalue:0,
				keywords:'',
				group:false,
			}
		},
		mounted(){
			const s=this;
			s.xginit();
		},
		methods:{
			tabclick:function(index){
				const s=this;
				s.curtab=index;
				s.opdata={};
			let firstid=s.opdata[s.topkey]=s.barcates[index].value;
				if(s.conts.length>1)s.curitem=index;
				s.page=1;
				s.conts=[];
				const pid=s.pid;
				const option=[];
				for(let j in s.cates){
					if(firstid==s.cates[j].pid){
						option[0]=option[0]||[];
						option[0].push({text:s.cates[j].title,value:s.cates[j].id});
					}
				}
				s.option=option;
				s.opvalue=firstid;
				s.opchange({key:s.cidname,value:firstid});
			},
			opchange:function(e){
				const s=this;
				s.page=1;
				s.conts=[];
				if(!s.isobj(e)){
					s.opdata[s.topkey]=e;
				}else if(e.value){
					s.opdata[e.key]=e.value;
				}else if(e.key){
					delete s.opdata[e.key];
				}
				s.keywords='';
				s.getconts();
			},
			getconts:function(){
				const s=this;
				if(!s.taburl){
					s.taburl=s.url('app/index/data');
				}
				const data={};
				if(s.keywords)data.keywords=s.keywords;
				data.page=s.page;
				for(let i in s.opdata){
					data[i]=s.opdata[i];
				}
				data['sys']=s.sys;
				s.request({url:s.taburl,data:data,success:function(res){
					res=res.data||res;
					if(s.isarr(res)){
						for(let i in res){
							s.conts.push(res[i]);
						}
					}
				}});
			},
			scroll:function(){
				const s=this;
				// s.$nextTick(()=>{
				//     s.$refs.picker.hide();
				// });
			},
			render:function(){
				const s=this;
				let isinit=false;
				if(!s.barcates.length){
					isinit=true;
				}
				s.topkey=s.cidname;
				if(s.block.sidebar_source=='costom')s.pid=s.block.sidebar_data;
				if(s.block.sidebar_source=='opcate')s.pid=s.options.cid;
				if(s.block.sidebar_source=='cate'){
					if(s.options.cid){
						s.pid=s.options.cid;
					}else{
						s.pid=s.block.sidebar_data;
					}
				}
				if(s.pid){
					const barcates=[];
					for(let i in s.cates){
						if(s.cates[i].pid==s.pid){
							barcates.push({text:s.cates[i].title,value:s.cates[i].id,order:s.cates[i].order});
						}
					}
					barcates.sort(function(a, b) {
						return a.order - b.order;
					});
					s.barcates=barcates;
				}
				if(isinit){
					if(s.barcates.length)s.tabclick(0);
					s.o(`xg-search`,function(keywords){
						if(s.keywords!=keywords){
							s.conts=[];
							s.page=1;
							s.g.options.page=1;
							s.keywords=keywords;
						}
						s.getconts();
					});
				}
			},
			reachbottom:function(){
				const s=this;
				s.page++;
				s.getconts();
			}
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				if(s.block.bar_width)style['--bar-width']=s.block.bar_width;
				if(s.block.sidebar_width)style['--bar-width']=s.block.sidebar_width;
				
				if(s.block.sidebar_holder){
					style['--bar-holder']=s.block.sidebar_holder;
				}else{
					style['--bar-holder']='0px';
				}
				if(s.option.length>=1){
					style['--bar-holder-option']='2.4rem';
				}else{
					style['--bar-holder-option']='0px';
				}
				style['--bar-holder-top-bar']='0px';
				// #ifdef H5
				if(!s.inxg())style['--bar-holder-top-bar']='40px';
				// #endif
				// if(s.block.bar_item_height)style['--bar-item-height']=s.block.bar_item_height;
				// if(s.block.bar_height)style['--bar-height']=s.block.bar_height;
				// if(s.block.right_bar_height)style['--right-bar-height']=s.block.right_bar_height;
				if(s.block.bar_item_fontsize)style['--bar-item-fontsize']=s.block.bar_item_fontsize;
				return style;
			}
		}
	}
</script>

<style scoped>
.sidebar{
	--bar-height:calc(100vh - var(--bar-holder) - var(--bar-holder-top-bar));
	--bar-right-height:calc(var(--bar-height) - var(--bar-holder-option));
	display:flex;width:100%;height:var(--bar-height,100%);overflow:hidden;
}
.bar-left{width:var(--bar-width,10rem);height:var(--bar-height,100%);background:var(--theme-color2,#f7f7f7);padding-top:0.25rem;box-sizing:border-box;}
.bar-left .item{display:flex;padding-left:1.2rem;height:var(--bar-item-height,2.5rem);font-size:var(--bar-item-fontsize,0.85rem);line-height:var(--bar-item-height,2.5rem);}
.bar-left .item.active{padding-left:0.95rem;border-left:solid 0.25rem var(--theme-color);color:var(--theme-color);}
.bar-right{width:calc(100% - var(--bar-width,10rem));}
.bar-right .title{position:relative;margin-top:1rem;padding:0.5rem;text-align:center;}
.bar-right .scroll-view{height:var(--bar-right-height,var(--bar-height,100%));}
.bar-right .list{display:flex;flex-wrap:wrap;padding:var(--item-padding,0.25rem)}
.bar-right .item{width:50%;box-sizing:border-box;padding:var(--item-padding);padding:var(--item-padding,0.25rem)}
.bar-right .item .image{display:block;width:100%;border-radius:0.5rem;}
.bar-right .item .text{display:block;margin-top:0.5rem;text-align:center;font-size:0.85rem;}
.bar-right-2{width:100%;border-radius:0;height:100%;}
.bar-right-2 .item{display:block;width:100%;border-radius:0;}
.bar-right-2 .image{display:block;width:100%;border-radius:0;}
</style>