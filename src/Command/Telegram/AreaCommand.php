<?php
declare(strict_types=1);


namespace App\Command\Telegram;

use App\Entity\Area;
use App\Entity\User;
use App\Entity\UserSettings;
use Telegram\Bot\Keyboard\Keyboard;

class AreaCommand  extends MainCommand
{
    /**
     * @var string Command Name
     */
    protected $name = "area";

    /**
     * @var string Command Name
     */
    protected $backAction = "settings";
    /**
     * @var string Command Description
     */
    protected $description = "Район";

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

        $areasData = $this->entityManager->getRepository(Area::class)->findAll();

        $areas = [];
        foreach ($areasData as $area) {
            $areas[$area->getId()] = $area->getName();
        }

        $areaUser = $this->userSettings->getArea() ?? [];

        if (isset($this->getUpdate()['callback_query'])) {
            $areaSelected = (int) $this->getUpdate()['callback_query']['data'];

            if (!in_array($areaSelected, $areaUser)) {
                $areaUser = array_merge($areaUser, [$areaSelected]);
            } else {
                unset($areaUser[array_search($areaSelected,$areaUser)]);
                $areaUser = array_values($areaUser);
            }
            $this->userSettings->setArea($areaUser);
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

        $inlineLayout = $this->getKeyboard($areas,$areaUser);

        $reply_markup = Keyboard::make([
            'inline_keyboard' => $inlineLayout,
            'resize_keyboard' => true
        ]);

        $this->replyWithMessage([
            'text' => 'Районы:',
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
