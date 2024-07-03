<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Filesystem\Filesystem;
use Aws\Ssm\SsmClient;
use Psr\Log\LoggerInterface;
use Throwable;
use App\Traits\ErrorLogTrait;

#[AsCommand(
    name: 'app:refresh-twitch-token',
    description: 'Gets a new App Token from the Twitch ID service'
)]
class RefreshTwitchTokenCommand extends Command
{
    use ErrorLogTrait;

    private HttpClientInterface $client;
    private ParameterBagInterface $params;
    private Filesystem $file;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Filesystem $file, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->params = $params;
        $this->file = $file;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // ->setDescription(self::$defaultDescription)
            ->addArgument('environment_type', InputArgument::REQUIRED, 'Environment type.')
            ->addOption('topicId', 'i', InputOption::VALUE_OPTIONAL, 'Your Client ID for your Twitch application')
            ->addOption('twitchSecret', 's', InputOption::VALUE_OPTIONAL, 'Your Client Secret for your Twitch application')
            ->addOption('envFile', 'f', InputOption::VALUE_OPTIONAL, 'The .env.local file to rewrite the TWITCH_APP_TOKEN variable');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {

            $env_parameters = [
                'dev' => 'dev/web',
                'demo' => 'dev/demo/web',
                'prod' => 'prod/web',
                'prod-m' => 'prod/m'
            ];
            $this->logger->debug("CronJob Is running.");

            $envirenment = $input->getArgument('environment_type');
            $env = $env_parameters[$envirenment];

            $params = [];
            $credentials = [
                'key'    => $this->params->get('app.aws_access_key_id'),
                'secret' => $this->params->get('app.aws_secret_access_key')
            ];

            // Create an instance of the SSM Client
            $client = new SsmClient([
                'version'     => 'latest',
                'region' => 'us-west-1',
                'credentials' => $credentials
            ]);

            $result = $client->getParameters([
                'Names' => ["/gamersx/$env/TWITCH_CLIENT_ID", "/gamersx/$env/TWITCH_CLIENT_SECRET"],
                'WithDecryption' => true
            ]);

            $parameters = $result['Parameters'];

            foreach ($parameters as $parameter) {
                $params[$parameter['Name']] = $parameter['Value'];
            }
        } catch (Throwable $th) {
            $this->logger->error($th->getMessage());
            $this->log_error($th->getMessage(), 500, 'aws_paraameter');
            return 1;
        }


        $io = new SymfonyStyle($input, $output);
        $argFile = $input->getOption('envFile');

        $topicId = !empty($params["/gamersx/$env/TWITCH_CLIENT_ID"]) ? $params["/gamersx/$env/TWITCH_CLIENT_ID"] : $this->params->get('app.twitch_id');
        $twitchSecret = !empty($params["/gamersx/$env/TWITCH_CLIENT_SECRET"]) ? $params["/gamersx/$env/TWITCH_CLIENT_SECRET"] : $this->params->get('app.twitch_secret');

        $request = $this->client->request('POST', 'https://id.twitch.tv/oauth2/token', [
            'query' => [
                'client_id' => $topicId,
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
                    $io->success("The new token is $token.");

                    $result = $client->putParameter([
                        'Name' => "/gamersx/$env/TWITCH_APP_TOKEN",
                        'Overwrite' => true,
                        'Value' => $token,
                    ]);

                    if ($argFile) {
                        if ($this->file->exists($argFile)) {
                            $newFile = '';
                            $envFile = fopen($argFile, 'r+');
                            while (!feof($envFile)) {
                                $line = fgets($envFile);
                                if (str_starts_with($line, 'TWITCH_APP_TOKEN')) {
                                    $line = "TWITCH_APP_TOKEN=$token\n";
                                }
                                $newFile .= $line;
                            }
                        } else {
                            $newFile = "TWITCH_APP_TOKEN=$token\n";
                        }
                        $this->file->dumpFile($argFile, $newFile);
                        $io->success("Wrote the new token to $argFile");
                    }

                    return 0;
                } else {
                    $message = "No token in successful twitch response. Check to see if the API has changed.";
                }
            }

            $message = "Twitch returned status code " . $request->getStatusCode();
            $this->log_error($request->getContent(false), $request->getStatusCode(), "twitch_token_generation");
        } catch (\Exception $ex) {
            $msg = $ex->getMessage()." ".$ex->getFile() . " " .$ex->getLine();
            $this->logger->error($msg);
            $this->log_error($msg, 500, "twitch_token");
        }
        $io->error($message);
        $this->logger->debug($message);
        return -1;
    }

    /* From Laravel */
//    private function str_starts_with($haystack, $needle): bool
//    {
//        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
//    }
}
