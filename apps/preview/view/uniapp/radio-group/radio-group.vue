<template name="uni-block">
	<view ref="main" :class="classnames"><slot></slot></view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "radio-group",
				classnames:[]
			}
		},
		props:{
			class:[Object,Array,String]
		},
		methods:{
			render:function(){
				const s=this;
				s.classnames.push(s.class);
				$(s.$refs.main).find('label').click(function(e){
					s.$emit('change',$(this).find('input').val());
				});
			},
			init:function(){
				const s=this;
				s.render();
			},
		},
		computed:{
		},
		mounted:function(){
			const s=this;
			s.init();
		},
	}
</script>

<style scoped>
</style>