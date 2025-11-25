<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Applicant $applicant,
        public string $username,
        public string $temporaryPassword,
        public string $campusName
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ESSU Admission Account Has Been Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $fullName = trim(
            $this->applicant->first_name . ' ' . 
            ($this->applicant->middle_name ? $this->applicant->middle_name . ' ' : '') . 
            $this->applicant->last_name
        );

        return new Content(
            text: 'emails.credentials',
            with: [
                'fullName' => $fullName,
                'username' => $this->username,
                'temporaryPassword' => $this->temporaryPassword,
                'campusName' => $this->campusName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

