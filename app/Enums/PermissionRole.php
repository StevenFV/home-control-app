<?php

namespace App\Enums;

use ArchTech\Enums\Names;

enum PermissionRole: string
{
    use Names;

    /*
     * When this enum is modified, the enum "resources/js/Enums/PermissionRole.js"
     * have to be change too for consistance with Vue.js.
    */

    case ADMIN = 'admin';
}
