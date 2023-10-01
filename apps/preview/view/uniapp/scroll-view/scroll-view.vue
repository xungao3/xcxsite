<template name="scroll-view">
	<view :style1="style" class="view block" :class="classnames" ref="view" :data-bid="dataBid" :data-block="dataBlock"><slot></slot></view>
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
			scrollX:[Boolean,String,Number],
			scrollY:[Boolean,String,Number],
			scrollTop:[String,Number],
			dataBid:[Number,String],
			dataBlock:String,
			style:[Object,Array,String]
		},
		watch:{
			scrollTop:{
				handler(n,o) {
					const s=this;
					$(s.$refs.view).stop().animate({scrollTop:n},100);
				},
				immediate:true
			}
		},
		methods:{
			render:function(){
				const s=this;
				let scroll=false;
				s.classnames.push(s.class);
				if(s.scrollX===true||s.scrollX==='true'||s.scrollX===1||s.scrollX==='1'){
					s.classnames.push('scrollx');
					scroll=true;
				}
				if(s.scrollY===true||s.scrollY==='true'||s.scrollY===1||s.scrollY==='1'){
					s.classnames.push('scrolly');
					scroll=true;
				}
				if(scroll){
					s.scroll();
				}
			},
			scroll:function(){
				const s=this;
				$(s.$refs.view).mousedown(function(e){
					s.o('link-no-jump',0);
					e.stopImmediatePropagation();
					var that=$(this);
					var mouseDownX = e.pageX;
					var scrollLeft = $(that).scrollLeft();
					var mouseDownY = e.pageY;
					var scrollTop = $(that).scrollTop();
					$(document).one('mouseup',function() {
						$(that).unbind('mousemove.xg');
					});
					$(that).bind('mousemove.xg',function(e) {
						s.o('link-no-jump',1);
						var mouseMoveX = e.pageX;
						var mouseOffsetX = mouseMoveX-mouseDownX;
						$(that).scrollLeft(scrollLeft-mouseOffsetX);
						var mouseMoveY = e.pageY;
						var mouseOffsetY = mouseMoveY-mouseDownY;
						$(that).scrollTop(scrollTop-mouseOffsetY);
						
						
						
						var scrollWidth = $(s.$refs.view)[0].scrollWidth;
						var currentScroll = $(that).scrollLeft();
						if (currentScroll > scrollWidth) {
							$(that).scrollLeft(scrollWidth);
						}
					});
				});
			},
			blockbtns:function(){
				const s=this;
				blockbtns();
			},
			init:function(){
				const s=this;
				s.render();
				s.blockbtns();
				setInterval(function(){
					s.blockbtns();
				},10);
				$("#app,#app > view,#app > view > view").on("scroll",function(e){
					$(this).scrollLeft(0);
				});
				(function(){
					let timer;
					let noreq=false;
					$(s.$refs.view).unbind('scroll.xg').bind('scroll.xg',function() {
					if($(this).scrollTop() + $(this).height() >= $(this)[0].scrollHeight-2) {
						if(!noreq&&s.isfun(s.reachbottom)){
							clearTimeout(timer)
							timer=setTimeout(function(){
								noreq=false;
							},500);
							noreq=true;
							s.reachbottom();
						}
					}
				});
				})();
				setTimeout(function(){
					$('.compiling-block .block').each(function(){
						const $that=$(this);
						if(!$(this).hasClass('block-menu')){
							$(this).addClass('xg-drag-item').attr('xg-drag-id',$(this).data('bid'));
						}
					});
				},100);
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
.view{overflow:hidden;}
.view.scrollx{overflow-x:auto;}
.view.scrolly{overflow-y:auto;}
</style>