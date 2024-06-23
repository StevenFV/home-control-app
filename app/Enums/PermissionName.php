<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Values;

enum PermissionName: string
{
    use Names;
    use Values;

    /*
     * When this enum is modified, the corresponding "resources/js/Enums/PermissionName.js" must also be changed to
     * maintain consistency with Vue.js.
    */

    case VIEW_LIGHTING = 'view lighting';
    case CONTROL_LIGHTING = 'control lighting';
}
