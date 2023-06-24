import {usePage} from '@inertiajs/vue3';

export function usePermissions() {
    const hasRole = (name) => usePage().props.auth.user.roles.includes(name);
    const hasPermission = (...names) => names.filter(name => usePage().props.auth.user.permissions.includes(name)).length === names.length;
    const hasAnyPermission = (...names) => names.filter(name => usePage().props.auth.user.permissions.includes(name)).length > 0;

    return {hasRole, hasPermission, hasAnyPermission};
}
