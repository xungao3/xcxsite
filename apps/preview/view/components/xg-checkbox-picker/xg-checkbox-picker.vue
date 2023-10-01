<template>
	<view :style="[root,styles.main]" :class="classnames">
		<view class="titles">
			<view v-if="items.length" v-for="(item,index) in items" @click="show(index)" :class="['title',items[index].show]"><view class="title-title">{{item.title}}</view><image class="arrow" src="./arrow.png" mode="heightFix"></image></view>
		</view>
		<view class="checkboxes" :style="[styles.boxes]" v-if="items.length" v-for="(item,index) in items">
			<view :class="['checkbox',items[index].show]">
				<xg-checkbox :multiple="multiple||item.multiple" :showicon="showicon" :mode="mode" v-model="item.value" :datas="item.data" @change="change(index,item.value,item.key)" :theme_color="block.theme_color||theme_color"></xg-checkbox>
			</view>
		</view>
		<view v-if="showbtn&&showing" class="opbtns" :style="[styles.opbtns]">
			<view class="opbtn reset" @click="reset">重置</view>
			<view class="opbtn submit" @click="submit">确定</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default {
		mixins:[mixin],
		props: {
			datas: {
				type: Array,
				default: []
			},
			multiple: {
				type: [Boolean,Number,String],
				default: 0
			},
			showicon: {
				type: [Boolean,Number,String],
				default: 0
			},
			mode:String,
			styles:{
				type:Object,
				default:{}
			},
			showbtn:{
				type: [Boolean,Number,String],
				default: 0
			}
		},
		watch: {
			datas: {
				handler(n,o) {
					const s=this;
					if(s.isobj(n))n=s.obj2arr(n);
					s.items=n;
				},
				deep: true,
				immediate: true
			},
		},
		data() {
			return {
				xgname: 'xg-checkbox-picker',
				items:[],
				values:{},
				showing:0,
			};
		},
		computed: {
			root() {
				const s=this;
				const style=s.mainstyles;
				return style;
			}
		},
		created() {
		},
		mounted:function(){
			const s=this;
			s.xginit();
		},
		methods: {
			show(i){
				const s=this;
				let showing=0;
				for(let j in s.items){
					if(j==i){
						if(s.items[i].show=='show'){
							s.items[i].show='';
						}else{
							s.items[i].show='show';
							showing=1;
						}
					}else{
						s.items[j].show='';
					}
				}
				s.showing=showing;
			},
			hide(){
				const s=this;
				for(let j in s.items){
					s.items[j].show='';
				}
				s.showing=0;
			},
			reset:function(){
				const s=this;
				const items=s.items;
				for(let i in items){
					items[i].value=null;
				}
				s.items=items;
				s.values={};
				s.$emit('reset');
			},
			submit:function(){
				const s=this;
				s.showing=false;
				const items=s.items;
				for(let i in items){
					items[i].show=0;
				}
				s.items=items;
				s.$emit('submit',s.values);
			},
			change(i,v,k){
				const s=this;
				s.values[k]=v;
				s.$emit('update:modelValue',s.values);
				s.$emit('change',{i,value:v,key:k});
			}
		}
	}
</script>

<style scoped>
.titles{display:flex;justify-content:space-between;line-height:1.5em;padding:0.25rem;font-size:0.9rem;}
.titles .title{display:flex;flex:1;width:var(--block-title-width,auto);margin:0.25rem;}
.titles .title .arrow{height:1.3rem!important;transform:rotate(180deg);}
.titles .title.show .arrow{transform:rotate(0deg);}
.checkboxes{}
.checkboxes .checkbox{display:none;}
.checkboxes .checkbox.show{display:block;}
.opbtns{display:flex;justify-content:space-between;padding:0.25rem;}
.opbtns .opbtn{display:block;width:3.5rem;margin:0;height:1.9rem;padding:0;line-height:1.9rem;background-color:var(--theme-color);color:#fff;text-align:center;border-radius:0.3rem;}
</style>