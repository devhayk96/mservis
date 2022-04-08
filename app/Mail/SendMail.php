<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $data;

    /**
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        if (empty($data)) {
            throw new \Exception("Data is empty");
        }
        $this->data = $data;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->subject('Api transaction error')
            ->markdown('mail.transaction_create', [
                'details' => $this->data,
            ]);
    }
}
