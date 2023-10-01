<template>
	<view class="root" :class="classnames" :style="root">
		<xg-picker :blockdata="picker"></xg-picker>
	</view>
</template>

<script>
	import {mixin} from '../../js/mixin.js'
	export default {
		mixins:[mixin],
		props: {
		},
		watch: {
			modelValue: {
				handler(n,o) {
					const s=this;
				},
				deep: true
			},
		},
		data() {
			return {
				xgname:'xg-region',
				picker:{},
			};
		},
		computed: {
			root:function(){
				const s=this;
				const style=s.mainstyles;
				return style;
			}
		},
		mounted() {
			const s=this;
			s.xginit();
		},
		methods: {
			render(){
				const s=this;
				const block=s.block;
				block.placeholder='请选择省份';
				const bid=block.bid;
				const data=[];
				const param={pid:0};
				if(block.region_status>0)param.status=1;
				data.push({msg:'省份',link:xg.url('app/region/region',param)});
				if(block.region_level>1){
					param.pid='[datas.0.value]';
					data.push({msg:'城市',cond:'datas.0.value',link:xg.url('app/region/region',param)});
					block.placeholder='请选择城市';
				}
				if(block.region_level>2){
					param.pid='[datas.1.value]';
					data.push({msg:'区县',cond:'datas.0.value&&datas.1.value',link:xg.url('app/region/region',param)});
					block.placeholder='请选择区县';
				}
				block.picker_data=data;
				s.picker=block;
			},
		}
	}
</script>
<style scoped>
</style>