<?php

namespace App\Command;

use League\HTMLToMarkdown\HtmlConverterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'app:codeur:send-offer')]
class SendCodeurOffer extends Command
{
    public function __construct(
        private readonly HttpClientInterface    $client,
        private readonly HtmlConverterInterface $htmlConverter,
        private readonly string                 $codeurFilePath
    )
    {
        parent::__construct();
    }

    /**
     * @throws ExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $content = $this->client->request('GET', 'https://www.codeur.com/projects', [
            'query' => [
                'c' => 'developpement',
                'format' => 'rss',
                'sc' => 'framework-zend-symfony',
            ]
        ])->getContent();

        $data = json_decode(json_encode(simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $lastGuid = intval(Yaml::parseFile($this->codeurFilePath)['last_guid_sent']);
        $items = array_reverse($data['channel']['item']);

        $messages = [];
        foreach ($items as ['title' => $title, 'pubDate' => $pubDate, 'description' => $description, 'guid' => $guid, 'link' => $link]) {
            $title = $this->htmlConverter->convert($title);
            $guid = intval($guid);
            $pubDate = new \DateTime($pubDate);
            $description = $this->htmlConverter->convert(trim($description));
            if ($guid <= $lastGuid) {
                continue;
            }
            $lastGuid = $guid;
            $lines = explode("\n", $description);
            $description = [];
            for ($i = 0; $i < count($lines) - 1; $i++) {
                if (empty($lines[$i])) {
                    continue;
                }

                $description[] = trim($lines[$i]);
            }
            $description = preg_replace('/https?:\/\/[^\s]+/', '<$0>', implode("\n", $description));
            $messages[] = "## [$title](<$link>)\n**{$pubDate->format('d/m/y H:i')}**\n```$description```";
        }
        file_put_contents($this->codeurFilePath, Yaml::dump(['last_guid_sent' => $lastGuid]));
        if (!empty($messages)) {
            $arguments = [
                'user' => 'Jean',
                'message' => $messages,
            ];
            $command = $this->getApplication()->find('app:discord:send-message');
            return $command->run(new ArrayInput($arguments), $output);
        }

        return Command::SUCCESS;
    }
}