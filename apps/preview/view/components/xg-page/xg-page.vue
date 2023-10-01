<template name="xg-page">
	<view class="xg-page-root" :class="classnames" :style="root">
		<view class="xg-page" v-if="pages.length">
			<view v-if="page!=1" :class="['xg-page-link']" :data-page="1" @click="gotopage">首页</view>
			<view v-for="p in pages" :class="['xg-page-link',page==p?'xg-page-cur':'']" :data-page="p" @click="gotopage">{{p}}</view>
			<view v-if="page!=pagecount" :class="['xg-page-link']" :data-page="pagecount" @click="gotopage">末页</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname:"xg-page",
				pages:[],
				pagecur:1,
				size:0,
				pagecount:0,
				count:0,
			}
		},
		props:{
			pagesize:[Number,String],
			theme:String,
			countdata:Number,
		},
		watch:{
			pagesize:{
				handler(n,o){
					const s=this;
					if(n){
						s.size=n;
						s.render();
					}
				},
				immediate:true,
			},
			countdata:{
				handler(n,o){
					const s=this;
					if(n){
						s.count=n;
						s.render();
					}
				},
				immediate:true,
			}
		},
		methods:{
			init:function(){
				const s=this;
				s.render();
			},
			render:function(){
				const s=this;
				var showmax=4;
				var page=0;
				var pagecur=parseInt(s.page);
				var count=s.countdata||s.count;
				var pagecount=Math.ceil(count/s.size);
				var tmp=Math.ceil(showmax/2);
				var pages=[];
				for(var i=1;i<=showmax;i++){
					if((pagecur-tmp)<=0){
						page=i;
					}else if((pagecur+tmp-1)>=pagecount){
						page=pagecount-showmax+i;
					}else{
						page=pagecur-tmp+i;
					}
					if(page>0&&page!=pagecur){
						if(page<=pagecount){
							pages.push(page);
						}else{
							break;
						}
					}else{
						if(page>0&&1!=pagecount){
							pages.push(page);
						}
					}
				}
				s.pagecount=pagecount;
				if(pages.length>1){
					s.pages=pages;
				}
			},
			gotopage:function(e){
				const s=this;
				var page=e.currentTarget.dataset.page;
				s.link(s.extend(s.options,{page:page}));
			}
		},
		computed:{
			root:function(){
				const s=this;
				const style=s.mainstyles;
				if(s.theme)style['--theme-color']=s.theme;
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
.xg-page-root{display:flex;justify-content:left;margin-top:0.5rem;margin-bottom:0.5rem;}
.xg-page{width:auto;margin:auto;font-size:0.9rem;border:0;background:rgba(255,255,255,0.3);justify-content:center;}
.xg-page .xg-page-link{border:0;padding:0.5rem 0.6rem;}
.xg-page-cur{background-color:var(--theme-color);}
</style>