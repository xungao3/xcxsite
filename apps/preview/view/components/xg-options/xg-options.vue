<template name="xg-options">
	<view :style="root" class="xg-options" :class="classnames">
		<xg-checkbox-picker ref="picker" :showbtn="block.options_btn>0?1:0" mode="column" :datas="opdata" @submit="opchange" v-if="opdata.length" :theme_color="block.theme_color||theme_color"></xg-checkbox-picker>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-options",
				opdata:[]
			}
		},
		methods:{
			opchange:function(v){
				const s=this;
				s.o(`options-change-${s.cid}`,v);
			},
			init:function(){
				const s=this;
				if(s.block.options_data){
					const options={};
					s.block.options_data.split(/[\r\n]+/g).map(function(v){
						if(v){
							v=v.split(':');
							options[v[0]]=v[1];
						}
					});
					const opkey=Object.keys(options);
					s.request({url:s.url('index/options',{sys:s.sys,cid:s.cid,options:opkey.join(',')}),success:function(res){
					if(res.ok){
						for(let i in res.opdata){
							res.opdata[i]['title']=options[res.opdata[i]['key']];
						}
						s.opdata=res.opdata;
					}
					}});
				}
			},
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
.xg-options{width:auto;color:var(--text-color);margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);border:var(--block-border);background:var(--block-bg);font-size:var(--block-fontsize);}
</style>