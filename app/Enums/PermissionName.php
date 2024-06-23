<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Values;

enum PermissionName: string
{
    use Names;
    use Values;

    /*
     * When this enum is modified, the enum "resources/js/Enums/PermissionName.js"
     * have to be change too for consistance with Vue.js.
    */

    case VIEW_LIGHTING = 'view lighting';
    case CONTROL_LIGHTING = 'control lighting';
}
