<?php

namespace App\Mail;

use App\Models\PengusulanPerbub;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PengusulanStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pengusulan;
    public $statusLabel;
    public $catatan;

    public function __construct(PengusulanPerbub $pengusulan, $catatan = null)
    {
        $this->pengusulan = $pengusulan;
        $this->catatan = $catatan;
        
        $statusLabels = [
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'revisi' => 'Perlu Revisi'
        ];
        
        $this->statusLabel = $statusLabels[$pengusulan->status] ?? $pengusulan->status;
    }

    public function build()
    {
        $subject = "Update Status Pengusulan PERBUB: {$this->pengusulan->judul_perbub}";
        
        return $this->subject($subject)
            ->view('emails.pengusulan-status')
            ->with([
                'pengusulan' => $this->pengusulan,
                'statusLabel' => $this->statusLabel,
                'catatan' => $this->catatan,
            ]);
    }
}

