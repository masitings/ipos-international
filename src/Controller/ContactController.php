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
        // $sendMail = 'enquiry@iposinternational.com';
        $sendMail = 'zhikai.yap@aikendigital.co';
        $sendMails = [];

        foreach ($emailObj as $email) {
            $sendMails[] = $email->getEmail();
        }

        $list = new WebsiteSetting\Listing();

        $list->setCondition('`name` LIKE ' . $list->quote('%smtp_mail%'));
        $list = $list->load();
        $mailConfig = [];
        foreach ($list as $item) {
            if (in_array($item->getName(), self::$SMTP_ARRAY)) {
                $mailConfig[$item->getName()] = $item->getData();
            }
        }


        // $mailConfig['smtp_mail_password'] = $this->decrypt($mailConfig['smtp_mail_password']);


        // $mailConfig = require '../config/mail.php';

        $state = $request->get('state');

        $firstName = $request->get('firstName');

        $lastName = $request->get('lastName');

        $company = $request->get('company');

        $designation = $request->get('designation');

        $source = str_replace("/", "", $request->get('infoSource'));
        $infoSourceOthers = "";
        $sourceRecordToDb = $source;
        if ($source == "Others") {
            $infoSourceOthers = $request->get('infoSourceOthers');
            $sourceRecordToDb = "Other - " . $infoSourceOthers;
        }

        $message = $request->get('message');
        $phone = $request->get('phone');

        $c_email = $request->get('email');

        $subemail = $request->get('subsemail') ? 'Yes' : 'No';

        $mail = new PHPMailer(true);

        $mail->CharSet = "UTF-8";                     //设定邮件编码

        $mail->SMTPDebug = 0;                        // 调试模式输出

        $mail->isSMTP();                             // 使用SMTP

        $mail->Host = 'smtp.office365.com';                // SMTP服务器

        $mail->SMTPAuth = true;                      // 允许 SMTP 认证

        $mail->Username = 'noreply@iposinternational.com';                // SMTP 用户名  即邮箱的用户名

        $mail->Password = 'zDb#v&1kAK2dzZ';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)

        $mail->SMTPSecure = 'STARTTLS';                    // 允许 TLS 或者ssl协议

        $mail->Port = '587';                            // 服务器端口 25 或者465 具体要看邮箱服务器支持



        $mail->setFrom('noreply@iposinternational.com', '');  //发件人

        if ($sendMails) {
            foreach ($sendMails as $email) {
                $mail->addAddress($email);
            }
        } else {
            $mail->addAddress($sendMail);
        }


        //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
        $mail->addReplyTo('noreply@iposinternational.com', 'info'); //回复的时候回复给哪个邮箱 建议和发件人一致

        $mail->isHTML(false);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容

        $mail->Subject = $state;

        $mail->Body    = 'FirstName : ' . $firstName . "\r\n";
        $mail->Body .= 'LastName : ' . $lastName . "\r\n";


        $mail->Body .= 'Company : ' . $company . "\r\n";

        $mail->Body .= 'Designation : ' . $designation . "\r\n";
        $mail->Body .= 'Phone : ' . $phone . "\r\n";
        $mail->Body .= 'Email : ' . $c_email . "\r\n";

        $mail->Body .= 'ReceiveMarketingEmail :' . $subemail . "\r\n";

        if ($infoSourceOthers != "") {
            $mail->Body .= 'InfoSource : ' . $source . "\r\n";
            $mail->Body .= 'InfoSourceOthers : ' . $infoSourceOthers . "\r\n";
        } else {
            $mail->Body .= 'InfoSource : ' . $source . "\r\n";
            $mail->Body .= 'InfoSourceOthers : ' . $infoSourceOthers . "\r\n";
        }

        $mail->Body .= 'Message : ' . "\r\n" . $message;

        /*$mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';*/
        try {
            $mail->send();
            $date = date('Y-m-d H:i:s', time());
            $conn = $doctrine->getConnection();

            $conn->executeQuery("insert into contact_history(firstName,lastName,companyName,designationText,receiveEmail,messageText,phoneNumber,
		            email,sendTime,source) values('" . $firstName . "','" . $lastName . "','" . $company . "','" . $designation . "','" . $subemail . "','" . $message . "','" . $phone . "','" . $c_email . "','" . $date . "','" . $sourceRecordToDb . "')");
            // $conn->executeQuery("insert into contact_history(firstName,lastName,companyName,designationText,receiveEmail,messageText,phoneNumber,
            //         email,sendTime) values('".$firstName."','".$lastName."','".$company."','".$designation."','".$subemail."','".$message."','".$phone."','".$c_email."','".$date."')");
        } catch (\Exception $e) {
        }

        return new JsonResponse([]);
    }
}
