<template>
	<view class="root" :class="classnames" :style="root">
		<checkbox-group v-if="multi" ref="group" :class="['group','group-'+mode]" @change="change">
			<slot class="slot-left" name="left"></slot>
			<label :class="['box',(item.checked?'checked':'')]" v-for="(item,index) in items" :key="index">
				<checkbox class="hidden" hidden :value="item.value" :checked="item.checked" />
				<view :class="['icon',(item.checked?'checked':'')]" v-if="showicon"><svg class="svg" width="20" height="20" viewBox="-4 7 50 30"><path d="M1.952 18.080q-0.32-0.352-0.416-0.88t0.128-0.976l0.16-0.352q0.224-0.416 0.64-0.528t0.8 0.176l6.496 4.704q0.384 0.288 0.912 0.272t0.88-0.336l17.312-14.272q0.352-0.288 0.848-0.256t0.848 0.352l-0.416-0.416q0.32 0.352 0.32 0.816t-0.32 0.816l-18.656 18.912q-0.32 0.352-0.8 0.352t-0.8-0.32l-7.936-8.064z"></path></svg></view>
				<text class="text">{{item.text}}</text>
			</label>
			<slot class="slot-right" name="right"></slot>
		</checkbox-group>
		<radio-group v-else ref="group" :class="['group','group-'+mode]" @change="change">
			<slot class="slot-left" name="left"></slot>
			<label :data-value="item.value" :class="['box',(item.checked?'checked':'')]" v-for="(item,index) in items" :key="index">
				<radio class="hidden" hidden :value="item.value" :checked="item.checked" />
				<view :class="['icon','icon-radio']" v-if="showicon"><view class="icon-dot"></view></view>
				<text class="text">{{item.text}}</text>
			</label>
			<slot class="slot-right" name="right"></slot>
		</radio-group>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js'
	export default {
		mixins:[mixin],
		props: {
			datas: {
				type: [Array,Object],
				default: []
			},
			multiple: {
				type: [Boolean,Number,String],
				default: false
			},
			showicon: {
				type: [Boolean,Number,String],
				default: 0
			},
			mode: {
				type: String,
				default: 'line'
			},
			theme_color:String,
			value:[String,Array,Number],
			modelValue:[String,Array,Number]
		},
		watch: {
			datas: {
				handler(n,o) {
					const s=this;
					if(n){
						n=s.obj2arr(n);
						if(n.length){
							s.items=n;
							s.check();
							s.hooks('block-mutated',s);
						}
					}
				},
				deep: true,
				immediate: true
			},
			multiple: {
				handler(n,o) {
					const s=this;
					if(n===true||n>0){
						s.multi=true;
					}
				},
				immediate: true
			},
			value: {
				handler(n,o) {
					const s=this;
					if(n!==o)s.check(n);
				},
				deep: true
			},
			modelValue: {
				handler(n,o) {
					const s=this;
					if(n!==o)s.check(n);
				},
				deep: true
			},
		},
		data() {
			return {
				xgname: 'xg-checkbox',
				items:[],
				form:{name:'',value:''},
				multi:false,
			};
		},
		computed: {
			root:function(){
				const s=this;
				const style=s.mainstyles;
				if(s.theme_color)style['--theme-color']=s.theme_color;
				return style;
			}
		},
		mounted() {
			const s=this;
			s.xginit();
			var v=s.isunde(s.modelValue)?(s.isunde(s.value)?'':s.value):s.modelValue;
			if(v)setTimeout(function(){s.check(v);},50);
		},
		methods: {
			render(){
				const s=this;
				if(s.block){
					if(s.block.multi>0){
						s.multi=true;
					}
					if(s.block.form_name){
						s.form.name=s.block.form_name;
					}
					if(s.block.opdata){
						let obj=s.op2obj(s.block.opdata);
						let items=[];
						for(let k in obj){
							items.push({value:k,text:obj[k]});
						}
						s.items=items;
					}
				}
			},
			change(e){
				const s=this;
				if(s.isobj(e)){
					if(e.detail){
						var v=e.detail.value;
					}else if(e.target){
						var v=e.target.defaultValue;
					}
				}else{
					var v=e;
				}
				s.form.value=v;
				s.check(v);
				s.$emit('update:modelValue',v);
				s.$emit('change',v);
				s.blockemit();
			},
			check(n){
				const s=this;
				const items=s.items;
				if(s.isunde(n))return;
				if(s.multi){
					if(s.isstr(n))n=[n];
					for(let i in items){
						items[i].checked=false;
						for(let j in n){
							if(items[i].value==n[j]){
								items[i].checked=true;
							}
						}
					}
				}else{
					for(let i in items){
						if(items[i].value==n){
							items[i].checked=true;
						}else{
							items[i].checked=false;
						}
					}
				}
				if(items.length)s.items=items;
			}
		}
	}
</script>
<style scoped>
.root{display:flex;width:100%;}
.group{display:flex;width:100%;flex-shrink:1;flex-wrap:wrap;padding:0.15rem;font-size:0.85rem;line-height:0.95rem;box-sizing:border-box;align-items:center;box-sizing:border-box;}
.group .box{display:flex;padding:var(--block-padding,0.5rem);}
.group.group-column .box{width:50%;margin:0.25rem 0;text-align:left;box-sizing:border-box;}
.group.group-line{width:auto;flex-wrap:nowrap;overflow:scroll;}
.group.group-line .box{flex-shrink:0;margin-right:0.5rem;}
.box .icon{width:0.9rem;height:0.9rem;margin-right:0.25rem;border:solid 1px var(--theme-color-2,#000);border-radius:0.25rem;}
.box .icon .svg{display:none;fill:var(--theme-color-2,#000);}
.box .icon .icon-dot{display:none;width:0.6rem;height:0.6rem;margin:0.15rem;border-radius:80%;background:var(--theme-color-2,#000);}
.box .icon.icon-radio{border-radius:50%;}
.box .text{color:var(--theme-color-2,var(--text-color,#000));}
.box.checked .icon{border:solid 1px var(--theme-color,--theme-color-2);}
.box.checked .icon .icon-dot{display:block;background:var(--theme-color,--theme-color-2);}
.box.checked .icon .svg{display:block;fill:var(--theme-color,--theme-color-2);}
.box.checked .text{color:var(--theme-color,var(--text-color,#000));font-weight:bold;}
</style>