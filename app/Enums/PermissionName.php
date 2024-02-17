<?php

namespace App\Enums;

use ArchTech\Enums\Names;

enum PermissionName: string
{
    use Names;

    /*
     * When this enum is modified, the enum "resources/js/Enums/PermissionName.js"
     * have to be change too for consistance with Vue.js.
    */

    case VIEW_LIGHTING = 'view lighting';
    case CONTROL_LIGHTING = 'control lighting';
}
