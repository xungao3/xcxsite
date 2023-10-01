<template name="xg-list-d">
	<view v-for="item,index in items" class="xg-col-3 xg-col-6 xg-n-col-12 list-d-info" :class="classnames" :style="cont_style.box" @click="linkclick" :data-link="linkdata(item,1)">
		<view class="info-info">{{item.title}}</view>
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
		data() {
			return {
				xgname: "xg-list-d",
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
		props: {
			list: [Object, Array],
			cont_style: Object,
			box_style: Object
		},
		methods: {},
		mounted: function() {
			const s = this;
			s.xginit();
		},
	}
</script>

<style scoped>
	.list-d-info {
		display: flex;
		flex:auto;
		flex-direction: column;
		/* #ifdef MP-BAIDU */
		margin: 0.4rem 0.2rem;
		/* #endif */
		/* #ifndef MP-BAIDU */
		margin: 0.2rem;
		/* #endif */
		padding: 0.2rem;
		font-size: 0.9rem;
		background-color: rgba(255, 255, 255, 0.3);
		border-radius: 0.4rem;
		overflow:hidden;
	}

	.info-info {
		margin: 0.2rem;
	}

	.info-bottom {
		margin: 0.2rem;
		display: flex;
		justify-content: space-between;
	}

	.desc {
		font-size: 0.7rem;
		line-height: 1.3em;
		max-height: 3.9em;
		flex: 1;
		color: #999;
	}
</style>