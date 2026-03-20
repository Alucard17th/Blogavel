<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

final class BlogavelMakeAdminCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'blogavel:make-admin
        {--name= : The user name}
        {--email= : The user email}
        {--password= : The user password (discouraged; prefer interactive prompt)}
        {--use-id : Add the created user id to BLOGAVEL_MANAGE_BLOG_ADMIN_IDS instead of using email}
        {--no-env : Do not write to .env (only create the user)}';

    /**
     * @var string
     */
    protected $description = 'Create a Blogavel admin user and enable the manage-blog gate configuration.';

    public function handle(): int
    {
        $userModel = (string) config('auth.providers.users.model');
        if ($userModel === '' || ! class_exists($userModel)) {
            $this->error('Cannot resolve auth.providers.users.model. Please ensure your auth configuration defines a valid users model.');

            return self::FAILURE;
        }

        $name = (string) ($this->option('name') ?? '');
        if ($name === '') {
            $name = (string) $this->ask('Name');
        }

        $email = (string) ($this->option('email') ?? '');
        if ($email === '') {
            $email = (string) $this->ask('Email');
        }

        $password = (string) ($this->option('password') ?? '');
        if ($password === '') {
            $password = (string) $this->secret('Password');
            $confirm = (string) $this->secret('Confirm password');

            if ($password !== $confirm) {
                $this->error('Passwords do not match.');

                return self::FAILURE;
            }
        }

        if ($name === '' || $email === '' || $password === '') {
            $this->error('Name, email and password are required.');

            return self::FAILURE;
        }

        /** @var \Illuminate\Database\Eloquent\Model $user */
        $user = (new $userModel());

        $existing = $user->newQuery()->where('email', $email)->first();
        if ($existing !== null) {
            $this->warn("A user with email {$email} already exists (id: {$existing->getAuthIdentifier()}).");
            $useExisting = (bool) $this->confirm('Use the existing user as Blogavel admin?', true);

            if (! $useExisting) {
                return self::FAILURE;
            }

            $user = $existing;
        } else {
            $user->fill([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $user->save();
            $this->info("User created (id: {$user->getAuthIdentifier()}).");
        }

        if ((bool) $this->option('no-env')) {
            $this->warn('Skipped .env update because --no-env was provided.');

            return self::SUCCESS;
        }

        $envPath = base_path('.env');
        if (! is_file($envPath)) {
            $this->error(".env file not found at: {$envPath}");

            return self::FAILURE;
        }

        if (! is_writable($envPath)) {
            $this->error(".env file is not writable: {$envPath}");

            return self::FAILURE;
        }

        $env = (string) file_get_contents($envPath);

        $env = $this->setEnvValue($env, 'BLOGAVEL_MANAGE_BLOG_GATE', 'true');

        if ((bool) $this->option('use-id')) {
            $env = $this->appendEnvCsvValue($env, 'BLOGAVEL_MANAGE_BLOG_ADMIN_IDS', (string) $user->getAuthIdentifier());
            $this->info('Added user id to BLOGAVEL_MANAGE_BLOG_ADMIN_IDS.');
        } else {
            $env = $this->appendEnvCsvValue($env, 'BLOGAVEL_MANAGE_BLOG_ADMIN_EMAILS', (string) $user->email);
            $this->info('Added user email to BLOGAVEL_MANAGE_BLOG_ADMIN_EMAILS.');
        }

        file_put_contents($envPath, $env);

        $this->info('Blogavel manage-blog gate enabled.');
        $this->warn('If you are using config caching, run: php artisan config:clear');

        return self::SUCCESS;
    }

    private function setEnvValue(string $env, string $key, string $value): string
    {
        $pattern = "/^".preg_quote($key, '/')."=.*/m";
        $line = $key.'='.$value;

        if (preg_match($pattern, $env) === 1) {
            return (string) preg_replace($pattern, $line, $env);
        }

        $env = rtrim($env, "\r\n");

        return $env."\n".$line."\n";
    }

    private function appendEnvCsvValue(string $env, string $key, string $value): string
    {
        $pattern = "/^".preg_quote($key, '/')."=(.*)$/m";

        if (preg_match($pattern, $env, $matches) === 1) {
            $current = (string) ($matches[1] ?? '');
            $items = array_values(array_filter(array_map('trim', explode(',', $current))));

            if (! in_array($value, $items, true)) {
                $items[] = $value;
            }

            $line = $key.'='.implode(',', $items);

            return (string) preg_replace($pattern, $line, $env);
        }

        $env = rtrim($env, "\r\n");

        return $env."\n".$key.'='.$value."\n";
    }
}
