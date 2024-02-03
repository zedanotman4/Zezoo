<?php
$token = "5768700920:AAFZbyerH2SvvxrvBHTC7t10pGekGykmRWE";
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message) || isset($update->callback_query)):
$message = $update->message ;
$data=  $update->callback_query->data;
$id = $message->from->id ?? $update->callback_query->from->id;
$chat_id = $message->chat->id ?? $update->callback_query->message->chat->id;
$text = $message->text ;
$user = $message->from->username ?? $update->callback_query->from->username;
$name = $message->from->first_name ?? $update->callback_query->from->first_name;
$message_id = $message->message_id ?? $update->callback_query->message->message_id;
$type = $message->chat->type ?? $update->callback_query->message->chat->type;
$reply = $message->reply_to_message;
endif;
$link =  "https://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
echo file_get_contents("https://api.telegram.org/bot$token/setWebHook?url=$link");
$info = json_decode(file_get_contents("info.json"),1);
function save(){
	global $info;
	if(! empty ($info)) 
	file_put_contents("info.json",json_encode($info,448));
}
$api_key = ! empty ($info["key"])?$info["key"]: null;
$admin = 1689271304;//الادمن
require "class.php";
require "Telegram.php";
$api = new sms_man_com($api_key);
$bot = new Telegram ($token);
$ex = explode ("#",$data);
if($id == $admin ){
	
	if($text == "/start" or $data == "back") {
		$info["admin"] = "";
		save();
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>"/work لجعل البوت يبدا الصيد\n/stop لجعل البوت يتوقف عن الصيد\nعند ايقاف الصيد لا يتوقف مباشرة وانما يتوقف بعد مرور دقيقة",
			"reply_markup"=>json_encode([
				"inline_keyboard"=>[
					[["text"=>"اضافة دولة ➕","callback_data"=>"add"],
					["text"=>"حذف دولة 🗑️","callback_data"=>"del"]],
					[["text"=>"رفع api key","callback_data"=>"up"],
					["text"=>"حذف api key","callback_data"=>"rem"]],
					[["text"=>"الدول المضافة 📊","callback_data"=>"all"]],
				]
			])
		]);
		
	} elseif($text == "/work") {
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>"تم تشغيل الصيد"
		]);
		$info["status"]="work";
		save();
	} elseif ($text == "/stop") {
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>"تم ايقاف الصيد"
		]);
		$info["status"]=null;
		save();
	} elseif ($data) {
		if($data == "all"){
			$all = join ("\n",$info["countries"])??"لا توجد دول مضافة";
			$bot->answercallbackquery([
				"callback_query_id" => $update->callback_query->id,
				"text"=>"$all",
				"show_alert"=>true,
			]);
		} elseif ($data == "add") {
			$bot->editmessagetext([
				"chat_id"=>$chat_id,
				"text"=>"قم بارسال رمز الدولة في موقع sms-man",
				"message_id"=>$message_id,
				"reply_markup"=>json_encode([
					"inline_keyboard"=>[
						[["text"=>"رجوع🔙","callback_data"=>"back"]],
					]
				])
			]);
			$info["admin"] = "add";
			save();
		} elseif ($data == "del") {
			$bot->editmessagetext([
				"chat_id"=>$chat_id,
				"text"=>"قم بارسال كود الدولة",
				"message_id"=>$message_id,
				"reply_markup"=>json_encode([
					"inline_keyboard"=>[
						[["text"=>"رجوع🔙","callback_data"=>"back"]],
					]
				])
			]);
			$info["admin"] = "del";
			save();
		} /*elseif ($ex[0] == "getCode" ) {
			$res = $api->getCode($ex[1]);
			if($res["ok"] == true ) {
				$code = $res["code"];
				$bot->editmessagetext([
					"chat_id"=>$chat_id,
					"text"=>"تم وصول الكود بنجاح\n$ex[2]\n$code",
					"message_id"=>$message_id
				]);
			} else {
				$bot->answercallbackquery([
					"callback_query_id" => $update->callback_query->id,
					"text"=>"🚫 لم يصل الكود",
					"show_alert"=>true,
				]);
			}
		} elseif ($ex[0] == "ban" ) {
			$res = $api->cencel($ex[1]);
			$bot->editmessagetext([
				"chat_id"=>$chat_id,
				"text"=>"تم حظر الرقم بنجاح",
				"message_id"=>$message_id
			]);
		}*/
		elseif ($data == "up") {
			if($api_key == null) {
				$bot->editmessagetext([
					"chat_id"=>$chat_id,
					"text"=>"قم بارسال api key الخاص بحسابك",
					"message_id"=>$message_id,
					"reply_markup"=>json_encode([
						"inline_keyboard"=>[
							[["text"=>"رجوع🔙","callback_data"=>"back"]],
						]
					])
				]);
				$info["admin"] = "up";
				save();
			}else{
				$bot->answercallbackquery([
					"callback_query_id" => $update->callback_query->id,
					"text"=>"لايمكنك اضافة api key جديد الا بعد حذف القديم",
					"show_alert"=>true,
				]);
			}
		}elseif ($data == "rem") {
			$bot->editmessagetext([
				"chat_id"=>$chat_id,
				"text"=>"تم الحذف بنجاح",
				"message_id"=>$message_id,
				"reply_markup"=>json_encode([
					"inline_keyboard"=>[
						[["text"=>"رجوع🔙","callback_data"=>"back"]],
					]
				])
			]);
			unset($info["key"]);
			save();
		}
	} elseif ($text && $info["admin"] == "add") {
		$code = uniqid (1);
		$info["countries"][$code] = $text;
		$info["admin"] = "";
		save();
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>"تمت الاضافة بنجاح\nكود الدولة\n$code\nيستخدم هذا الكود عند الرغبة بحذف الدولة"
		]);
	} elseif ($text && $info["admin"] == "del") {
		if($info["countries"][$text] == null){
			$bot->sendmessage ([
				"chat_id"=>$chat_id,
				"text"=>"لاتوجد دولة مضافة بهذا الكود"
			]);
			$info["admin"] = "";
			save();
		} else {
			unset($info["countries"][$text]);
			$info["admin"] = "";
			save();
			$bot->sendmessage ([
				"chat_id"=>$chat_id,
				"text"=>"تم الحذف بنجاح"
			]);
		}
	}elseif ($text && $info["admin"] == "up") {
		$info["key"] = $text;
		$info["admin"] = "";
		save();
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>"تم الحفظ بنجاح"
		]);
	}
} 


if ($ex[0] == "getCode" ) {
	$res = $api->getCode($ex[1]);
	if($res["ok"] == true and $res["code"] != 0) {
		$code = $res["code"];
		$bot->editmessagetext([
			"chat_id"=>$chat_id,
			"text"=>"تم وصول الكود بنجاح\n$ex[2]\n$code",
			"message_id"=>$message_id
		]);
	} else {
		$bot->answercallbackquery([
			"callback_query_id" => $update->callback_query->id,
			"text"=>"🚫 لم يصل الكود",
			"show_alert"=>true,
		]);
	}
} elseif ($ex[0] == "ban" ) {
	$res = $api->cencel($ex[1]);
	$bot->editmessagetext([
		"chat_id"=>$chat_id,
		"text"=>"تم حظر الرقم بنجاح",
		"message_id"=>$message_id
	]);
}


