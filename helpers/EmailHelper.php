<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

class EmailHelper {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Configure SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'your-email@gmail.com'; // Your Gmail
        $this->mailer->Password = 'your-app-password'; // Gmail App Password
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
    }

    public function sendEmail($data) {
        try {
            $this->mailer->setFrom('your-email@gmail.com', 'Event Registration System');
            $this->mailer->addAddress($data['to']);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $data['subject'];
            
            // Get email template
            $template = $this->getEmailTemplate($data['template'], $data['data']);
            $this->mailer->Body = $template;

            // Attach QR code if available
            if (isset($data['data']['qr_code_path'])) {
                $this->mailer->addAttachment($data['data']['qr_code_path'], 'event-qr-code.png');
            }

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    private function getEmailTemplate($template, $data) {
        ob_start();
        include "../email_templates/{$template}.php";
        return ob_get_clean();
    }
} 