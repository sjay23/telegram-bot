<?php
declare(strict_types=1);

namespace App\Command\Telegram;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * Class StartCommand
 * @package App\Command\Telegram
 */
class PhoneCommand  extends MainCommand
{
    /**
     * @var string Command Name
     */
    protected $name = "phone";

    /**
     * @var string Command Name
     */
    protected $backAction = "start";
    /**
     * @var string Command Description
     */
    protected $description = "Указать номер";

    protected $entityManager;
    /**
     * @var User $user
     */
    protected $user;
    /**
     * @var UserSettings $userSettings
     */
    protected $userSettings;
    protected $telegram;

    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        if ($this->user->getLastCommand() !== $this->name) {
            $this->setLastNameCommand($this->name);
        }

        if ($this->getUpdate()->getMessage()->contact != null) {
            $phone = $this->getUpdate()->getMessage()->contact->phoneNumber;
            $this->user->setPhone($phone);
            $this->entityManager->persist($this->user);
            $this->entityManager->flush();

            $keyboard = [];
            $keyboard[] = ['⬅️Назад в меню'];

            $reply_markup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);

            $text = 'Ваш номер успешно добавлен!';

            $this->replyWithMessage([
                'text' => $text,
                'chat_id' => $this->getUpdate()->getChat()->id,
                'reply_markup' => $reply_markup
            ]);

            return new Response('phone add', 200);
        }


        $keyboard = [];

        $keyboard[] = [Keyboard::button(['text' => 'Поделиться своим номером', 'request_contact' => true])];
        $keyboard[] = ['⬅️Назад в меню'];

        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $text = 'Отправьте свой номер телефона';
        $this->replyWithMessage([
            'text' => $text,
            'chat_id' => $this->getUpdate()->getChat()->id,
            'reply_markup' => $reply_markup
        ]);
    }

    public function getOptions(): array
    {
        return [];
    }

    public function getBackAction(): string
    {
        return $this->backAction;
    }
}
