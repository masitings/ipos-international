<?php

namespace App\Controller;

use PHPMailer\PHPMailer\PHPMailer;
use Pimcore\Model\WebsiteSetting;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;



class ContactController extends BaseController
{

    private static $SMTP_ARRAY = [
        'smtp_mail_name',
        'smtp_mail_password',
        'smtp_mail_port',
        'smtp_mail_service'
    ];

    protected function encrypt($str)
    {
        $rsa_prikey = file_get_contents('/usr/share/nginx/rsa_private.key');
        $crypted = "";
        openssl_private_encrypt($str, $crypted, $rsa_prikey);
        return base64_encode($crypted);
    }

    protected function decrypt($str)
    {
        $rsa_pubkey = file_get_contents('/usr/share/nginx/rsa_public.key');
        $decrypted = "";
        openssl_public_decrypt(base64_decode($str), $decrypted, $rsa_pubkey);

        return $decrypted;
    }

    /**
     * @Route ("/api/contact")
     * @param Request $request
     * @return JsonResponse
     */
    public function contactAction(Request $request, PersistenceManagerRegistry $doctrine)
    {

        /** CSRF Security */
        if ($request->get('csrf_token') == "" or $request->get('csrf_token') != "9847h3hchc65rdytegbhcjcccc21") {
            return "";
        }

        $emailObj = new DataObject\Emails\Listing();

        $emailObj->load();
        $sendMail = 'enquiry@iposinternational.com';
        $sendMails = [];

        foreach ($emailObj as $email) {
            $sendMails[] = $email->getEmail();
        }

        $list = new WebsiteSetting\Listing();

        $list->setCondition('`name` LIKE ' . $list->quote('%smtp_mail%'));
        $list = $list->load();

        $mailConfig = [
            'mail_host' => 'smtp.office365.com',
            'mail_name' => 'IPOS International',
            'mail_username' => 'noreply@iposinternational.com',
            'mail_passwd' => $_ENV('MAIL_PASSWORD'),
            'mail_port' => 587,
            'mail_from' => 'noreply@iposinternational.com'
        ];

        $state = $request->get('state');

        $firstName = $request->get('firstName');

        $lastName = $request->get('lastName');

        $company = $request->get('company');

        $designation = $request->get('designation');

        // $industry = $request->get('industry');
        $industry = str_replace("/", "", $request->get('industry'));
        $industryOthers = "";
        $industryRecordToDb = $industry;
        if ($industry == "Others") {
            $industryOthers = $request->get('industryOptionOthers');
            $industryRecordToDb = "Other - " . $industryOthers;
        }

        $source = str_replace("/", "", $request->get('infoSource'));
        $infoSourceOthers = "";
        $eventSource = "";
        $sourceRecordToDb = $source;
        if ($source == "Others") {
            $infoSourceOthers = $request->get('infoSourceOthers');
            $sourceRecordToDb = "Other - " . $infoSourceOthers;
        }
        if ($source == "EventsTalksWorkshops") {
            $eventSource = $request->get('eventSource');
            $sourceRecordToDb = "Event/Talks/Workshop - " . $eventSource;
        }

        $message = $request->get('message');

        $companyOverview = $request->get('companyOverview');

        $existingIP = $request->get('existingIP');

        $overseasExpansion = $request->get('overseasExpansion');

        $proprietaryTechnology = $request->get('proprietaryTechnology');

        $phone = $request->get('phone');

        $c_email = $request->get('email');
        $c_website = $request->get('companyWebsite');

        $subemail = $request->get('subsemail') ? 'Yes' : 'No';

        $mail = new PHPMailer(true);

        $mail->CharSet = "UTF-8";                     //设定邮件编码

        $mail->SMTPDebug = 0;                        // 调试模式输出

        $mail->isSMTP();                             // 使用SMTP

        $mail->Host = $mailConfig['mail_host'];                // SMTP服务器

        $mail->SMTPAuth = true;                      // 允许 SMTP 认证

        $mail->Username = $mailConfig['mail_username'];                // SMTP 用户名  即邮箱的用户名

        $mail->Password = $mailConfig['mail_passwd'];             // SMTP 密码  部分邮箱是授权码(例如163邮箱)

        $mail->SMTPSecure = 'STARTTLS';                    // 允许 TLS 或者ssl协议

        $mail->Port = $mailConfig['mail_port'];                            // 服务器端口 25 或者465 具体要看邮箱服务器支持



        $mail->setFrom($mailConfig['mail_from'], "noreply@iposinternational.com");  //发件人
        // $mail->setFrom();  //发件人

        if ($sendMails) {
            foreach ($sendMails as $email) {
                $mail->addAddress($email);
            }
        } else {
            if (array_key_exists('ENV_STAGE', $_ENV)) {
                if ($_ENV['ENV_STAGE'] == 'staging' || $_ENV['ENV_STAGE'] == 'dev') {
                    $mail->addAddress("arigiwiratama@gmail.com");
                    $mail->addAddress("zhikai.yap@aikendigital.co");
                }
            } else {
                $mail->addAddress($sendMail);
            }
        }


        // //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
        $mail->addReplyTo($mailConfig['mail_from'], "noreply@iposinternational.com"); //回复的时候回复给哪个邮箱 建议和发件人一致

        /*
        $mail->isHTML(false);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容

        $mail->Subject = $state;

        $mail->Body    = 'FirstName : ' . $firstName . "\r\n";
        $mail->Body .= 'LastName : ' . $lastName . "\r\n";


        $mail->Body .= 'Company : ' . $company . "\r\n";

        $mail->Body .= 'Phone : ' . $phone . "\r\n";
        $mail->Body .= 'Email : ' . $c_email . "\n";
        
        $mail->Body .= 'Designation : ' . $designation . "\n";
        
        if ($industryOthers !== "") {
            $mail->Body .= 'Industry : ' . $industry . "\r\n";
        } else {
            $mail->Body .= 'Industry : ' . $industry . "\r\n";
            $mail->Body .= 'Industry : Others - ' . $industryOthers . "\r\n";
        }

        $mail->Body .= 'Company Website : ' . $c_website . "\n";

        $mail->Body .= 'Message : ' . $message . "\n";

        $mail->Body .= 'Company Overview : ' . $companyOverview . "\n";
        $mail->Body .= 'Existing IP Portfolio : ' . $existingIP . "\n";
        $mail->Body .= 'Overseas Expansion : ' . $overseasExpansion . "\n";
        $mail->Body .= 'Proprietary Technology : ' . $proprietaryTechnology . "\n";

        
        if ($infoSourceOthers != "") {
            $mail->Body .= 'InfoSource : ' . $source . "\r\n";
            $mail->Body .= 'InfoSourceOthers : ' . $infoSourceOthers . "\r\n";
        } else {
            $mail->Body .= 'InfoSource : ' . $source . "\r\n";
            $mail->Body .= 'InfoSourceOthers : ' . $infoSourceOthers . "\r\n";
        }
        $mail->Body .= 'Consent Marketing Email : ' . $subemail . "\r\n";

        */
        $mail->Subject = $state;
        $mail->isHTML(true); // Set email format to HTML
        $mail->Body = "
            <p>FirstName : $firstName</p>
            <p>LastName : $lastName</p>
        ";

        if ($company) {
            $mail->Body .= "<p>Company : $company</p>";
        }

        $mail->Body .= "
            <p>Phone : $phone</p>
            <p>Email : $c_email</p>
        ";

        if ($designation) {
            $mail->Body .= "<p>Designation : $designation</p>";
        }

        if ($industry) {
            if ($industryOthers !== "") {
                $mail->Body .= "<p>Industry : $industry – $industryOthers</p>";
            } else {
                $mail->Body .= "<p>Industry : $industry</p>";
            }
        }
        if ($c_website) {
            $mail->Body .= "<p>Company Website : $c_website</p>";
        }

        $mail->Body .= "<p>Message : $message</p>";

        if (!in_array($state, ["Academy programme", "General", "Business"])) {
            $mail->Body .= "
                <p>Company Overview : $companyOverview</p>
                <p>Existing IP Portfolio : $existingIP</p>
                <p>Overseas Expansion : $overseasExpansion</p>
                <p>Proprietary Technology : $proprietaryTechnology</p>
            ";
        }


        if ($infoSourceOthers != "") {
            $mail->Body .= "<p>InfoSource : $source</p><p>InfoSourceOthers : $infoSourceOthers</p>";
        } else {
            if ($source == 'EventsTalksWorkshops') {
                $mail->Body .= "<p>InfoSource : $source - $eventSource</p>";
            } else {
                $mail->Body .= "<p>InfoSource : $source</p>";
            }
        }

        $mail->Body .= "<p>Consent Marketing Email : $subemail</p>";

        /*$mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';*/
        try {
            $date = date('Y-m-d H:i:s', time());
            $conn = $doctrine->getConnection();

            $conn->executeQuery("insert into contact_history(firstName,lastName,companyName,designationText,receiveEmail,messageText,phoneNumber,
		            email,sendTime,source,companyUrl,industryText,companyOverviewText,existingIaIpProfileText,overseasExpansionText,proprietaryTechnologyText) 
                    values('" . $firstName . "','" . $lastName . "','" . $company . "','" . $designation . "','" . $subemail . "','" . $message . "','" . $phone . "','" . $c_email . "','" . $date . "','" . $sourceRecordToDb . "','".$c_website."','".$industryRecordToDb."','".$companyOverview."','". $existingIP ."','". $overseasExpansion ."','". $proprietaryTechnology ."')");
            $mail->send();
            return new JsonResponse([]);
            // $conn->executeQuery("insert into contact_history(firstName,lastName,companyName,designationText,receiveEmail,messageText,phoneNumber,
            //         email,sendTime) values('".$firstName."','".$lastName."','".$company."','".$designation."','".$subemail."','".$message."','".$phone."','".$c_email."','".$date."')");
        } catch (\Exception $e) {
            return new JsonResponse([$e->getMessage()]);
        }
    }
}
