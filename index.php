<?php
include_once 'xml2array.php';

$MyButtons= [
    "Гитлер", "Выбери карту", "Повтори карту", "Сбрось карту",
];

$response = '';
$buttons = [];
foreach ($MyButtons as $button) {
    $buttons[] = [
        'title'=>$button,
        'hide'=>true
    ];
        //    "buttons": [
        //        {
        //            "title": "Надпись на кнопке",
        //            "payload": {},
        //            "url": "https://example.com/",
        //            "hide": true
        //        }
        //    ],
}

$dataRow = file_get_contents('php://input');
header('Content-Type: application/json');

/**
 * Впишите сюда своё активационное имя
 */
$mySkillName = 'Хост настолок';

$EvilCards = ['Карта 1', 'Карта 2', 'Карта 3', 'Карта 4'];
$CurrentCard = file_get_contents('card.txt', FILE_USE_INCLUDE_PATH);



try{
    if (!empty($dataRow)) {
        /**
         * Простейший лог, чтобы проверять запросы. Закомментируйте эту стрчоку, если он вам не нужен
         */
//        file_put_contents('alisalog.txt', date('Y-m-d H:i:s') . PHP_EOL . $dataRow . PHP_EOL, FILE_APPEND);

        /**
         * Преобразуем запрос пользователя в массив
         */
        $data = json_decode($dataRow, true);

        /**
         * Проверяем наличие всех необходимых полей
         */
        if (!isset($data['request'], $data['request']['command'], $data['session'], $data['session']['session_id'], $data['session']['message_id'], $data['session']['user_id'])) {
            /**
             * Нет всех необходимых полей. Не понятно, что вернуть, поэтому возвращаем ничего.
             */
            $result = json_encode([]);
        } else {
            /**
             * Получаем что конкретно спросил пользователь
             */
            $text = $data['request']['command'];

            session_id($data['session']['session_id']); // В Чате спрашивали неодногравтно как использовать сессии в навыке - показываю
            session_start();

            /**
             * Приводим на всякий случай запрос пользователя к нижнему регистру
             */
            $textToCheck = strtolower($text);

//            if (strpos($text, $mySkillName) !== false) {
            if (empty($text)) {
                $response = json_encode([
                    'version' => '1.0',
                    'session' => [
                        'session_id' => $data['session']['session_id'],
                        'message_id' => $data['session']['message_id'],
                        'user_id' => $data['session']['user_id']
                    ],
                    'response' => [
                        'text' => 'Привет. Я хост настолок и могу помочь вам проводить игры',
                        /**
                         * Ставьте плюсик перед гласной, на которую делается ударение.
                         * Если вам нужна пауза, добавьте " - ", т.е. дефис с пробелом до и после него.
                         */
                        'tts' => 'Прив+ет. Я хост наст+олок и мог+у пом+очь вам провод+ить +игры',
                        'buttons' => $buttons
                    ]
                ]);
            } elseif($text == 'помощь') {
                $response = json_encode([
                    'version' => '1.0',
                    'session' => [
                        'session_id' => $data['session']['session_id'],
                        'message_id' => $data['session']['message_id'],
                        'user_id' => $data['session']['user_id']
                    ],
                    'response' => [
                        'text' => 'Текущие доступные действия "гитлер", "выбрать, повторить или сбросить карту", для выхода "Алиса хватит"',
                        'tts' => 'Текущие доступные действия "гитлер", "выбрать, повторить или сбросить карту", для выхода "Алиса хватит"',
                        'buttons' => $buttons
                    ]
                ]);
            }elseif($text == 'гитлер') {

                $SoundArray = ['<speaker audio="alice-sounds-human-laugh-3.opus">', '<speaker audio="alice-sounds-human-laugh-4.opus">', '<speaker audio="alice-sounds-animals-rooster-1.opus">'];
                $ttsString = 'Начинаем игру Секретный Гитлер.Все закрывают глаза sil<[1500]> Открывают глаза фашисты. 
                                   Фашисты находят друг друга. 
                                   Гитлер подаёт знак. ' . $SoundArray[random_int(0, count($SoundArray) - 1)] . 'Гитлер перестаёт подавать знак. 
                                   Фашисты закрывают глаза.
                                   Открывают глаза все';
                $response = json_encode([
                    'version' => '1.0',
                    'session' => [
                        'session_id' => $data['session']['session_id'],
                        'message_id' => $data['session']['message_id'],
                        'user_id' => $data['session']['user_id']
                    ],
                    'response' => [
                        'text' => 'Начинаем игру Секретный Гитлер. 
                                   Посмотрите на свои карты. 
                                   Все закрывают глаза. 
                                   Открывают глаза фашисты. 
                                   Фашисты находят друг друга. 
                                   Гитлер подаёт знак. 
                                   Гитлер перестаёт подавать знак. 
                                   Фашисты закрывают глаза.
                                   Открывают глаза все',
                        'tts' => $ttsString,
                        'buttons' => $buttons
                    ]
                ]);
            } elseif($text == 'выбери карту') {
                
                if ($CurrentCard == PHP_EOL)
                {
                    $CurrentCard = $EvilCards[random_int(0, count($EvilCards) - 1)];
                    file_put_contents('card.txt', $CurrentCard . PHP_EOL);
                    $ttsAnswer = 'Выбираю карту. Текущий вопрос это sil<[500]>' . $CurrentCard;
                }
                else
                {
                    $ttsAnswer = 'Карта уже выбрана. Это sil<[500]>' . $CurrentCard;
                }
                
                $response = json_encode([
                    'version' => '1.0',
                    'session' => [
                        'session_id' => $data['session']['session_id'],
                        'message_id' => $data['session']['message_id'],
                        'user_id' => $data['session']['user_id']
                    ],
                    'response' => [
                        'text' => ''. $CurrentCard,
                        'tts' =>  $ttsAnswer,
                        'buttons' => $buttons
                    ]
                ]);

            } elseif($text == 'повтори карту') {
                
                if ($CurrentCard == PHP_EOL)
                {
                    $ttsAnswer = 'Карта не выбранна. Для выбора скажите "Выбери карту"';
                }
                else
                {
                    $ttsAnswer = 'Текущий вопрос это sil<[500]>' . $CurrentCard;
                }
                
                $response = json_encode([
                    'version' => '1.0',
                    'session' => [
                        'session_id' => $data['session']['session_id'],
                        'message_id' => $data['session']['message_id'],
                        'user_id' => $data['session']['user_id']
                    ],
                    'response' => [
                        'text' => ''. $CurrentCard,
                        'tts' =>  $ttsAnswer,
                        'buttons' => $buttons
                    ]
                ]);
        
            } elseif($text == 'сбрось карту') {
                
                $CurrentCard = file_put_contents('card.txt', PHP_EOL);
                
                $ttsAnswer = 'Карта сброшена';
                
                $response = json_encode([
                    'version' => '1.0',
                    'session' => [
                        'session_id' => $data['session']['session_id'],
                        'message_id' => $data['session']['message_id'],
                        'user_id' => $data['session']['user_id']
                    ],
                    'response' => [
                        'text' => ''. $CurrentCard,
                        'tts' =>  $ttsAnswer,
                        'buttons' => $buttons
                    ]
                ]);
        
            
        
        
            }elseif($text == 'хватит' || $text == 'выход') { // Обязательно добавляем условия выхода
                $answerArray = [
    'Пока'=> 'Пок+а', 'До свидания'=>'До свид+ания', 'Приятного дня'=> 'При+ятного дня',
];
                $currentAnswer =  'Пока';//$answerArray[random_int(0, 2)];
                $response = json_encode([
                    'version' => '1.0',
                    'session' => [
                        'session_id' => $data['session']['session_id'],
                        'message_id' => $data['session']['message_id'],
                        'user_id' => $data['session']['user_id']
                    ],
                    'response' => [
                        'text' => '' . $currentAnswer,
                        'tts' =>  '' . $answerArray[$currentAnswer],
                        'buttons' => [],
                        'end_session' => true // при возврате true сессия в навыке прерывается,
                                              // но на смартфонах навык не закрывается и следующий запрос пользователя
                                              // идет опять в наш навык, не в алису.
                    ]
                ]);
            } else {
            
                $answer_text = 'Я не умею ' . $text . '. Попробуйте спросить ещё раз';

                $response = json_encode([
                    'version' => '1.0',
                    'session' => [
                        'session_id' => $data['session']['session_id'],
                        'message_id' => $data['session']['message_id'],
                        'user_id' => $data['session']['user_id']
                    ],
                    'response' => [
                        'text' => $answer_text,
                        'tts' => $answer_text,
                        'buttons' => $buttons,
                        'end_session' => false
                    ]
                ]);

            }
        }
    } else {
        $response = json_encode([
            'version' => '1.0',
            'session' => 'Error',
            'response' => [
                'text' => 'Отсутствуют данные',
                'tts' =>  'Отсутствуют данные'
            ]
        ]);
    }

    echo $response;
} catch(\Exception $e){
    echo '["Error occured"]';
}
