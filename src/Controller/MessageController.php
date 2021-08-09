<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserSettings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Telegram\Bot\Api;

class MessageController extends AbstractController
{
    private $commands;

    private $commandsByName;

    public function __construct(iterable $commands)
    {
        $this->commands = $commands;
        foreach ($commands as $command) {
            if ($command->getName() != '') {
                $this->commandsByName[$command->getName()] = $command;
            }
        }
    }

    /**
     * @Route("/", name="routing", methods={"POST"})
     */
    public function index(Api $bot, EntityManagerInterface $em): Response
    {
        $userData = $bot->getWebhookUpdate()->getChat();
        $user = $em->getRepository(User::class)->findOneBy([
            'username' => $userData->username
        ]);
        if (empty($user)) {
            $user = new User();
            $user->setUsername($userData->username);
            $user->setName($userData->first_name);
            $user->setLastName($userData->last_name);
            $user->setTelegramId($userData->id);
            $em->persist($user);
            $em->flush();

            $userSettings = new UserSettings();
            $userSettings->setUserId($user->getId());
            $em->persist($userSettings);
            $em->flush();
        }

        try {
            // запуск команд через слеш: /start
            if ($bot->getWebhookUpdate()->hasCommand()) {
                foreach ($this->commands as $command) {
                    $bot->addCommand($command);
                }
                $bot->commandsHandler(true);
                return new Response('command accepted');
            }

            $commandMessage = $bot->getWebhookUpdate()->getMessage()->text;

            $commandByText = null;
            foreach ($this->commands as $command) {
                if($command->getName() != '') {
                    $bot->addCommand($command);
                    $bot->commandsHandler(true);
                    if ($command->getDescription() == $commandMessage) {
                        $commandByText = $command->getName();
                    }
                }
            }

            if (!empty($user->getLastCommand()) && $commandByText == null) {
                $commandBack = $user->getLastCommand();
                if ($bot->getWebhookUpdate()->getMessage()->text == '⬅️Назад в меню') {
                    $commandBack = $this->commandsByName[$user->getLastCommand()]->getBackAction();
                }
                $commandMessage = $this->commandsByName[$commandBack]->getDescription();

                $commandByText = '';
                foreach ($this->commands as $command) {
                    if($command->getName() != '') {
                        if ($command->getDescription() == $commandMessage) {
                            $commandByText = $command->getName();
                        }
                    }
                }
            }


            if ($commandByText !== '') {
                $bot->triggerCommand($commandByText, $bot->commandsHandler(true));
            }

            return new Response('');
        } catch(\Throwable $e) {
            $bot->sendMessage([
                'chat_id' => $bot->getWebhookUpdate()->getChat()->id,
                'text' => $e->getMessage()
            ]);
        }
        return new Response('not a command', 200);
    }
}
