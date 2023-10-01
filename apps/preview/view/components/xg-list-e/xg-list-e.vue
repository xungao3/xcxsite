<template name="xg-list-e">
	<view v-for="item,index in items" class="list-e-info" :class="classnames" :style="[cont_style.box,root]" @click="linkclick" :data-link="linkdata(item,1)">
		<view class="info-pic" :style="cont_style.img_box"><image :style="cont_style.img" class="image" mode="aspectFill" :src="fileurl(item.pic||item.img)" /></view>
		<view class="info-title">{{item.title}}</view>
		<view class="info-desc" v-if="item.desc||item.description">{{item.desc||item.description}}</view>
		<view class="info-bottom">
			<view class="info-left" v-if="item.left">{{item.left}}</view>
			<view class="info-right" v-if="item.right">{{item.right}}</view>
		</view>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default {
		components: {},
		mixins:[mixin],
		props: {
			list: [Object, Array],
			cont_style: {
				type:Object,
				default:{},
			},
			box_style: {
				type:Object,
				default:{},
			},
		},
		data() {
			return {
				xgname: "xg-list-e",
				items:[]
			}
		},
		watch:{
			list:{
				handler(n,o){
					const s=this;
					s.items=n;
				},
				immediate:true,
				deep:true,
			}
		},
		computed: {
			root() {
				const s=this;
				const style=s.mainstyles;
				if(s.block.img_height)style['--img-height']=s.block.img_height;
				return style;
			}
		},
		methods: {},
		mounted: function() {
			const s = this;
			s.xginit();
		},
	}
</script>

<style scoped>
	.list-e-info {
		display: block;
		overflow:hidden;
		background:var(--item-bg,#fff);
		border-radius:0.5rem;
		margin:var(--item-margin,0 0 0.5rem 0);
		width:100%;
	}

	.info-title {
		margin:var(--title-margin,0.25rem 0.5rem);
	}

	.info-pic {
		width:100%;
		margin-bottom:0.25rem;
		height:var(--img-height,12rem);
	}

	.info-pic .image {
		display:block;
		width:100%;
		height:var(--img-height,12rem);
	}

	.info-bottom {
		margin: 0.25rem 0.5rem;
		margin-bottom:0.5rem;
		display: flex;
		justify-content: space-between;
	}

	.info-desc {
		margin: 0 0.5rem;
		font-size: 0.8rem;
		line-height: 1.3em;
		max-height: 3.9em;
		flex: 1;
		color: #999;
	}
</style>