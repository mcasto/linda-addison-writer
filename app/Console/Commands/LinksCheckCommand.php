<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BrokenLink;

class LinksCheckCommand extends Command
{
    protected $signature = 'links:check';
    protected $description = 'Check all URLs in various tables and update broken_links table';

    public function handle(): int
    {
        $this->info("Starting monthly link check...");
        $this->info("Note: Facebook and social media links may show false positives due to bot protection.");
        $this->info("Consider manual verification for important social media links.");
        $this->newLine();

        $tables = [
            'covers' => 'purchase_url',
            'events' => 'url',
            'finds' => 'url',
            'online_resource_links' => 'url',
            'publications' => 'url',
            'socials' => 'url',
        ];

        $totalChecked = 0;
        $brokenDetected = 0;

        foreach ($tables as $table => $urlField) {
            $this->info("Checking {$table}...");

            DB::table($table)->orderBy('id')->chunk(50, function ($rows) use ($table, $urlField, &$totalChecked, &$brokenDetected) {
                foreach ($rows as $row) {
                    $totalChecked++;

                    if (empty($row->$urlField)) {
                        continue;
                    }

                    $isBroken = $this->isLinkBroken($row->$urlField);

                    if ($isBroken) {
                        $brokenDetected++;
                        $this->handleBrokenLink($table, $row->id, $row->$urlField);
                        $this->line(" - [{$table} #{$row->id}] DETECTED AS BROKEN: {$row->$urlField}");
                    } else {
                        $this->line(" - [{$table} #{$row->id}] OK: {$row->$urlField}");
                    }

                    // Add a small delay to avoid rate limiting
                    usleep(200000); // 200ms delay
                }
            });
        }

        $this->newLine();
        $this->info("Link checking completed.");
        $this->info("Total URLs checked: {$totalChecked}");
        $this->info("Potential broken links detected: {$brokenDetected}");
        $this->info("Check the admin panel for manual verification.");

        return self::SUCCESS;
    }

    private function handleBrokenLink(string $table, int $tableId, string $url): void
    {
        $existingRecord = BrokenLink::where('table_name', $table)
            ->where('table_id', $tableId)
            ->first();

        if ($existingRecord) {
            // Check if manual confirmation is older than 3 months
            $isExpired = $existingRecord->confirmed_date &&
                $existingRecord->confirmed_date->diffInMonths(now()) >= 3;

            if ($isExpired) {
                // Remove expired confirmation - let it be re-checked next month
                $existingRecord->delete();
                $this->line("   ↳ Removed expired confirmation for {$table} #{$tableId}");
            }
            // If record exists and not expired, do nothing (keep manual confirmation)
        } else {
            // Create new detection record (not confirmed yet)
            BrokenLink::create([
                'table_name' => $table,
                'table_id' => $tableId,
                'confirmed_broken' => false, // Not confirmed yet
                'confirmed_working' => false, // Not confirmed yet
                'confirmed_date' => null, // No manual confirmation
            ]);
            $this->line("   ↳ Flagged for manual verification: {$table} #{$tableId}");
        }

        // Log to file for reference
        file_put_contents(storage_path('logs/broken-links.log'), print_r([
            'timestamp' => now()->toDateTimeString(),
            'table' => $table,
            'table_id' => $tableId,
            'url' => $url,
            'action' => $existingRecord ? ($isExpired ? 'expired_removed' : 'existing_kept') : 'new_flagged'
        ], true), FILE_APPEND);
    }

    private function isLinkBroken(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        // Normalize URL
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
        }

        $domain = parse_url($url, PHP_URL_HOST);

        // Special handling for Facebook links
        if (strpos($domain, 'facebook.com') !== false) {
            return $this->checkFacebookLink($url);
        }

        // Special handling for Amazon author pages
        if (
            strpos($url, 'amazon.com/author/') !== false ||
            strpos($url, 'amazon.co.uk/author/') !== false
        ) {
            return $this->checkAmazonAuthorLink($url);
        }

        // Special handling for other social media sites
        if ($this->isSocialMediaUrl($url)) {
            return $this->checkSocialMediaLink($url);
        }

        // General link checking for all other URLs
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_NOBODY         => false,
            CURLOPT_HEADER         => true,
            CURLOPT_ENCODING       => '',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        curl_close($ch);

        // Valid status codes (including redirects)
        $validCodes = [200, 201, 202, 203, 204, 205, 206, 301, 302, 303, 304, 307, 308];

        return $err !== 0 || !in_array($httpCode, $validCodes);
    }

    private function checkFacebookLink(string $url): bool
    {
        // Special handling for Facebook Live links
        if (
            strpos($url, 'facebook.com/watch/live') !== false ||
            strpos($url, 'facebook.com/watch/?v=') !== false
        ) {
            return $this->checkFacebookLiveLink($url);
        }

        // Special handling for Facebook profile/group/page links
        if (strpos($url, 'facebook.com/') !== false) {
            return $this->checkFacebookProfileLink($url);
        }

        // Default Facebook handling for other types of links
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_COOKIE => 'locale=en_US',
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        curl_close($ch);

        // For Facebook, assume most things are valid unless we get clear errors
        $brokenCodes = [404, 410];
        $validCodes = [200, 301, 302, 303, 307, 308, 403, 429];

        if (in_array($httpCode, $brokenCodes)) {
            return true;
        }

        if ($err !== 0 || !in_array($httpCode, $validCodes)) {
            // Try one more time with GET for uncertain cases
            return $this->checkFacebookLinkWithGet($url);
        }

        return false;
    }

    private function checkFacebookLiveLink(string $url): bool
    {
        // Facebook Live links are particularly tricky
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_REFERER => 'https://www.facebook.com/',
            CURLOPT_COOKIE => 'locale=en_US',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        curl_close($ch);

        if ($err !== 0) {
            return true;
        }

        // Check for specific error patterns in the response
        $errorPatterns = [
            'This video isn\'t available anymore',
            'Video unavailable',
            'Content not available',
            'Page not found',
            'Sorry, something went wrong',
        ];

        foreach ($errorPatterns as $pattern) {
            if (stripos($response, $pattern) !== false) {
                return true;
            }
        }

        // If we get any successful response, assume it's valid
        if ($httpCode >= 200 && $httpCode < 400) {
            return false;
        }

        // For Live links, be extra conservative
        return in_array($httpCode, [404, 410]);
    }

    private function checkFacebookProfileLink(string $url): bool
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_REFERER => 'https://www.google.com/',
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        curl_close($ch);

        return $err !== 0 || in_array($httpCode, [404, 410]);
    }

    private function checkFacebookLinkWithGet(string $url): bool
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HEADER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        curl_close($ch);

        // Check for specific error patterns
        $errorPatterns = [
            'Content Not Available',
            'This content isn\'t available right now',
            'Page not found',
        ];

        foreach ($errorPatterns as $pattern) {
            if (stripos($response, $pattern) !== false) {
                return true;
            }
        }

        return $err !== 0 || $httpCode >= 400;
    }

    private function checkAmazonAuthorLink(string $url): bool
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        curl_close($ch);

        return $err !== 0 || ($httpCode !== 200 && $httpCode !== 301 && $httpCode !== 302);
    }

    private function isSocialMediaUrl(string $url): bool
    {
        $socialDomains = [
            'twitter.com',
            'x.com',
            'instagram.com',
            'linkedin.com',
            'youtube.com',
            'tiktok.com',
            'pinterest.com',
            'reddit.com',
            'goodreads.com',
        ];

        foreach ($socialDomains as $domain) {
            if (strpos($url, $domain) !== false) {
                return true;
            }
        }

        return false;
    }

    private function checkSocialMediaLink(string $url): bool
    {
        // Conservative checking for social media sites
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_errno($ch);
        curl_close($ch);

        // Be lenient with social media - they often have bot protection
        $validSocialCodes = [200, 301, 302, 303, 307, 308, 403, 429];
        return $err !== 0 || !in_array($httpCode, $validSocialCodes);
    }
}
