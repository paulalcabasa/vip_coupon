<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClaimHeader;
use App\Models\ClaimLine;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Approval;
use Carbon\Carbon;
use App\Models\Email;

class ApprovedClaimMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approved_claim:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $claimHeader = new ClaimHeader;
        $claimLine = new ClaimLine;
        $email = new Email;
        $mailCredentials = $email->getMailCredentials();
    
        $approved = $claimHeader->getApproved();
        
        foreach($approved as $row){
            $mail = new PHPMailer();                            // Passing `true` enables exceptions
            
            try {
                $headerDetails = $claimHeader->get($row->claim_header_id);
                // Server settings
                $mail->SMTPDebug = 0;                                	// Enable verbose debug output
                $mail->isSMTP();       
                $mail->CharSet    = "iso-8859-1";                      // Set mailer to use SMTP
                $mail->Host       = 'smtp.office365.com';              // Specify main and backup SMTP servers
                $mail->SMTPAuth   = true;                              // Enable SMTP authentication
                $mail->Username   = $mailCredentials->email;           // SMTP username
                $mail->Password   = $mailCredentials->email_password;  // SMTP password
                $mail->SMTPSecure = 'tls';                             // Enable TLS encryption, `ssl` also accepted
                $mail->Port       = 587;                               // TCP port to connect to

                //Recipients
                $mail->setFrom($mailCredentials->email, 'System Notification');
               
                $mail->addAddress($row->email_address, $row->approver_name);
    
                    // Add a recipient, Name is optional
                $mail->addBCC('paul-alcabasa@isuzuphil.com');
                //$mail->addBCC('paulalcabasa@gmail.com');
                //Content
                $data = [
                    'approver' => $row,
                    'headerDetails' => $headerDetails
                ];
            
                $mail->isHTML(true); 																	// Set email format to HTML
                $mail->Subject = 'System Notification : VIP Coupon Claim Request';
                $mail->Body    = view('email/claim-approved', $data); // . $row->email_address;
                $mail->AddEmbeddedImage(config('app.project_root') . 'public/images/isuzu-logo.png', 'isuzu_logo');
            
                if($mail->send()){
                    $claimHeaderUpdate = ClaimHeader::find($row->claim_header_id);
                    $claimHeaderUpdate->date_mail_sent = Carbon::now(); // set to active
                    $claimHeaderUpdate->save(); 
                   // $email->updateStatus($approver->id);
                    \Log::info("Mail sent");
                }
            
            
            } catch (Exception $e) {
                \Log::info("Mail not sent" . $e);
            }
                
        }
        
    }
}
