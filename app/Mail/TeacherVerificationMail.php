<?php

namespace App\Mail;

use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeacherVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Teacher $teacher,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vérification de votre adresse e-mail — Revizy',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'teacher.emails.verify',
        );
    }
}
