<template name="scroll-view">
	<view class="view" :class="classnames" ref="view"><slot></slot></view>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return{
				xgname: "scroll-view",
				classnames:[]
			}
		},
		props:{
		},
		methods:{
			render:function(){
				const s=this;
				$(s.$refs.view).each(function(){
					const attr=$(this).data();
					let id;
					for(let key in attr){
						if(key.substr(0,2)=='v-'){
							id=key;
						}
					}
					if($('#slide-'+id).length==0){
						const datas=[];
						$(this).find('.i-item').each(function(){
							const link=$(this).data('link');
							const img=$(this).find('img').attr('src');
							const title=$(this).find('.title').text();
							datas.push({img,title,link});
						});
						const $ul=$('<ul></ul>');
						for(let i in datas){
							let img=datas[i].img;
							let title=datas[i].title;
							let link=datas[i].link;
							$ul.append($('<li class="i-item"></li>').attr('data-'+id,'').append($('<img class="image" />').attr('data-'+id,'').attr('mode','widthFix').attr('src',img)).append($('<div class="title xg-slide-title"></div>').text(title))).click(function(){
								window.s.link(link);
							});
						}
						const $div=$('<div class="swiper xg-slide"></div>');
						$div.attr('data-'+id,'').attr('id','slide-'+id);
						$div.append($ul);
						$(this).replaceWith($div);
						xg.mod('slide',function(){xg.slide('#slide-'+id);});
					}
				});
			},
			init:function(){
				const s=this;
				setTimeout(function(){
					s.render();
				},20);
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
.view{overflow:hidden;}
.view.scrollx{overflow-x:auto;}
.view.scrolly{overflow-y:auto;}
</style>