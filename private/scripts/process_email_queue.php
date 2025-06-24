<?php
// File: e:\Application\laragon\www\rtk_web_admin\private\scripts\process_email_queue.php

// Increase execution time limit for this script, if necessary and allowed
set_time_limit(0); // 0 = no time limit

// --- Configuration & Setup ---
define('MAX_EMAILS_PER_RUN', 20); // Max emails to process in one run
define('MAX_SEND_ATTEMPTS', 5);  // Max attempts to send an email

// Error logging for this script (custom file logging disabled per user request)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to stdout for cron

// --- Include Dependencies ---
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php'; // Defines DB_HOST, DB_NAME, etc.
require_once __DIR__ . '/../config/mail.php';     // Defines MAIL_HOST, MAIL_USERNAME, etc.
require_once __DIR__ . '/../classes/Database.php'; // Assumes Database class for PDO connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

// --- Main Processing Logic ---
try {
    $db = Database::getInstance()->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    error_log("Cron Email Queue: Starting run. Processing up to " . MAX_EMAILS_PER_RUN . " emails.");

    // Fetch pending emails
    $stmt = $db->prepare("SELECT * FROM email_queue 
                           WHERE status = 'pending' AND attempts < :max_attempts
                           ORDER BY created_at ASC 
                           LIMIT :limit");
    $stmt->bindValue(':max_attempts', MAX_SEND_ATTEMPTS, PDO::PARAM_INT);
    $stmt->bindValue(':limit', MAX_EMAILS_PER_RUN, PDO::PARAM_INT);
    $stmt->execute();
    $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($emails)) {
        error_log("Cron Email Queue: No pending emails to process.");
        // No need to echo anything for cron, logging is sufficient
    }

    $processedCount = 0;
    $sentCount = 0;
    $failedCount = 0;

    foreach ($emails as $email) {
        $processedCount++;
        $mail = new PHPMailer(true);
        try {
            // Server settings from mail.php
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = defined('MAIL_ENCRYPTION') ? MAIL_ENCRYPTION : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';

            // Recipients
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email['recipient_email']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $email['subject'];
            $mail->Body    = $email['html_body'];
            if (!empty($email['text_body'])) {
                $mail->AltBody = $email['text_body'];
            }

            $mail->send();
            
            // Update email status to 'sent'
            $updateStmt = $db->prepare("UPDATE email_queue 
                                        SET status = 'sent', sent_at = NOW(), attempts = attempts + 1 
                                        WHERE id = :id");
            $updateStmt->execute([':id' => $email['id']]);
            error_log("Cron Email Queue: Successfully sent email ID {$email['id']} to {$email['recipient_email']}. Attempt: " . ($email['attempts'] + 1));
            $sentCount++;

        } catch (PHPMailerException $e) {
            // Update email status to 'failed' or keep 'pending' for retry
            $newStatus = ($email['attempts'] + 1 >= MAX_SEND_ATTEMPTS) ? 'failed' : 'pending';
            $updateStmt = $db->prepare("UPDATE email_queue 
                                        SET status = :status, last_attempt_at = NOW(), attempts = attempts + 1 
                                        WHERE id = :id");
            $updateStmt->execute([':status' => $newStatus, ':id' => $email['id']]);
            error_log("Cron Email Queue: Failed to send email ID {$email['id']} to {$email['recipient_email']}. Error: {$mail->ErrorInfo}. Attempt: " . ($email['attempts'] + 1) . ". New status: {$newStatus}");
            $failedCount++;
        } catch (PDOException $e) {
            error_log("Cron Email Queue: Database error while processing email ID {$email['id']}. DB Error: " . $e->getMessage());
            // Don't stop the whole script for one DB error during update, but log it.
        }
    }

    error_log("Cron Email Queue: Run finished. Processed: {$processedCount}, Sent: {$sentCount}, Failed: {$failedCount}.");

} catch (PDOException $e) {
    error_log("Cron Email Queue: Database connection error or critical query failed: " . $e->getMessage());
    // Echo for cron runner if critical DB issue
    echo "Critical Database Error: " . $e->getMessage() . "\n";
} catch (Throwable $t) { // Catch any other errors/exceptions
    error_log("Cron Email Queue: An unexpected error occurred: " . $t->getMessage() . " in " . $t->getFile() . " on line " . $t->getLine());
    echo "Unexpected Error: " . $t->getMessage() . "\n";
} finally {
    // --- Release Lock File ---
    if ($lockFp) {
        flock($lockFp, LOCK_UN); // Release the lock
        fclose($lockFp);         // Close the file pointer
        // Optionally, you can unlink($lockFile); if you prefer the lock file to be absent when not running
    }
}

exit(0); // Successful exit for cron
?>
