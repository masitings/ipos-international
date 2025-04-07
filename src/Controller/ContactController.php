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
        // Create a logger
        $logger = new \Psr\Log\NullLogger();
        if ($this->container->has('logger')) {
            $logger = $this->container->get('logger');
        }
        
        $logger->info('Contact form submission started');

        /** CSRF Security */
        if ($request->get('csrf_token') == "" or $request->get('csrf_token') != "9847h3hchc65rdytegbhcjcccc21") {
            $logger->warning('CSRF token validation failed');
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $emailObj = new DataObject\Emails\Listing();
        $emailObj->load();
        $sendMail = 'enquiry@iposinternational.com';
        $sendMails = [];

        foreach ($emailObj as $email) {
            $sendMails[] = $email->getEmail();
        }
        
        $logger->info('Recipient emails loaded', ['count' => count($sendMails)]);

        $list = new WebsiteSetting\Listing();
        $list->setCondition('`name` LIKE ' . $list->quote('%smtp_mail%'));
        $list = $list->load();

        // NEW CONFIG
        $mailConfig = [
            'mail_host' => 'smtp.office365.com',
            'mail_name' => 'IPOS International',
            'mail_username' => 'zhikai2505@gmail.com',
            'mail_passwd' => 'okikbaqyjnuxqvwn',
            'mail_port' => 587,
            'mail_from' => 'zhikai2505@gmail.com'
        ];
        
        $logger->info('Mail configuration loaded', ['host' => $mailConfig['mail_host'], 'port' => $mailConfig['mail_port']]);

        // Get form data
        $state = $request->get('state');
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $company = $request->get('company');
        $designation = $request->get('designation');
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

        $logger->info('Form data received', [
            'state' => $state,
            'email' => $c_email,
            'name' => "$firstName $lastName"
        ]);

        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->Debugoutput = function($str, $level) use ($logger) {
                $logger->debug("PHPMailer [$level]: $str");
            };
            
            $mail->CharSet = "UTF-8";
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'STARTTLS';

            $mail->Username = $mailConfig['mail_username'];
            $mail->Password = $mailConfig['mail_passwd'];
            $mail->Host = $mailConfig['mail_host'];
            $mail->Port = $mailConfig['mail_port'];
            
            $logger->info('SMTP configuration set');

            $mail->setFrom($mailConfig['mail_from'], "noreply@iposinternational.com");

            if ($sendMails) {
                foreach ($sendMails as $email) {
                    $mail->addAddress($email);
                    $logger->info('Added recipient', ['email' => $email]);
                }
            } else {
                if (array_key_exists('ENV_STAGE', $_ENV)) {
                    if ($_ENV['ENV_STAGE'] == 'staging' || $_ENV['ENV_STAGE'] == 'dev') {
                        $mail->addAddress("arigiwiratama@gmail.com");
                        $mail->addAddress("zhikai.yap@aikendigital.co");
                        $logger->info('Added development recipients');
                    }
                } else {
                    $mail->addAddress($sendMail);
                    $logger->info('Added default recipient', ['email' => $sendMail]);
                }
            }

            $mail->addReplyTo($mailConfig['mail_from'], "noreply@iposinternational.com");
            
            $mail->Subject = $state;
            $mail->isHTML(true);
            
            // Build email body
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
            
            $logger->info('Email body prepared');

            // Save to database first
            $date = date('Y-m-d H:i:s', time());
            $conn = $doctrine->getConnection();

            $conn->executeQuery("insert into contact_history(firstName,lastName,companyName,designationText,receiveEmail,messageText,phoneNumber,
                    email,sendTime,source,companyUrl,industryText,companyOverviewText,existingIaIpProfileText,overseasExpansionText,proprietaryTechnologyText) 
                    values('" . $firstName . "','" . $lastName . "','" . $company . "','" . $designation . "','" . $subemail . "','" . $message . "','" . $phone . "','" . $c_email . "','" . $date . "','" . $sourceRecordToDb . "','".$c_website."','".$industryRecordToDb."','".$companyOverview."','". $existingIP ."','". $overseasExpansion ."','". $proprietaryTechnology ."')");
            
            $logger->info('Contact saved to database');
            
            // Send the email
            $mail->send();
            $logger->info('Email sent successfully');
            
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $logger->error('Failed to send email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    // ... existing code ...
}
