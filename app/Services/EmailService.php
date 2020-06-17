<?php

namespace App\Services;
use DB;
use Carbon\Carbon;
use App\Models\Email;
use App\Models\Coupon;
use App\Models\Approval;
use App\Models\Denomination;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {

    public function sendCouponApproval(){
      $email = new Email;
      $denomination = new Denomination;
      $mailCredentials = $email->getMailCredentials();
      $approval = $email->getCouponApprovalMail();
    
      foreach($approval as $row){
    
        $mail = new PHPMailer();                            // Passing `true` enables exceptions
        $coupon = new Coupon;
        $couponDetails = $coupon->getDetails($row->module_reference_id);
        $denominations = $denomination->getByCoupon($row->module_reference_id);
        
        try {
          
          $data = [
            'couponDetails' => $couponDetails,
            'denomination' => $denominations,
            'approval_details' => $row,
            'approve_link' => url('/') . '/api/approve/' . $row->id,
            'reject_link' => url('/') . '/api/reject/' . $row->id
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
          //Content
          $mail->isHTML(true); 																	// Set email format to HTML
          $mail->Subject = 'System Notification : VIP Coupon Approval';
          $mail->Body    = view('email/approval', $data); // . $row->email_address;
          $mail->AddEmbeddedImage(config('app.project_root') . 'public/images/isuzu-logo.png', 'isuzu_logo');
          
          if($mail->send()){
            $email->updateStatus($row->id);
            \Log::info("Mail sent");
          }
        
          
        } catch (Exception $e) {
          \Log::info("Mail not sent" . $e);
        }
      }
    }
}