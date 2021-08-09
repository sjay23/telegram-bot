<?php
declare(strict_types=1);

namespace App\Command\Telegram;

use App\Entity\User;
use App\Entity\UserSettings;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * Class KeywordCommand
 * @package App\Command\Telegram
 */
class KeywordCommand  extends MainCommand
{

    /**
     * @var string Command Name
     */
    protected $name = "keyword";

    /**
     * @var string Command Name
     */
    protected $backAction = "settings";
    /**
     * @var string Command Description
     */
    protected $description = "Ключевые слова";

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
        $keywordUser = $this->userSettings->getKeyword() ?? null;

        if ($this->user->getLastCommand() !== $this->name) {
            $this->setLastNameCommand($this->name);
            $keyboard = [];

            $keyboard[] = ['⬅️Назад в меню'];

            $reply_markup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);

            $text = 'Введите ключевые слова через запятую:';
            $this->replyWithMessage([
                'text' => $text,
                'chat_id' => $this->getUpdate()->getChat()->id,
                'reply_markup' => $reply_markup
            ]);
        } else {
            $text = $this->getUpdate()->getMessage();

            $keyword = explode(',', $text['text']);
            $this->userSettings->setKeyword($keyword);
            $this->entityManager->persist($this->userSettings);
            $this->entityManager->flush();

            $keyboard[] = ['⬅️Назад в меню'];

            $this->telegram->triggerCommand($this->backAction, $this->telegram->commandsHandler(true));
            return true;
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
