<template name="uni-image">
	<img class="img" :class="classnames" :mode="mode" ref="img" :src="src" draggable="false">
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "uni-image",
				classnames:[]
			}
		},
		props:{
			src:String,
			mode:String
		},
		methods:{
			render:function(){
				const s=this;
				$(s.$refs.img).imgsize();
			},
			init:function(){
				const s=this;
				s.render();
			},
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
			s.init();
		},
	}
</script>

<style scoped>
</style>