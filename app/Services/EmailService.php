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

  private $mailCredentials;
  private $email;

  public function __construct(){
    $this->email = new Email;
    $this->mailCredentials = $this->email->getMailCredentials();
   
  }
    public function sendCouponApproval(){
      $denomination = new Denomination;
      $approval = $this->email->getCouponApprovalMail();
      
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
          $mail->Username = $this->mailCredentials->email;             // SMTP username
          $mail->Password = $this->mailCredentials->email_password;              // SMTP password
          $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
          $mail->Port = 587;                                    // TCP port to connect to

          //Recipients
          $mail->setFrom($this->mailCredentials->email, 'System Notification');
          //$mail->addAddress('paulalcabasa@gmail.com');
          $mail->addAddress($row->email_address, $row->approver_name);	// Add a recipient, Name is optional
          $mail->addBCC('paul-alcabasa@isuzuphil.com');
          //$mail->addBCC('paulalcabasa@gmail.com');
          //Content
          $mail->isHTML(true); 																	// Set email format to HTML
          $mail->Subject = 'System Notification : VIP Coupon Approval';
          $mail->Body    = view('email/approval', $data); // . $row->email_address;
          $mail->AddEmbeddedImage(config('app.project_root') . 'public/images/isuzu-logo.png', 'isuzu_logo');
          $directory = config('app.project_root') . $couponDetails->attachment;
        //  $directory = config('app.project_root') . 'storage/app/public/uploads/' . $couponDetails->new_filename;
          //dd($couponDetails->attachment);
          $mail->addAttachment($directory, $couponDetails->filename);
          if($mail->send()){
            $this->email->updateStatus($row->id);
            \Log::info("Mail sent");
          }
        
          
        } catch (Exception $e) {
          \Log::info("Mail not sent" . $e);
        }
      }
    }

    public function sendGeneratedCoupons(){
      $coupon = new Coupon;
      $denomination = new Denomination;
      $data = $coupon->getGeneratedCoupons();
     
      foreach($data as $row){
        
          $couponDetails = $coupon->getDetails($row->coupon_id);
          $denominations = $denomination->getByCoupon($row->coupon_id);
        
          $email_recipients = explode(";",$couponDetails->email);
          
          foreach($email_recipients as $email){
            $mail = new PHPMailer();                            // Passing `true` enables exceptions
            try {
              $data = [
                'couponDetails' => $couponDetails,
                'denomination' => $denominations,
                'message' => 'Your request for Coupon No. <strong>' . $row->coupon_id . '</strong> has been approved.',
                'print_link' => url('/') . '/api/print-coupon/' . $row->coupon_id . '/'  . $email
              ];
             // Server settings
              $mail->SMTPDebug = 0;                                	// Enable verbose debug output
              $mail->isSMTP();       
              $mail->CharSet    = "iso-8859-1";	// Set mailer to use SMTP
              $mail->Host = 'smtp.office365.com';												// Specify main and backup SMTP servers
              $mail->SMTPAuth = true;                              	// Enable SMTP authentication
              $mail->Username = $this->mailCredentials->email;             // SMTP username
              $mail->Password = $this->mailCredentials->email_password;              // SMTP password
              $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
              $mail->Port = 587;                                    // TCP port to connect to

              //Recipients
              $mail->setFrom($this->mailCredentials->email, 'System Notification');
      
              $mail->addAddress($email);	// Add a recipient, Name is optional
              
              // if service - add 
              if($couponDetails->coupon_type_id == 1){ // sales
                //$mail->addBCC('paul-alcabasa@isuzuphil.com');
              }
              else if($couponDetails->coupon_type_id == 2){
                $mail->addCC('christine-dimaano@isuzuphil.com');
                $mail->addCC('jad-ramos@isuzuphil.com');
               
              }

              $mail->addBCC('paul-alcabasa@isuzuphil.com');
            
              //Content
              $mail->isHTML(true); 																	// Set email format to HTML
              $mail->Subject = 'System Notification : VIP Coupon';
              $mail->Body    = view('email/print-voucher', $data); // . $row->email_address;
              $mail->AddEmbeddedImage(config('app.project_root') . 'public/images/isuzu-logo.png', 'isuzu_logo');
              
              if($mail->send()){
                $coupon->updateMailStatus([
                  'coupon_id' => $row->coupon_id,
                  'sent_flag' => 'Y',
                  'date_sent' => Carbon::now()
                ]);
                \Log::info("Mail sent");
              }
            } catch (Exception $e) {
              \Log::info("Mail not sent" . $e);
            }
          }
          
      }
    }
}