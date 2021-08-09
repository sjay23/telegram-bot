<?php
declare(strict_types=1);


namespace App\Command\Telegram;

use App\Entity\Room;
use App\Entity\User;
use App\Entity\UserSettings;
use Telegram\Bot\Keyboard\Keyboard;

class RoomCommand extends MainCommand
{

    /**
     * @var string Command Name
     */
    protected $name = "room";

    /**
     * @var string Command Name
     */
    protected $backAction = "settings";
    /**
     * @var string Command Description
     */
    protected $description = "Количество комнат";

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
        $rooms = Room::getRooms();

        $roomUser = $this->userSettings->getRooms() ?? [];

        if (isset($this->getUpdate()['callback_query'])) {
            $roomSelected = (int) $this->getUpdate()['callback_query']['data'];

            if (!in_array($roomSelected, $roomUser)) {
                $roomUser = array_merge($roomUser, [$roomSelected]);
            } else {
                unset($roomUser[array_search($roomSelected,$roomUser)]);
                $roomUser = array_values($roomUser);
            }
            $this->userSettings->setRooms($roomUser);
            $this->entityManager->persist($this->userSettings);
            $this->entityManager->flush();
        }

        if ($this->user->getLastCommand() !== $this->name) {
            $this->setLastNameCommand($this->name);

            $keyboard = [];
            $keyboard[] = ['⬅️Назад в меню'];

            $reply_markup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);

            $this->replyWithMessage([
                'text' => "Выберите районы",
                'chat_id' => $this->getUpdate()->getChat()->id,
                'reply_markup' => $reply_markup
            ]);
        }

        $inlineLayout = $this->getKeyboard($rooms,$roomUser);
        $reply_markup = Keyboard::make([
            'inline_keyboard' => $inlineLayout,
            'resize_keyboard' => true
        ]);

        $this->replyWithMessage([
            'text' => 'Выберите количество комнат:',
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
