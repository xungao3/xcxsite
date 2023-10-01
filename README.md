# xcxsite
讯高小程序服务端 免费开源发布
## 开源协议
本系统是使用的Apache2开源协议 使用过程中请遵守此协议的条款
## 低代码
后台即可设计模块 不懂代码也可以制作搭建自己的小程序系统
## 自动缓存
本系统使用了自动缓存技术 不需要手动生成静态文件缓存 使用静态缓存 响应速度更优秀
## 高扩展性
系统关键位置都内置钩子 可以不侵入代码的情况下进行系统的扩展

## 演示地址
[https://demo.e.xg3.cn/admin/](https://demo.e.xg3.cn/admin/)
账号:admin 密码:admin.123

## 安装步骤
### 1.克隆项目
[https://github.com/xungao3/xcxsite.git](https://github.com/xungao3/xcxsite.git)
![克隆项目](https://e.xg3.cn/upload/202309/06/72fbd26cf01eb270.jpg)
### 2.创建站点
这里使用的宝塔系统（可自行百度宝塔面板）请记住你设置的数据库密码。
![创建站点](https://e.xg3.cn/upload/202309/06/40c27b969486b52d.jpg)
### 3.设置运行目录
为了安全需要将运行目录设置到public目录。
![设置运行目录](https://e.xg3.cn/upload/202309/06/0724d6fb2d7a6f5a.png)
### 4.配置伪静态
伪静态规则如下
```nginx
location / {
	if (-d $request_filename) {
		rewrite ^(.*)$ /index.php?xg=$1 last; break;
		break;
	}
	if (!-e $request_filename){
		rewrite ^(.*)$ /index.php?xg=$1 last; break;
	}
}
```
如果不需要HTML静态缓存功能，也可以使用以下规则。
```nginx
location / {
	if (!-e $request_filename){
		rewrite ^(.*)$ /index.php?xg=$1 last; break;
	}
}
```
### 5.开始安装服务端
![安装服务端](https://e.xg3.cn/upload/202309/06/38e20c8c7432cdd1.png)
### 6.检测服务器环境
![安装服务端](https://e.xg3.cn/upload/202309/06/c11f9470b07a8747.png)
### 7.配置网站信息
![配置网站信息](https://e.xg3.cn/upload/202309/06/2e0e9fdc2245bd8e.png)
### 8.开始安装
安装是自动的，安装完成后点击打开后台按钮。
![开始安装](https://e.xg3.cn/upload/202309/06/a4b27b5543971a08.png)
### 9.安装完成
![安装完成](https://e.xg3.cn/upload/202309/06/f69f0cd0ad660e55.png)
### APP端
[https://github.com/xungao3/xcxapp.git](https://github.com/xungao3/xcxapp.git)