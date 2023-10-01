function htmlstyle(html,pstyle,imgstyle){
	html=html.replace(/\\\"/g,'"');
	const tagreg = /(<([a-z]+)(?:[^>]*)>)/g;
	html=html.replace(tagreg,function(a, b, c) {
		const attrreg = /(style="(.*?)")/g;
		const attrreg2 = /(style='(.*?)')/g;
		if(c=='p'){
			let rt;
			if(rt=attrreg.exec(b)){
				return b.replace(rt[2],rt[2].trim(';')+';'+pstyle);
			}else if(rt=attrreg2.exec(b)){
				return b.replace(rt[2],rt[2].trim(';')+';'+imgstyle);
			}else{
				return b.replace('>',` style="${pstyle}">`);
			}
		}else if(c=='img'){
			let rt;
			if(rt=attrreg.exec(b)){
				return b.replace(rt[2],rt[2].trim(';')+';'+imgstyle);
			}else if(rt=attrreg2.exec(b)){
				return b.replace(rt[2],rt[2].trim(';')+';'+imgstyle);
			}else{
				return b.replace('>',` style="${imgstyle}">`);
			}
		}else{
			return b;
		}
	});
	return html;
}
export default htmlstyle;