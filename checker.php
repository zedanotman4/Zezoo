<?php
$token="5768700920:AAFZbyerH2SvvxrvBHTC7t10pGekGykmRWE";
require "class.php";
require "Telegram.php";
$bot = new Telegram ($token);
$info = json_decode(file_get_contents("info.json"),1);
function save(){
	global $info;
	if(! empty ($info)) 
	file_put_contents("info.json",json_encode($info,448));
}
$api_key = ! empty ($info["key"])?$info["key"]: null;
$admin = -1001602685079;//ايدي قناة الصيد 
if($api_key == null) exit;
$api = new sms_man_com($api_key);
if($info["status"] == "work"){
	if(! empty ($info["countries"])){
		for($i=0;$i<100;$i++) {
			foreach ($info["countries"] as $country) {
				$res = $api->getNumber($country,"wa");
				if($res["ok"] == true ) {
					$id = $res["id"];
					$num = $res["number"];
					if(empty ($id) || empty ($num)) continue;
					$txt  = "تم شراء الرقم بنجاح ☑️\n\n📞 الرقم: `+$num`\n🆔 ايدي العملية: $id\nhttps://wa.me/+$num";
					$bot->sendmessage ([
						"chat_id"=>$admin,
						"text"=>$txt,
						"parse_mode"=>"markdown",
						"reply_markup"=>json_encode([
							"inline_keyboard"=>[
								[["text"=>"🌚 طلب الكود","callback_data"=>"getCode#$id#$num"]],
								[["text"=>"❌ حظر الرقم","callback_data"=>"ban#$id"]]
							]
						])
					]);
					usleep(100000);
				} else { continue; }
			}
		}
	} else { exit; }
} else { exit; }








