<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Promo;
use App\Models\Approval;
use Carbon\Carbon;


class PromoRequestorNotif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requestor:send';

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
        $email = new Email;
        $mailCredentials = $email->getMailCredentials();

        $promo = new Promo;
        $validatedPromos = $promo->getValidated();

        foreach($validatedPromos as $row){
            $mail = new PHPMailer();                            // Passing `true` enables exceptions
        
            try {
        
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
                
                $mail->addAddress($row->email_address);
            
              //  echo $row->email_address;
                //die;
                // Add a recipient, Name is optional
                $mail->addBCC('paul-alcabasa@isuzuphil.com');
                //$mail->addBCC('paulalcabasa@gmail.com');
                //Content
               
                $data = [
                    'promo' => $row
                ];
               
                $mail->isHTML(true); 																	// Set email format to HTML
                $mail->Subject = 'System Notification : VIP Coupon Promo';
                $mail->Body    = view('email/promo-message', $data); // . $row->email_address;
                $mail->AddEmbeddedImage(config('app.project_root') . 'public/images/isuzu-logo.png', 'isuzu_logo');
            
                if($mail->send()){
                    $promo_update = Promo::find($row->id);
    
                    $promo_update->mail_sent = 'Y'; // set to active
                    $promo_update->date_sent = Carbon::now(); // set to active
                    $promo_update->save();
                    \Log::info("Mail sent");
                }
            
            
            } catch (Exception $e) {
            \Log::info("Mail not sent" . $e);
            }
        }
    }
}
