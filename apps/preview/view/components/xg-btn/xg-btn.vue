<template name="xg-btns">
	<button :class="[classnames,'xg-btn '+btnsize]" v-if="actname=='share'" open-type="share" form-type="button" type="button" :style="root">
		<view class="xg-icon" :class="block.btn_icon" v-if="block.btn_icon"></view>{{text}}
	</button>
	<button :class="[classnames,'xg-btn '+btnsize]" v-else-if="actname=='submit'" @click="submit" form-type="submit" type="button" :style="root">
		<view class="xg-icon" :class="block.btn_icon" v-if="block.btn_icon"></view>{{text}}
	</button>
	<button :class="[classnames,'xg-btn '+btnsize]" v-else @click="execute" form-type="button" type="button" :style="root">
		<view class="xg-icon" :class="block.btn_icon" v-if="block.btn_icon"></view>{{text}}
	</button>
</template>

<script>
	import {mixin} from '../../js/mixin.js';
	export default{
		mixins:[mixin],
		data(){
			return {
				xgname: "xg-btns",
				values:{},
				html:'',
				text:'',
				actname:'',
				btnsize:''
			}
		},
		methods:{
			render:function(){
				const s=this;
				if(s.block.btn_size=='small'){
					s.btnsize='xg-btn-s';
				}else if(s.block.btn_size=='large'){
					s.btnsize='xg-btn-l';
				}
				s.actname=s.block.btn_action;
				//s.actname='downopen';
				if(s.block.btn_text){
					s.text=s.block.btn_text;
				}else if(s.actname=='star'){
					if(s.storage('star_'+s.id)){
						s.text='已赞';
					}else{
						s.text='点赞';
					}
				}else if(s.actname=='share'){
					s.text='分享';
				}else if(s.actname=='download'){
					s.text='下载';
				}else if(s.actname=='downopen'){
					s.text='下载并打开';
				}else if(s.actname=='upload'){
					s.text='上传';
				}
			},
			submit:function(){
				const s=this;
				s.blocksubmit();
			},
			execute:function(){
				const s=this;
				const blocks=[];
				const cont=s.blockinfo(s.block.btn_action_content||'',s.cont||{});
				if(s.actname=='star'){
					s.addstar();
				}else if(s.actname=='download'||s.actname=='downopen'){
					s.download(cont,function(res){
						if(s.isstr(res)){
							s.openfile(res);
						}else if(!res){
							s.msg('下载失败');
						}
					});
				}else if(s.actname=='upload'){
					console.log('todo');
				}else if(s.actname=='fun'){
					const regex = /([a-zA-Z_]+)\((.*)\)/;
					const match = cont.match(regex);
					if (match) {
					  const functionName = match[1];
					  const argsString = match[2];
					  const args = argsString;
					  s[functionName](...args);
					}
				}
			},
			addstar:function(){
				const s=this;
				var name='star_'+(s.id);
				if(s.storage(name)){
					s.text='点赞';
					s.storage(name,null);
					s.request({url:s.url('index/star',s.urldata(s.options)),data:{star:'remove'}});
				}else{
					s.text='已赞';
					s.storage(name,1);
					s.request({url:s.url('index/star',s.urldata(s.options)),data:{star:'add'}});
				}
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
			s.xginit();
			s.render();
		},
		onShareAppMessage(){
			const s=this;
			return {title:s.info.title,path:'/pages/page?page='+s.pagename+'&id='+s.id}
		}
	}
</script>

<style scoped>
.xg-icon{display:inline-block;margin-right:0.3rem;margin-left:-0.2rem;font-size:1.2em;}
.xg-btn{background:var(--theme-color);width:var(--block-width);height:var(--block-height);margin:var(--block-margin);padding:var(--block-padding,0 0.8rem);border-radius:var(--block-radius,0.4rem);font-size:var(--block-fontsize);color:var(--block-color);}
.xg-btn-s{padding:var(--block-padding,0 0.5rem);border-radius:var(--block-radius,0.3rem);}
.xg-btn-s .xg-icon{margin-right:0.2rem;margin-left:-0.1rem;}
.xg-btn-l{padding:var(--block-padding,0 1rem);border-radius:var(--block-radius,0.5rem);}
.xg-btn-l .xg-icon{margin-right:0.4rem;margin-left:-0.3rem;}
</style>