
const basemixin={
	data(){
		return {
			showwxlogin:false,
			fontsize:16,
			page:1,
		}
	},
	props:{
	},
	methods: {
	},
	computed:{
		options:{
			get(){
				const s=this;
				return s.g.options||{};
			},
			set(n){
				const s=this;
				if(n.page)s.page=n.page;
				s.g.options=n;
			}
		},
		getinfo:function(){
			const s=this;
			let info={};
			for(let name in s.systems){
				let sysi=s.systems[name];
				if((s.block&&s.block.sys&&s.block.sys==name)||(s.pagedata&&s.pagedata.sys&&s.pagedata.sys==name)||s.options[sysi.cateid]||(s.g.sys&&s.g.sys==name)){
					info={sys:name,idname:sysi.contid,cidname:sysi.cateid,url:sysi.url};
					break;
				}
			}
			return info;
		},
		sys:function(){
			const s=this;
			return s.getinfo.sys||'xg';
		},
		cidname:function(){
			const s=this;
			return s.getinfo.cidname||'cid';
		},
		idname:function(){
			const s=this;
			return s.getinfo.idname||'id';
		},
		cidnames:function(){
			const s=this;
			const names={};
			if(s.g.datas){
				const systems=s.systems;
				for(let sys in systems){
					if(!systems[sys]||!systems[sys].cateid)continue;
					names[sys]=systems[sys].cateid;
				}
			}
			return names;
		},
		idnames:function(){
			const s=this;
			const names={};
			if(s.g.datas){
				const systems=s.systems;
				for(let sys in systems){
					if(!systems[sys]||!systems[sys].contid)continue;
					names[sys]=systems[sys].contid;
				}
			}
			return names;
		},
		cid:function(){
			const s=this;
			if(s.block&&s.block.cateid){
				const cateid=s.block.cateid;
				if(s.isobj(cateid)){
					for(let name in s.systems){
						let sysi=s.systems[name];
						if(cateid.key==sysi.cateid){
							s.block.sys=name;
							return cateid[cateid.key];
						}
					}
				}
			}
			return s.options[s.cidname]||s.blockinfo(s.pagedata.cateid)||s.cateid;
		},
		id:function(){
			const s=this;
			return s.options[s.idname]||s.blockinfo(s.pagedata.contid);
		},
		cates:function(){
			const s=this;
			if(s.g.datas&&s.g.datas.cates&&s.sys){
				return s.g.datas.cates[s.sys];
			}
			return {};
		},
		model:function(){
			const s=this;
			if(s.cates&&s.cid){
				return s.cates[s.cid]&&s.cates[s.cid].model;
			}
		},
		count:function(){
			const s=this;
			if(s.cates&&s.cid){
				let c=count(s.cid);
				return c;
			}
			function count(pid) {
				let sum = 0;
				if(s.cates[pid]){
					sum += parseInt(s.cates[pid].count);
				}
				for (let id in s.cates) {
					const cate = s.cates[id];
					if (cate.pid===pid) {
						sum += count(s.cates, id);
					}
				}
				return sum;
			}
			return 0;
		},
		contlinks:function(){
			const s=this;
			const links=(s.g.datas||{}).links||{};
			return links.cont;
		},
		catelinks:function(){
			const s=this;
			const links=(s.g.datas||{}).links||{};
			return links.cate;
		},
		topiclinks:function(){
			const s=this;
			const links=(s.g.datas||{}).links||{};
			return links.topic;
		},
		pages:function(){
			const s=this;
			return (s.g.datas||{}).pages||{};
		},
		systems:function(){
			const s=this;
			return (s.g.datas||{}).systems||{};
		},
		pagedata:function(){
			const s=this;
			return (s.pages[s.pagename]||{}).data||{};
		},
		pagename:function(){
			const s=this;
			return s.options.pagename||'index';
		}
	}
};
const mixin={
	data(){
		return {
			block:{},
			conts:[],
			cont:{},
			user:null,
			classnames:[],
			on:{}
		}
	},
	mixins:[basemixin],
	props:{
		sysdata:String,
		contsdata:Array,
		blockdata:Object,
		contdata:Object,
		userdata:Object,
		cateid:[String,Number],
		theme_color:String,
		class:[Array,Object,String]
	},
	computed:{
	},
	methods: {
		xginit:function(){
			const s=this;
			s.page=s.options.page||s.page;
			s.o('xg-emit',function(d){
				if(d.key=='wxlogin'||d.key=='wxmobile'){
					return;
				}
				let key,val,data;
				key=d.key.split('.');
				val=d.val;
				let block=d&&d.block;
				if(!block||block==s.block.bid||block==s.xgname||'xg-'+block==s.xgname){
					const vname=key[0];
					const kname=key[1];
					if(vname=='on'&&s.on){
						data=s.on;
					}else if(vname=='cont'&&s.cont){
						data=s.cont;
					}else if(vname=='conts'&&s.conts){
						data=s.conts;
					}else if(vname=='block'&&s.block){
						data=s.block;
					}
					if(data){
						if(s.isunde(val)){
							if(data[kname]){
								val=false;
							}else{
								val=true;
							}
						}
						data[kname]=val;
						if(s.block.show){
							s.block.show='1&&'+s.block.show;
						}
					}
				}
			});
		},
		linkclick:function(e){
			const s=this;
			if(s.block&&s.block.data_url){
				s.blocksubmit();
				return;
			}
			if(s.block&&s.block.emit){
				s.blockemit();
				return;
			}
			if(s.block&&s.cont&&s.block.link_data){
				e={};
				let data=s.block.link_data.split(',');
				for(let i in data){
					e[data[i]]=s.cont[data[i]];
				}
				e=s.linkdata(e);
			}
			if(s.block&&s.block.link){
				let link=s.block.link;
				if(s.cont){
					link=s.blockinfo(s.block.link,s.cont);
				}
				e=link;
			}
			s.link(e);
		}
	},
	directives: {
		'dom-update': {
			mounted(el,binding){
				const s=binding.instance;
				s.hooks('dom-updated',el,s);
			}
		}
	},
	watch:{
		class:{
			handler(n,o){
				const s=this;
				for(let i in s.classnames){
					if(o===s.classname[i])delete s.classname[i];
				}
				s.classnames.push(n);
			},
			immediate:true,
			deep:true
		},
		blockdata:{
			handler(n,o){
				const s=this;
				if(n){
					if(n.cateid&&n.cateid.cid)n.cateid=n.cateid.cid;
					s.block=n;
					if(n.classnames)s.classnames.push(n.classnames);
					if(s.isfun(s.render))s.render();
					s.hooks('block-mutated',s,n.bid);
				}
			},
			immediate:true,
			deep:true,
		},
		contsdata:{
			handler(n,o){
				const s=this;
				if(s.block.info_left||s.block.info_right){
					for(let i in n){
						n[i].left=s.blockinfo(s.block.info_left,n[i]);
						n[i].right=s.blockinfo(s.block.info_right,n[i]);
					}
				}
				s.conts=n;
			},
			immediate:true,
			deep:true,
		},
		contdata:{
			handler(n,o){
				const s=this;
				if(n&&s.isobj(n)&&!s.iseobj(n)){
					s.cont=n;
					if(n!==o&&s.isfun(s.render))s.render();
				}
			},
			immediate:true,
			deep:true,
		},
		userdata:{
			handler(n,o){
				const s=this;
				s.user=n;
			},
			immediate:true,
			deep:true,
		}
	},
	computed:{
		showself:function(){
			const s=this;
			var show=true;
			if(s.block&&s.block.show){
				show=s.blockcond(s.block.show);
			}
			return show;
		},
		mainstyles:function(){
			const s=this;
			const block=s.block;
			const style={}
			style['--theme-color']=s.theme_color||s.c.theme_color;
			if(!block)return style;
			if(block.margin){
				const margin=s.style4in1(block.margin);
				style['--block-margin-left']=margin.left;
				style['--block-margin-right']=margin.right;
			}else{
				style['--block-margin-left']='0px';
				style['--block-margin-right']='0px';
			}
			style['--block-border-width']=block.border_width||'1px';
			if(block.styles)style['block-style']=[block.styles];
			if(block.theme_color)style['--theme-color']=block.theme_color;
			if(block.theme_color2)style['--theme-color2']=block.theme_color2;
			if(block.z_index)style['--block-z-index']=block.z_index;
			if(block.border)style['--block-border']=block.border;
			if(block.weight)style['--block-weight']=block.weight;
			if(block.margin)style['--block-margin']=block.margin;
			if(block.padding)style['--block-padding']=block.padding;
			if(block.width)style['--block-width']=block.width;
			if(block.height)style['--block-height']=block.height;
			if(block.align)style['--block-align']=block.align;
			if(block.line_height)style['--block-line-height']=block.line_height;
			if(block.radius)style['--block-radius']=block.radius;
			if(block.bg_color)style['--block-bg']=block.bg_color;
			if(block.text_color)style['--text-color']=block.text_color;
			if(block.fontsize)style['--block-fontsize']=block.fontsize;
			return style;
		},
	}
};
export {mixin,basemixin};