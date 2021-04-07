<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class RefreshTwitchTokenCommand extends Command
{
    private $client;
    private $params;
    private $file;

    protected static $defaultName = 'app:refresh-twitch-token';
    protected static $defaultDescription = 'Gets a new App Token from the Twitch ID service';

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Filesystem $file)
    {
        $this->client = $client;
        $this->params = $params;
        $this->file = $file;
        parent::__construct();

    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('twitchId', 'i', InputOption::VALUE_OPTIONAL, 'Your Client ID for your Twitch application')
            ->addOption('twitchSecret', 's', InputOption::VALUE_OPTIONAL, 'Your Client Secret for your Twitch application')
            ->addOption('envFile', 'f', InputOption::VALUE_OPTIONAL, 'The .env.local file to rewrite the TWITCH_APP_TOKEN variable')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $io = new SymfonyStyle($input, $output);
        $argId = $input->getOption('twitchId');
        $argSecret = $input->getOption('twitchSecret');
        $argFile = $input->getOption('envFile');

        $twitchId = $argId ?? $this->params->get('app.twitch_id');
        $twitchSecret = $argSecret ?? $this->params->get('app.twitch_secret');

        $request = $this->client->request('POST', 'https://id.twitch.tv/oauth2/token', [
            'query' => [
                'client_id' => $twitchId,
                'client_secret' => $twitchSecret,
                'grant_type' => 'client_credentials',

            ]
        ]);

        $message = '';
        try {
            if (200 == $request->getStatusCode()) {
                $response = $request->toArray();
                if (array_key_exists('access_token', $response)) {
                    $token = $response['access_token'];
                    $io->success("The new token is ${token}.");

                    if ($argFile) {
                        if ($this->file->exists($argFile)) {
                            $newFile = '';
                            $envFile = fopen($argFile, 'r+');
                            while(!feof($envFile)) {
                                $line = fgets($envFile);
                                if (str_starts_with($line, 'TWITCH_APP_TOKEN')) {
                                    $line = "TWITCH_APP_TOKEN=${token}\n";
                                }
                                $newFile .= $line;
                            }
                        } else {
                            $newFile = "TWITCH_APP_TOKEN=${token}\n";
                        }
                        $this->file->dumpFile($argFile, $newFile);
                        $io->success("Wrote the new token to ${argFile}");
                    }

                    return 0;
                } else {
                    $message = "No token in successful twitch response. Check to see if the API has changed.";
                }
            }

            $message = "Twitch returned status code ".$request->getStatusCode();
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }
        $io->error($message);
        return -1;
    }

    /* From Laravel */
    private function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
