<?php
declare(strict_types=1);

namespace App\Command\Telegram;

use App\Entity\Area;
use App\Entity\RentOrSale;
use App\Entity\Room;
use App\Entity\Type;
use App\Entity\UserSettings;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * Class MainCommand
 * @package App\Command\Telegram
 */
class MainCommand  extends Command implements OptionsAwareInterface
{
    /**
     * @var string Command Name
     */
    protected $name = '';

    /**
     * @var string Command Name
     */
    protected $backAction = null;
    /**
     * @var string Command Description
     */
    protected $description;

    protected $entityManager;

    /**
     * @var User $user
     */
    protected $user;
    /**
     * @var UserSettings $userSettings
     */
    protected $userSettings;
    protected $api;

    protected $telegram;

    public function __construct(
        EntityManagerInterface $entityManager,
        Api $api
    )
    {
        $this->entityManager = $entityManager;
        $this->api = $api;

        $userData = $this->api->getWebhookUpdate()->getChat();
        $this->user = $this->entityManager->getRepository(User::class)->findOneBy([
            'username' => $userData->username
        ]);
        $this->userSettings = $this->entityManager->getRepository(UserSettings::class)->findOneBy(['userId' => $this->user->getId()]);
    }

    public function handle()
    {
    }

    public function getOptions(): array
    {
    }

    public function getBackAction(): string
    {
    }

    protected function getCommandNameForKeyboard()
    {
        $commands = $this->api->getCommands();
        $keyboard = [];
        foreach ($this->getOptions() as $nameAction) {
            if (isset($commands[$nameAction]))
                /* @var Command $handler */
                $keyboard[] = [$commands[$nameAction]->getDescription()];
        }
        return $keyboard;
    }

    protected function setLastNameCommand($command): bool
    {
        $this->user->setLastCommand($command);
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
        return true;
    }

    protected function getAllFilters(): array
    {
        if ($this->userSettings->getArea() != null) {
            $area = $this->entityManager->getRepository(Area::class)->getSelectedAreaName($this->userSettings->getArea());
        } else {
            $area = null;
        }

        $filters = [
            'area' => $area,
            'rent_or_sale' => $this->userSettings->getRentOrSaleValue(),
            'type' => $this->userSettings->getTypeValue(),
            'rooms' => $this->userSettings->getRoomsValue(),
            'price_from' => $this->userSettings->getPriceFrom(),
            'price_to' => $this->userSettings->getPriceTo(),
            'keyword' => $this->userSettings->getKeywordValue(),
        ];

        $result = [];
        foreach ($filters as $actionName => $filter) {
            $commands = $this->api->getCommands();
            if (isset($commands[$actionName])) {
                /* @var Command $handler */
                $description = $commands[$actionName]->getDescription();
                $result[] = $description . ': ' . ($filter ? $filter : ' - ');
            }
        }
        return $result;

    }

    protected function getKeyboard($arrAll, $arrSelected): array
    {
        $left = [];
        $right = [];
        $i = 0;
        foreach ($arrAll as $id => $item) {
            $i++;
            $text = in_array($id, $arrSelected) ? "✅ " . $item : $item;
            if ($i % 2 != 0) {
                $left[] = ['value' => $text, 'id' => $id];
            } else {
                $right[] = ['value' => $text, 'id' => $id];
            }
        }

        $inlineLayout = [];
        foreach ($left as $key => $leftArea){
            $inlineButton = [];
            $inlineButton[] = Keyboard::inlineButton(['text' => $leftArea['value'], 'callback_data' => $leftArea['id']]);
            if (isset($right[$key])) {
                $inlineButton[] = Keyboard::inlineButton(['text' => $right[$key]['value'], 'callback_data' => $right[$key]['id']]);
            }
            $inlineLayout[] = $inlineButton;
        }

        return $inlineLayout;
    }
}
