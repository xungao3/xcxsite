<template name="xg-cate-list">
	<view :style="root" v-if="cates.length" class="cate-list xg-row xg-gutter-x-0" :class="classnames">
		<view class="xg-m-col-4 xg-n-col-6 xg-w-a" v-for="cate in cates" @click="linkclick" :data-link="linkdata({cid:cate.cid},1)">
			<view class="cate">{{cate.title}}</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname:"xg-cate-list",
				pagename:'index',
				cid:0,
				id:0,
				cates:[],
				catelist:{},
			}
		},
		methods:{
			pid:function(pid,level){
				const s=this;
				const cates=[];
				for(let id in s.catelist){
					let cate=s.catelist[id];
					if(cate.pid==pid.toString()){
						let catei={cid:id,title:cate.title};
						if(level<s.block.cate_list_level)catei.children=s.pid(id,level+1);
						cates.push(catei);
					}
				}
				if(level>1){
					return cates;
				}
				if(cates.length){
					s.show(cates);
				}
			},
			show:function(cates){
				const s=this;
				for(let i in cates){
					s.cates.push(cates[i]);
					s.show(cates[i].children);
				}
			},
			init:function(){
				const s=this;
				s.catelist=s.g.datas.cates;
				const cates=[];
				var pid=null;
				if(s.block.cate_list=='custom'){
					if(s.block.cateids){
						for(let i in s.block.cateids){
							cates.push(s.block.cateids[i]);
						}
					}
				}else if(s.block.cate_list=='curcate'){
					var pid=s.cid||0;
				}else if(s.block.cate_list=='parent'){
					if(s.cid){
						var pid=s.catelist[s.cid].pid;
						if(pid){
							var pid=s.catelist[pid].pid;
						}
					}else{
						var pid=0;
					}
				}else if(s.block.cate_list=='sibling'){
					if(s.cid){
						var pid=s.catelist[s.cid].pid;
					}else{
						var pid=0;
					}
				}else if(s.block.cate_list=='allcate'){
					var pid=0;
				}
				if(!s.isnan(pid)){
					s.pid(pid,1);
				}else if(cates.length){
					s.show(cates);
				}
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
.cate-list{margin:var(--block-margin);padding:var(--block-padding,0.25rem);border:var(--block-border,0);border-radius:var(--block-radius,0.5rem);background:var(--block-bg,rgba(0,0,0,0.1));text-align:center;font-size:0.9rem;}
.cate-list .cate{background:var(--theme-color,rgba(255,255,255,0.3));padding:0.25rem;margin:0.25rem;border-radius:0.3rem;color:var(--text-color,#000)}
</style>