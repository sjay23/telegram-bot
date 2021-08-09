<?php
declare(strict_types=1);

namespace App\Command\Telegram;

use App\Entity\User;
use App\Entity\UserSettings;
use Telegram\Bot\Actions;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * Class AdsCommand
 * @package App\Command\Telegram
 */
class AdsCommand  extends MainCommand
{

    /**
     * @var string Command Name
     */
    protected $name = "ads";

    /**
     * @var string Command Name
     */
    protected $backAction = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Показать подходящие объекты";

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

    protected $api;

    public function handle()
    {
        if ($this->user->getLastCommand() !== $this->name) {
            $this->setLastNameCommand($this->name);
        }

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $keyboard = $this->getCommandNameForKeyboard();

        if (isset($commands[$this->backAction])) {
            $keyboard[] = ['⬅️Назад в меню'];
        }

        $reply_markup = Keyboard::make([
           'keyboard' => $keyboard,
           'resize_keyboard' => true,
           'one_time_keyboard' => true
        ]);

        $filters = $this->getAllFilters();
        $this->replyWithMessage([
           'text' => "Ваши фильтры:\n" . implode("\n", $filters),
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
