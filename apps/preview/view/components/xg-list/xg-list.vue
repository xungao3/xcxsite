<template name="info-list">
	<view class="list-blocks" :class="classnames" :style="root">
		<block v-for="cont,conti in conts" v-if="showself">
			<block v-for="blocki,index in blocks" v-if="blocks.length">
				<xg-blocks class="block block-blocks" :contdata="cont" :userdata="user" :blockdata="blocki" :blocksdata="blocki.blocks" :data-block="blocki.block" :data-bid="blocki.bid" :theme_color="block.theme_color||theme_color"></xg-blocks>
			</block>
		</block>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js'
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-list",
				catetitle:'',
				size:0,
				pmargin:'',
				ops:{},
				requested:false,
				keywords:'',
				countdata:0,
				blocks:[],
			}
		},
		methods:{
			render:function(){
				const s=this;
				s.blocks=s.block.blocks;
				s.size=s.block.pagesize;
				if(s.block.data_url){
					s.dataurl();
				}else{
					if(s.cid)s.getconts();
				}
				s.o(`options-change-${s.cid}`,function(ops){
					if(s.g.conts_reqed)return;
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
					s.countdata=0;
					s.g.conts_reqed=false;
					s.getconts(s.ops);
				});
				s.o(`reachbottom`,function(){
					s.page++;
					s.countdata=0;
					s.g.conts_reqed=false;
					if(s.block.auto_load>0)s.getconts(s.ops);
				});
			}
		},
		computed:{
			root:function(){
				const s = this;
				const style=s.mainstyles;
				for(let name in style){
					let name2=name.replace('--blocks-','--list-blocks-').replace('--block-','--list-blocks-');
					if(name2!=name){
						style[name2]=style[name];
						delete style[name];
					}
				}
				if(!style['--list-blocks-margin'])style['--list-blocks-margin']='0px';
				if(!style['--list-blocks-padding'])style['--list-blocks-padding']='0px';
				if(s.block.blocks_display=='justify'){
					style['--list-blocks-display']='flex';
					style['--list-blocks-justify-content']='space-between';
				}
				if(s.block.blocks_display=='flex')style['--list-blocks-display']='flex';
				if(s.block.blocks_display=='flex-col'){
					style['--list-blocks-display']='flex';
					style['--list-blocks-direction']='column';
				}
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
.root{}
.list-blocks{display:var(--list-blocks-display,block);flex:1;width:var(--list-blocks-width,100%);height:var(--list-blocks-height);flex-direction:var(--list-blocks-direction);margin:var(--list-blocks-margin);padding:var(--list-blocks-padding);border:var(--list-blocks-border);border-radius:var(--list-blocks-radius);background:var(--list-blocks-bg);justify-content:var(--list-blocks-justify-content);}
</style>