function mobile(e,s) {
	if (e.detail.errMsg == 'getPhoneNumber:fail') {
		s.msg('未获取到手机号码');
		return false;
	} else if (!e.detail.code) {
		s.msg('授权失败');
		return false;
	} else {
		const url = s.url('app/user/wxmobile');
		const data = {
			appid: s.c.wxappid||s.c.appid,
			code: e.detail.code
		};
		s.request({
			url: url,
			data: data,
			method: 'POST',
			success: function(res) {
				console.log(res);
				if (res.ok) {
					s.mobile=res.mobile;
					s.showing=false;
				} else if (res.msg) {
					s.msg(res.msg);
				}
			},
			fail: function(res) {
				s.msg('获取手机号失败');
			}
		})
	}
}

export {mobile};