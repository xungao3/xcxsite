<template>
	<view :class="classnames" class="root" :style="root">
		<block v-for="cateid,index in cateids">
			<xg-cate-box :data-block="block.block" :cateid="cateid" :blockdata="block"></xg-cate-box>
		</block>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js'
	export default {
		mixins:[mixin],
		data() {
			return {
				xgname: 'xg-cate-boxes',
				cateids:[]
			}
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
		},
		methods: {
			render:function(){
				const s=this;
				s.cateids=s.block.cateids;
			}
		}
	}
</script>
<style scoped>
</style>