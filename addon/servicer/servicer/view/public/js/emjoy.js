let emjoyList={
	"[emjoy_01]":'public/static/img/emojy/emjoy_01.gif',
	"[emjoy_02]":'public/static/img/emojy/emjoy_02.gif',
	"[emjoy_03]":'public/static/img/emojy/emjoy_03.gif',
	"[emjoy_04]":'public/static/img/emojy/emjoy_04.gif',
	"[emjoy_05]":'public/static/img/emojy/emjoy_05.gif',
	"[emjoy_06]":'public/static/img/emojy/emjoy_06.gif',
	"[emjoy_07]":'public/static/img/emojy/emjoy_07.gif',
	"[emjoy_08]":'public/static/img/emojy/emjoy_08.gif',
	"[emjoy_09]":'public/static/img/emojy/emjoy_09.gif',
	
	"[emjoy_10]":'public/static/img/emojy/emjoy_10.gif',
	"[emjoy_11]":'public/static/img/emojy/emjoy_11.gif',
	"[emjoy_12]":'public/static/img/emojy/emjoy_12.gif',
	"[emjoy_13]":'public/static/img/emojy/emjoy_13.gif',
	"[emjoy_14]":'public/static/img/emojy/emjoy_14.gif',
	"[emjoy_15]":'public/static/img/emojy/emjoy_15.gif',
	"[emjoy_16]":'public/static/img/emojy/emjoy_16.gif',
	"[emjoy_17]":'public/static/img/emojy/emjoy_17.gif',
	"[emjoy_18]":'public/static/img/emojy/emjoy_18.gif',
	"[emjoy_19]":'public/static/img/emojy/emjoy_19.gif',
	
	"[emjoy_20]":'public/static/img/emojy/emjoy_20.gif',
	"[emjoy_21]":'public/static/img/emojy/emjoy_21.gif',
	"[emjoy_22]":'public/static/img/emojy/emjoy_22.gif',
	"[emjoy_23]":'public/static/img/emojy/emjoy_23.gif',
	"[emjoy_24]":'public/static/img/emojy/emjoy_24.gif',
	"[emjoy_25]":'public/static/img/emojy/emjoy_25.gif',
	"[emjoy_26]":'public/static/img/emojy/emjoy_26.gif',
	"[emjoy_27]":'public/static/img/emojy/emjoy_27.gif',
	"[emjoy_28]":'public/static/img/emojy/emjoy_28.gif',
	"[emjoy_29]":'public/static/img/emojy/emjoy_29.gif',
	
	"[emjoy_30]":'public/static/img/emojy/emjoy_30.gif',
	"[emjoy_31]":'public/static/img/emojy/emjoy_31.gif',
	"[emjoy_32]":'public/static/img/emojy/emjoy_32.gif',
	"[emjoy_33]":'public/static/img/emojy/emjoy_33.gif',
	"[emjoy_34]":'public/static/img/emojy/emjoy_34.gif',
	"[emjoy_35]":'public/static/img/emojy/emjoy_35.gif',
	"[emjoy_36]":'public/static/img/emojy/emjoy_36.gif',
	"[emjoy_37]":'public/static/img/emojy/emjoy_37.gif',
	"[emjoy_38]":'public/static/img/emojy/emjoy_38.gif',
	"[emjoy_39]":'public/static/img/emojy/emjoy_39.gif',
	
	"[emjoy_40]":'public/static/img/emojy/emjoy_40.gif',
	"[emjoy_41]":'public/static/img/emojy/emjoy_41.gif',
	"[emjoy_42]":'public/static/img/emojy/emjoy_42.gif',
	"[emjoy_43]":'public/static/img/emojy/emjoy_43.gif',
	"[emjoy_44]":'public/static/img/emojy/emjoy_44.gif',
	"[emjoy_45]":'public/static/img/emojy/emjoy_45.gif',
	"[emjoy_46]":'public/static/img/emojy/emjoy_46.gif',
	"[emjoy_47]":'public/static/img/emojy/emjoy_47.gif',
}
var stringToEmjoy=function(value){
	if(!value) return;
	let string = value; // 需要把[握手]和[微笑]匹配出来
	let reg = new RegExp('\\[emjoy_(.+?)\\]', 'g');
	let emjoyString = string.replace(reg,function(v){
		let emjoy = '';
		for (let index in emjoyList) {
			if(v==index){
				// emjoy = emjoyList[index]
				let img=ns.img(emjoyList[index] )
				emjoy = "<img src='"+ img +"'/>"
			}
		}
		
		if(emjoy){
			return emjoy;
		}else{
			return v
		}
		
	});
	return emjoyString;
}