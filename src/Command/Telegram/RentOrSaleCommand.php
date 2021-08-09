<?php
declare(strict_types=1);

namespace App\Command\Telegram;

use App\Entity\RentOrSale;
use App\Entity\User;
use App\Entity\UserSettings;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * Class StartCommand
 * @package App\Command\Telegram
 */
class RentOrSaleCommand extends MainCommand
{

    /**
     * @var string Command Name
     */
    protected $name = "rent_or_sale";

    /**
     * @var string Command Name
     */
    protected $backAction = "settings";
    /**
     * @var string Command Description
     */
    protected $description = "Выберите Аренда или Продажа";

    protected $entityManager;
    /**
     * @var User $user
     */
    protected $user;
    /**
     * @var UserSettings $userSettings
     */
    protected $userSettings;
    /**
     * @var Api $telegram
     */
    protected $telegram;

    public function handle()
    {
        $rentOrSaleUser = $this->userSettings->getRentOrSale() ?? null;

        $types = RentOrSale::getTypes();

        $text = $this->getUpdate()->getMessage();
        foreach ($types as $typeId => $type) {
            if($text['text'] == $type) {
                $this->userSettings->setRentOrSale($typeId);
                $this->entityManager->persist($this->userSettings);
                $this->entityManager->flush();

                $this->setLastNameCommand($this->backAction);

                $this->telegram->triggerCommand($this->backAction, $this->telegram->commandsHandler(true));
                return true;
            }
        }

        if ($this->user->getLastCommand() !== $this->name) {
            $this->setLastNameCommand($this->name);
            $keyboard = [];

            foreach ($types as $typeId => $type) {
                $keyboard[] = [$type];
            }

            $keyboard[] = ['⬅️Назад в меню'];

            $reply_markup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);

            $text = '🏢 Выберите Аренда или Продажа';
            $this->replyWithMessage([
                'text' => $text,
                'chat_id' => $this->getUpdate()->getChat()->id,
                'reply_markup' => $reply_markup
            ]);
        }
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
