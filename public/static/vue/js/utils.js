import {md5} from './md5.js';
var session_id;
var options;
var xgurl;
var isstr=function(v){return type(v)=='string';}
var obj2arr=function(v){var r=[];for(var i in v){r.push(v[i]);}return r;}
var isobj=function(v){return type(v)=='object';}
var iseobj=function(v){if(isobj(v)){for(var i in v){return false;}return true;}return false;}
var isarr=function(v){return type(v)=='array';}
var isfun=function(v){return type(v)=='function';}
var isnum=function(v){return type(v)=='number';}
var isnan=function(v){return isNaN(v);}
var isbool=function(v){return type(v)=='boolean';}
var isunde=function(v){return type(v)=='undefined';}
var isnull=function(v){return v===null;}
var isfile=function(v){return type(v)=='file';}
var ismobile=function(v){
	return /^1[3|4|5|6|7|8|9]{1}[\d]{9}$/.test(v.replace('+86',''));
}
var isemail=function(v){
	return /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]{2,10}){1,2}$/.test(v);
}
var type=function(v){
	if(v===null)return 'null';
	var type=typeof(v);
	if(type=='object')return((Array.isArray||function(v){return Object.prototype.toString.call(v)==='[object Array]';})(v)?'array':'object');
	return type;
}

function fileurl(url,surl){
	if(!isstr(url))return '';
	if(url&&(url.substr(0,7)=='http://'||url.substr(0,8)=='https://'||url.substr(0,2)=='//'))return url;
	if(surl){
		xgurl=surl;
	}else if(this){
		const s=this;
		var xgurl=s.getinfo.imgurl||s.getinfo.url||s.xgimgurl||s.c.xgimgurl||s.xgurl;
	}
	return xgurl+url;
}

function xgset(url){
	xgurl=url;
}

function url(u, param) {
	const s=this;
	if (!param) var param = {};
	var match=/([a-zA-Z0-9\-_]+)@([a-zA-Z0-9\-_]+).?([a-zA-Z0-9\-_]*)/.exec(u);
	if(match){
		let sys=match[1];
		let ctl=match[2];
		let act=match[3];
		let params=Object.assign({sys:sys,controller:ctl,action:act},param);
		return url('/app/sys/execute',params);
	}
	if (u.indexOf('?') > -1) {
		var arr = u.split('?');
		u = arr[0];
		var query1 = arr[1];
		if (query1) {
			var query2 = query2obj(query1);
			if (query2) param = extend(query2, param);
		}
	}
	if(!s.c||!s.c.route){
		u=u.replace('.html','');
		var arr = u.trim('/').split('/');
		var app = 'app';
		var ctl = 'index';
		var act = 'index';
		if (arr.length == 1) {
			var ctl = 'index';
			if (arr[0]) {
				var act = arr[0];
			} else {
				var act = ctl;
			}
		} else if (arr.length == 2) {
			var ctl = arr[0];
			var act = arr[1];
		} else if (arr.length == 3) {
			var app = arr[0];
			var ctl = arr[1];
			var act = arr[2];
		} else if (arr.length == 4) {
			var app = arr[1];
			var ctl = arr[2];
			var act = arr[3];
		}
		if(session_id)param['set_session_id']=session_id;
		param['appname']=platform();
		param['jstime']=Math.random();
		return `${xgurl}/index.php?xgapp=${app}&xgctl=${ctl}&xgact=${act}&${obj2query(param)}`;
	}
	if(u.substr(0,1)!='/'){
		var arr = u.trim('/').split('/');
		var app = 'app';
		if (arr.length == 1) {
			var ctl = 'index';
			if (arr[0]) {
				var act = arr[0];
			} else {
				var act = ctl;
			}
		} else if (arr.length == 2) {
			var ctl = arr[0];
			var act = arr[1];
		} else if (arr.length == 3) {
			var app = arr[0];
			var ctl = arr[1];
			var act = arr[2];
		}
		u='/' + app + '/' + ctl + '/' + act;
	}
	if(session_id)param['set_session_id']=session_id;
	param['appname']=platform();
	param['jstime']=Math.random();
	return xgurl + u + (Object.keys(param).length > 0 ? '?' + obj2query(param) : '');
}
function copydata(info,callback){
	if(!info){
		if(typeof callback=='function')callback(false);
		return;
	}
	if(inxg()){
		xg.copy(info);
	}else{
		uni.setClipboardData({
			data: info,
			success: e => {
				if(typeof callback=='function')callback(true);
			},
			fail: e => {
				if(typeof callback=='function')callback(false);
			}
		});
	}
}
function extend() {
	const c = {};
	const list=[];
	var deep=false;
	for(let i in arguments){
		if(isarr(arguments[i])||isobj(arguments[i])){
			list.push(arguments[i]);
		}else if(arguments[i]===true){
			deep=true;
		}
	}
	for(let i in list){
		for(let j in list[i]){
			c[j]=deep?extend(c[j],list[i][j],true):list[i][j];
		}
	}
	return c;
}

function query2obj(data){
	if(!data)return {};
	if(data.indexOf('?')>-1){
		data=data.substr(data.indexOf('?')+1);
	}
	var arr=data.split('&');
	var newdata={};
	for(var i in arr){
		var arr2=arr[i].split('=');
		if(arr2.length==2&&arr2[0]){
			newdata[decodeURIComponent(replace(arr2[0],'+',' '))]=decodeURIComponent(replace(arr2[1],'+',' '));
		}
	}
	return newdata;
}

function goto(link){
	uni.navigateTo({url:link,fail:function(){uni.redirectTo({url:link});}});
}

function obj2query(param) {
	var arr = [];
	for (var i in param) {
		arr.push(encodeURIComponent(i) + '=' + encodeURIComponent(param[i]));
	}
	return arr.join('&');
}

function replace(str, from, to) {
	while (str && str.indexOf(from) > -1) {
		str = str.replace(from, to);
	}
	return str;
}

function platform(){
	var platform = '';
	if(inxg())return 'preview';
	//#ifdef APP-PLUS
	platform = 'AppPlus';
	//#endif
	//#ifdef APP-PLUS-NVUE
	platform = 'AppPlusNvue';
	//#endif
	//#ifdef H5
	platform = 'H5';
	//#endif
	//#ifdef MP
	platform = 'Mp';
	//#endif
	//#ifdef MP-WEIXIN
	platform = 'MpWeixin';
	//#endif
	//#ifdef MP-ALIPAY
	platform = 'MpAlipay';
	//#endif
	//#ifdef MP-BAIDU
	platform = 'MpBaidu';
	//#endif
	//#ifdef MP-TOUTIAO
	platform = 'MpToutiao';
	//#endif
	//#ifdef MP-QQ
	platform = 'MpQq';
	//#endif
	//#ifdef MP-360
	platform = 'Mp360';
	//#endif
	//#ifdef quickapp-webview
	platform = 'QuickappWebview';
	//#endif
	//#ifdef quickapp-webview-union
	platform = 'QuickappWebviewUnion';
	//#endif
	//#ifdef quickapp-webview-huawei
	platform = 'QuickappWebviewHuawei';
	//#endif
	return platform
}


function getlocation(callback){
	const s = this
	// #ifdef H5
		uni.getLocation({
			success: function(data) {
				if(s.isfun(callback))callback([data.longitude,data.latitude]);
			},
			fail:function(data){
				msg(JSON.stringify(data));
			}
		});
	// #endif
}

function inxg(){
	// #ifdef H5
	return !isunde(window.xg);
	// #endif
}

function request(info) {
	const s=this;
	const dataType = info.dataType||'json';
	info.method = info.method || 'GET';
	info.header = info.header || {};
	info.data = info.data || {};
	info.header = header(info.header);
	if(s.isunde(info.loading))info.loading = 1;
	if(info.loading)s.loading(1);
	if(storage('session_id'))info.url+='&set_session_id='+storage('session_id');
	if(inxg()){
		var request=$.ajax;
	}else{
		var request=uni.request;
	}
	return request({
		url: info.url,
		data: info.data || {},
		header: info.header,
		method: info.method,
		dataType: dataType,
		success: data => {
			if(!inxg()){
				var data=data.data;
			}
			if (data.session_id) {
				session_id = data.session_id;
				storage('session_id', session_id);
			}
			if (typeof info.success == 'function') {
				info.success(data);
			}
		},
		fail: (data, code) => {
			if (typeof data.fail == 'function') {
				info.data.fail(data, code);
			}
		},
		complete: (a) => {
			if (typeof info.complete == 'function') {
				info.complete(a);
			}
			if(info.loading)s.loading(-1);
		}
	});
	function header(head) {
		var header = {};
		header['content-type'] = 'application/x-www-form-urlencoded';
		header['X-Requested-With'] = "XMLHttpRequest";
		for (var i in head) {
			header[i] = head[i];
		}
		return header;
	}
}

function msg(msg,callback){
	uni.showModal({
		title: '提示消息',
		content: msg,
		showCancel: false,
		success: function(data) {
			if (typeof(callback) == 'string') {
				uni.navigateTo({
					url: callback
				})
			} else if (typeof(callback) == 'number') {
				uni.navigateBack({
					delta: callback
				})
			} else if (typeof(callback) == 'function') {
				callback(data);
			}
		}
	})
}
function confirm(msg,callback,cancel){
	uni.showModal({
		title: '提示消息',
		content: msg,
		showCancel: true,
		success: function(res) {
			if(res.confirm){
				if (isstr(callback)) {
					uni.navigateTo({url:callback});
				} else if (isnum(callback)) {
					uni.navigateBack({delta:callback});
				} else if (isfun(callback)) {
					callback(res);
				}
			}
			if(res.cancel){
				if(isfun(cancel))cancel(res);
			}
		}
	})
}
var loadingtimer=0,loadingcount=0,loadingthis=[];
function loading(i){
	const s=this;
	loadingthis.push(s);
	s.showloading=true;
	loadingcount+=i;
	if (loadingcount<=0)loadingtimer=setTimeout(function(){
		for(var j in loadingthis){
			loadingthis[j].showloading=false;
		}
		loadingthis=[];
		clearTimeout(loadingtimer);
		loadingtimer=null;
	},300);
}


function date(type){
	return s.format(new Date().getTime(),type);
}


function format(info,type,nozero){
	if(typeof info=='string'){
		if(info.includes('-')||info.includes(':')){
			try{
				info=Date.parse(info);
			}catch(e){
				info=parseInt(info);
			}
		}else{
			info=parseInt(info);
		}
		
	}
	if (typeof info == 'number' && info<100000000000) {
		info=info*1000;
	}
	if(!info)return '';
	info = new Date(info);
	const year = number(info.getFullYear());
	const month = number(info.getMonth() + 1);
	const day = number(info.getDate());
	const hour = number(info.getHours());
	const minute = number(info.getMinutes());
	const second = number(info.getSeconds());
	if(type==0){
		return [year, month, day].join('-');
	}else if(type==1){
		return [hour, minute, second].join(':');
	}else if(type==2){
		return [year, month, day].join('-') + ' ' + [hour, minute/* , second */].join(':');
	}else if(type==3){
		return year+'年'+month+'月'+day+'日';
	}else if(isstr(type)){
		return {y:year,m:month,d:day,h:hour,i:minute,s:second}[type];
	}else if(isarr(type)){
		let result='';
		for(let i in type){
			result+=format(info,type[i]);
		}
		return result;
	}
	function number(n){
		if(nozero)return n;
		n = n.toString();
		return n[1] ? n : '0' + n
	}
}

function style4in1(style){
	var style = style.trim().split(' ');
	if (style.length == 1) {
		return {top:style[0],right:style[0],bottom:style[0],left:style[0]};
	} else if (style.length == 2) {
		return {top:style[0],right:style[1],bottom:style[0],left:style[1]};
	} else if (style.length == 3) {
		return {top:style[0],right:style[1],bottom:style[2],left:style[1]};
	} else if (style.length == 4) {
		return {top:style[0],right:style[1],bottom:style[2],left:style[1]};
	}
}

function cachepath(name,type,sys){
	var file='/data/'+sys+'/'+type+'/'+md5(type+'-'+name).substr(0,2)+'/'+md5(type+'-'+name).substr(2)+'.json';
	if(inxg()&&type!='nav')var file='/app/index/data';
	return file;
}

function storage(name,val){
	if(inxg()){
		return xg.storage(name,val);
	}
	if(val===null){
		return uni.removeStorageSync(name);
	}else if(val){
		return uni.setStorageSync(name,val);
	}else{
		return uni.getStorageSync(name);
	}
}

function popup(o){
	const s=this;
	return s.g.box(o);
}

function rmbdfiles(){
	const fileSystemManager = swan.getFileSystemManager();
	fileSystemManager.getSavedFileList({
		success: res => {
			for(let i in res.fileList){
				fileSystemManager.removeSavedFile({
					filePath: res.fileList[i].filePath,
					success: res => {
						console.log(`remove file ${res.fileList[i].filePath} success`);
					},
					fail: err => {
						console.log(`remove file fail`,err);
					}
				});
			}
		},
		fail: err => {
			console.log('getSavedFileList fail', err);
		}
	});
}

function unzip(filepath,callback){
	const s=this;
	console.log(filepath,'unzip');
	// #ifdef MP-BAIDU
	const fileSystemManager = swan.getFileSystemManager();
	const zipname=Math.ceil(Math.random()*1000000);
	fileSystemManager.saveFileSync(filepath, `${swan.env.USER_DATA_PATH}/${zipname}.zip`);
	fileSystemManager.unzip({
		zipFilePath: `${swan.env.USER_DATA_PATH}/${zipname}.zip`,
		targetPath: `${swan.env.USER_DATA_PATH}/${zipname}`,
		success: res => {
			console.log('unzip success', res);
			s.filelist(`${swan.env.USER_DATA_PATH}/${zipname}`,callback);
		},
		fail: err => {
			swan.showToast({
				title: '解压文件失败',
				icon: 'none'
			});
			console.log('unzip fail', err);
		}
	});
	// #endif
}

function openfile(filepath,callback){
	const s=this;
	if(isarr(filepath)){
		let list=filepath;
		for(let i in list){
			if(!list[i]||['doc','xls','ppt','pdf','docx','xlsx','pptx','zip'].indexOf(list[i].split('.').pop().toLowerCase())===-1){
				list.splice(i,1);
			}
		}
		if(list.length===0){
			s.msg('文件列表为空');
		}else if(list.length===1){
			let filepath=list.pop();
			s.openfile(filepath,callback);
		}else{
			let items=[];
			for(let i in list){
				let filepath=list[i];
				let filename=filepath.split('/').pop();
				items.push({block:'custom',custom:filename,click:function(){
					s.openfile(filepath);
					callback(!!1);
				}});
			}
			s.popup({blocks:items,title:'请选择需要打开的文件'});
		}
	}else if(isobj(filepath)){
		const filelist={};
		for(let name in filepath){
			if(!filepath[name]||['doc','xls','ppt','pdf','docx','xlsx','pptx','zip'].indexOf(filepath[name].split('.').pop().toLowerCase())>-1){
				filelist[name]=filepath[name];
			}
		}
		const filearr=s.obj2arr(filelist);
		if(filearr.length===0){
			s.msg('文件列表为空');
		}else if(filearr.length===1){
			let filepath=filearr.pop();
			s.openfile(filepath,callback);
		}else{
			let items=[];
			for(let name in filelist){
				items.push({block:'custom',custom:name,click:function(){
					s.openfile(filelist[name]);
					callback(!!1);
				}});
			}
			s.popup({blocks:items,title:'请选择需要打开的文件'});
		}
	}else if(isstr(filepath)){
		if(filepath.split('.').pop().toLowerCase()=='zip'){
			s.unzip(filepath,function(filelist){
				s.openfile(filelist);
			});
		}else if(['doc','xls','ppt','pdf','docx','xlsx','pptx'].indexOf(filepath.split('.').pop().toLowerCase())>-1){
			swan.openDocument({
				filePath: filepath,
				fileType: filepath.split('.').pop(),
				success: res => {
					if(isfun(callback))callback(res);
					console.log('openDocument success', res);
				},
				fail: err => {
					if(isfun(callback))callback(!!0);
					s.msg('打开文件失败');
					console.log('openDocument fail', err);
				}
			});
		}
	}
}

function filelist(filepath,callback){
	// #ifdef MP-BAIDU
	const fileSystemManager = swan.getFileSystemManager();
	fileSystemManager.getSavedFileList({
		path:filepath,
		success: res => {
			const list=[];
			for(let i in res.fileList){
				let file=res.fileList[i].filePath;
				if(file.indexOf(filepath+'/')===0){
					list.push(file);
				}
			}
			if(isfun(callback)){
				callback(list);
			}
		},
		fail: err => {
			swan.showToast({
				title: '读取文件列表失败',
				icon: 'none'
			});
			console.log('access fail', err);
		}
	});
	// #endif
}
function uploadimg(url,callback,count){
	const s=this;
	uni.chooseImage({
		count: count||1,
		sizeType: ['compressed'],
		sourceType: ['album'],
		success: (res) => {
			console.log('chooseImage success', res.tempFilePaths)
			for(let imageSrc of res.tempFilePaths){
				uni.uploadFile({
					url: url,
					filePath: imageSrc,
					fileType: 'image',
					name: 'file',
					header:{'X-Requested-With':'XMLHttpRequest'},
					success: (res) => {
						if(s.isbool(res.ok)){
							callback(res);
						}else{
							callback(JSON.parse(res.data));
						}
					},
					fail: (err) => {
						callback(false,err.errMsg,err);
					}
				});
			}
		},
		fail: (err) => {
			console.log('chooseImage fail', err);
			if(!s.inxg()){
				// #ifdef MP
				uni.getSetting({
					success: (res) => {
						let authStatus = res.authSetting['scope.album'];
						if (!authStatus) {
							uni.showModal({
								title: '授权失败',
								content: '需要从您的相册获取图片，请在设置界面打开相关权限',
								success: (res) => {
									if (res.confirm) {
										uni.openSetting()
									}
								}
							})
						}
					}
				})
				// #endif
			}
		}
	})
}
function download(url,filename,callback){
	const s=this;
	if(isfun(filename)){
		callback=filename;
		filename=null;
	}
	if(!filename)filename=url.split('/').pop();
	var hooks=s.hooks('download-before',{url,filename,callback});
	if(hooks.stop)return;
	// #ifdef H5
	var a = document.createElement('a');
	a.href = url;
	a.download = filename;
	a.click();
	if(isfun(callback))callback(!!1);
	if(1)return;
	// #endif
	// #ifndef H5
	// #ifdef MP-BAIDU
	rmbdfiles();
	// #endif
	uni.downloadFile({
		url: url,
		success: (res) => {
			if (res.statusCode === 200) {
				const result=res.tempFilePath;
				var hooks=s.hooks('download-after',{url,filename,callback,result});
				if(hooks.stop)return;
				if(isfun(callback))callback(result);
			}
		},fail:function(){
			if(isfun(callback))callback(!!0);
		}
	});
	// #endif
}
var px=function(v,u){
	var v=isstr(v)?v.toLowerCase():v;
	if(isstr(v)&&(v.substr(-1)=='%'||v.substr(-2)=='px'||v.substr(-2)=='em'||v.substr(-3)=='rem'))return v;
	if(isnum(v)||!isnan(v))return v+(u?u:'px');
	return '';
}

function hooks(name){
	const s=this;
	if(s.g&&s.g.hooks){
		if(!isobj(s.g.hooks[name]))s.g.hooks[name]={};
		const datas={};
		for(let key in s.g.hooks[name]){
			if(isfun(s.g.hooks[name][key])){
				let args=[];
				for(let i=1;i<arguments.length;i++){
					args.push(arguments[i]);
				}
				args.push(datas);
				let rt=s.g.hooks[name][key].apply(s,args);
				if(!isunde(rt)){
					datas[key]=rt;
					if(rt===false){
						datas.stop=true;
					}
				}
			}
		}
		return datas;
	}
}

function hook(name){
	const s=this;
	if(s.g&&s.g.hooks){
		if(!isobj(s.g.hooks[name]))s.g.hooks[name]={};
		var datas={};
		for(let key in s.g.hooks[name]){
			if(isfun(s.g.hooks[name][key])){
				let args=[];
				for(let i=1;i<arguments.length;i++){
					args.push(arguments[i]);
				}
				args.push(datas);
				let rt=s.g.hooks[name][key].apply(s,args);
				if(!isunde(rt)){
					return rt;
				}
			}
		}
	}
}
function reghook(name,key,fun){
	const s=this;
	if(!['stop'].includes(key)){
		s.g.hooks=s.g.hooks||{};
		s.g.hooks[name]=s.g.hooks[name]||{};
		s.g.hooks[name][key]=fun;
	}
};

function region(pid,callback){
	const s=this;
	s.request({url:url('app/region/region',{pid}),success:function(res){
		if(res.ok===true){
			callback(res.data);
		}
	}});
}




function o(n,v,o){
	if(isfun(v)){
		if(o===1){
			uni.$once(n,v);
		}else{
			uni.$on(n,v);
		}
	}else{
		uni.$emit(n,v);
	}
}
function str2val(str,state){
	if(!state)return;
	const keys = str.split('.');
	let value = state;
	for (const key of keys) {
		if(value&&isobj(value)){
			value=value[key];
		}else{
			return;
		}
	}
	return value.value;
}

function findobj(obj, key, value) {
	if (typeof obj === 'object' && obj !== null) {
		if (Array.isArray(obj)) { // 如果是数组
			for (let i = 0; i < obj.length; i++) {
				const result = findobj(obj[i], key, value);
				if (result !== null) {
					return result;
				}
			}
		} else {
			if (obj[key] === value) {
				return obj;
			}
			const keys = Object.keys(obj);
			for (let i = 0; i < keys.length; i++) {
				const result = findobj(obj[keys[i]], key, value);
				if (result !== null) {
					return result;
				}
			}
		}
	}
	return null;
}
function findobjs(obj, key, value) {
	let results = [];
	function search(obj) {
		if (typeof obj === 'object' && obj !== null) {
			if (Array.isArray(obj)) {
				for (let i = 0; i < obj.length; i++) {
					search(obj[i]);
				}
			} else {
				if (obj[key] === value) {
					results.push(obj);
				}
				const keys = Object.keys(obj);
				for (let i = 0; i < keys.length; i++) {
					search(obj[keys[i]]);
				}
			}
		}
	}
	search(obj);
	return results;
}
function op2obj(str){
	var obj = {};
	str.replace(/[\r\n]+/g, '\n').split('\n').forEach(function(item) {
	  var pair = item.split('=');
	  obj[pair[0]] = pair[1];
	});
	return obj;
}
export {
	hook,hooks,reghook,copydata,goto,o,op2obj,
	xgset,fileurl,inxg,uploadimg,download,openfile,filelist,unzip,popup,
	request,url,storage,loading,msg,confirm,getlocation,
	extend,obj2query,query2obj,replace,date,format,cachepath,md5,
	style4in1,px,ismobile,isemail,region,
	isstr,isobj,iseobj,isarr,isfun,isnum,isnan,isbool,isunde,isnull,isfile,type,obj2arr,findobj,findobjs
};