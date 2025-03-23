<?php

namespace Project\Enums\Router;

enum RouteEnum: string
{
    //TODO это не будет использоваться
    case KURAGA = "курага";
    case WATSON = "ватсон";
    case VASILISA = "василиса";
    case LIKE = "like";
    case UNLIKE = "unlike";

    /**
     * @return string
     */
    public function name(): string
    {
        return match ($this) {
            self::KURAGA => "show-kuraga-image",     //showCatKuragaImage
            self::WATSON => "show-watson-image",     //showCatWatsonImage
            self::VASILISA => "show-vasilisa-image",     //showCatVasilisaImage
            self::LIKE => "like",     //like
            self::UNLIKE => "unlike",     //unlike
        };
    }
}