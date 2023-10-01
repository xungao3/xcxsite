<template name="xg-btns">
	<view class="btns" :class="classnames" :style="root">
		<view class="view" :style="(block.margin?'margin:'+block.margin+';':'')+(block.padding?'padding:'+block.padding+';':'')+(block.border?'border:'+block.border+';':'')+(block.bg_color?'background-color:'+block.bg_color+';':'')+(block.radius?'border-radius:'+block.radius+';':'')+(block.fontsize?'font-size:'+block.fontsize+';':'')">
			<block v-for="item,index in block.items">
				<button :class="'button xg-btn xg-btn-'+(block.btn_size=='small'?'s':(block.btn_size=='large')?'l':'')" v-if="item.type=='star'" @click="addstar">{{star}}</button>
				<button :class="'button xg-btn xg-btn-'+(block.btn_size=='small'?'s':(block.btn_size=='large')?'l':'')" v-if="item.type=='share'" open-type="share">分享</button>
			</block>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-btns",
				html:'',
				star:'点赞'
			}
		},
		methods:{
			init:function(){
				const s=this;
				if(s.storage('star_'+s.id))s.star='已赞';
			},
			addstar:function(){
				const s=this;
				var name='star_'+(s.id);
				if(s.storage(name)){
					s.star='点赞';
					s.storage(name,null);
					s.request({url:s.url('index/star',s.urldata(s.options)),data:{star:'remove'}});
				}else{
					s.star='已赞';
					s.storage(name,1);
					s.request({url:s.url('index/star',s.urldata(s.options)),data:{star:'add'}});
				}
			},
		},
		computed:{
			root:function(){
				const s=this;
				var style=s.mainstyles;
				return style;
			}
		},
		mounted:function(){
			const s=this;
			s.xginit();
			s.init();
		},
		onShareAppMessage(){
			const s=this;
			return {title:s.info.title,path:'/pages/article?id='+s.id}
		}
	}
</script>

<style scoped>
.button{display:inline-block;margin:0 0.1rem;}
</style>