<template>
	<view class="root" :class="classnames" :style="root">
		<view v-if="showself" class="info" @click="pclick">
			<view v-if="opened">
				<text v-for="data,index in datas">{{(index>0&&data.text)?',':''}}{{data.text}}</text>
			</view>
			<view v-if="!opened">{{block.placeholder}}</view>
		</view>
		<xg-popup class="popup" ref="popup" :blockdata="pblock">
			<view class="menu xg-bg-white xg-pd-2 xg-clear">
				<view class="xg-btn xg-fl" @click="cancel">取消</view>
				<view class="xg-btn xg-fr" @click="submit">确定</view>
			</view>
			<view class="main">
				<view class="picker">
					<scroll-view class="view picker-scroll-view" @scroll="scroll" :scroll-top="data.top" :scroll-y="true" :scroll-x="false" v-for="data,index in datas" :ref="'scroll-view-'+index" :data-index="index" :data-dataurl="getdata[index]">
						<view v-for="item,index in data.data" :data-value="item.value" class="name">{{item.text}}</view>
					</scroll-view>
				</view>
				<view class="mask"><view class="mask-top"></view><view class="mask-bottom"></view></view>
			</view>
		</xg-popup>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js'
	export default {
		mixins:[mixin],
		props: {
			mode:String,
		},
		watch: {
			// modelValue: {
			// 	handler(n,o) {
			// 		const s=this;
			// 	},
			// 	deep: true
			// },
			// datas: {
			// 	handler(n,o) {
			// 		const s=this;
			// 		s.datas=s.dateinfo();
			// 	},
			// 	deep: true,
			// 	immediate:true,
			// },
		},
		data() {
			return {
				xgname: 'xg-picker',
				datas:[],
				inited:0,
				timer:[],
				opened:false,
				pblock:{
					popup_pos:'bottom',
					show:'block.opened',
					opened:false,
				}
			};
		},
		computed: {
			getdata:function(){
				const s=this;
				for(let i in s.urls){
					const url=s.urls[i];
					if(url){
						if(url!==s.datas[i].url)s.request({url,success:function(res){
							res=res.data||res.conts||res;
							const data=[];
							data.push({},{},{});
							data.push({text:'请选择'+s.datas[i].msg});
							for(let i in res){
								const value=res[i].id||res[i].name||data[i].text||i;
								const text=res[i].title||res[i].name||res[i];
								data.push({value,text});
							}
							data.push({},{},{});
							s.datas[i].data=data;
						},dataType:'json'});
					}else if(s.datas[i].link){
						s.datas[i].data=[];
						s.datas[i].text='';
						s.datas[i].value='';
					}
					s.datas[i].url=url;
				}
				return s.urls;
			},
			root:function(){
				const s=this;
				const style=s.mainstyles;
				return style;
			},
			urls:function(){
				const s=this;
				const urls=[];
				for(let i in s.datas){
					const data=s.datas[i];
					let url='';
					if(data.cond){
						if(s.blockcond(data.cond)){
							url=s.blockinfo(data.link+'',s);
						}
					}else{
						url=s.blockinfo(data.link);
					}
					urls.push(url);
				}
				return urls;
			}
		},
		mounted() {
			const s=this;
			s.xginit();
			s.render();
		},
		methods: {
			render(){
				const s=this;
				if(s.inited)return;
				s.inited=1;
				const datas=s.block.picker_data;
				if(s.mode=='date'){
					s.block={placeholder:'选择日期',z_index:9999};
					s.datas=s.dateinfo();
				}else if(datas){
					for(let i in datas){
						datas[i].timer=0;
						datas[i].selected=0;
						const data=datas[i];
						if(data.data){
							const datai=[];
							datai.push({},{},{});
							datai.push({text:'请选择'+data.msg});
							data.data.split(/[\r\n]+/g).map(function(v){
								if(v){
									v=v.trim().split('=');
									datai.push({text:v[1]||v[0],value:v[0]});
								}
							});
							datai.push({},{},{});
							data.data=datai;
							data.link='';
						}
						s.datas.push(data);
					}
				}
			},
			pclick(){
				const s=this;
				s.pblock.opened=!s.pblock.opened;
				if(s.opened==true)return;
				s.opened=true;
				for(let i in s.datas){
					s.scroll(0,i);
				}
			},
			scroll(e,e2){
				const s=this;
				let top=(e.target&&e.target.scrollTop)||(e.detail&&e.detail.scrollTop)||e;
				let index=parseInt((e.currentTarget&&e.currentTarget.dataset.index));
				if(s.isnan(top)||!top)top=0;
				if(s.isnan(index))index=e2;
				let lineHeight = 32;
				if(!s.datas[index])return;
				let selected = Math.round(top / lineHeight);
				if(s.isnan(selected)||!selected)selected=0;
				clearTimeout(s.datas[index].timer);
				let nextScrollTop = selected * lineHeight;
				s.datas[index].timer=setTimeout(function(){
					const data=s.datas[index].data&&s.datas[index].data[selected+3];
					if(!data)return;
					s.datas[index].selected = selected;
					s.datas[index].text = data.text;
					s.datas[index].value = data.value;
					s.datas[index].top = nextScrollTop;
					if(s.mode=='date')s.datas=s.dateinfo();
				},100);
				return;
			},
			submit(e){
				const s=this;
				let value;
				const data=[];
				for(let i in s.datas){
					data.push(s.datas[i].value);
				}
				if(s.mode=='date'){
					value=data.join('-');
				}else{
					value=data;
				}
				s.blockemit();
				s.$emit('update:modelValue',value)
				s.pblock.opened=false;
			},
			cancel(e){
				const s=this;
				s.pblock.opened=false;
			},
			dateyear:function(){
				const s=this;
				const info=s.datas[0]||{msg:'年份',selected:0,timer:0,top:0};
				const data=[];
				data.push({},{},{});
				data.push({text:'选择年份'});
				let max=new Date().getFullYear()+3;
				for(let i=max;i>=1949;i--){
					const value=i;
					const text=i;
					data.push({value,text});
				}
				data.push({},{},{});
				info.data=data;
				info.value=data[info.selected+3].value;
				return info;
			},
			datemonth:function(){
				const s=this;
				const info=s.datas[1]||{msg:'月份',selected:0,timer:0,top:0};
				const data=[];
				data.push({},{},{});
				data.push({text:'选择月份'});
				for(let i=1;i<=12;i++){
					const value=i;
					const text=i;
					data.push({value,text});
				}
				data.push({},{},{});
				info.data=data;
				info.value=data[info.selected+3].value;
				return info;
			},
			dateday:function(){
				const s=this;
				const info=s.datas[2]||{msg:'月份',selected:0,timer:0,top:0};
				const data=[];
				data.push({},{},{});
				data.push({text:'选择月份'});
				let max=31;
				const month=(s.datas[1]&&s.datas[1].value)||1;
				const year=(s.datas[0]&&s.datas[0].value)||1949;
				if(month==2){
					if(year%4){
						max=28;
					}else{
						max=29;
					}
				}else if(![1,3,5,7,8,10,12].includes(month)){
					max=30;
				}
				for(let i=1;i<=max;i++){
					const value=i;
					const text=i;
					data.push({value,text});
				}
				data.push({},{},{});
				info.data=data;
				info.value=data[info.selected+3].value;
				return info;
			},
			dateinfo:function(){
				const s=this;
				const data=[];
				data.push(s.dateyear());
				data.push(s.datemonth());
				data.push(s.dateday());
				return data;
			},
		}
	}
</script>
<style scoped>
.info{color:var(--text-color);width:var(--block-width,100%);height:var(--block-height);margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);text-align:var(--block-align);border:var(--block-border);background:var(--block-bg);font-size:var(--block-fontsize);font-weight:var(--block-weight);line-height:var(--block-line-height,1.5em);z-index:9999;}
.main{height:14em;width:22.5rem;box-sizing:border-box;background:rgba(255,255,255,1);text-align:center;font-size:16px;line-height:2em;position:relative;overflow:hidden;font-size:16px;z-index:9999;}
.popup{z-index:9999;color:#000;}
.menu{border-bottom:solid 1px #eee;font-size:16px;}
.menu .xg-btn{background:var(--theme-color,#aaa);font-size:16px;}
.main .picker{display:flex;height:14em;width:22.5rem;display:flex;position:relative;z-index:1;font-size:16px;}
.main .picker .view{--picker-padding:6em;flex:1;height:14em;flex:1;padding:0 0.5em;user-select:none;box-sizing:border-box;font-size:16px;}
.main .picker .view .name{display:block;width:100%;white-space:nowrap;font-size:16px;height:2em;}
.mask{width:100%;height:100%;pointer-events:none;background:none;font-size:16px;}
.mask-top,.mask-bottom{position:absolute;top:0;left:0;width:100%;height:calc(50% - 1px - 1em);z-index:2;font-size:16px;}
.mask-top{border-bottom:solid 1px #eee;background:linear-gradient(to bottom,rgba(255,255,255,1),rgba(255,255,255,0.5));font-size:16px;}
.mask-bottom{top:auto;bottom:0;border-top:solid 1px #eee;background:linear-gradient(to bottom,rgba(255,255,255,0.5),rgba(255,255,255,1));font-size:16px;}
</style>