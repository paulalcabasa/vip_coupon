<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {

    public function sendCouponApproval(){
      $email = new Email;

      $mailCredentials = $email->getMailCredentials();

      $approval = $email->getCouponApprovalMail();
    
      foreach($approval as $row){
        
        $mail = new PHPMailer();                            // Passing `true` enables exceptions

        try {
          
          $data = [
            'coupon_no' => 1
          ];


          // Server settings
          $mail->SMTPDebug = 0;                                	// Enable verbose debug output
          $mail->isSMTP();       
          $mail->CharSet    = "iso-8859-1";	// Set mailer to use SMTP
          $mail->Host = 'smtp.office365.com';												// Specify main and backup SMTP servers
          $mail->SMTPAuth = true;                              	// Enable SMTP authentication
          $mail->Username = $mailCredentials->email;             // SMTP username
          $mail->Password = $mailCredentials->email_password;              // SMTP password
          $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
          $mail->Port = 587;                                    // TCP port to connect to

          //Recipients
          $mail->setFrom($mailCredentials->email, 'System Notification');
          $mail->addAddress($row->email_address, $row->approver_name);	// Add a recipient, Name is optional
          $mail->addBCC('paul-alcabasa@isuzuphil.com');
          $mail->addBCC('paulalcabasa@gmail.com');
          $mail->AddEmbeddedImage(config('app.pub_url') . '/public/images/isuzu-logo-compressor.png', 'isuzu_logo');
           //Content
          $mail->isHTML(true); 																	// Set email format to HTML
          $mail->Subject = 'System Notification : VIP Coupon Approval';
          $mail->Body    =  view('email/approval', $data); // . $row->email_address;

          $mail->send();
        
          \Log::info("Mail sent");
        } catch (Exception $e) {
          \Log::info("Mail not sent" . $e);
        }
      }
    }
}