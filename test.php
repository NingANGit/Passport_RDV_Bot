<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use Joli\JoliNotif\Notification;
    use Joli\JoliNotif\NotifierFactory;

    require 'vendor/autoload.php';

    function notif($titre, $body) {
        $notifier = NotifierFactory::create();

        $notification =
            (new Notification())
            ->setTitle($titre)
            ->setBody($body)
            ->setIcon(__DIR__.'/icon.png')
            //->addOption('subtitle', $message) // Only works on macOS (AppleScriptNotifier)
            //->addOption('sound', 'Frog') // Only works on macOS (AppleScriptNotifier)
            ;
            // Send it
        $notifier->send($notification);
      
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://ppt.mfa.gov.cn/appo/service/reservation/data/getReservationDateBean.json?rid=0.07024190259625152");
    //curl_setopt($ch, CURLOPT_URL, "https://ppt.mfa.gov.cn/appo/service/reservation/data/getReservationDateBean.json?param=0.5639025980197203");
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //'Cookie: Hm_lvt_b957667300eff41ee03e59e191c81545=1617733231; Hm_lpvt_b957667300eff41ee03e59e191c81545=1617733246; JSESSIONID=116dwkb01n8icvzc8wn0z96ul; tgw_l7_route=a7675a6831a3996a7f1b08f285478767; pcxSessionId=651817a4-9d4d-4d67-8736-3084858f3c5c',
        'Cookie: Hm_lvt_b957667300eff41ee03e59e191c81545=1616691038; Hm_lpvt_b957667300eff41ee03e59e191c81545=1617728408; JSESSIONID=q1smnlduhd0q1v48f8o6sh5kt; pcxSessionId=99f5c9c2-ef4a-480e-bfb3-801123ec2fff; tgw_l7_route=0fb11371c098ba3c3c183a3ceced03fc',
        'Connection: keep-alive'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);

    curl_setopt($ch, CURLOPT_POSTFIELDS,
            "addressName=9392c17d9c4b4b0687c2ab115cbc1aac");
    $output = curl_exec($ch);
    curl_close($ch);  
    //var_dump($output);

    $output_array = json_decode($output, true);

    $choix_array = $output_array['data'];
    $messag = '';
    $dispo = '❌❌❌';
    foreach($choix_array as $choix) {
        $date = $choix['date'];
        $periodOfTimeList = $choix['periodOfTimeList'][0];
        //$periodOfTimeList['userNumber'] = 1;
        // echo $date.'  ';
        // echo 'peopleNumber = '.$periodOfTimeList['peopleNumber'].'   ';
        // echo 'userNumber = '.$periodOfTimeList['userNumber'];
        // echo "\n";

        if($periodOfTimeList['peopleNumber'] != $periodOfTimeList['userNumber']) {
            $dispo = '✅✅✅';
            $message = 'dispo';
            break;
        } else {
            $message .= $date.':'.$periodOfTimeList['peopleNumber'].'-'.$periodOfTimeList['userNumber']." / ";
        }

    }
    
    
    echo $dispo;
    echo "\t";
    echo date("H:i:s");
    echo "\t";
    echo $message;
    echo "\n";

    notif($dispo, $message);

?>