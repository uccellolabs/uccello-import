<?php

namespace Uccello\Import\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ImportIsReadyNotification extends Notification
{
    use Queueable;

    protected $importType;
    protected $importData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($importType, $importData = null)
    {
        $this->importType = $importType;
        $this->importData   = $importData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // dd($this->importData );

        if ($this->importData
            && $this->importData['lines']
            && $this->importData['lines'] == $this->importData['created']
        ) {
            $mail = $this->newMailSucces();
        } else {
            $mail = $this->newMailErrors();
        }

        return $mail;
    }

    public function newMailSucces()
    {
        $mail = new MailMessage();

        $mail->subject($this->importType . ' : Import terminé');
        $mail->line('L\'import des ' . $this->importType . ' est terminé.');

        if ($this->importData && $this->importData['lines']) {
            $mail->line('Lignes importées : ' . $this->importData['lines']);
        }

        return $mail;
    }

    public function newMailErrors()
    {
        $mail = new MailMessage();

        $mail->subject($this->importType . ' : Import terminé - À VÉRIFIER');
        $mail->line('L\'import des ' . $this->importType . ' s\'est terminé avec des erreurs...');

        if ($this->importData) {
            if ($this->importData['lines']) {
                $mail->line('Lignes importées : ' . $this->importData['lines']);
            }

            if ($this->importData['ignored']) {
                $mail->line('Lignes ignorées : ' . $this->importData['ignored']);
            }

            if ($this->importData['created']) {
                $mail->line($this->importType . ' créés : ' . $this->importData['created']);
            }

            if ($this->importData['updated']) {
                $mail->line($this->importType . ' modifiés : ' . $this->importData['updated']);
            }

            // Add attached log file to the email (from $this->importData['log'])
            $mail->attachData($this->importData['log'], 'log.txt', [
                'mime' => 'text/plain',
            ]);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}