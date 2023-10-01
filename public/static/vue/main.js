// import * as u from './js/utils.js';
// import d from './js/data.js';
// import c from './config.js';
// import App from './App';
// import {createSSRApp} from 'vue';
// export function createApp(){
// 	const app = createSSRApp(App);
// 	init(app.config.globalProperties);
// 	return {app};
// }

//import shijuan from './hooks/shijuan.js';
function inithooks(s){
	//这里初始化钩子
	//shijuan(s);
}

// #ifdef H5
window.uniapp={};
window.init=init;
// #endif
function init(s,u,d,c){
	s.vue=Vue;
	const app=getApp();
	app.globalData=app.globalData||{};
	s.g=app.globalData;
	s.xgurl=c.xgurl;
	u.xgset(s.xgurl);
	s.c=c;
	for(let i in u){
		s[i]=u[i];
	}
	for(let i in d){
		s[i]=d[i];
	}
	s.inithooks=inithooks;
}
