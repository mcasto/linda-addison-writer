<?php

namespace App\Providers;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\ServiceProvider;

date_default_timezone_set('America/Phoenix');

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'events' => \App\Models\Event::class,
            'covers' => \App\Models\Cover::class,
            'finds' => \App\Models\Find::class,
            'publications' => \App\Models\Publication::class,
            'online_resource_links' => \App\Models\OnlineResourceLink::class,
            'socials' => \App\Models\Social::class,
        ]);
    }

    public static function getSeedData($table)
    {
        $raw = file_get_contents(__DIR__ . '/seed-data.json');
        $decoded = json_decode($raw, true);
        return $decoded[$table];
    }

    public static function sendEmail(string $from, string $fromName, string $subject, string $replyToEmail, string $replyToName, ?string $message)
    {
        $to = env('CONTACT_EMAIL');
        $sgKey = env('SENDGRID_KEY');

        // send email with link to reset password
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from, $fromName);
        $email->setReplyTo($replyToEmail, $replyToName);
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent('text/plain', $message);
        $sendgrid = new \SendGrid($sgKey);

        try {
            $response = $sendgrid->send($email);
            return [
                'statusCode' => $response->statusCode(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ];
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/sendgrid-error.json', $e->getMessage());
            print json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
