<template name="xg-input">
	<view class="root" :class="classnames" :style="root">
		<block v-if="showself">
			<input v-if="type=='text'" class="input" v-model="value" />
			<input v-if="type=='password'" class="input" type="password" v-model="value" />
			<input v-if="type=='image'" class="input image" v-model="value" @click="image" />
			<xg-picker v-if="type=='date'" class="input picker" mode="date" :blockdata="dateblock" v-model="value"></xg-picker>
		</block>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "xg-input",
				type:'text',
				value:'',
				dateblock:{}
			}
		},
		props:{
			modelValue:[Number,String,Array,Object,Boolean]
		},
		methods:{
			image:function(){
				const s=this;
				const data=s.extend({sys:s.sys,'isimg':1},s.urldata(s.options));
				s.uploadimg(s.url('app/index/upload',data),function(res){
					if(res.ok===true){
						s.value=res.imgurl;
						console.log(s.value);
						if(res.msg){
							s.msg(res.msg);
						}
					}
				});
			},
			render:function(){
				const s=this;
				const block=s.block;
				s.type=block.form_type;
				s.value=block.value||'';
				const callback=function(f){
					f.values[s.block.form_name||s.block.bid]=s.value;
				};
				s.o('xg-form-value',callback);
				s.o('xg-form-value-'+s.block.bid,callback);
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
			s.render();
		},
	}
</script>

<style scoped>
.root{color:var(--text-color);width:var(--block-width);height:var(--block-height);margin:var(--block-margin);padding:var(--block-padding);border-radius:var(--block-radius);text-align:var(--block-align);border:var(--block-border);background:var(--block-bg);font-size:var(--block-fontsize);font-weight:var(--block-weight);line-height:var(--block-line-height,1.5em);}
.root .input{display:block;width:100%;height:100%;background:none;font-size:var(--block-fontsize);font-weight:var(--block-weight);line-height:var(--block-line-height,1.5em);margin:0;padding:0;border:none;color:var(--text-color);text-align:var(--block-align);}
</style>