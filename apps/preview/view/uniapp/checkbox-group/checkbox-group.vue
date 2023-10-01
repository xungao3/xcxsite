<template name="uni-block">
	<view ref="main" :class="classnames"><slot></slot></view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "checkbox-group",
				classnames:[]
			}
		},
		props:{
		},
		methods:{
			render:function(){
				const s=this;
				s.classnames.push(s.class);
				$(s.$refs.main).find('input[type="checkbox"]').change(function(){
					const vals=[];
					$(s.$refs.main).find('input[type="checkbox"]:checked').each(function(){
						vals.push($(this).val());
					});
					s.$emit('change',vals);
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