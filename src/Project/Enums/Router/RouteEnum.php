<?php

namespace Project\Enums\Router;

enum RouteEnum: string
{
    //TODO это не будет использоваться
    case START = "/start";
    case ABOUT_ME = "обо мне";
    case COMMANDS_LIST = "список команд";
    case KURAGA = "курага";
    case WATSON = "ватсон";
    case VASILISA = "василиса";
    case LIKE = "like";
    case UNLIKE = "unlike";
    case TEST_NOTIFICATION = "test_notification";
    case DAILY_NOTIFICATION = "daily_notification";
    case USE_BUTTONS = "use_buttons";

    /**
     * @return string
     */
    public function name(): string
    {
        return match ($this) {
            self::START => "start",     //startBot
            self::ABOUT_ME => "about-me",     //showAboutBotInfo
            self::COMMANDS_LIST => "commands",     //showCommandsList
            self::KURAGA => "show-kuraga-image",     //showCatKuragaImage
            self::WATSON => "show-watson-image",     //showCatWatsonImage
            self::VASILISA => "show-vasilisa-image",     //showCatVasilisaImage
            self::LIKE => "like",     //like
            self::UNLIKE => "unlike",     //unlike
            self::TEST_NOTIFICATION => "test-notification",     //notifyTestMembers
            self::DAILY_NOTIFICATION => "daily-notification",     //notifyMembers
            self::USE_BUTTONS => "use-buttons-msg"     //useButtonsMessage
        };
    }
}