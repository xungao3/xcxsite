<template name="xg-nav-d">
	<block v-for="item,index in items">
		<view class="item" @click="linkclick" :id="itemid(item)" :data-link="linkdata(item,1)" :class="itemclass(item)">
			<text class="text">{{item.title}}</text>
			<view v-if="item.children&&item.children.length" class="toggler" @click.stop="trigger(index)" :data-index="index" :class="{opened:open[index]}"></view>
		</view>
		<view v-if="item.children&&item.children.length" class="children xg-pl-2" :class="{opened:open[index]}">
			<xg-nav-d :itemsdata="item.children" :blockdata="block"></xg-nav-d>
		</view>
	</block>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				items:[],
				open:{},
			}
		},
		props:{
			itemsdata:Array,
			opened:[Boolean,String,Number]
		},
		watch:{
			itemsdata:{
				handler(n,o){
					const s=this;
					s.items=n;
				},
				immediate:true,
				deep:true
			}
		},
		methods:{
			itemid:function(d){
				const s=this;
				return 'id-'+s.md5(JSON.stringify(s.linkdata(d)));
			},
			trigger:function(index){
				const s=this;
				s.open[index]=!s.open[index];
			},
			itemclass:function(d){
				const s=this;
				return ['link',(s.isthis(d)?'cur':'')];
			},
			isthis:function(t){
				const s=this;
				if(s.pagename=='index'&&s.iseobj(t))return true;
				return s.linkthis(t);
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
		},
	}
</script>

<style scoped>
.item{display:flex;justify-content:space-between;word-spacing:normal;word-break:normal;padding:0.5rem;line-height:1rem;}
.item.cur{color:var(--theme-color);}
.children{display:none;}
.children.opened{display:block;}
	
.toggler{font-family:"xg";}
.toggler:before{content:"\e01c";}
.toggler.opened:before{content:"\e01d";}
</style>