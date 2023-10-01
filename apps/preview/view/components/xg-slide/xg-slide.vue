<template name="xg-slide">
	<view :class="[mainclass,classnames]" :style="root">
		<swiper v-if="items.length" class="swiper" :autoplay="autoplay" interval="5000" :current="cur" :indicator-dots="block.slide_theme=='a'" indicator-color="#fff" indicator-active-color="var(--theme-color)" duration="500" @change="change" ref="slide">
			<block v-for="item,index in items">
				<swiper-item class="s-item">
					<view class="i-item" @click="linkclick" :data-link="linkdata(item,1)" :ref="'item-'+index">
						<image 
						v-if="item.img" 
						:class="'image image-'+index"
						:src="fileurl(item.img)"
						mode="widthFix" 
						:data-index="index" 
						@load="load" 
						@error="error(index)" />
						<view :class="'title title-'+block.slide_title_pos">{{item.title}}</view>
					</view>
				</swiper-item>
			</block>
		</swiper>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-slide",
				cur:null,
				height:'auto',
				items:[],
			}
		},
		props:{
			autoplay:{
				type:[Boolean,Number],
				default:1
			}
		},
		methods:{
			render:function(){
				const s=this;
				const list=s.block.list;
				if(s.block.height)s.height=s.block.height;
				const items=[];
				if(s.block.imgs){
					let imgs=s.obj2arr(s.block.imgs);
					for(const img of imgs){
						items.push({img});
					}
				}
				if(s.block.source=='custom'){
					const block=s.block;
					const cates=[];
					if(s.block.data){
						for(let cate of s.block.data){
							cates.push(cate[cate.key]);
						}
						const name=block.bid+'-'+cates.join(',');
						const file=s.cachepath(name,'custom',s.sys);
						const param={cid:cates.join(','),bid:s.block.bid,sys:s.sys,count:s.block.show_count};
						s.request({url:s.url(file,param),success:function(data){
							if(s.isarr(data)){
								for(const datai of data){
									items.push(datai);
								}
							}else if(s.isobj(data)&&data.block=='slide'){
								for(const datai of data.list){
									items.push(datai);
								}
							}
						}});
					}
				}else{
					if(s.block.list){
						for(let i in s.block.list){
							items.push(s.block.list[i]);
						}
					}
				}
				if(items){
					for(let i in items){
						items[i].img=s.fileurl(items[i].img||items[i].pic);
					}
					s.items=items;
				}
			},
			error:function(e){
				const s=this;
			},
			load:function(e){
				const s=this;
				var index = e.currentTarget.dataset.index;
				if (index == 0) {
					s.resize('.image-' + index);
				}
			},
			resize:function(e){
				const s=this;
				if(!s.inxg()){
					s.$nextTick(() => {
						let query = uni.createSelectorQuery().in(s);
						query.select(e).boundingClientRect(data => {
							if(data && data.height){
								s.height = data.height+'px';
							}
						}).exec();
					});
				}
			},
			change:function(e){
				const s=this;
				if(typeof e=='object'){
					var current = e.detail.current;
				}else{
					var current = e;
				}
				s.resize('.image-' + current);
			},
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				style['--height']=s.height;
				return style;
			},
			mainclass:function(){
				const s=this;
				return 'slide slide-'+s.block.slide_theme;
			}
		},
		mounted:function(){
			const s=this;
			s.xginit();
		},
	}
</script>

<style scoped>
.slide{overflow:hidden;max-height:var(--height);}
.slide .swiper{width:calc(100% - var(--block-margin-left) - var(--block-margin-right));margin:var(--block-margin);--text-color:#000;color:var(--text-color);border-radius:var(--block-radius);height:var(--height);max-height:var(--height);overflow:hidden;}
.slide .title-top{top:0;bottom:auto;}
.slide .title-bottom{bottom:0;}
.slide .title-middle{top:calc(50% - 1.5rem)}
.slide .s-item{width:100%;}
.slide .i-item{width:100%;height:var(--height);}
.slide .image{width:100%;}

.slide-a .title{display:none;}

.slide-b .points{display:none;}
.slide-b .title{display:block;position:absolute;left:0;bottom:0;width:100%;height:3rem;padding:0.5rem 1rem;line-height:1.2rem;box-sizing:border-box;--text-color:#fff;color:var(--text-color);text-align:center;text-shadow:1px 1px 1px #000, -1px -1px 1px #000, 1px -1px 1px #000, -1px 1px 1px #000;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;}

.slide-c .points{display:none;}
.slide-c .title{position:absolute;left:0;width:100%;height:2.4rem;bottom:0;padding:0.3rem 0.5rem;line-height:1.8rem;background:rgba(0, 0, 0, 0.5);box-sizing:border-box;color:#fff;text-align:center;}
.slide-c.slide .title-middle{top:calc(50% - 1.2rem);}
</style>