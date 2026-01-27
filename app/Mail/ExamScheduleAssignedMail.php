<?php

namespace App\Mail;

use App\Mail\Traits\EmailSafetyTrait;
use App\Models\Applicant;
use App\Models\ExamSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExamScheduleAssignedMail extends Mailable
{
    use Queueable, SerializesModels, EmailSafetyTrait;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Applicant $applicant,
        public ExamSchedule $schedule,
        public string $examTitle,
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
            subject: 'Your ESSU Admission Exam Schedule',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            text: 'emails.schedule',
            with: [
                'examTitle' => $this->examTitle,
                'scheduleDate' => $this->schedule->schedule_date->format('F d, Y'),
                'startTime' => \Carbon\Carbon::parse($this->schedule->start_time)->format('g:i A'),
                'endTime' => \Carbon\Carbon::parse($this->schedule->end_time)->format('g:i A'),
                'campusName' => $this->campusName,
                'appRefNo' => $this->applicant->app_ref_no,
                'location' => $this->schedule->location ?? 'TBA',
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

