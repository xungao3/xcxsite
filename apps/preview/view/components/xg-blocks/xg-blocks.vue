<template name="blocks">
	<view class="blocks" :class="['blocks-view block',((block.bid||dataBid)?'blocks-'+(block.bid||dataBid):''),(block&&block.blocks_display?' block-item-'+block.blocks_display:''),classnames]" :data-bid="block.bid||dataBid" :data-block="block.block||dataBlock" :style="[root]" @click="linkclick">
		<block v-for="blocki,index in blocks" v-if="showself">
			<xg-blocks @click="linkclick" class="block block-blocks" v-if="blocki.block=='blocks'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-blocks>
			<xg-region class="block block-region" v-if="blocki.block=='region'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-region>
			<xg-form class="block block-form" v-if="blocki.block=='form'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-form>
			<xg-input class="block block-input" v-if="blocki.block=='input'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-input>
			<xg-picker class="block block-picker" v-if="blocki.block=='picker'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-picker>
			<xg-list @click="linkclick" class="block block-list" v-if="blocki.block=='list'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-list>
			<xg-popup @click="linkclick" class="block block-popup" v-if="blocki.block=='popup'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-popup>
			<xg-nav class="block block-nav" v-if="blocki.block=='nav'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-nav>
			<xg-icon-nav class="block block-icon-nav" v-if="blocki.block=='icon-nav'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-icon-nav>
			<xg-search-bar class="block block-search-bar" v-if="blocki.block=='search-bar'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-search-bar>
			<xg-slide class="block block-slide" v-if="blocki.block=='slide'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-slide>
			<xg-sidebar class="block block-sidebar" v-if="blocki.block=='sidebar'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-sidebar>
			<xg-cate-box class="block block-cate-box" v-if="blocki.block=='cate-box'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-cate-box>
			<xg-cate-boxes class="block block-cate-boxes" v-if="blocki.block=='cate-boxes'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-cate-boxes>
			<xg-info-list class="block block-info-list" v-if="blocki.block=='info-list'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-info-list>
			<xg-html class="block block-html" v-if="blocki.block=='html'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-html>
			<xg-custom class="block block-custom" v-if="blocki.block=='custom'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-custom>
			<xg-icon class="block block-icon" v-if="blocki.block=='icon'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-icon>
			<xg-comment class="block block-comment" v-if="blocki.block=='comment'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-comment>
			<xg-cart class="block block-cart" v-if="blocki.block=='cart'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-cart>
			<xg-btns class="block block-btns" v-if="blocki.block=='btns'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-btns>
			<xg-btn class="block block-btn" v-if="blocki.block=='btn'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-btn>
			<xg-checkbox class="block block-checkbox" v-if="blocki.block=='checkbox'||blocki.block=='radio'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-checkbox>
			<xg-cate-list class="block cate-list" v-if="blocki.block=='cate-list'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-cate-list>
			<xg-img-nav class="block img-nav" v-if="blocki.block=='img-nav'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-img-nav>
			<xg-options class="block options" v-if="blocki.block=='options'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-options>
			<xg-image class="block image" v-if="blocki.block=='image'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-image>
			<xg-web class="block web" v-if="blocki.block=='web'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-web>
			<xg-video class="block video" v-if="blocki.block=='video'" :data-bid="blocki.bid" :sysdata="sys" :data-block="blocki.block" :blockdata="blocki" :contdata="cont" :userdata="user" :contsdata="conts" :blocksdata="blocki.blocks" :cateid="block.cateid||cateid" :theme_color="block.theme_color||theme_color"></xg-video>
		</block>
		<xg-menu class="block block-menu" v-if="menu" :data-bid="menu.bid" :data-block="menu.block" :blockdata="menu"></xg-menu>
		<block v-for="popup,index in popups">
			<xg-popup ref="popup" :blocks="popup.blocks"></xg-popup>
		</block>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js'
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname:"xg-blocks",
				popups:[],
				blocks:[],
				menu:null,
				showloading:false,
				data_requested_url:'',
			}
		},
		props:{
			isloop:Boolean,
			blocksfrom:String,
			blocksdata: Array,
			dataBlock:String,
			dataBid:[String,Number],
			style:[String,Array,Object]
		},
		watch:{
			blocksdata:{
				handler(n,o){
					const s=this;
					for(let i in n){
						if(n[i].block=='menu'){
							s.menu=n[i];
							break;
						}
					}
					s.blocks=n;
					s.hooks('blocks-mutated',s);
				},
				immediate:true,
				deep:true
			},
		},
		methods:{
			init:function(){
				const s=this;
				if(s.blocksfrom=='main'){
					s.g.box=s.box;
					s.classnames.push('compiling-block');
				}
			},
			render:function(){
				const s=this;
				s.dataurl();
			},
			box:function(data){
				const s=this;
				const index=s.popups.length;
				s.popups.push(data);
				setTimeout(function(){
					s.$refs['popup'][index].open();
				},200);
				return function(){
					return s.$refs['popup'][index];
				};
			}
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				if(s.block.blocks_display=='flex')style['--blocks-display']='flex';
				if(s.block.blocks_display=='block')style['--blocks-display']='block';
				if(s.block.blocks_display=='flex-col'){
					style['--blocks-display']='flex';
					style['--blocks-direction']='column';
				}
				if(s.block.blocks_display=='justify'){
					style['--blocks-display']='flex';
					style['--blocks-justify-content']='space-between';
					style['--blocks-direction']='none';
				}
				for(let name in style){
					let name2=name.replace('--block-','--blocks-');
					if(name2!==name){
						style[name2]=style[name];
						delete style[name];
					}
				}
				if(!style['--blocks-margin'])style['--blocks-margin']='0px';
				if(!style['--blocks-padding'])style['--blocks-padding']='0px';
				return style;
			}
		},
		mounted:function(){
			const s=this;
			s.xginit();
			s.init();
		},
		onShareAppMessage(){
			const s=this;
			//return {title:s.cont.title,path:'/pages/article?id='+s.id}
		},
	}
</script>

<style scoped>
.blocks-view{overflow:hidden;}
.blocks{display:var(--blocks-display,block);width:var(--blocks-width);height:var(--blocks-height);flex-direction:var(--blocks-direction);margin:var(--blocks-margin);padding:var(--blocks-padding);border:var(--blocks-border);border-width:var(--blocks-border-width);border-radius:var(--blocks-radius);background:var(--blocks-bg);justify-content:var(--blocks-justify-content);overflow:hidden;box-sizing:border-box;}
</style>