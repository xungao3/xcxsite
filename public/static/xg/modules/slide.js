/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
(function(win,doc,$){
xg.def('slide',function(){
	var S=function(sl,o){
		const s=this;
		win.xg_slide_list=win.xg_slide_list||{};
		win.xg_slide_list[sl]=s;
		win.xg_slide_list[$(sl)]=s;
		s.div=$(sl);
		s.ul=s.div.find('ul');
		s.lis=s.ul.find('li');
		if(s.lis.length==0)return s;
		s.stoping=false;
		s.timer=0;
		s.showing=0;
		s.left=0;
		s.top=0;
		s.points=xg.newdiv('xg-slide-points');
		s.playing=false;
		o=$.extend({},{time:5000,width:null,height:null,vertical:false,touch:true,autoimg:true,autosize:true},o);
		s.o=o;
		s.aning=false;
		if(s.lis.length>1){
			for(i=0;i<s.lis.length;i++){
				xg.newdiv((i==0)?'xg-this xg-slide-point':'xg-slide-point').attr('xg-id',i).mouseenter(function(){s.show(xg.int($(this).attr('xg-id')));}).appendTo(s.points);
			}
		}
		s.div.append(s.points);
		s.point=s.points.find('div');
		$(window).resize(function(){if(s.o.autosize)s.size(1);});
		if(s.o.autosize)s.size();
		for(ii=0;(ii<s.lis.length&&ii<3);ii++){
			if(!s.lis.eq(ii).find('img').attr('src'))s.lis.eq(ii).find('img').attr('src',s.lis.eq(ii).find('img').attr('xg-src'));
		}
		if(s.o.autoimg&&s.div.find('img').length){
			xg.img.load(s.div.find('img:first'),function(){s.show(0);});
			xg.img.resize(s.div);
		}else{
			s.show(0);
		}
		if(o.touch)s.touch();
		return s;
	}
	S.prototype.size=function(resize){
		const s=this;
		if(resize)s.div[0].style.removeProperty('width');
		if(s.o.width)s.div.width(s.o.width);
		s.width=s.div.width();
		if(resize)s.div[0].style.removeProperty('height');
		if(s.o.height){
			if(!xg.isnan(s.o.height)&&s.o.height<10)s.o.height=s.width*s.o.height;
			s.div.height(s.o.height);
		}
		s.height=s.div.height();
		s.div.width(s.width);
		//s.div.height(s.height);
		if(s.o.vertical)s.ul.width(s.width).height(s.lis.length*s.height);
		if(!s.o.vertical)s.ul.width(s.lis.length*s.width).height(s.height);
		s.lis.width(s.width);
		//s.lis.height(s.height);
		if(s.o.autoimg){
			s.lis.find('img').width(s.width);
			s.lis.find('img').height(s.height);
			//s.lis.find('img').removeAttr('xg-img-resize').css({marginLeft:null,marginTop:null});
			xg.img.resize(s.lis);
		}
		s.show(s.showing);
	}
	S.prototype.play=function(n){
		const s=this;
		if(s.stoping)return;
		if(!xg.isnum(n))n=1;
		var show=s.showing+n;
		if(show>=s.lis.length)show=0;
		if(show<0)show=s.lis.length-1;
		s.show(show);
		return s;
	}
	S.prototype.touch=function(){
		const s=this;
		var dis=0;
		xg.touch(s.div,{
			start:function(e){
				if(s.aning)return;
				s.stoping=true;
				s.retime();
				dis=0;
				if(xg.isfun(s.o.touch.start))s.o.touch.start(e);
			},
			click:function(e){
				return Math.abs(dis)<5;
			},
			move:function(e){
				if(s.aning)return;
				s.stoping=false;
				if(s.o.vertical){
					s.y(e.y);
					dis+=e.y;
				}else{
					s.x(e.x);
					dis+=e.x;
				}
				s.retime();
				if(xg.isfun(s.o.touch.move))s.o.touch.move(e);
				e.e.preventDefault();
			},
			end:function(e){
				if(s.aning)return;
				var next=null;
				if(Math.abs(dis)>5){
					if(Math.abs(e.x)>Math.abs(e.y)){
						if(!s.o.vertical)next=e.x>0;
					}else{
						if(s.o.vertical)next=e.y>0;
					}
				}
				if(!xg.isnull(next)){
					if(next){
						if(s.showing<s.lis.length-1){
							s.play(1);
						}else{
							s.show(s.showing);
						}
					}else{
						if(s.showing>0){
							s.play(-1);
						}else{
							s.show(s.showing);
						}
					}
				}else if(Math.abs(dis)>5){
					s.show(s.showing)
				}
				s.stoping=false;
				if(xg.isfun(s.o.touch.end))s.o.touch.end(e);
			}
		});
	}
	S.prototype.x=function(i){
		const s=this;
		s.left=s.left-i;
		s.ul.css({left:-s.left});
	}
	S.prototype.y=function(i){
		const s=this;
		s.top=s.top-i;
		s.ul.css({top:-s.top});
	}
	S.prototype.retime=function(i){
		const s=this;
		clearTimeout(s.timer);
		s.timer=setTimeout(function(){
			s.play(1);
		},s.o.time);
		return s;
	}
	S.prototype.show=function(i){
		const s=this;
		if(xg.isfun(s.o.onshow))s.o.onshow(i);
		s.point.eq(s.showing).removeClass('xg-this');
		s.showing=i;
		s.point.eq(s.showing).addClass('xg-this');
		s.playing=true;
		s.aning=true;
		if(s.o.vertical){
			s.top=i*s.height;
			s.ul.stop().animate({top:-s.top},500);
		}else{
			s.left=i*s.width;
			s.ul.stop().animate({left:-s.left},500);
		}
		if(s.o.autoimg&&!s.o.height&&s.lis.eq(s.showing).find('img').length){
			s.height=s.lis.eq(s.showing).find('img').css({height:'auto'}).height();
			s.div.stop().animate({height:s.height},500);
		}
		s.retime();
		if(s.lis.eq(i+3).find('img').length>0){
			xg.lazy(s.lis.eq(i+3).find('img')[0]);
		}
		setTimeout(function(){
			s.playing=false;
			if(s.o.vertical){
				s.top=i*s.height;
			}else{
				s.left=i*s.width;
			}
			s.aning=false;
		},500);
		return s;
	};
	return function(sl,o){
		xg.loadcss(xg.c.dir+'modules/slide/slide.css');
		return new S(sl,o);
	};
});
xg.slide.get=function(sl){
	var s=win.xg_slide_list[sl];
	if(!s)xg.error('没有此名称的幻灯片');
	return s;
}
})(window,document,jQuery);