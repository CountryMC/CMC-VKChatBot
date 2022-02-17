<?php

require_once "autoload.php"; require_once "config.php"; 
use DigitalStar\vk_api\vk_api; // Основной классuse DigitalStar\vk_api\LongPoll; //работа с longpolluse DigitalStar\vk_api\Execute; // Поддержка Executeuse DigitalStar\vk_api\Group; // Работа с группами с ключем пользователяuse DigitalStar\vk_api\Auth; // Авторизацияuse DigitalStar\vk_api\Message; // Конструктор сообщенийuse DigitalStar\vk_api\VkApiException; // Обработка ошибок






$vk = vk_api::create($token, $api_version)->setConfirm($confirm);
$vk->initVars($id, $message, $payload, $user_id, $type, $data);






        $method = "messages.getByConversationMessageId";
		$conv_id = $data->object->conversation_message_id;
        $link_url = "https://api.vk.com/method/messages.delete?delete_for_all=1&group_id=208941205&cmids=$conv_id&peer_id=$id&message_ids=0&v=5.131&access_token=554752b4be0933aabce0ff3271eaab46b691b038e2a2f5f775262ed724deb3821bdf36fc07846649a79ea";
	//getting json and chek mute time
	    $date = $data->object->date;
	    $resend_msg_id = $data->object->reply_message->from_id;
	    $get_json = file_get_contents("json/".$id.".json");
	    $json_decoded = json_decode($get_json, true);
	    if($json_decoded[$user_id]['time'] > $date){
			file_get_contents($link_url);
		} 
	






//получение имени и фамилии пользователя, на чье сообщение было отвечено
		$get_resend_user = file_get_contents("https://api.vk.com/method/users.get?user_ids=$resend_msg_id&v=5.131&access_token=554752b4be0933aabce0ff3271eaab46b691b038e2a2f5f775262ed724deb3821bdf36fc07846649a79ea");
		$resend_name = json_decode($get_resend_user)->response[0]->first_name;
		$resend_last = json_decode($get_resend_user)->response[0]->last_name;





































// запись в переменную $admin значение true и false в зависимости от наличия админки
 try {
            $members = $vk->request('messages.getConversationMembers', ['peer_id' => $id])['items'];
        } catch (\Exception $e) {
           $admin = 'false';
        }
        foreach ($members as $key) {
            if ($key['member_id'] == $user_id)
				
			if($key["is_admin"] == "admin"){
                $admin = 'true';
        }else {
			$admin = 'false';}
		}
    






//забей, это особо не надо, ну крч это можно убрать, но лучше пусть будет
if(file_get_contents("json/".$id.".json") == ""){
	file_put_contents("json/".$id.".json", '{"123456789":{"time":"1", "moder":"false"}}');
}
//если тип сообщения - новое
if($type == "message_new"){
	
    
	
	
	
	//выводить ид пользователя командой !id 
	if($message == "!id"){
		$vk->reply($user_id);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//снять юзера с модера
	if($message == '!снять'){
		if($admin == "true"){
		
		if($resend_msg_id != ""){
                 
				        $get_json = file_get_contents("json/".$id.".json");
	    $json_decoded = json_decode($get_json, true); 
				$json_decoded[$resend_msg_id]['moder'] = "false";
				$json_back = json_encode($json_decoded);
	            file_put_contents("json/".$id.".json", $json_back);
				$vk->reply("[id$resend_msg_id|$resend_name $resend_last] был снят с модератора!");
				
			
			
		} else {
			$vk->reply('Выберите пользователя, ответив на его сообщение');
		}
		
		}else {
			$vk->reply('Снимать модераторов могут только админы!');
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//выдать юсеру модера
	if($message == '!модер'){
		if($admin == "true"){
		
		if($resend_msg_id != ""){
                 
				        $get_json = file_get_contents("json/".$id.".json");
	    $json_decoded = json_decode($get_json, true); 
				$json_decoded[$resend_msg_id]['moder'] = "true";
				$json_back = json_encode($json_decoded);
	            file_put_contents("json/".$id.".json", $json_back);
				$vk->reply("[id$resend_msg_id|$resend_name $resend_last] стал модератором!");
				
			
			
		} else {
			$vk->reply('Выберите пользователя, ответив на его сообщение');
		}
		
		}else {
			$vk->reply('Выдавать модераторов могут только админы!');
		}
	}
	
	
	
	
	
	
	
	
	
	
	

	
	//размутить юзера
	if($message == "!размут" or $message == "!размутить" or $message == "!unmute" and $resend_msg_id != ""){	


        $get_json = file_get_contents("json/".$id.".json");
	    $json_decoded = json_decode($get_json, true);
		if($json_decoded[$user_id]['moder'] != "true" and $admin != "true"){
			
			
				$vk->reply('У вас нету прав для выполнения данной команды!');
			exit();
		}

	$date = $data->object->date;
	
	if($json_decoded[$resend_msg_id]['time'] > $date and $json_decoded[$resend_msg_id]['time'] != ""){
		    $json_decoded[$resend_msg_id]['time'] = "1";
$json_back = json_encode($json_decoded);
	file_put_contents("json/".$id.".json", $json_back);			
			$vk->reply("[id$resend_msg_id|$resend_name $resend_last] был размучен!");
		
	} else {
		$vk->reply("[id$resend_msg_id|$resend_name $resend_last] не в муте!");
	}
	}
		
		
	
	
	
	
	
	
	
	
	
	//обрезания текста после !мут, для дальнейшей проверки на команду
		$mute_check = mb_strimwidth($message, 0, 4);//если при обрезании получили !мут, то мутим 
	if($mute_check == "!мут"){
		if($resend_msg_id == ""){
			$vk->reply('Укажите пользователя, ответив командой на его сообщение');
			exit();
		}
		
		        $get_json = file_get_contents("json/".$id.".json");
	    $json_decoded = json_decode($get_json, true);
		if($json_decoded[$user_id]['moder'] != "true" and $admin != "true"){
			
			
				$vk->reply('У вас нету прав для выполнения данной команды!');
			exit();
		}
		
		
	$count_date = mb_substr($message, 4, -1);
	$date_type = mb_substr($message, -1);
	$date = $data->object->date;
	
	
	
	if($count_date == "" or $date_type == ""){
		$vk->reply("Использование<br>!мут (число) (с/м/д/н)");
		exit();
	}
	
	
	

	

	//проверка времени мута в json, если время больше настоящего времени, то выдается мут, в противном случае выводит, что юзер уже в муте
	if($json_decoded[$resend_msg_id]['time'] < $date or $json_decoded[$resend_msg_id]['time'] == ""){
		
		if($date_type == "с" or $date_type == "s" or $date_type == "c"){$totime = "+".$count_date." sec";}
		else if($date_type == "м" or $date_type == "m" or $date_type == "min"){$totime = "+".$count_date." min";}
		else if($date_type == "ч" or $date_type == "h" or $date_type == "hour"){$totime = "+".$count_date." hour";}
		else if($date_type == "д" or $date_type == "d" or $date_type == "day"){$totime = "+".$count_date." day";}
		else if($date_type == "н" or $date_type == "w" or $date_type == "week"){$totime = "+".$count_date." week";}
		
		$normal_time = strtotime($totime);
		
    $json_decoded[$resend_msg_id]['time'] = strtotime($totime);
		$json_back = json_encode($json_decoded);
	file_put_contents("json/".$id.".json", $json_back);
	
	$time = array('с' => 'секунд', 'м' => 'минут', 'ч' => 'часов', 'д' => 'дней', 'н' => 'недель');
	
	$ru_months = array( '0','Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря' );
	
	$month = date("n",$normal_time);
	$ru_date = date('d', $normal_time).' '.$ru_months[$month].' '.date('Y', $normal_time).', '.date('H:i:s', $normal_time);
	
		$vk->reply("[id$resend_msg_id|$resend_name $resend_last] получил мут на $count_date ".$time[$date_type]."<br>Срок окончания : ".$ru_date);
	} else {
		
		$vk->reply("На [id$resend_msg_id|$resend_name"."a $resend_last"."a"."]  уже наложен мут!");
	}
	

  
	
	

	}

}






?>









/*	
$vk = vk_api::create('+201069476979', 'BBeowHfuwoab', "7.12");//или используйте токен вместо лог/пас
$vk = new LongPoll($vk);
$vk->listen(function()use($vk){ //longpoll для пользователя
    $vk->on('message_new', function($data)use($vk) { //обработка входящих сообщений
        $data = $vk->initVars($id, $message, $payload, $user_id, $type, $else);
      $vk->
    });
});*/